<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuoteHelp;
use App\Services\FirebaseService;
use App\Models\User;
use App\Models\Notification;
use Auth;
use Validator;

class QuoteHelpController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    public function index()
    {
        try
        {
            $user = QuoteHelp::with('bid','bid.user_info','negotiator_info')->where(['user_id'=>Auth::user()->id])->orderBy('id','desc')->paginate(10);

			return response()->json(['success'=>true,'message'=>'Quote List','quote_info'=>$user]);
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try
        {
            $data = QuoteHelp::create([
                'user_id' => Auth::user()->id,
                'service_preference' => $request->service_preference,
            ]);
            $fullName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

            $users = User::whereJsonContains('expertise', $request->service_preference)->get();
            if($users)
            {
                foreach($users as $user)
                {
                    $fcmToken = $user->device_token;
                    Notification::create([
                        'user_id' =>  $user->id,
                        'quote_id' =>  $data->id,
                        'service_preference' =>  $request->service_preference,
                        'title' => 'Quote Help',
                        'body' => 'MR '. $fullName .' is looking for an '. $request->service_preference .' expert to help him',
                    ]);
                    // $service_preference = $request->service_preference;

                    $title = 'Quote Help';
                    $body = 'MR '. $fullName .' is looking for an '. $request->service_preference .' expert to help him';
                    $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);
                }
            }
            // return User::where('role','Qbid Negotiator')->where('expertise', 'like', "%$service_preference%")->get();

			return response()->json(['success'=>true,'message'=>'Created Successfully']);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

            $validator = Validator::make($request->all(), [
                'offered_price' => 'numeric',
                'negotiator_tip' => 'numeric',
            ]);
            if($validator->fails())
            {
    		    return $this->sendError($validator->errors()->first(),500);
            }

            $quote = QuoteHelp::find($id);

            $quote->update([
                'title' => $request->title,
                'state' => $request->state,
                'city' => $request->city,
                'offered_price' => $request->offered_price,
                'negotiator_tip' => $request->negotiator_tip,
                'description' => $request->description,
            ]);

			return response()->json(['success'=>true,'message'=>'Updated Successfully']);
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
    public function destroy($id)
    {
        //
    }
}
