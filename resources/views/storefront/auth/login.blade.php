@extends('layouts.storefront')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h1 class="text-2xl font-bold text-gray-900 text-center mb-8">Login to Your Account</h1>
        
        <form action="{{ route('account.login') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <a href="{{ route('account.password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">Forgot password?</a>
                </div>
                <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
            </div>
            
            <div class="flex items-center mb-6">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Remember me
                </label>
            </div>
            
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Sign in
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('account.register') }}" class="font-medium text-blue-600 hover:text-blue-500">Sign up</a>
            </p>
        </div>
    </div>
</div>
@endsection
