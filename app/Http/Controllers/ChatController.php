<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;


class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');

        // Broadcast the event
        broadcast(new MessageSent($message));

        return response()->json(['message' => 'Broadcasted successfully!']);
    }
}
