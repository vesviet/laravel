@extends('layouts.storefront')

@section('title', 'Register')

@section('content')
<div class="max-w-md mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <h1 class="text-2xl font-bold text-gray-900 text-center mb-8">Create an Account</h1>
        
        <form action="{{ route('account.register') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number (Optional)</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
            </div>
            
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Register
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? 
                <a href="{{ route('account.login') }}" class="font-medium text-blue-600 hover:text-blue-500">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
