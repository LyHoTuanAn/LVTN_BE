@extends('layouts.app')
@section('title', __('API Management'))
@section('page-title', __('API Management'))
@section('content')
<div class="space-y-6">
    <x-tabs :defaultTab="'keys'" :tabs="['keys' => __('API Keys'), 'usage' => __('API Usage')]">
        <x-tab-panel tabKey="keys">
            <x-card-container 
                title="{{ __('API Keys') }}" 
                description="{{ __('Manage your API keys for authentication') }}"
            >
                @include('components.api-keys')
            </x-card-container>
        </x-tab-panel>
        
        <x-tab-panel tabKey="usage">
            <x-card-container 
                title="{{ __('API Usage') }}" 
                description="{{ __('Monitor your API usage and limits') }}"
            >
                @include('components.api-usage')
            </x-card-container>
        </x-tab-panel>
    </x-tabs>
</div>
@endsection

