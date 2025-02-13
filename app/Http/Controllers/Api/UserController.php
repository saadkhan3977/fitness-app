<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Review;
use App\Models\Quote;
use App\Models\Bid;
use App\Models\Notification;
use Image;
use File;
use Auth;
use Validator;
class UserController extends BaseController
{
	public function __construct()
    {
		$stripe = \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

	public function un_reead_notification()
	{
		$notification = Auth::user()->unreadNotifications;
		$notificationold = Auth::user()->readNotifications;
		$unread = count(Auth::user()->unreadNotifications);
		$read = count(Auth::user()->readNotifications);
		// return $notification[0]->data['title'];
		$data = null;
		if($notification)
		{
			foreach($notification as $row)
			{
				$data[] = [
					'id' => $row->id,
					'title' => $row->data['title'],
					'description' => $row->data['description'],
					'created_at' => $row->data['time'],
					'status' => 'unread'
				];
				// $data[] = $row->data;
			}
		}

		$olddata = null;
		if($notificationold){

			foreach($notificationold as $row)
			{
				$data[] = [
					'id' => $row->id,
					'title' => $row->data['title'],
					'description' => $row->data['description'],
					'read_at' => $row->data['time'],
					'status' => 'read'
				];
			}
		}
		return response()->json(['success'=>true,'unread'=> $unread,'read'=> $read,'notification' => $data]);
	}


	public function read_notification(Request $request)
	{
		try{
			$validator = Validator::make($request->all(),[
				'notification_id' => 'required',
			]);
			if($validator->fails())
			{

				return response()->json(['success'=>false,'message'=> $validator->errors()->first()]);
			}

			$notification= Notification::find($request->notification_id);
			if($notification){
				$notification->read_at = date(now());
				$notification->save();
				$status= $notification;
				if($status)
				{
					return response()->json(['success'=>true,'message'=> 'Notification successfully deleted']);
				}
				else
				{
					return response()->json(['success'=>false,'message'=> 'Error please try again']);
				}
			}
			else
			{
				return response()->json(['success'=>false,'message'=> 'Notification not found']);
			}
		}
		catch(\Eception $e)
		{
			return response()->json(['error'=>$e->getMessage()]);
	   	}
	}

    public function profile(Request $request)
	{
		try {
			$olduser = User::where('id', Auth::user()->id)->first();

			$validator = Validator::make($request->all(), [
				'height' => 'nullable|numeric',
				'weight' => 'nullable|numeric',
				'goal' => 'nullable|string',
				'additional_goal' => 'nullable|string',
				'food_preferences' => 'nullable|string',
				'hear_about' => 'nullable|string',
				'variety' => 'nullable|string',
				'meal_in_day' => 'nullable|array',
				'allow_reminders' => 'nullable|boolean',

				// New fields
				'current_weight' => 'nullable|numeric',
				'dob' => 'nullable|date',
				'active_diet' => 'nullable|string',
				'activity_level' => 'nullable|string',
				'calories' => 'nullable|integer',
				'vegan' => 'nullable|boolean',
				'vegetarian' => 'nullable|boolean',
				'pascetarian' => 'nullable|boolean',
				'allergic_to_nuts' => 'nullable|boolean',
				'allergic_to_fish' => 'nullable|boolean',
				'allergic_to_shellfish' => 'nullable|boolean',
				'allergic_to_egg' => 'nullable|boolean',
				'allergic_to_milk' => 'nullable|boolean',
				'lactose_intolerant' => 'nullable|boolean',
				'gluten_intolerant' => 'nullable|boolean',
				'whete_intolerant' => 'nullable|boolean',
				'water_tracker' => 'nullable|boolean',
				'fasting' => 'nullable|boolean',
				'vegetable_tracker' => 'nullable|boolean',
				'seafood_tracker' => 'nullable|boolean',
			]);

			if ($validator->fails()) {
				return $this->sendError($validator->errors()->first());
			}

			$userdetail = UserDetail::where('user_id', Auth::id())->first();
			$input = $request->all();
			$input['user_id'] = Auth::id();

			if ($userdetail) {
				$userdetail->update([
					'height' => $request->height,
					'weight' => $request->weight,
					'goal' => $request->goal,
					'additional_goal' => $request->additional_goal,
					'food_preferences' => $request->food_preferences,
					'hear_about' => $request->hear_about,
					'variety' => $request->variety,
					'meal_in_day' => $request->meal_in_day,
					'allow_reminders' => $request->allow_reminders,
				]);
			} else {
				UserDetail::create([
					'user_id' => Auth::id(),
					'height' => $request->height,
					'weight' => $request->weight,
					'goal' => $request->goal,
					'additional_goal' => $request->additional_goal,
					'food_preferences' => $request->food_preferences,
					'hear_about' => $request->hear_about,
					'variety' => $request->variety,
					'meal_in_day' => $request->meal_in_day,
					'allow_reminders' => $request->allow_reminders,
				]);
			}

			// Update user table with new fields
			$user = User::find(Auth::id());
			$user->update([
				'current_weight' => $request->current_weight,
				'dob' => $request->dob,
				'active_diet' => $request->active_diet,
				'activity_level' => $request->activity_level,
				'calories' => $request->calories,
				'vegan' => $request->vegan,
				'vegetarian' => $request->vegetarian,
				'pascetarian' => $request->pascetarian,
				'allergic_to_nuts' => $request->allergic_to_nuts,
				'allergic_to_fish' => $request->allergic_to_fish,
				'allergic_to_shellfish' => $request->allergic_to_shellfish,
				'allergic_to_egg' => $request->allergic_to_egg,
				'allergic_to_milk' => $request->allergic_to_milk,
				'lactose_intolerant' => $request->lactose_intolerant,
				'gluten_intolerant' => $request->gluten_intolerant,
				'whete_intolerant' => $request->whete_intolerant,
				'water_tracker' => $request->water_tracker,
				'fasting' => $request->fasting,
				'vegetable_tracker' => $request->vegetable_tracker,
				'seafood_tracker' => $request->seafood_tracker,
			]);

			$user = User::with('user_profile')->find(Auth::user()->id);

			return response()->json([
				'success' => true,
				'message' => 'Profile Updated Successfully',
				'user_info' => $user
			]);

		} catch (\Exception $e) {
			return $this->sendError($e->getMessage());
		}
	}

	public function review(Request $request)
	{
		try
		{
			//return Auth::user()->role;
			$validator = Validator::make($request->all(),[
				'quote_id' =>'required',
				'rating' =>'required',
				'text' =>'string',
			]);
			if($validator->fails())
			{
				return $this->sendError($validator->errors()->first());
			}

			$quote = Quote::find($request->quote_id);
			if(Auth::user()->role == 'Qbid Member')
			{
				$assign_user_id = Bid::where('quote_id',$quote->id)->where('status','accept')->first();
			}
			else
			{
				$assign_user_id = $quote->user_id;
			}

			//return $assign_user_id;
			$review = Review::create([
				'quote_id' => $request->quote_id,
				'assign_user_id' => (Auth::user()->role == 'Business Qbidder') ? $quote->negotiator_id : null,
				'user_id' => (Auth::user()->role == 'Qbid Member') ? $quote->user_id : null,
				'rating' => $request->rating,
				'text' => $request->text,
			]);

			$quote->status = 'review';
			$quote->save();



			return response()->json(['success'=>true,'message'=>'Review Created Successfully','review'=>$review]);

		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}


	public function review_list(Request $request)
	{
		try
		{
			$review = Review::with('user_info','quote_info','quote_info.review_user_info')->where('user_id',Auth::user()->id)->get();

			return response()->json(['success'=>true,'message'=>'Review Lists','review'=>$review]);

		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}

	public function status_update(Request $request)
	{
		$user = User::find(Auth::user()->id);

		$user->update([
			'status' => $request->status,
		]);
		// $users = User::find(Auth::user()->id);
		$users = User::with('negotiator_review','negotiator_review.user_info',"sum_negotiator")->find(Auth::user()->id);
                        $avg = Review::where('assign_user_id', $users->id)->avg('rating');
                        $users->total_earning = $users->sum_negotiator()->sum("negotiator_amount");
                        $users->current_month_earning = $users->current_month_earning()->sum("negotiator_amount");

		return response()->json(['success'=>true,'message'=>'Status Update Successfully','user'=>$users,'average_rating'=>$avg]);

	}

	public function negotiator_photo_update(Request $request)
	{
		$user = User::find(Auth::user()->id);

		$profile = $user->photo;
		if($request->hasFile('photo'))
		{
			$file = request()->file('photo');
			$fileName = md5($file->getClientOriginalName() . time()) . $file->getClientOriginalExtension();
			$file->move('uploads/user/profiles/', $fileName);
			$profile = asset('uploads/user/profiles/'.$fileName);
		}

		$user->update([
			'photo' => $profile,
		]);
		$users = User::find(Auth::user()->id);

		return response()->json(['success'=>true,'message'=>'Profile Photo Update Successfully','user'=>$users]);

	}

	public function negotiator_coverphoto_update(Request $request)
	{
		$user = User::find(Auth::user()->id);

		// $profile = $user->photo;
		if($request->hasFile('coverphoto'))
		{
			$file = request()->file('coverphoto');
			$fileName = md5($file->getClientOriginalName() . time()) . $file->getClientOriginalExtension();
			$file->move('uploads/user/profiles/', $fileName);
			$profile = asset('uploads/user/profiles/'.$fileName);
		}

		$user->update([
			'coverphoto' => $profile,
		]);
		$users = User::find(Auth::user()->id);

		return response()->json(['success'=>true,'message'=>'Profile Cover Photo Update Successfully','user'=>$users]);

	}

	public function negotiator_profile_update(Request $request)
	{
		try
		{
			$user = User::find(Auth::user()->id);
			$language = $user->language;
			$expertise = $user->expertise;

			if($request->language){
				$language = json_encode($request->language);
			}
			if($request->expertise){
				$expertise = json_encode($request->expertise);
			}

			$user->update([
				'first_name' => $request->first_name ,
				'last_name' => $request->last_name,
				'company_name' => $request->company_name,
				'address' => $request->address,
				'city' => $request->city,
				'state' => $request->state,
				'zip' => $request->zip,
				'language' => $language,
				'expertise' => $expertise,
				'expertise' => $expertise,
			]);
			$users = User::find(Auth::user()->id);

			return response()->json(['success'=>true,'message'=>'Profile Update Successfully','user'=>$users]);
		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}


	public function support(Request $request)
	{
		try
		{
			$validator = Validator::make($request->all(),[
				'name' =>'required',
				'phone' =>'required',
				'email' =>'required',
				'subject' =>'required',
				'description' =>'required',
			]);
			if($validator->fails())
			{
				return $this->sendError($validator->errors()->first());
			}
			$review = Support::create([
				'user_id' => Auth::user()->id,
				'name' => $request->name,
				'phone' => $request->phone,
				'email' => $request->email,
				'subject' => $request->subject,
				'description' => $request->description,
			]);

			return response()->json(['success'=>true,'message'=>'Support Created Successfully','review'=>$review]);

		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}

	public function support_list(Request $request)
	{
		try
		{

			$review = Support::get();

			return response()->json(['success'=>true,'message'=>'Support Lists','review'=>$review]);

		}
		catch(\Exception $e)
		{
			return $this->sendError($e->getMessage());
		}
	}

	public function current_plan(Request $request)
	{
		try{
		//$user= User::findOrFail(Auth::id());
		$user = User::with(['child','goal','temporary_wallet','wallet','payments'])->where('id',Auth::user()->id)->first();

		$amount = 100;
		$charge = \Stripe\Charge::create([
			'amount' => $amount,
			'currency' => 'usd',
			'customer' => $user->stripe_id,
		]);
		if($request->current_plan == 'basic')
		{
			$user->update(['current_plan' =>"premium",'card_change_limit'=>'1','created_plan'=> \Carbon\Carbon::now()]);
			return response()->json(['success'=>true,'message'=>'Current Plan Updated Successfully','user_info'=>$user,'payment' => $charge]);

		}
		elseif($request->current_plan == 'premium')
		{
			$user->update(['current_plan' =>"basic",'card_change_limit'=>'0','created_plan'=> \Carbon\Carbon::now()]);

		 return response()->json(['success'=>true,'message'=>'Current Plan Updated Successfully','user_info'=>$user]);
		}
		else
		{
			return $this->sendError("Invalid Body ");
		}
		}
		catch(\Exception $e){
	  return $this->sendError($e->getMessage());

		}

	}


}
