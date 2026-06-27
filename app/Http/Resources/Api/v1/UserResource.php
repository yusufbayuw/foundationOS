<?php

namespace App\Http\Resources\Api\v1;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Models\User;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;
        $createdAt = $user->getAttribute('created_at');

        return [
            'id' => $user->getKey(),
            'name' => $user->getAttribute('name'),
            'email' => $user->getAttribute('email'),
            'created_at' => $createdAt instanceof DateTimeInterface ? $createdAt->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
