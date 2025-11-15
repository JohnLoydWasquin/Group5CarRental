@extends(auth()->user()->role === 'admin' ? 'layouts.authorities.admin' : 'layouts.orgStaff.staff')
@vite('resources/js/chat.js')

@section('content')
@php
$firstUser = $users->first();
$receiverId = $receiverId ?? $firstUser?->id;
$receiver = $receiver ?? $firstUser;
@endphp

<div class="flex space-x-4">

    <!-- User list -->
    <div class="w-1/4 bg-gray-100 rounded p-2 space-y-2 h-[80vh] overflow-y-auto">
        <h3 class="font-bold mb-2">Users</h3>
        @foreach($users as $chatUser)
            <button class="w-full text-left px-3 py-2 rounded hover:bg-gray-200 chat-user-btn {{ $chatUser->id === $receiverId ? 'bg-gray-300' : '' }}" 
                data-user-id="{{ $chatUser->id }}">
                {{ $chatUser->name }}
            </button>
        @endforeach
    </div>

    <!-- Chat container -->
    <div id="chatContainer"
        class="flex flex-col w-3/4 h-[80vh] bg-white rounded-lg shadow p-4"
        data-user-id="{{ auth()->user()->id }}"
        data-receiver-id="{{ $receiverId }}"
        data-chats='@json($messages)'
        data-chat-send-route="{{ route('chat.send') }}"
        data-csrf="{{ csrf_token() }}"
    >
        <!-- Chat Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-2">
            <h2 id="chatHeader" class="text-xl font-bold text-gray-800">
                Chat with {{ $receiver?->name ?? 'Select a user' }}
            </h2>
            <span class="text-gray-500 text-sm">Real-time messaging</span>
        </div>

        <!-- Messages Container -->
        <div id="messages" class="flex-1 overflow-y-auto mb-4 space-y-2 px-2"></div>

        <!-- Input Area -->
        <div class="flex items-center gap-2 border-t border-gray-200 pt-2">
            <input type="text" id="messageInput" placeholder="Type your message..." 
                class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-yellow-400">
            <button id="sendBtn" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                Send
            </button>
        </div>
    </div>
</div>
@endsection
