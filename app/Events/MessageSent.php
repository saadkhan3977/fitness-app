<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Auth;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // print_r(Auth::user()->id);die;
        $chat = Conversation::where('user_id',Auth::user()->id)->orwhere('target_id',Auth::user()->id)->first();
        if(!$chat)
        {
            $chat = Conversation::create([
                // 'chat_id' => request()->chat_id,
                'user_id' => request()->chat_id, //Auth::user()->id,
                'target_id' => request()->target_id,
            ]);

        }
        return ['my-channel-'.$chat->id];
   
        // return new PrivateChannel('my-'.Auth::user()->id);
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}