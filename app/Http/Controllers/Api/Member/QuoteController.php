<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\QuoteImage;
use App\Models\QuoteHelp;
use App\Models\Review;
use App\Models\User;
use App\Models\Bid;
use Auth;
use DB;
use App\Services\FirebaseService;
use App\Models\Notification;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\MessageSent;
use Validator;
class QuoteController extends BaseController
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        try
        {
            // $users = User::where('role','Qbid Negotiator')->get(); // Assuming User is your model
            // //$recipientDeviceToken = $recipient->device_token;

            // foreach($users as $user)
            // {
            //     $notification = new Notification();
            //     $notification->title = 'test';
            //     $notification->body = 'hello world';
            //     $notification->recipient_id = $user->id; // Assuming you have a recipient ID
            //     $notification->save();
            // }

            // // Send notification via Firebase Cloud Messaging
            // // $factory = (new Factory)->withServiceAccount('/path/to/firebase_credentials.json');
            // // $messaging = $factory->createMessaging();

            // // $message = CloudMessage::fromArray([
            // //     'notification' => [
            // //         'title' => $notification->title,
            // //         'body' => $notification->body,
            // //     ],
            // //     'token' => $recipientDeviceToken, // FCM token of the recipient device
            // // ]);

            // // $messaging->send($message);

            // return response()->json(['message' => 'Notification sent successfully']);







            $user = Quote::with('images','review','review.user_info','bids','bids.user_info')->where('user_id',Auth::user()->id)->orderBy('id','desc')->paginate(10);

			return response()->json(['success'=>true,'message'=>'Quote List','quote_info'=>$user]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

//     public function member_help_index()
//     {
//         try
//         {

//             $user = Quote::with('bid','bid.user_info')->where(['type'=>'help','user_id'=>Auth::user()->id])->orderBy('id','desc')->paginate(10);

// 			return response()->json(['success'=>true,'message'=>'Quote List','quote_info'=>$user]);
// 		}
// 		catch(\Eception $e)
// 		{
// 			return $this->sendError($e->getMessage());
// 		}
//     }



    public function search(Request $request)
    {
        // return count($request->expertise);
        // return response()->json(['success'=>true,'message'=>'Negotiator Lists','negotiator_info'=>[]]);
        try{
        // print_r($request->all());die;
            $expertiseArray = $request->expertise;
            $levels = $request->level;
            //return $levels;

            if(count($levels) > 0)
            {

               // if($levels != [null]){
                    // return $levels;
                    $usersQuery = User::with('user_info','ratings','ratings.user_info')->select(['users.id','users.first_name','users.last_name','users.company_name','users.email', DB::raw('AVG(reviews.rating) as average_rating')])
                    ->join('reviews', 'users.id', '=', 'reviews.assign_user_id')
                    ->groupBy('users.id','users.first_name','users.last_name','users.company_name','users.email');

                    $usersQuery->having(function ($query) use ($levels) {
                        foreach ($levels as $level) {
                            if($level == 'bronze'){
                            $query->havingRaw('AVG(reviews.rating) >= 2.5 AND AVG(reviews.rating) <= 3.1');
                            }
                            if($level == 'silver'){
                            $query->orhavingRaw('AVG(reviews.rating) >= 3 AND AVG(reviews.rating) <= 3.5');
                            }
                            if($level == 'gold'){
                            $query->orhavingRaw('AVG(reviews.rating) >= 3.5 AND AVG(reviews.rating) <= 4');
                            }
                            if($level == 'platinum'){
                            $query->orhavingRaw('AVG(reviews.rating) >= 4 AND AVG(reviews.rating) <= 5');
                            }
                            else{
                                $query->havingRaw('AVG(reviews.rating) >= 1 AND AVG(reviews.rating) <= 5');
                            }
                        }
                    });
                // }
                // else {
                //     $usersQuery = User::with('negotiator_review','negotiator_review.user_info');
                // }
            }
            else{

                $usersQuery = User::with('ratings','ratings.user_info');
            }
             //return count($request->expertise);
            // if(!empty($request->expertise))
            // {
             if($request->search)
            {
                // return $request->search;
                // $usersQuery->where('first_name',  $request->search );
                $usersQuery->where('first_name', 'like', '%' . $request->search . '%');
                // $usersQuery->orWhere('last_name', 'like', '%' . $request->search . '%');
            }
            else
            {

            if(count($request->expertise) > 0)
            {

                // return $request->expertise[0];
                // return 'saad test';
                foreach($expertiseArray as $expertise) {
                    // return $expertise;
                    $usersQuery->orWhere('expertise', 'like', '%' .  $expertise. '%');
                }
            }
        }


            $usersQuery->where('role', 'Qbid Negotiator')->orWhere('role','Business Qbidder');
                $users = $usersQuery->get()->map(function ($user) {
            //        $user['ratings'] = $user->averageRating();
                    return $user;
                });
            return response()->json(['success'=>true,'message'=>'Negotiator Lists','negotiator_info'=>$users]);

		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

    public function search_type(Request $request)
    {
        try
        {
            // return $request->title;
            $quoteq = Quote::with('images')->where('user_id',Auth::user()->id);
            if($request->status)
			{
				$quoteq->where('status', $request->status);
            }
			if($request->title){

                $quoteq->where('title', 'like', '%' .   $request->title . '%');
            }
            $quote = $quoteq->get();
			return response()->json(['success'=>true,'message'=>'Quote Lists','quote_info'=>$quote]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

    public function negotitator_list(Request $request)
    {
        try
        {
            //return Auth::user();
            $quote = User::with('ratings','ratings.user_info')->where('role', 'Qbid Negotiator')->orWhere('role','Business Qbidder')->orderBy('id','desc')->get()->map(function ($user) {
                $user['average_rating'] = $user->averageRating();
                return $user;
            });
			return response()->json(['success'=>true,'message'=>'Negotitator Lists','negotitator_info'=>$quote]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }


    public function update_status(Request $request,$id)
    {
        try{

            $bid = Quote::find($id);
            $user = User::find($bid->negotiator_id);
            $bid->update([
                'status' => $request->status
            ]);

            if($request->status == 'completed')
            {
                $body = $bid->title.' has request to complete';
                $title = 'Quote Complete';
                $fcmToken = $user->device_token;

                $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);
                Notification::create([
                    'user_id' =>  $bid->negotiator_id,
                    'quote_id' =>  $bid->id,
                    // 'service_preference' =>  $request->service_preference,
                    'title' => $title,
                    'body' => $body,
                ]);
            }

			return response()->json(['success'=>true,'message'=>'Updated Successfully','quote_info'=>$bid]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

    public function ongoing()
    {
        try
        {
            $user = Quote::with('images','bids','bids.user_info')->where('status','onGoing')->where('user_id',Auth::user()->id)->paginate(10);

			return response()->json(['success'=>true,'message'=>'Quote List','quote_info'=>$user]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

	public function my_bid_list()
    {
        try
        {

            $bids = Bid::where('status','pending')->where('user_id',auth()->user()->id)->get()->pluck('quote_id'); // Assuming you have the authenticated user

			$quotes = Quote::with('images','bids','bids.user_info')->whereIn('id', $bids)->get();


			return response()->json(['success'=>true,'message'=>'Quote List','quote_info'=>$quotes]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

	public function bid_list()
    {
        try
        {

            $user = User::with('quotes')->find(auth()->user()->id); // Assuming you have the authenticated user
            $userQuotes = $user->quotes; // Get the quotes added by the user
            $quoteBids = null;
            $bigs = [];
            foreach ($userQuotes as $quote)
            {
                $bids[] = Bid::with('quote_info')->first();
                $quoteBids = $quote->bids; // Get bids on this quote
            }
           // return $bids;

            // $user = Bid::with('quote_infos')->get();

			return response()->json(['success'=>true,'message'=>'Bid List','bid_info'=>$quoteBids]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

    public function bid_update(Request $request,$id)
    {
        try{
            // return $this->firebaseService->getAccessToken();
            $bid = Bid::find($id);
            $quote = Quote::find($bid->quote_id);

            // $fcmToken  = $request->input('fcmtoken');
            $fullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $user = User::find($bid->user_id);
            $fcmToken = $user->device_token;






			// return date('Y-m-d H:i:s');
            $user = [
                '_id' => Auth::user()->id,
                'name' => Auth::user()->first_name,
                'avatar' => Auth::user()->photo,
            ];
            // return $user;
            $bid = Bid::find($id);
            $bids = Bid::where('quote_id',$bid->quote_id)->get();

            if($request->status == 'reject')
            {
                $bid->update([
                    'status' => $request->status
                ]);

                $body = 'Your bid '.$quote->title.' is rejected.';
                $title = 'Bid Rejected';
                $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);
                Notification::create([
                    'user_id' =>  $bid->user_id,
                    'quote_id' =>  $bid->quote_id,
                    // 'service_preference' =>  $request->service_preference,
                    'title' => $title,
                    'body' => $body,
                ]);

                return response()->json(['success'=>true,'message'=>'Updated Successfully','bid_info'=>$bid]);
            }
            else
            {

                foreach($bids as $row)
                {
                    if($row->id != $id)
                    {
                        $row->update([
                            'status' => 'reject'
                        ]);
                    }

                }
                $bid->update([
                    'status' => $request->status
                ]);
                $body = 'Congrats! Your bid for the '.$quote->title.' has been accepted';;
                $title = 'Bid Accept';

                $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);
                Notification::create([
                    'user_id' =>  $bid->user_id,
                    'quote_id' =>  $bid->quote_id,
                    // 'service_preference' =>  $request->service_preference,
                    'title' => $title,
                    'body' => $body,
                ]);

                $quote = Quote::find($bid->quote_id);
                $quote->update([
                    'negotiator_id' => $bid->user_id,
                    'status' => 'onGoing'
                ]);


                // Create chate list for member or negotiator
                $chat = Conversation::where('user_id',Auth::user()->id)->where('target_id',$bid->user_id)->first();
                if($chat)
                {
                    Message::create([
                        'chat_id' => $chat->id,
                        'user_id' => Auth::user()->id,
                        'target_id' => $bid->user_id,
                        'text' => 'Congrats! Your bid for the '.$quote->title.' has been accepted',
                        'user' => $user,
                    ]);
                }
                else
                {
                    $chat = Conversation::where('user_id',$bid->user_id)->where('target_id',Auth::user()->id)->first();
                    if($chat)
                    {
                        Message::create([
                            'chat_id' => $chat->id,
                            'user_id' => Auth::user()->id,
                            'target_id' => $bid->user_id,
                            'text' => 'Congrats! Your bid for the '.$quote->title.' has been accepted',
                            'user' => $user,
                        ]);
                    }
                    else
                    {
                        $chat = Conversation::create([
                            // 'chat_id' => request()->chat_id,
                            'user_id' => Auth::user()->id, //Auth::user()->id,
                            'target_id' => $bid->user_id,
                        ]);
                        Message::create([
                            'chat_id' => $chat->id,
                            'user_id' => Auth::user()->id,
                            'target_id' => $bid->user_id,
                            'text' => 'Congrats! Your bid for the '.$quote->title.' has been accepted',
                            'user' => $user,
                        ]);


                    }
                }

                $message = [
                    'chat_id' => $chat->id,
                    'target_id' => $bid->user_id,
                    'text' => 'Congrats! Your bid for the '.$quote->title.' has been accepted',
                    'createdAt' => date('Y-m-d H:i:s'),
                    'user' => $user,
                ];




                // Broadcast the event
                broadcast(new MessageSent((object)$message))->toOthers();

                return response()->json(['success'=>true,'message'=>'Updated Successfully','bid_info'=>$bid]);
            }
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return '';

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

//     public function quote_help(Request $request)
//     {
//         try
//         {

//             $data = Quote::create([
//                 'user_id' => Auth::user()->id,
//                 'service_preference' => $request->service_preference,
//                 'type' => 'help',
//             ]);
//             $service_preference = $request->service_preference;

//             // return User::where('role','Qbid Negotiator')->where('expertise', 'like', "%$service_preference%")->get();

// 			return response()->json(['success'=>true,'message'=>'Created Successfully']);
// 		}
// 		catch(\Eception $e)
// 		{
// 			return $this->sendError($e->getMessage());
// 		}

//     }

//     public function quote_help_list(Request $request)
//     {
//         try
//         {
// // 			return Auth::user();
//             // $data = Quote::create([
//             //     'user_id' => Auth::user()->id,
//             //     'service_preference' => $request->help_type,
//             //     'type' => 'help',
//             // ]);
//             // $service_preference = $request->help_type;

// //             return User::where('role','Qbid Negotiator')->where('expertise', 'like', "%$service_preference%")->get();

// // 			return response()->json(['success'=>true,'message'=>'Created Successfully']);

//             $user = Auth::user();
//             $expertise = json_decode($user->expertise);
//             $quotes = Quote::with('user_info')->where('type', 'help')
//                 ->where(function ($query) use ($expertise) {
//                     foreach ($expertise as $skill) {
//                         // dd($skill);
//                         $query->orWhere('service_preference', $skill);
//                     }
//                 })
//                 ->orderBy('id','desc')->paginate(10);

// 			return response()->json(['success'=>true,'message'=>'Help List','quote_info'=>$quotes]);
// 		}
// 		catch(\Eception $e)
// 		{
// 			return $this->sendError($e->getMessage());
// 		}

//     }

    public function withdraw(Request $request)
    {
        try{

            $qhelp = QuoteHelp::find($request->quote_id);
		  //  $service_preference = $qhelp->service_preference;
		    $qhelp->delete();
	    	return response()->json(['success'=>true,'message'=>'withdraw Successfully']);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}
    }
    public function store(Request $request)
    {
        try{
			if($request->withdraw)
			{
			    $qhelp = QuoteHelp::find($request->quote_help_id);
			    $service_preference = $qhelp->service_preference;

			    $qhelp->delete();
			}
			else
			{
    			$service_preference = $request->service_preference;
			}
            $price = $request->quoted_price - $request->asking_price;
            $data = Quote::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'state' => $request->state,
                'city' => $request->city,
                'quoted_price' => $request->quoted_price,
                'asking_price' => $request->asking_price,
                'negotiator_amount' => ($price * $request->offering_percentage) / 100,
                'offering_percentage' => $request->offering_percentage,
                'service_preference' => $request->service_preference,
                'notes' => $request->notes,
				'lat' => $request->lat,
				'lng' => $request->lng,
                'type' => 'quote',
            ]);


			if ($request->hasFile('images')) {
                $uploadedFiles = $request->file('images');
                $profileUrls = [];

                foreach ($uploadedFiles as $file) {
                    $fileName = md5($file->getClientOriginalName() . time()) . "Qbid." . $file->getClientOriginalExtension();
                    $file->move('uploads/quotes/', $fileName);
                    $profileUrls = asset('uploads/quotes/' . $fileName);

                    QuoteImage::create([
                        'quote_id' => $data->id,
                        'image' => $profileUrls
                    ]);
                }
            }
           $user = Quote::with('images')->where('user_id',Auth::user()->id)->get();

			return response()->json(['success'=>true,'message'=>'Quote Created Successfully','quote_info'=>$user]);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}

    }

    public function hiring_store(Request $request)
    {
        try{

            $data = Quote::create([
                'user_id' => Auth::user()->id,
                'negotiator_id' => $request->negotiator_id,
                'title' => $request->title,
                'state' => $request->state,
                'city' => $request->city,
                'quoted_price' => $request->quoted_price,
                'asking_price' => $request->asking_price,
                'offering_percentage' => $request->offering_percentage,
                'notes' => $request->notes,
                'type' => 'specific',
            ]);


			if ($request->hasFile('images')) {
                $uploadedFiles = $request->file('images');
                $profileUrls = [];

                foreach ($uploadedFiles as $file) {
                    $fileName = md5($file->getClientOriginalName() . time()) . "Qbid." . $file->getClientOriginalExtension();
                    $file->move('uploads/quotes/', $fileName);
                    $profileUrls = asset('uploads/quotes/' . $fileName);

                    QuoteImage::create([
                        'quote_id' => $data->id,
                        'image' => $profileUrls
                    ]);
                }
            }
           $user = Quote::with('images')->where('user_id',Auth::user()->id)->get();

			return response()->json(['success'=>true,'message'=>'Hiring Created Successfully']);
		}
		catch(\Eception $e)
		{
			return $this->sendError($e->getMessage());
		}

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return 'show';

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
	{

		try
		{
        	// Find the existing quote
			$quote = Quote::find($id);

			if ($request->withdraw) {
				$qhelp = QuoteHelp::find($request->quote_help_id);
				$service_preference = $qhelp->service_preference;

				$qhelp->delete();
			} else {
				$service_preference = $request->service_preference;
			}

			$price = $request->quoted_price - $request->asking_price;

			// Update the quote data
			$quote->update([
				'title' => $request->title,
				'state' => $request->state,
				'city' => $request->city,
				'quoted_price' => $request->quoted_price,
				'asking_price' => $request->asking_price,
				'negotiator_amount' => ($price * $request->offering_percentage) / 100,
				'offering_percentage' => $request->offering_percentage,
				'service_preference' => $service_preference,
				'notes' => $request->notes,
				'lat' => $request->lat,
				'lng' => $request->lng,
			]);

			// Handle image uploads
			if ($request->hasFile('images')) {

				$oldImages = QuoteImage::where('quote_id', $quote->id)->get();

                foreach ($oldImages as $oldImage) {
                    $oldImagePath = str_replace(asset(''), '', $oldImage->image);
                    if (file_exists(public_path($oldImagePath))) {
                        unlink(public_path($oldImagePath)); // Delete the old image file
                    }
                    $oldImage->delete(); // Remove record from the database
                }

				$uploadedFiles = $request->file('images');
				$profileUrls = [];

				foreach ($uploadedFiles as $file) {
					$fileName = md5($file->getClientOriginalName() . time()) . "Qbid." . $file->getClientOriginalExtension();
					$file->move('uploads/quotes/', $fileName);
					$profileUrls = asset('uploads/quotes/' . $fileName);

					QuoteImage::create([
						'quote_id' => $quote->id,
						'image' => $profileUrls
					]);
				}
			}

			// Return the updated quote with images
			$user = Quote::with('images')->where('user_id', Auth::user()->id)->get();
			return response()->json(['success' => true, 'message' => 'Quote Updated Successfully', 'quote_info' => $user]);
		}
		catch(\Eception $e)
		{

			return $this->sendError($e->getMessage());
		}
	}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
		$id = $request->id;
		$quote = Quote::find($id);
		if(!$quote)
		{
			return response()->json(['success'=>false,'message'=>'Quote Already Deleted']);
		}
		$quote->delete();

		$bid_ids = Bid::where('quote_id',$id)->get()->pluck('id');
        Review::where('quote_id',$id)->delete();
		$oldbidImages = BidImage::whereIn('bid_id', $bid_ids)->get();

		foreach ($oldbidImages as $oldImage) {
			$oldImagePath = str_replace(asset(''), '', $oldImage->image);
			if (file_exists(public_path($oldImagePath))) {
				unlink(public_path($oldImagePath)); // Delete the old image file
			}

			$bid = Bid::find($oldImage->bid_id);
			$bid->delete();
			$oldImage->delete(); // Remove record from the database
		}

        $oldImages = QuoteImage::where('quote_id', $id)->get();

		foreach ($oldImages as $oldImage) {
			$oldImagePath = str_replace(asset(''), '', $oldImage->image);
			if (file_exists(public_path($oldImagePath))) {
				unlink(public_path($oldImagePath)); // Delete the old image file
			}
			$oldImage->delete(); // Remove record from the database
		}
        return response()->json(['success'=>false,'message'=>'Quote Deleted']);
    }

    public function image_destroy($id)
    {
        $oldImage = QuoteImage::find($id);
		if(!$oldImage)
		{
			return response()->json(['success'=>false,'message'=>'Image Already Deleted']);
		}

        $oldImagePath = str_replace(asset(''), '', $oldImage->image);
        if (file_exists(public_path($oldImagePath))) {
            unlink(public_path($oldImagePath)); // Delete the old image file
        }
        $oldImage->delete(); // Remove record from the database
        return response()->json(['success'=>true,'message'=>'Image Deleted'],200);
    }
}
