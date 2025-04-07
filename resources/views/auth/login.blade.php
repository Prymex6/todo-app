@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>
            </div>

            <div class="flex items-center justify-between mb-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-500 hover:underline" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif
            </div>

            <div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Login
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="mt-4 text-center text-sm">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Register</a>
            </div>
        @endif
    </div>
</div>
@endsection