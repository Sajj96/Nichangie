<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    const PAYMENT_INPROGRESS = "In progress";
    const PAYMENT_PAID = "Accepted";
    const PAYMENT_REJECTED = "Rejected";

    public function campaignTransactions($campaign_id,$user_id)
    {
        $transaction = DB::table('transactions')
                        ->where('campaign', $campaign_id)
                        ->where('user_id', $user_id)
                        ->sum('amount');
        $transaction_amount = $transaction ?? 0;
        return $transaction_amount;
    }

    public function userTransactions($user_id)
    {
        $transaction = DB::table('transactions')
                        ->where('user_id', $user_id)
                        ->sum('amount');
        $transaction_amount = $transaction ?? 0;
        return $transaction_amount;
    }

    public function userNonFeaturedTransactions($user_id)
    {
        $transaction = DB::table('transactions')
                            ->where('transactions.user_id', $user_id)
                            ->whereNotIn('transactions.campaign',DB::table('stories')->select('id')->where('type', 1))
                            ->sum('transactions.amount');
        $transaction_amount = $transaction ?? 0;
        return $transaction_amount;
    }

    public function campaignBalance($campaign_id,$user_id)
    {
        $donation = new Donation;
        $campaign_donation = $donation->campaignDonations($campaign_id,$user_id);
        $campaign_transaction = self::campaignTransactions($campaign_id,$user_id);
        $campaign_balance = $campaign_donation - $campaign_transaction;
        $balance = $campaign_balance ?? 0;
        return $balance;
    }

    public function userBalance($user_id)
    {
        $donation = new Donation;
        $user_donation = $donation->userDonations($user_id);
        $user_transaction = self::userTransactions($user_id);
        $user_balance = $user_donation - $user_transaction;
        $balance = $user_balance ?? 0;
        return $balance;
    }

    public function userNonFeaturedBalance($user_id)
    {
        $donation = new Donation;
        $user_donation = $donation->userNonFeaturedDonations($user_id);
        $user_transaction = self::userNonFeaturedTransactions($user_id);
        $user_balance = $user_donation - $user_transaction;
        $balance = $user_balance ?? 0;
        return $balance;
    }

    public function earning()
    {
        $transaction = DB::table('transactions')
                        ->sum('earned');
        $transaction_amount = $transaction ?? 0;
        return $transaction_amount;
    }
}
