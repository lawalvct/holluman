@extends('layouts.admin')

@section('title', 'Application Settings')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Application Settings</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.settings') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Company Name</label>
            <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring @error('company_name') border-red-500 @enderror">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Company Logo</label>
            @if(!empty($settings['company_logo']))
                <img src="{{ asset('images/' . $settings['company_logo']) }}" alt="Logo" class="h-16 mb-2">
            @endif
            <input type="file" name="company_logo" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Company Address</label>
            <input type="text" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring @error('company_address') border-red-500 @enderror">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Company Email</label>
            <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring @error('company_email') border-red-500 @enderror">
        </div>
        <div class="mb-6">
            <h2 class="font-bold text-lg mb-2">Nomba Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Nomba Account ID</label>
                    <input type="text" name="nomba_account_id" value="{{ old('nomba_account_id', $settings['nomba_account_id'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Nomba Client ID</label>
                    <input type="text" name="nomba_client_id" value="{{ old('nomba_client_id', $settings['nomba_client_id'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Nomba Private Key</label>
                    <input type="text" name="nomba_private_key" value="{{ old('nomba_private_key', $settings['nomba_private_key'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Nomba Webhook Secret</label>
                    <input type="text" name="nomba_webhook_secret" value="{{ old('nomba_webhook_secret', $settings['nomba_webhook_secret'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
            </div>
        </div>
        <div class="mb-6">
            <h2 class="font-bold text-lg mb-2">Paystack Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Paystack Public Key</label>
                    <input type="text" name="paystack_public_key" value="{{ old('paystack_public_key', $settings['paystack_public_key'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Paystack Secret Key</label>
                    <input type="text" name="paystack_secret_key" value="{{ old('paystack_secret_key', $settings['paystack_secret_key'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
                <div class="flex items-center mt-2">
                    <input type="checkbox" name="paystack_enabled" id="paystack_enabled" value="1" {{ old('paystack_enabled', $settings['paystack_enabled'] ?? false) ? 'checked' : '' }} class="mr-2">
                    <label for="paystack_enabled" class="font-semibold">Enable Paystack</label>
                </div>
            </div>
        </div>
        <div class="mb-6">
            <h2 class="font-bold text-lg mb-2">Other Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-1">Support Phone</label>
                    <input type="text" name="support_phone" value="{{ old('support_phone', $settings['support_phone'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Support Email</label>
                    <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? '') }}" class="border rounded px-3 py-2 w-full focus:outline-none focus:ring">
                </div>
            </div>
        </div>
        <div class="flex gap-4 mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save Settings</button>
        </div>
    </form>
</div>
@endsection
