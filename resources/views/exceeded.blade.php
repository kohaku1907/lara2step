@extends('2step::layout')

@section('title', __('2step::messages.exceeded_title'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-lg shadow-md">
    <div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        <i class="fas fa-lock text-red-500"></i> {{ __('2step::messages.exceeded_title') }}
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        <i class="fas fa-clock text-yellow-500"></i> {{ __('2step::messages.locked') }} <span class="font-bold">{{ $timeUntilUnlock }}</span>
      </p>
      <p class="mt-2 text-center text-sm text-gray-600">
        <i class="fas fa-hourglass-half text-blue-500"></i> {{ __('2step::messages.try_again') }} <span class="font-bold">{{ $timeCountdownUnlock }}</span>
      </p>
    </div>
    <div class="mt-5 text-center">
      <a href="{{ route('logout') }}"
         onclick="event.preventDefault();
         document.getElementById('logout-form').submit();"
         class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
        <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
      </a>

      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </div>
  </div>
</div>
@endsection