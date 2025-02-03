<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Quote;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\AdminInfo;
use Validator;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function admin_info()
    {
        $admin = AdminInfo::first();
        return view('admin.info', compact('admin'));
    }
    public function negotiators_list()
    {
        $users = User::where('role', 'Qbid Negotiator')->get();
        return view('admin.negotiator_list', compact('users'));
    }
// Review
    public function reviews()
    {
        $reviews = Review::orderby('created_at', 'desc')->get();
        // dd($review);
        return view('admin.review', compact('reviews'));
    }
    public function reviewsDel($id)
    {
        $guide = Review::find($id);
        $guide->delete();
        return redirect()->back()->with('success', 'Review Deleted Successfully');

    }
// End Review

// Quotes
    public function quotes()
    {
        $quotes = Quote::orderby('created_at', 'desc')->get();
        // dd($review);
        return view('admin.quotes', compact('quotes'));
    }
    public function quotesDel($id)
    {
        $guide = Quote::find($id);
        $guide->delete();
        return redirect()->back()->with('success', 'Quote Deleted Successfully');

    }
// End Quotes

// Bids
    public function bids()
    {
        $bids = Bid::orderby('created_at', 'desc')->get();
        // dd($review);
        return view('admin.bids', compact('bids'));
    }
    public function bidsDel($id)
    {
        $guide = Bid::find($id);
        $guide->delete();
        return redirect()->back()->with('success', 'Quote Deleted Successfully');

    }
// End Bids

// Users
    public function users()
    {
        $users = User::where('role', 'Qbid Member')->orderby('created_at', 'desc')->get();
        // dd($review);
        return view('admin.users', compact('users'));
    }
    public function usersDel($id)
    {
        $guide = User::find($id);
        $guide->delete();
        return redirect()->back()->with('success', 'User Deleted Successfully');

    }
// End Users
    public function admin_info_post(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'official_email' => 'required|email',
                'phone' => 'required|numeric'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->with(['error' => $validator->errors()->first()]);
            }
            if ($request->id != null) {
                $admin = AdminInfo::first();
                $admin->official_email = $request->official_email;
                $admin->phone = $request->phone;
                $admin->save();
            } else {
                $admin = new AdminInfo();
                $admin->official_email = $request->official_email;
                $admin->phone = $request->phone;
                $admin->save();
            }
            return redirect()->back()->with(['success' => 'Record Created Successfully']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }
}
