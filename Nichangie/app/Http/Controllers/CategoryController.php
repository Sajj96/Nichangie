<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Donation;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.categories', compact('categories'));
    }

    public function show()
    {
        return view('admin.category.create');
    }

    public function getCategories()
    {
        return view('category.categories');
    }

    public function getCategoryCampaigns($name)
    {
        $category = Category::where('name',$name)->first();
        $campaigns = DB::table('stories')
                        ->join('users','stories.owner_id','=','users.id')
                        ->select('stories.*','users.name','users.lastname')
                        ->where('stories.status','<>',"Closed")
                        ->where('stories.category',$name)
                        ->get();

        $campaign_data = array();
        foreach($campaigns as $key=>$rows) {
            $donations = DB::table('donations')
                            ->where('campaign_id', $rows->id)
                            ->where('status',Donation::PAID)
                            ->get();
            $total_donations = DB::table('donations')
                                ->where('campaign_id', $rows->id)
                                ->where('status',Donation::PAID)
                                ->sum('amount');
            $total_donations = $total_donations ?? 0;
            $donation_percent = ($rows->fundgoals > 0) ? ($total_donations/$rows->fundgoals) * 100 : 0;
            $donation_array = (object) array(
                "total_donation"      => $total_donations,
                "donation_percentage" => $donation_percent,
                "doners"              => count($donations)
            );
            $campaign_data[] = (object) array_merge((array) $rows, (array) $donation_array);;
        }
        return view('category.single-category', compact('category', 'campaign_data'));
    }

    public function create(Request $request)
    {
        
    }
}
