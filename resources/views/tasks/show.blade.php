@extends('layouts.app')

@section('title', $task->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $task->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('tasks.edit', $task) }}" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if(Auth::user()->google_id)
            <form action="{{ route('tasks.google.sync', $task) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    Sync with Google Calendar
                </button>
            </form>
        @else
            <a href="{{ route('google.login') }}" class="btn btn-primary">
                Connect Google Account
            </a>
        @endif
        <div class="p-6">
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
                <h3 class="text-sm font-medium text-gray-500">Created</h3>
                <p class="mt-1 text-sm">{{ $task->created_at->format('M d, Y H:i') }} by {{ $task->user->name }}</p>
            </div>
        </div>

        @if($activeShares->count())
            <div class="mt-8">
                <h3 class="text-lg font-medium mb-2">Active Share Links</h3>
                <div class="space-y-2">
                    @foreach($activeShares as $share)
                        <div class="p-3 border rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm break-all">
                                        <span class="font-medium">Link:</span> 
                                        <a href="{{ route('tasks.shares.show', $share->token) }}" target="_blank" class="text-blue-500 hover:underline">
                                            {{ route('tasks.shares.show', $share->token) }}
                                        </a>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Expires: {{ $share->expires_at->format('M d, Y H:i') }} | 
                                        Uses: {{ $share->use_count }}@if($share->max_uses)/{{ $share->max_uses }}@endif | 
                                        {{ $share->allow_editing ? 'Can edit' : 'View only' }} |
                                        Shared with: {{ $share->shared_with_email ?? 'Anyone with link' }}
                                    </p>
                                </div>
                                <form action="{{ route('tasks.shares.destroy', ['task' => $task, 'share' => $share]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to revoke this share link?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border-t px-6 py-4 bg-gray-50">
            <div class="flex justify-between items-center">
                <a href="{{ route('tasks.history.index', $task) }}" class="text-blue-500 hover:underline">View History</a>
                <a href="{{ route('tasks.shares.create', $task) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">
                    Share Task
                </a>
            </div>
        </div>
    </div>
</div>
@endsection