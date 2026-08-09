@extends('layouts.storefront')

@section('title', 'Forgot Password')

@section('content')
<div class="max-w-md mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Reset Password</h1>
        <p class="text-gray-600 mb-8">Please contact customer support to reset your password.</p>
        
        <a href="{{ route('account.login') }}" class="font-medium text-blue-600 hover:text-blue-500">
            Back to login
        </a>
    </div>
</div>
@endsection
