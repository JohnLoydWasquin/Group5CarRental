<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function index($userId = null)
    {
        $authId = Auth::id();

        $users = User::where('id', '!=', $authId)
                    ->whereIn('role', ['staff', 'admin'])
                    ->get();

        $messages = collect();
        $receiver = null;

        if ($userId) {
            $receiver = User::findOrFail($userId);

            $messages = Chat::where(function ($q) use ($userId, $authId) {
                    $q->where('sender_id', $authId)
                    ->where('receiver_id', $userId);
                })
                ->orWhere(function ($q) use ($userId, $authId) {
                    $q->where('sender_id', $userId)
                    ->where('receiver_id', $authId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            Chat::where('sender_id', $userId)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

                $users = $users->map(function ($user) use ($authId) {
                $last = Chat::where(function ($q) use ($authId, $user) {
                            $q->where('sender_id', $authId)
                            ->where('receiver_id', $user->id);
                        })
                        ->orWhere(function ($q) use ($authId, $user) {
                            $q->where('sender_id', $user->id)
                            ->where('receiver_id', $authId);
                        })
                        ->orderBy('created_at', 'desc')
                        ->first();

                $unread = Chat::where('sender_id', $user->id)
                            ->where('receiver_id', $authId)
                            ->where('is_read', false)
                            ->count();

                // ---- last message preview ----
                $preview = null;
                if ($last) {
                    if ($last->message) {
                        $preview = $last->message;
                    } elseif ($last->file) {
                        $preview = '📎 Attachment';
                    } elseif ($last->audio) {
                        $preview = '🎤 Voice message';
                    }
                }

                // ---- short time label like 24m, 3h, 2d ----
                $timeLabel = null;
                if ($last && $last->created_at) {
                    $date = $last->created_at instanceof Carbon
                        ? $last->created_at
                        : Carbon::parse($last->created_at);

                    $now = Carbon::now();

                    $mins = (int) $date->diffInMinutes($now);
                    if ($mins < 60) {
                        $timeLabel = $mins . 'm';
                    } else {
                        $hours = (int) $date->diffInHours($now);
                        if ($hours < 24) {
                            $timeLabel = $hours . 'h';
                        } else {
                            $days = (int) $date->diffInDays($now);
                            $timeLabel = $days . 'd';
                        }
                    }
                }

                $user->last_message       = $preview;
                $user->last_message_time  = $last?->created_at;
                $user->last_message_label = $timeLabel;
                $user->unread_count       = $unread;

                return $user;
            })
            ->sortByDesc('last_message_time')
            ->values();

        $unreadCount = Chat::where('receiver_id', $authId)
            ->where('is_read', false)
            ->count();

        if (request()->wantsJson()) {
            return response()->json([
                'messages'     => $messages,
                'receiverId'   => $userId,
                'receiverName' => $receiver?->name ?? 'Unknown',
                'unreadCount'  => $unreadCount,
            ]);
        }

        return view('layouts.chat.chat', [
            'users'           => $users,
            'messages'        => $messages,
            'receiverId'      => $userId,
            'receiver'        => $receiver,
            'chatUnreadCount' => $unreadCount,
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
            'file' => 'required|file|max:10240',
            'receiver_id' => 'required|exists:users,id',
        ]);

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
        Log::error('Send File Error: '.$e->getMessage());
        return response()->json(['error' => 'Failed to send file'], 500);
    }
}
}
