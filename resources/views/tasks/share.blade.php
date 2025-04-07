@extends('layouts.app')

@section('title', 'Share Task: ' . $task->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Share Task: {{ $task->name }}</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('tasks.shares.store', $task) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Recipient Email</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="expiry_days" class="block text-sm font-medium text-gray-700">Expires In (days)</label>
                    <input type="number" name="expiry_days" id="expiry_days" min="1" max="30" value="7" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="max_uses" class="block text-sm font-medium text-gray-700">Max Uses (optional)</label>
                    <input type="number" name="max_uses" id="max_uses" min="1" max="100"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="allow_editing" id="allow_editing" value="1" checked
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="allow_editing" class="ml-2 block text-sm text-gray-700">Allow editing</label>
                </div>
            </div>
            
            <div class="flex justify-end gap-2">
                <a href="{{ route('tasks.show', $task) }}" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Cancel</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Generate Share Link</button>
            </div>
        </form>
        
        @if($task->shares->count())
            <div class="mt-8">
                <h3 class="text-lg font-medium mb-2">Active Share Links</h3>
                <div class="space-y-2">
                    @foreach($task->shares->where('expires_at', '>', now()) as $share)
                        <div class="p-3 border rounded-lg bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm">
                                        <span class="font-medium">Link:</span> 
                                        <a href="{{ route('tasks.shares.show', $share->token) }}" target="_blank" class="text-blue-500 hover:underline">
                                            {{ route('tasks.shares.show', $share->token) }}
                                        </a>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Expires: {{ $share->expires_at->format('M d, Y H:i') }} | 
                                        Uses: {{ $share->use_count }}@if($share->max_uses)/{{ $share->max_uses }}@endif | 
                                        {{ $share->allow_editing ? 'Can edit' : 'View only' }}
                                    </p>
                                </div>
                                <form action="{{ route('tasks.shares.destroy', $share) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
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
    </div>
</div>
@endsection