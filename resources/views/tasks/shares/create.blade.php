@extends('layouts.app')

@section('title', 'Share Task: ' . $task->name)

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Share Task: {{ $task->name }}</h1>
        
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
    </div>
</div>
@endsection