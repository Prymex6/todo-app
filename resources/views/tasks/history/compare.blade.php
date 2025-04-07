@extends('layouts.app')

@section('title', 'Compare History Versions')

@section('content')
<div class="container mx-auto py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">Compare History Versions</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Version #{{ $first_version->id }}</h3>
                    <pre class="bg-gray-100 p-4 rounded">{{ json_encode($first_version->after ?? $first_version->before, JSON_PRETTY_PRINT) }}</pre>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2">Version #{{ $second_version->id }}</h3>
                    <pre class="bg-gray-100 p-4 rounded">{{ json_encode($second_version->after ?? $second_version->before, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>

            <h3 class="text-lg font-medium mb-2">Differences</h3>
            <div class="bg-gray-100 p-4 rounded">
                @foreach($differences as $field => $diff)
                    <div class="mb-4 last:mb-0">
                        <h4 class="font-medium">{{ str_replace('_', ' ', $field) }}</h4>
                        <div class="grid grid-cols-2 gap-4 mt-1">
                            <div class="bg-red-100 p-2 rounded">
                                <strong>From:</strong> {{ is_array($diff['from']) ? json_encode($diff['from']) : $diff['from'] ?? 'null' }}
                            </div>
                            <div class="bg-green-100 p-2 rounded">
                                <strong>To:</strong> {{ is_array($diff['to']) ? json_encode($diff['to']) : $diff['to'] ?? 'null' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection