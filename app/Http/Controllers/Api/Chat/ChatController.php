<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MediaMessage;
use App\Models\Message;
use App\Models\Room;
use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Traits\ApiResponseTrait;
use App\Services\MessageService;
use App\Services\RoomService;
use App\Services\CommonService;
use App\Events\MessageSent;

class ChatController extends Controller
{
    use ApiResponseTrait;
    protected $messageService;
    protected $roomService;
    protected $commonService;
    public function __construct(MessageService $messageService, RoomService $roomService, CommonService $commonService)
    {
        $this->messageService = $messageService;
        $this->roomService = $roomService;
        $this->commonService = $commonService;
    }

    public function getRooms() {
        $rooms = $this->roomService->getUserRooms();
        return $this->successCollection($rooms, RoomResource::class, 'Rooms retrieved successfully');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $data = $this->messageService->getMessages($request->query('room_id'), $page);
        $room = $this->roomService->find($request->query('room_id'));
        return response()->json([
            'data' => MessageResource::collection($data['data']->items()),
            'current_page' => $data['data']->currentPage(),
            'has_more' => $data['data']->hasMorePages(),
            'unread_count' => $data['unread_count'],
            'room' => new RoomResource($room),
        ]);
    }

    public function getCustomerChat(Request $request)
    {
        $user = $request->user();
        $page = (int) $request->query('page', 1);
        $countOnly = $request->query('count', 0);
        $perPage = 20;

        $room = Room::where('user_id', $user->id)->first();

        if (!$room) {
            return response()->json([
                'message' => 'No chat room found for this user.'
            ], 404);
        }

        $unreadQuery = Message::where('room_id', $room->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0);

        if ($page == 1 && !$countOnly) {
            $unreadQuery->update(['is_read' => 1]);
        }

        $messageCount = Message::where('room_id', $room->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0)
            ->count();

        $messages = Message::where('room_id', $room->id)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => MessageResource::collection($messages->items()),
            'current_page' => $messages->currentPage(),
            'has_more' => $messages->hasMorePages(),
            'unread_count' => $messageCount,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'sometimes|exists:rooms,id',
            'message' => 'required_without:media|string|nullable',
            'media'   => 'required_without:message|array|nullable',
            'media.*.url'  => 'required_with:media|url',
            'media.*.type' => 'required_with:media|string',
            'media.*.name' => 'required_with:media|string',
        ]);
        $payload = [
            'sender_id' => auth()->user()->id,
            'message' => $data['message'] ?? '',
        ];
        if(!isset($data['room_id'])) {
            $roomId = auth()->user()->getCustomerRoom->id;
            $receiver_id = $this->roomService->getReceiverId($roomId);
            $payload['room_id'] = $roomId;
        }
        else {
            $receiver_id = $this->roomService->getReceiverId($data['room_id']);
            $payload['room_id'] = $data['room_id'];
        }

        $message = $this->messageService->createMessage($payload);
       

        if(isset($data['media'])) {
            foreach($data['media'] as $media) {
              MediaMessage::create([
                'message_id' => $message['id'],
                'file' => $media['url'],
                'type' => $media['type'],
                'file_name' => $media['name'],
              ]);
            }
        }
        if($message) {
            event(new MessageSent(new MessageResource($message), $receiver_id));
        }
        return $this->successResource($message, MessageResource::class, 'Message created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = $this->messageService->find($id);
        if (!$message) {
            return $this->error('Message not found', 404);
        }
        return $this->successResource($message, MessageResource::class, 'Message retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $message = $request->validated();
        $message = $this->messageService->updateMessage($id, $message);
        return $this->successResource($message, MessageResource::class, 'Message updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = $this->messageService->find($id);
        if (!$message) {
            return $this->error('Message not found', 404);
        }
        $this->messageService->deleteMessage($id);
        return $this->success('Message deleted successfully');
    }

    public function uploadChatMedia(Request $request) {
       $data = $request->validate([
        'media' => 'required|file|max:5120',
        ], 
        [
            'media.max' => 'The file size must not exceed 5 MB.',
            'media.required' => 'Please select a file to upload.',
        ]);
        $path = $this->commonService->uploadFile($data['media'], 'chat-media');
        return $this->success($path);
    }
}