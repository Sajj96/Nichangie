<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    public function getDonations()
    {
        $user = Auth::user();
        $donations = DB::table('donations')
                                ->join('stories','donations.campaign_id','stories.id')
                                ->where('stories.owner_id', $user->id)
                                ->select('donations.*','stories.title')
                                ->get();
        return view('admin.donations.donations', compact('donations'));
    }
    
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donate_amount'  => 'required|numeric',
            'phone'        => 'required|string'
        ]);

        if($validator->fails()) {
            return redirect()->route('campaign.show', $request->campaign_id)->with('error','Phone number and amount are required!');
        }

        try {

            $characters = '123456789';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 13; $i++) {
                $string .= $characters[mt_rand(0, $max)];
            }
            
            if(!empty($request->anonymous)) {
                $name = $request->anonymous; 
            } else if(empty($request->anonymous) && empty($request->name)) { 
                $name = "Anonymous";
            } else {
                $name = $request->name;
            }

            $donation = new Donation;
            $donation->campaign_id = $request->campaign_id;
            $donation->name = $name;
            $donation->email = $request->email;
            $donation->contact = str_replace('+','',$request->phone);
            $donation->comment = $request->comment;
            $donation->amount = $request->donate_amount;
            $donation->transaction_number = $string;
            $ussd = app(PaymentService::class);
            $response = $ussd->ussdPush(str_replace('+','',$request->phone),$request->donate_amount,$string)->getData();
            if($response->body->response->responseStatus == "Accepted Successfully") {
                if($donation->save())
                return redirect()->route('campaign.show', $request->campaign_id)->with('success','Donation is in progress. Please complete payment through USSD appeared on your phone');
            }
        } catch (\Exception $e) {
            return redirect()->route('campaign.show', $request->campaign_id)->with('error',$e->getMessage());
        }
    }

    public function donation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'amount'       => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json(['error' => 'Only valid details are required'], 422);
        }

        try {
            
            $characters = '123456789';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 13; $i++) {
                $string .= $characters[mt_rand(0, $max)];
            }

            $ussd = app(PaymentService::class);
            $response = $ussd->ussdPush($request->phone_number,$request->amount,$string);
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
