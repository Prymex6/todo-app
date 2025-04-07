<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'priority_label' => $this->priority_label,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'due_date' => $this->due_date->format('Y-m-d H:i:s'),
            'due_date_human' => $this->due_date->diffForHumans(),
            'is_overdue' => $this->isOverdue(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Relationships
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            
            'shares' => $this->whenLoaded('shares', function () {
                return $this->shares->map(function ($share) {
                    return [
                        'id' => $share->id,
                        'token' => $share->token,
                        'expires_at' => $share->expires_at->format('Y-m-d H:i:s'),
                        'is_valid' => $share->isValid(),
                        'allow_editing' => $share->allow_editing,
                        'shared_with_email' => $share->shared_with_email,
                    ];
                });
            }),
            
            'histories_count' => $this->whenCounted('histories'),
            
            'google_calendar_event' => $this->whenLoaded('googleCalendarEvent', function () {
                return [
                    'id' => $this->googleCalendarEvent->id,
                    'link' => $this->googleCalendarEvent->link,
                ];
            }),
        ];
    }
}