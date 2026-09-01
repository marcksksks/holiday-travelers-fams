<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'app_role' => $this->app_role,
            'department' => $this->department,
            'job_title' => $this->job_title,
            'is_active' => $this->is_active,
        ];
    }
}
