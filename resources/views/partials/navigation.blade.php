<nav class="bg-white shadow">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{ route('tasks.index') }}" class="text-xl font-bold text-gray-800">{{ config('app.name') }}</a>
        
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-gray-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-800">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-800">Register</a>
                @endif
            @endauth
        </div>
    </div>
</nav>