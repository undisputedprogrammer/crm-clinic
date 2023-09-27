<?php
namespace App\Services;

use App\Models\InternalChat;
use Carbon\Carbon;

class InternalChatService
{
    public function postMessage($text, $image)
    {

    }

    public function getMessages($userId, $timestamp)
    {
        $user = auth()->user();
        $messages = InternalChat::where('sender_id', $user->id)
            ->orWhereIn('target_channel_id', $user->chatRoomIds)
            ->where('create_at', '>=', (Carbon::now()->subMinutes(30))->timestamp)
            ->orderBy('create_at', 'desc');
        return [
            'messages' => [
                'craft' => [], // room_name => [messages]
                'craft_kodungallur' => [],
                '2' => [], //user_id => [messages]
                '3' => [], //user_id => [messages]
                '7' => [] //user_id => [messages]
            ],
            'last_timestamp' => ''
        ];
    }

    public function loadOldMessages($roomId, $timestamp)
    {
        return [

        ];
    }
}
?>
