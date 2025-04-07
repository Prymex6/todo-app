@extends('layouts.app')

@section('title', 'History: ' . $task->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">History: {{ $task->name }}</h1>
        <a href="{{ route('tasks.show', $task) }}" class="text-blue-500 hover:underline">Back to Task</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($histories as $history)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-medium">{{ $history->event_type_label }}</h3>
                                <span class="text-xs text-gray-500">{{ $history->created_at->diffForHumans() }}</span>
                            </div>
                            
                            @if($history->changer)
                                <p class="text-sm text-gray-600 mt-1">Changed by: {{ $history->changer->name }}</p>
                            @endif
                            
                            @if($history->change_comment)
                                <p class="text-sm mt-1">{{ $history->change_comment }}</p>
                            @endif
                            
                            @if(count($history->changes))
                                <div class="mt-2 space-y-1 text-sm">
                                    @foreach($history->changes as $field => $change)
                                        <div class="flex">
                                            <span class="font-medium w-32">{{ str_replace('_', ' ', $field) }}:</span>
                                            <span class="text-gray-600">
                                                @if(is_array($change['from']))
                                                    {{ json_encode($change['from']) }}
                                                @else
                                                    {{ $change['from'] ?? 'null' }}
                                                @endif
                                                →
                                                @if(is_array($change['to']))
                                                    {{ json_encode($change['to']) }}
                                                @else
                                                    {{ $change['to'] ?? 'null' }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        @if($history->after && $history->event_type !== 'created')
                            <form action="{{ route('tasks.history.restore', $history) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-blue-500 hover:text-blue-700 text-sm" 
                                    onclick="return confirm('Restore this version?')">
                                    Restore
                                </button>
                            </form>
                        @endif
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