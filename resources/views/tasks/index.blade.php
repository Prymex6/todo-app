@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Tasks</h1>
        <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            + New Task
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach(App\Models\Task::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All</option>
                        @foreach(App\Models\Task::PRIORITIES as $value => $label)
                            <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="date" name="due_date" value="{{ request('due_date') }}" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                </div>
                
                <div class="self-end">
                    <button type="submit" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Filter</button>
                    <a href="{{ route('tasks.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">Reset</a>
                </div>
            </form>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($tasks as $task)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-medium {{ $task->isOverdue() ? 'text-red-600' : '' }}">
                                <a href="{{ route('tasks.show', $task) }}" class="hover:underline">{{ $task->name }}</a>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                            <div class="flex gap-4 mt-2 text-sm">
                                <span class="px-2 py-1 bg-{{ $task->priority == 'high' ? 'red' : ($task->priority == 'medium' ? 'yellow' : 'green') }}-100 text-{{ $task->priority == 'high' ? 'red' : ($task->priority == 'medium' ? 'yellow' : 'green') }}-800 rounded">
                                    {{ $task->priority_label }}
                                </span>
                                <span>{{ $task->due_date->format('M d, Y') }}</span>
                                <span>{{ $task->status_label }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('tasks.edit', $task) }}" class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-500 hover:text-gray-700" onclick="return confirm('Are you sure?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    No tasks found. <a href="{{ route('tasks.create') }}" class="text-blue-500 hover:underline">Create one</a>.
                </div>
            @endforelse
        </div>

        @if($tasks->hasPages())
            <div class="p-4 border-t">
                {{ $tasks->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection