@extends('layouts.guest')

@section('title', $task->name)

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">{{ $task->name }}</h1>
                @if($share->allow_editing)
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Editable</span>
                @endif
            </div>
            
            <div class="mb-4">
                <p class="text-gray-600">{{ $task->description }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Priority</h3>
                    <p class="mt-1 text-sm">
                        <span class="px-2 py-1 bg-{{ $task->priority == 'high' ? 'red' : ($task->priority == 'medium' ? 'yellow' : 'green') }}-100 text-{{ $task->priority == 'high' ? 'red' : ($task->priority == 'medium' ? 'yellow' : 'green') }}-800 rounded">
                            {{ $task->priority_label }}
                        </span>
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1 text-sm">{{ $task->status_label }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Due Date</h3>
                    <p class="mt-1 text-sm {{ $task->isOverdue() ? 'text-red-600' : '' }}">
                        {{ $task->due_date->format('M d, Y H:i') }}
                        @if($task->isOverdue())
                            (Overdue)
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <p class="text-sm text-gray-600"><strong>Shared by:</strong> {{ $share->owner->name }}</p>
                <p class="text-sm text-gray-600"><strong>Expires:</strong> {{ $share->expires_at->format('Y-m-d H:i') }}</p>
                <p class="text-sm text-gray-600"><strong>Uses:</strong> {{ $share->use_count }}@if($share->max_uses)/{{ $share->max_uses }}@endif</p>
            </div>
        </div>
    </div>
</div>
@endsection