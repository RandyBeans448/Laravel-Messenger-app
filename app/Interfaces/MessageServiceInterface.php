<?php

namespace App\Interfaces;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface MessageServiceInterface
{
    public function createMessage(Request $payload): JsonResponse;
}
