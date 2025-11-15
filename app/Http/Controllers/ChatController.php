<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Auth;

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
}
