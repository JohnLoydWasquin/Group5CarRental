<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class ChatController extends Controller
{
    public function show(Customer $customer)
    {
        // return a chat view
        return view('chat.show', compact('customer'));
    }
}
