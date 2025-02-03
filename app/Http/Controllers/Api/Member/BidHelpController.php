<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BidHelp;
use App\Models\BidHelpImage;
use Auth;

class BidHelpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try
        {
            $user = BidHelp::with('images','negotiator_info')->where('user_id',Auth::user()->id)->paginate(10);

			return response()->json(['success'=>true,'message'=>'Bid Help List','bid_help_info'=>$user]);
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
        try{
			
            $data = BidHelp::create([
                'user_id' => Auth::user()->id,
                'bid_name' => $request->Qbid_name,
                'service_type' => $request->service_type,
                'description' => $request->description,
                // 'notes' => $request->notes,
            ]);

            
			if ($request->hasFile('images')) {
                $uploadedFiles = $request->file('images');
                $profileUrls = [];
            
                foreach ($uploadedFiles as $file) {
                    $fileName = md5($file->getClientOriginalName() . time()) . "Qbid." . $file->getClientOriginalExtension();
                    $file->move('uploads/bidhelp/', $fileName);
                    $profileUrls = asset('uploads/bidhelp/' . $fileName);

                    BidHelpImage::create([
                        'bid_help_id' => $data->id,
                        'image' => $profileUrls
                    ]);
                }
            }
           $user = BidHelp::with('images')->where('user_id',Auth::user()->id)->get();

			return response()->json(['success'=>true,'message'=>'Bid Help Created Successfully','bid_help_info'=>$user]);
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
        //
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
