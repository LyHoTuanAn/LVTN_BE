@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">{{ __('Settings') }}</h1>
    
    <x-tabs :defaultTab="'profile'" :tabs="[
        'profile' => __('Profile'), 
        'security' => __('Security'), 
        'notifications' => __('Notifications')
    ]">
        <x-tab-panel tabKey="profile">
            <x-card-container 
                title="{{ __('Profile Settings') }}" 
                description="{{ __('Manage your profile information') }}"
            >
                @include('components.profile-settings')
            </x-card-container>
        </x-tab-panel>
        
        <x-tab-panel tabKey="security">
            <x-card-container 
                title="{{ __('Security Settings') }}" 
                description="{{ __('Manage your account security') }}"
            >
                @include('components.security-settings')
            </x-card-container>
        </x-tab-panel>
        
        <x-tab-panel tabKey="notifications">
            <x-card-container 
                title="{{ __('Notification Preferences') }}" 
                description="{{ __('Manage your notification settings') }}"
            >
                @include('components.notification-settings')
            </x-card-container>
        </x-tab-panel>
    </x-tabs>
</div>
@endsection

