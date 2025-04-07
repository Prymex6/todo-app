<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskHistoryResource extends JsonResource
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
            'event_type' => $this->event_type,
            'event_type_label' => $this->event_type_label,
            'changed_field' => $this->changed_field,
            'change_comment' => $this->change_comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            
            // Changes data
            'before' => $this->before,
            'after' => $this->after,
            'changes' => $this->changes,
            
            // Relationships
            'task' => [
                'id' => $this->task->id,
                'name' => $this->task->name,
            ],
            
            'changer' => $this->whenLoaded('changer', function () {
                return [
                    'id' => $this->changer->id,
                    'name' => $this->changer->name,
                    'email' => $this->changer->email,
                ];
            }),
        ];
    }
}