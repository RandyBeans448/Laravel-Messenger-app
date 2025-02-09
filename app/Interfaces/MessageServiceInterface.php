<?php

namespace App\Interfaces;

use App\Models\Message;
use App\Http\Requests\CreateMessageRequest;

interface MessageServiceInterface
{
    public function createMessage(CreateMessageRequest $payload): Message;
}
