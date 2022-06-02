<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\File;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::VERIFY;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            // 'email' => ['required_if:user_type,2', 'string', 'email', 'max:255', 'unique:users'],
            'phonenumber' => ['required', 'string', 'unique:users','min:10',],
            'idtype' => ['required', 'string', 'max:255'],
            'idlink' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $fileName = $data['idlink']->getClientOriginalName();
        $extension = $data['idlink']->extension();
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
        $filePath = $data['idlink']->storeAs('IDs', $fileName,'public');
        $type = pathinfo($filePath, PATHINFO_EXTENSION);
        $image_data = File::get(storage_path('/app/public/IDs/'.$fileName));
        $base64encodedString = 'data:image/' . $type . ';base64,' . base64_encode($image_data);
        $fileBin = file_get_contents($base64encodedString);
        
        $fileLink = url('storage/IDs/'.$fileName);

        $user               = new User;
        $user->name         = $data['name'];
        $user->lastname     = $data['lastname'];
        $user->location     = $data['address'];
        $user->email        = $data['email'];
        $user->idtype       = $data['idtype'];
        $user->idlink       = $fileLink;
        $user->district     = $data['district'];
        $user->city         = $data['town_city'];
        $user->phonenumber  = str_replace('+','',$data['phonenumber']);
        $user->password     = Hash::make($data['password']);
        $user->status       = User::USER_INACTIVE;
        $user->user_type    = User::DONEE_USER_TYPE;
        if($user->save()) {
            $smsOpt = app(SMSService::class);
            $smsOpt->sendOtp(str_replace('+','',$data['phonenumber']));
        }
        // file_put_contents("/app/public/IDs/".$fileName, $fileBin);
        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        return $this->registered($request, $user)
           ?: redirect($this->redirectPath());
    }
}
