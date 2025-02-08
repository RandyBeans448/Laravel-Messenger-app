<?php

namespace App\Interfaces;

use App\Models\Message;
use Illuminate\Http\Request;

interface MessageServiceInterface
{
    public function createMessage(Request $payload): Message;
}
