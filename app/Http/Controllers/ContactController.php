<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Services\SmsService;

class ContactController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::create($validated);

        $smsMessage = "New message from {$validated['name']} ({$validated['email']})\n"
                    . "Subject: {$validated['subject']}\n"
                    . "Message: {$validated['message']}";

        $this->smsService->sendMessage(env('CLIENT_PHONE'), $smsMessage);

        return response()->json(['success' => 'Your message has been sent successfully!']);
    }
}
