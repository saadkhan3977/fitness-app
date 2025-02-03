<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Message;
use Auth;
use App\Services\FirebaseService;

class MessageController extends BaseController
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    public function chat_list()
    {
        // Get the authenticated user's ID
        $userId = Auth::user()->id;
    
        // Fetch conversations with necessary relationships and new message count
        $conversations = Conversation::with(['message', 'user_info', 'target_user_info'])
            ->withCount(['new_message' => function ($query) use ($userId) {
                $query->where('is_read', 0)
                      ->when($query->where('target_id', $userId), function ($q) use ($userId) {
                          $q->where('target_id', $userId);
                      })
                      ->orderBy('id', 'desc');
            }])
            ->where('user_id', $userId)
            ->orWhere('target_id', $userId)
            ->get();
    
        // Transform the conversations into the desired response format
        $response = $conversations->map(function ($conversation) use ($userId) {
            // Determine if the authenticated user is the sender or the target
            $isSender = $conversation->user_id == $userId;
            $otherUser = $isSender ? $conversation->target_user_info : $conversation->user_info;
    
            // Check if the authenticated user matches the target_id
            // if ($conversation->target_id == $userId) {
            //     // Mark messages sent to the target_id as read
            //     $conversation->message->where('target_id', $userId)->update(['is_read' => 1]);
            // }
    
            return [
                'id' => $conversation->id,
                'user' => $otherUser ? $otherUser : null,
                'message' => $conversation->message,
                'new_message_count' => $conversation->new_message_count,
                'is_sender' => $isSender, // Indicate if the logged-in user is the sender
            ];
        });
    
        // Return the response
        return $this->sendResponse($response, 'Chat Lists');
    }
    
    public function message_list(Request $request,$id)
    {
        //  return Auth::user()->id;
        $data = Message::where('chat_id',$id)->get();
        // foreach($data as $msg)
        // {
            
        //     $msgg = Message::where('id',$msg->id)->where('target_id',Auth::user()->id)->first();
        //     if($msgg)
        //     {
        //         $msgg['is_read']= 1;
        //         $msgg->save();
        //     }
        //     // return $msg;
            
        // }
        return $this->sendResponse($data ,'Messages Lists');
    }
    
    public function message_read(Request $request,$id)
    {
        //  return Auth::user()->id;
        // $data = Message::where('chat_id',$id)->get();
        // foreach($data as $msg)
        // {
            
        //     $msgg = Message::where('id',$msg->id)->where('target_id',Auth::user()->id)->first();
        //     if($msgg)
        //     {
        //         $msgg['is_read']= 1;
        //         $msgg->save();
        //     }
        //     // return $msg;
            
        // }
        // return $this->sendResponse($data ,'Messages Read Success');
        $userId = Auth::id();
        $messageId = $id;
    
        // $message = Message::where('id', $messageId)
        //     ->where('target_id', $userId)
        //     ->first();
        $messages = Message::where('chat_id', $messageId)
        ->where('target_id', $userId)
        ->where('is_read', 0) // Only unread messages
        ->get();
    
        // if ($message) {
        //     $message->is_read = 1;
        //     $message->save();
    
        //     // Broadcast the message read event
        //     broadcast(new MessageRead($message))->toOthers();
    
        //     return response()->json(['status' => 'success', 'message' => 'Message marked as read']);
        // }
        
        // if ($messages->isEmpty()) 
        // {
        //     return response()->json(['status' => 'error', 'message' => 'No messages to mark as read or not authorized'], 404);
        // }
    
        // Mark each message as read and broadcast the event
        if (!$messages->isEmpty()) 
        {
            foreach ($messages as $message) {
                $message->is_read = 1;
                $message->save();
        
                // Broadcast the message read event
                broadcast(new MessageRead($message))->toOthers();
            }
        }
    
        return response()->json(['status' => 'success', 'message' => 'Messages marked as read']);
    }
    
    public function sendMessage(Request $request)
    {
        // return Auth::user()->id;
        // dd($request->user['id']);

        $message = [
            'chat_id' => $request->chat_id,
            'target_id' => $request->target_id,
            'text' => $request->text,
            'createdAt' => $request->createdAt,
            'user' => $request->user,
        ];


        $chat = Conversation::where('user_id',Auth::user()->id)->orwhere('target_id',Auth::user()->id)->first();
        $user = User::find($request->target_id);
        $body = Auth::user()->first_name . ' ' . Auth::user()->last_name .' has send message';
        $title = request()->text;
        $fcmToken = $user->device_token;
        $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);
        // print_r($chat);die;
        if(!$chat)
        {
            $chat = Conversation::create([
                // 'chat_id' => request()->chat_id,
                'user_id' => request()->chat_id, //Auth::user()->id,
                'target_id' => request()->target_id,
            ]);

        }
        // return $chat->id;
        // return json_dencode(request()->user);
        Message::create([
            'chat_id' => $chat->id,
            'user_id' => Auth::user()->id,
            'target_id' => request()->target_id,
            'text' => request()->text,
            'user' => $request->user
        ]);
    
        // Broadcast the event
        broadcast(new MessageSent((object)$message))->toOthers();

        return response()->json(['status' => 'Message Sent!']);
    }

}
