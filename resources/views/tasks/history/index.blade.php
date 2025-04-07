@extends('layouts.app')

@section('title', 'History: ' . $task->name)

@section('content')
<div class="container mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">History: {{ $task->name }}</h1>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($histories as $history)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium">{{ $history->event_type }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Changed by: {{ $history->changer->name }}
                                <span class="text-xs text-gray-500 ml-2">{{ $history->created_at->diffForHumans() }}</span>
                            </p>
                            @if($history->change_comment)
                                <p class="text-sm mt-1">{{ $history->change_comment }}</p>
                            @endif
                        </div>
                        <a href="{{ route('tasks.history.show', ['task' => $task, 'history' => $history]) }}" class="text-blue-500 hover:text-blue-700">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    No history found for this task.
                </div>
            @endforelse
        </div>

        @if($histories->hasPages())
            <div class="p-4 border-t">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection