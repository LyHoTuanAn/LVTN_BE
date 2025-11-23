@extends('layouts.admin')

@section('title', __('Hello Admin'))
@section('page-title', __('Hello Admin'))

@section('content')
<div style="background: white; border-radius: 12px; padding: 60px 40px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); text-align: center;">
    <h2 style="font-size: 3em; color: #667eea; margin-bottom: 20px;">{{ __('Hello Admin') }}</h2>
    <p style="font-size: 1.2em; color: #666; margin-bottom: 30px;">{{ __('Welcome to the Admin Dashboard') }}</p>
    <div style="font-size: 1.5em; color: #333; margin-top: 20px;">
        {{ __('You are logged in as an Administrator') }}
    </div>
</div>
@endsection

