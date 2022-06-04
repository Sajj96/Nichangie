<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Category;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Carbon\Carbon;
use DataTables;

class StoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $category_types = DB::table('category_types')->select('*')->get();
        return view('campaign.create-campaign', compact('categories', 'category_types'));
    }

    public function show($id)
    {
        $campaign = Story::find($id);
        $donations = DB::table('donations')
                            ->where('campaign_id', $id)
                            ->where('status',Donation::PAID)
                            ->get();
        $total_donations = DB::table('donations')
                            ->where('campaign_id', $id)
                            ->where('status',Donation::PAID)
                            ->sum('amount');
        $donation_percent = ($campaign->fundgoals > 0) ? ($total_donations/$campaign->fundgoals) * 100 : 0;
        $date = date('Y-m-d');
        $start = strtotime($date);
        $end = strtotime($campaign->deadline);

        $days_between = ceil(abs($end - $start) / 86400);
        $doners = count($donations);

        return view('campaign.single-campaign', compact('campaign','total_donations','donation_percent','days_between','doners','donations'));
    }

    public function getAll()
    {
        $campaigns = DB::table('stories')
                        ->join('users','stories.owner_id','=','users.id')
                        ->select('stories.*','users.name','users.lastname')
                        ->where('stories.status','<>',"Closed")
                        ->where('stories.type',1)
                        ->get();
        $type = 'Featured';
        $campaign_data = array();
        foreach($campaigns as $key=>$rows) {
            $donations = DB::table('donations')
                            ->where('campaign_id', $rows->id)
                            ->get();
            $total_donations = DB::table('donations')
                                ->where('campaign_id', $rows->id)
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

        return view('campaign.campaigns', compact('campaign_data','type'));
    }

    public function getLatest()
    {
        $campaigns = DB::table('stories')
                        ->join('users','stories.owner_id','=','users.id')
                        ->select('stories.*','users.name','users.lastname')
                        ->where('stories.status','<>',"Closed")
                        ->orderByDesc('stories.id')
                        ->limit(20)
                        ->get();
        $type = 'Latest';
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
            $donation_percent = ($rows->fundgoals > 0) ? ($total_donations/$rows->fundgoals) * 100 : 0;
            $donation_array = (object) array(
                "total_donation"      => $total_donations,
                "donation_percentage" => $donation_percent,
                "doners"              => count($donations)
            );
            $campaign_data[] = (object) array_merge((array) $rows, (array) $donation_array);;
        }

        return view('campaign.campaigns', compact('campaign_data','type'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'story'       => 'required|string',
            'image'       => 'required',
            'category'    => 'required|string',
            'amount'      => 'required|numeric'
        ]);

        if($validator->fails()) {
            return redirect()->route('campaign')->with('error','All details are required!');
        }

        try {
            $fileName = $request->image->getClientOriginalName();
            $extension = $request->file('image')->extension();
            $generated = uniqid()."_".time().date("Ymd")."_IMG";
            if($extension == "png") {
                $fileName = $generated.".png";
            } else if($extension == "jpg") {
                $fileName = $generated.".jpg";
            } else if($extension == "jpeg") {
                $fileName = $generated.".jpeg";
            } else {
                return redirect()->route('campaign')->with('error', "Invalid file type only png, jpeg and jpg files are allowed.");
            }
            $filePath = $request->file('image')->storeAs('images', $fileName,'public');
            $type = pathinfo($filePath, PATHINFO_EXTENSION);
            $image_data = File::get(storage_path('/app/public/images/'.$fileName));
            $base64encodedString = 'data:image/' . $type . ';base64,' . base64_encode($image_data);
            $fileBin = file_get_contents($base64encodedString);
            $fileLink = url('storage/images/'.$fileName);

            $user = Auth::user();

            $campaign = new Story;
            $campaign->owner_id = $user->id;
            $campaign->owner_phonenumber = $user->phonenumber;
            $campaign->owner_name = $user->lastname;
            $campaign->title = $request->title;
            $campaign->description = strip_tags($request->story);
            $campaign->link = $fileLink;
            $campaign->fundgoals = $request->amount;
            $campaign->category = $request->category;
            $campaign->deadline = $request->enddate;
            $campaign->type = Story::NOT_FEATURED;
            $campaign->status = Story::STATUS_INPROGRESS;
            if($campaign->save()) {
                return redirect()->route('campaign.show', $campaign->id)->with('success','Campaign created successfully!');
            }
            file_put_contents("/app/public/images/".$fileName, $fileBin);
        } catch (\Exception $e) {
            return redirect()->route('campaign')->with('error','Something went wrong while creating a campaign!');
        }
    }

    public function storyLink($id)
    {
        $campaign = Story::find($id);
        if(!$campaign) {
            return response()->json(['error' => 'Sorry, campaign id maybe not be present!'], 422);
        }

        return response()->json(['link' => url('campaigns/'.$id)], 200);
    }

    public function getStories()
    {
        $user = Auth::user();
        $stories = Story::where('owner_id', $user->id)->get();
        $campaigns = DB::table('stories')
                        ->leftJoin('donations', 'stories.id','donations.campaign_id')
                        ->select('stories.id','stories.title','stories.type','stories.fundgoals','stories.deadline','stories.status','stories.description','donations.status as donationstatus',DB::raw('SUM(donations.amount) as amount')) 
                        ->where('stories.owner_id', $user->id)
                        ->orderBy('stories.id', 'DESC')
                        ->groupBy('stories.id','stories.title','stories.type','stories.fundgoals','stories.deadline','stories.status','stories.description')
                        ->get();
        return view('admin.campaigns.campaigns', compact('campaigns'));
    }

    public function exportCampaignData($id)
    {
        $user = Auth::user();
        $campaign = Story::find($id);
        $donations = DB::table('donations')
                        ->join('stories', 'donations.campaign_id','stories.id')
                        ->select('donations.*','stories.title')
                        ->where('stories.owner_id', $user->id)
                        ->where('donations.campaign_id', $id)
                        ->where('donations.status',Donation::PAID)
                        ->orderBy('donations.id', 'DESC')
                        ->get();
        $header_style = (new StyleBuilder())->setFontBold()->build();

        $rows_style = (new StyleBuilder())
            ->setFontSize(12)
            ->setBackgroundColor("EDEDED")
            ->build();   

        return (new FastExcel($donations))
        ->headerStyle($header_style)
        ->rowsStyle($rows_style)
        ->download('Donations for '.$campaign->title.'.xlsx', function($data){
            return [
                "Campaign Title"=> $data->title,
                "Donor Name"=> $data->name,
                "Donor Phone"=> $data->contact,
                "Amount Donated"=> $data->amount,
                "Date" => date('l, d Y',strtotime($data->created_at))
            ];
        });
    }

    public function close(Request $request)
    {
        try {
            if(Auth::user()->user_type == 2) {
                $user = User::find($request->user_id);
            } else {
                $user = Auth::user();
            }

            $campaign = Story::where('id',$request->campaign_id)->where('owner_id',$user->id)->first();
            $campaign->status = Story::STATUS_CANCELLED;
            $campaign->save();
            return redirect()->route('me.campaign')->with('success','Campaign was successfully closed.');
        } catch (\Exception $e) {
            return redirect()->route('me.campaign')->with('error','Something went wrong while closing a campaign!');
        }
    }

    public function getAllStories(Request $request)
    {
        if ($request->ajax()) {
            $campaigns = DB::table('stories')
                        ->leftJoin('users', 'stories.owner_id', 'users.id')
                        ->leftJoin('donations', 'stories.id','donations.campaign_id')
                        ->select('stories.id','stories.title','stories.fundgoals','stories.created_at','users.name', 'users.lastname','stories.owner_id','stories.deadline','stories.status','stories.description','donations.status as donationstatus',DB::raw('SUM(donations.amount) as amount')) 
                        ->groupBy('stories.id');
            // $campaigns = DB::table('stories');
            return Datatables::of($campaigns)
                    ->addIndexColumn()
                    ->addColumn('name', function ($row) {
                        return $row->name." ".$row->lastname;
                    })
                    ->addColumn('title', function ($row) {
                        return '<a href="'.route('campaign.show', $row->id).'">'.substr($row->title,0,15).'</a>';
                    })
                    ->addColumn('amount', function ($row) {
                        return ($row->donationstatus == 1) ? number_format($row->amount) : number_format(0);
                    })
                    ->addColumn('created_at', function ($row) {
                        return date('M d Y',strtotime($row->created_at));
                    })
                    ->addColumn('deadline', function ($row) {
                        return date('M d Y',strtotime($row->deadline));
                    })
                    ->addColumn('status', function ($row) {
                        if($row->status == "In progress"){
                            return '<div class="label-main"><label class="label label-lg bg-default">In progress</label></div>';
                        } else if($row->status == "Finished"){
                            return '<div class="label-main"><label class="label label-lg bg-success">Finished</label></div>';
                        } else {
                            return '<div class="label-main"><label class="label label-lg bg-danger">Closed</label></div>';
                        }
                    })
                    ->addColumn('action', function($row){
                            return '<div class="d-flex tooltip-btn button-list">
                                    <form data-id="'.$row->id.'" class="close-form" action="'.route('campaign.close').'" method="POST">
                                        <input type="hidden" name="_token" value="'.csrf_token().'">
                                        <input type="hidden" value="'.$row->id.'" name="campaign_id">
                                        <button type="submit" class="btn btn-danger waves-effect" data-toggle="tooltip" data-placement="top" title="Close Campaign"><i class="ti-close"></i></button>
                                    </form>
                                    <form data-id-withdraw="'.$row->id.'" class="withdraw-form" style="margin: 0 5px;" action="'.route("transaction.withdraw",$row->owner_id).'" method="POST">
                                        <input type="hidden" name="_token" value="'.csrf_token().'">
                                        <input type="hidden" value="'.$row->id.'" name="id">
                                        <button type="submit" class="btn btn-success waves-effect" data-toggle="tooltip" data-placement="top" title="Request Withdraw"><i class="ti-wallet"></i></button>
                                    </form>
                                    <a href="'.route("campaign.export", $row->id).'" class="btn btn-info waves-effect" data-toggle="tooltip" data-placement="top" title="Export Data"><i class="ti-download"></i></a>
                                    </div>';
                    })
                    ->filter(function ($query) use ($request) {
                        if (!empty($request->get('status')) && ($request->has('status'))) {
                            $query->where('stories.status', $request->get('status'));
                        }
        
                        if(!empty($request->get('from'))) {
                            if ($request->has('from') && $request->has('to')) {
                                $startDate = Carbon::createFromFormat('Y-m-d', $request->get('from'));
                                $endDate = Carbon::createFromFormat('Y-m-d', $request->get('to'));
                                $query->where('stories.created_at', '>=', $startDate)
                                ->where('stories.created_at', '<=', $endDate);
                            }
                        }

                        if (!empty($request->get('search'))) {
                            $query->where(function($w) use($request){
                               $search = $request->get('search');
                               $w->orWhere('stories.title', 'LIKE', "%$search%")
                               ->orWhere('stories.status', 'LIKE', "%$search%")
                               ->orWhere('users.name', 'LIKE', "%$search%")
                               ->orWhere('users.lastname', 'LIKE', "%$search%");
                           });
                       }
                    })
                    ->rawColumns(['title','action','status'])
                    ->make(true);
        }

        return view('admin.campaigns.all_campaigns');
    }

    public function upgradeStory(Request $request)
    {
        try {
            $campaign = Story::find($request->story_id);
            $campaign->fee_percent = 6;
            $campaign->type = Story::FEATURED;
            if($campaign->save()) {
                return redirect()->route('me.campaign')->with('success','You have successful upgraded your campaign.');  
            }
        } catch (\Exception $e) {
            return redirect()->route('me.campaign')->with('error','Something went wrong.');
        }
    }
}
