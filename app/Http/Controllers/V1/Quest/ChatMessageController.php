<?php

namespace App\Http\Controllers\V1\Quest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:255'],
        ]);
        $data['ip_address'] = $request?->ip();

        $chatMessage = ChatMessage::create($data);
        if (! $chatMessage) {
            Log::error('Failed to create chat message', [
                'user_id' => auth()?->id(),
                'ip_address' => $data['ip_address'] ?? null,
            ]);

            return ApiResponse::error([], 'Something went wrong, please try again later!', 403);
        }

        Log::info('New chat message sent', [
            'user_id' => auth()?->id(),
            'chat_message_id' => $chatMessage?->id,
            'ip_address' => $data['ip_address'] ?? null,
        ]);

        // customize the response to include the chat message data for a redirect to whatsapp app with pre-filled message
        // add the name and email to the message if they are provided eg "Hello, my name is John Doe and my email is john@example.com /n Message: {message}"

        $whatsappNumber = config('services.whatsapp.chat_number', env('WHATAPP_CHAT_NUMBER', '2348130000000'));

        $response = [
            'whatsapp_url' => 'https://wa.me/'.$whatsappNumber.'?text='.urlencode(
                "Hello, You have a new message from VeriScore website:\n"
                    .($chatMessage?->name ? "My name is {$chatMessage?->name}.\n" : '')
                    .($chatMessage?->email ? "My email is {$chatMessage?->email}.\n" : '')
                    ."{$chatMessage?->message}\n"
            ),
        ];

        return ApiResponse::success($response, 'chat messaged sent!', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatMessage $chatMessage)
    {
        //
    }
}
