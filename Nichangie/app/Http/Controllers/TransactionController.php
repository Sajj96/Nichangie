<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->user_type != 2) {
            $transactions = DB::table('transactions')
                                ->join('users', 'transactions.user_id', 'users.id')
                                ->leftJoin('stories', 'transactions.campaign', 'stories.id')
                                ->select('transactions.*', 'users.name', 'users.lastname', 'stories.title', 'stories.id as camp_id')
                                ->where('transactions.user_id', $user->id)
                                ->get();
            return view('admin.transactions.transaction', compact('transactions'));
        }

        $transactions = DB::table('transactions')
                                ->join('users', 'transactions.user_id', 'users.id')
                                ->leftJoin('stories', 'transactions.campaign', 'stories.id')
                                ->select('transactions.*', 'users.name', 'users.lastname', 'stories.title', 'stories.id as camp_id')
                                ->get();

        return view('admin.transactions.transaction', compact('transactions'));
    }

    public function withdraw(Request $request, $userid)
    {
 
        if(Auth::user()->user_type == 2) {
            $user = User::find($userid);
        } else {
            $user = Auth::user();
        }

        $transaction = new Transaction;

        if (!empty($request->id)) {
            $campaign = DB::table('stories')
                            ->leftJoin('donations', 'stories.id', 'donations.campaign_id')
                            ->select('stories.id', 'stories.title','stories.fee_percent', 'stories.fundgoals', 'stories.deadline', 'stories.status', 'stories.description', DB::raw('SUM(donations.amount) as amount'))
                            ->where('stories.owner_id', $user->id)
                            ->where('stories.id', $request->id)
                            ->groupBy('stories.id', 'stories.title','stories.fee_percent', 'stories.fundgoals', 'stories.deadline', 'stories.status', 'stories.description')
                            ->first();

            $balance = $transaction->campaignBalance($request->id, $user->id);
            return view('admin.transactions.withdraw', compact('campaign', 'balance','user'));
        }

        $balance = $transaction->userBalance($user->id);
        $campaign = (object) array('fee_percent' => 5);
        return view('admin.transactions.withdraw', compact('balance','user','campaign'));
    }

    public function requestWithdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric',
            'payment_method' => 'required'
        ]);

        if(Auth::user()->user_type == 2) {
            $user = User::find($request->user_id);
        } else {
            $user = Auth::user();
        }

        if ($validator->fails()) {
            return redirect()->route('transaction.withdraw', $user->id)->with('error', 'Amount and Payment Method are required!');
        }

        try {

            $transaction = new Transaction;

            if (!empty($request->campaign_id)) {
                $balance = $transaction->campaignBalance($request->campaign_id, $user->id);

                if ($request->amount > $balance) {
                    return redirect()->route('transaction.withdraw')->with('error', 'Sorry you don\'t have enough balance in this campaign to withdraw ' . $request->amount . '!');
                }
            }

            $fileName = '';
            if (!empty($request->invoice)) {
                $fileName = $request->invoice->getClientOriginalName();
                $extension = $request->file('invoice')->extension();
                $generated = uniqid() . "_" . time() . date("Ymd") . $extension;
                $fileName = $generated;
                $filePath = $request->file('invoice')->storeAs('invoices', $fileName, 'public');
                $type = pathinfo($filePath, PATHINFO_EXTENSION);
            }

            $transaction->user_id = $user->id;
            $transaction->campaign = (!empty($request->campaign_id)) ? $request->campaign_id : 0;
            $transaction->amount = $request->amount;
            $transaction->debit = $request->debit;
            $transaction->earned = ($request->amount - $request->debit);
            $transaction->payment_method = $request->payment_method;
            $transaction->phone = $request->phone;
            $transaction->account_name = $request->account_name;
            $transaction->account_number = $request->account_number;
            $transaction->bank_name = $request->bank_name;
            $transaction->branch_name = $request->branch_name;
            $transaction->invoice = $fileName;
            $transaction->supplier_contacts = $request->supplier_contacts;
            $transaction->done_by = (Auth::user()->user_type == 2) ? "Admin" : $user->name." ".$user->lastname;
            $transaction->status = Transaction::PAYMENT_INPROGRESS;
            if ($transaction->save()) {
                if (!empty($request->campaign_id)) {
                    $campaign = DB::table('stories')
                                    ->leftJoin('donations', 'stories.id', 'donations.campaign_id')
                                    ->select('stories.id', 'stories.title','stories.fee_percent', 'stories.fundgoals', 'stories.deadline', 'stories.status', 'stories.description', DB::raw('SUM(donations.amount) as amount'))
                                    ->where('stories.owner_id', $user->id)
                                    ->where('stories.id', $request->campaign_id)
                                    ->groupBy('stories.id', 'stories.title','stories.fee_percent', 'stories.fundgoals', 'stories.deadline', 'stories.status', 'stories.description')
                                    ->first();
        
                    $balance = $transaction->campaignBalance($request->campaign_id, $user->id);
                    return view('admin.transactions.withdraw', compact('campaign', 'balance','user'))->with('success', 'Withdraw request is submitted successfully. Please wait for confirmation.');
                }
                return redirect()->route('transaction.withdraw', $user->id)->with('success', 'Withdraw request is submitted successfully. Please wait for confirmation.');
            }
        } catch (\Exception $e) {
            return redirect()->route('transaction.withdraw', $user->id)->with('error', 'Something went wrong while requesting withdraw!');
        }
    }

    public function withdrawRequests()
    {
        $transactions = DB::table('transactions')
                            ->join('users', 'transactions.user_id', 'users.id')
                            ->leftJoin('stories', 'transactions.campaign', 'stories.id')
                            ->select('transactions.*', 'users.name', 'users.lastname', 'stories.title', 'stories.id as camp_id')
                            ->where('transactions.status',Transaction::PAYMENT_INPROGRESS)
                            ->get();
        return view('admin.transactions.withdraw_requests', compact('transactions'));
    }

    public function acceptWithdraw(Request $request)
    {
        try {
            $user = Auth::user();   
            $transaction = Transaction::find($request->id);
            $transaction->status = Transaction::PAYMENT_PAID;
            $transaction->managed_by = $user->name." ".$user->lastname;
            if($transaction->save()) {
                return redirect()->route('transaction.withdraw.request')->with('success', 'Withdraw accepted successfully!.');
            }
        } catch (\Exception $e) {
            return redirect()->route('transaction.withdraw.request')->with('error', 'Something went wrong.');
        }
    }

    public function rejectWithdraw(Request $request)
    {
        try {
            $user = Auth::user();
            $transaction = Transaction::find($request->reject_id);
            $transaction->status = Transaction::PAYMENT_REJECTED;
            $transaction->managed_by = $user->name." ".$user->lastname;
            if($transaction->save()) {
                return redirect()->route('transaction.withdraw.request')->with('success', 'Withdraw rejected successfully!.');
            }
        } catch (\Exception $e) {
            return redirect()->route('transaction.withdraw.request')->with('error', 'Something went wrong.');
        }
    }
}
