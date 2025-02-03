<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;


class AbcController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendNotification(Request $request)
    {
        // return 'asd';

        $validatedData = $request->validate([
            // 'token' => 'required|string', // Validate the FCM token
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $token = 'c_V14kXmR02189jbKmNaFD:APA91bHTkvGnnTbwFXOJyIN-TPMgVq4h9eWwLoNVO92-EAlKZsrCiz6WMprDFSfapz76_8xSdMrBQOUCmLvb5qEblZMaDluweJd_UGcE6GlJq88PiSxuwbt74bwmSxze33isaOsTZnCm';
        // Extract the validated data
        $fcmToken = $token;
        $title = $validatedData['title'];
        $body = $validatedData['body'];

        // Send notification using FirebaseService
        $response = $this->firebaseService->sendNotification($fcmToken, $title, $body);

        // Return the response
        return response()->json($response);
    }
}
