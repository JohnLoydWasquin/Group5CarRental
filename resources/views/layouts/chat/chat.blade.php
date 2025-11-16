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
            <button class="w-full text-left px-3 py-2 rounded hover:bg-gray-200 chat-user-btn 
                {{ $chatUser->id === $receiverId ? 'bg-gray-300' : '' }}" 
                data-user-id="{{ $chatUser->id }}">
                {{ $chatUser->name }}
            </button>
        @endforeach
    </div>


    @php
    $chatMessages = $messages->map(function($msg) {
        return [
            'id' => $msg->id,
            'sender_id' => $msg->sender_id,
            'receiver_id' => $msg->receiver_id,
            'message' => $msg->message,
            'audio' => $msg->audio,
            'file' => $msg->file,
            'created_at' => $msg->created_at,
        ];
    });
    @endphp

    <div id="chatContainer"
        class="flex flex-col w-3/4 h-[80vh] bg-white rounded-lg shadow p-4"
        data-user-id="{{ auth()->user()->id }}"
        data-receiver-id="{{ $receiver->id ?? '' }}"
        data-chats='@json($chatMessages)'
        data-chat-send-route="{{ route('chat.send') }}"
        data-chat-send-audio-route="{{ route('chat.send.audio') }}"
        data-csrf="{{ csrf_token() }}">


        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-2">
            <h2 id="chatHeader" class="text-xl font-bold text-gray-800">
                Chat with {{ $receiver?->name ?? 'Select a user' }}
            </h2>
        </div>

        <!-- Messages -->
        <div id="messages" class="flex-1 overflow-y-auto mb-4 space-y-2 px-2"></div>

        <div class="flex items-center border-t border-gray-200 pt-3 px-2 gap-3">

            <button id="voiceRecordBtn" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 3.75a3 3 0 0 1 3 3V12a3 3 0 1 1-6 0V6.75a3 3 0 0 1 3-3zM19.5 10.5v1.5a7.5 7.5 0 1 1-15 0v-1.5M12 18.75V21" />
                </svg>
            </button>

            <button id="attachBtn" onclick="document.getElementById('attachInput').click()" 
                class="text-gray-500 hover:text-gray-700 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 5v14m7-7H5" />
                </svg>
            </button>
            <input type="file" id="attachInput" class="hidden">

            <button id="emojiBtn" class="text-gray-500 hover:text-gray-700 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 20.25A8.25 8.25 0 1 0 12 3.75a8.25 8.25 0 0 0 0 16.5zm-3-6a3 3 0 0 0 6 0m-6 0h.008M15 14h.008" />
                </svg>
            </button>

            <div class="relative flex-1">
                <input type="text" id="messageInput" placeholder="Aa" 
                    class="w-full border rounded-full px-4 py-2 bg-gray-100 
                        focus:outline-none focus:ring-1 focus:ring-yellow-400">

                <div id="filePreview" class="absolute right-2 top-1/2 transform -translate-y-1/2 flex items-center gap-1"></div>
            </div>

            <button id="sendBtn" class="text-yellow-500 hover:text-yellow-600 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4.5 12l15-8v16l-15-8z" />
                </svg>
            </button>
        </div>

    </div>
</div>
@endsection
