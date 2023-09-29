<?php
namespace App\Services;

use App\Models\InternalChat;
use Carbon\Carbon;

class InternalChatService
{
    public function postMessage($sender_id,$target_channel_id, $text = null, $images = [])
    {
        $message = InternalChat::create([
            'sender_id' => $sender_id,
            'target_channel_id' => $target_channel_id,
            'message' => $text ?? '',
        ]);
        if (count($images) > 0) {
            $message->addMediaFromEAInput('chat_pics', $images);
        }
        
    }

    public function getMessages($userId, $timestamp)
    {
        $user = auth()->user();
        $cids = $user->chat_room_ids;
        $messages = InternalChat::where('sender_id', $user->id)
            ->orWhereIn('target_channel_id', $user->chatRoomIds)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $formatted = [];
        foreach ($messages as $m) {
            if ($m->sender_id == $user->id) {
                $formatted[$m->target_channel_id][] = $m;
            } else {
                $formatted[$m->sender_id][] = $m;
            }
        }
        return [
            'messages' => $formatted,
        ];
    }

    public function olderMessages($roomId, $cid)
    {
        $messages = InternalChat::where('target_channel_id', $roomId)
            ->orWhere('sender_id', $roomId)
            ->where('id', '<', $cid)
            ->orderBy('created_at')
            ->limit(30)
            ->get();
        return [
            'messages' => $messages
        ];
    }
}
?>
