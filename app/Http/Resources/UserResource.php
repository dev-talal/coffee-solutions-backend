<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role = $this->roles()->first();
        $user = [
            'id'    => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->getRoleNames()->first(),
            'profile' => $this->profile,
            'phone' => $this->phone,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
        if($this->getRoleNames()->first() == 'customer') {
            $user['credit'] = $this->credit_limit;
        }else{
            $user['permissions'] = $role->permissions->pluck('name')->toArray();
        }
        return $user;
    }
}
