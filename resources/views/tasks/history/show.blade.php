@extends('layouts.app')

@section('title', 'History Details')

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">History Details</h1>
        <a href="{{ route('tasks.history.index', $history->task) }}" class="text-blue-500 hover:underline">Back to History</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Before Changes</h3>
                    <pre class="bg-gray-100 p-4 rounded">{{ json_encode($history->before, JSON_PRETTY_PRINT) }}</pre>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2">After Changes</h3>
                    <pre class="bg-gray-100 p-4 rounded">{{ json_encode($history->after, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-600"><strong>Changed by:</strong> {{ $history->changer->name }}</p>
                <p class="text-sm text-gray-600"><strong>Date:</strong> {{ $history->created_at->format('Y-m-d H:i:s') }}</p>
                <p class="text-sm text-gray-600"><strong>Event Type:</strong> {{ $history->event_type }}</p>
                @if($history->change_comment)
                    <p class="text-sm text-gray-600 mt-2"><strong>Comment:</strong> {{ $history->change_comment }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection