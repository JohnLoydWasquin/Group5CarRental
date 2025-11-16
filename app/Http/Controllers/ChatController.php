<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index($userId = null)
    {
        $users = User::where('id', '!=', Auth::id())
                     ->whereIn('role', ['staff', 'admin'])
                     ->get();

        $messages = [];
        $receiver = null;

        if ($userId) {
            $receiver = User::findOrFail($userId);

            $messages = Chat::where(function ($q) use ($userId) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
        }

        if (request()->wantsJson()) {
            return response()->json([
                'messages' => $messages,
                'receiverId' => $userId,
                'receiverName' => $receiver?->name ?? 'Unknown'
            ]);
        }

        return view('layouts.chat.chat', [
            'users' => $users,
            'messages' => $messages,
            'receiverId' => $userId,
            'receiver' => $receiver
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);
        
        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new NewChatMessage($chat))->toOthers();

        return response()->json($chat);
    }

    public function sendAudio(Request $request)
    {
        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $filename = time() . '.webm';
            $path = $file->storeAs('chat_audio', $filename, 'public');

            $message = Chat::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => null,
                'audio' => $path,
            ]);

            broadcast(new NewChatMessage($message))->toOthers();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function sendFile(Request $request)
{
    try {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
            'receiver_id' => 'required|exists:users,id',
        ]);

        // Store the file in public disk
        $path = $request->file('file')->store('chat_files', 'public');

        $message = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'file' => $path,
            'message' => null,
        ]);

        return response()->json([
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'file' => $message->file,
            'created_at' => $message->created_at,
        ]);
    } catch (\Exception $e) {
        // Log the error so we can see it in storage/logs/laravel.log
        Log::error('Send File Error: '.$e->getMessage());
        return response()->json(['error' => 'Failed to send file'], 500);
    }
}
}
