<?php

namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use App\Models\UserBiometric;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponseTrait;
    protected $users;
    public function __construct(UserService $users)
    {
        $this->users = $users;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        $user = auth()->user();
        $token = $user->createToken('Personal Access Token');
        $accessToken = $token->accessToken;
        $expiresAt = Carbon::parse($token->token->expires_at)->toDateTimeString();
        if ($request->public_key) {
            UserBiometric::updateOrCreate(
                ['user_id' => $user->id],
                ['public_key' => $request->public_key]
            );
        }

        $tokenResult = [
            'role' => $user->roles[0]->name ?? 'admin',
            'access_token' => $accessToken,
            'expires_at' => $expiresAt,
            'user_id' => $user->id,
        ];

        return $this->success($tokenResult, 'Login successful');
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return $this->success(null, 'Logged out successfully');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = $this->users->forgetPassword($request->email);
        return $this->success($request->email, 'OTP sent successfully');
    }

    public function me()
    {
        $user = auth()->user();
        return $this->successResource($user, UserResource::class, 'User retrieved successfully');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $this->users->resetPassword($request->email, $request->otp, $request->password);
        if (!$user) {
            return $this->error('Invalid or expired OTP', 400);
        }

        return $this->success(null, 'Password reset successfully');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!$this->users->changePassword($request->old_password, $request->new_password)) {
            return $this->error('Old password is incorrect', 400);
        }

        return $this->success(null, 'Password changed successfully');
    }

    public function updateProfile(Request $request)
    {
        $profile = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'profile' => 'nullable',
        ]);
        // Log::info('profile', $profile);
        $user = $this->users->update($profile, auth()->id());
        return $this->successResource($user, UserResource::class, 'User retrieved successfully');
    }
}

?>