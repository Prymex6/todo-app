@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Create New Task</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Task Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                    <select name="priority" id="priority" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(App\Models\Task::PRIORITIES as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', 'medium') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(App\Models\Task::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ old('status', 'to-do') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date *</label>
                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end gap-2">
                <a href="{{ route('tasks.index') }}" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Cancel</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Save Task</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Set default due date to tomorrow at 9 AM
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(now.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0);
        
        const dueDateInput = document.getElementById('due_date');
        if (!dueDateInput.value) {
            const formattedDate = tomorrow.toISOString().slice(0, 16);
            dueDateInput.value = formattedDate;
        }
    });
</script>
@endpush
@endsection