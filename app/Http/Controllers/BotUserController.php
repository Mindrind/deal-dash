<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BotUser;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Cookie;
use Session;
use Nexmo;
use Twilio\Rest\Client;
use Artisan;
use Faker\Factory as Faker;



class BotUserController extends Controller
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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    public function index(Request $request)
    {
       // Fetch BotUser models with related Product model
        $users = User::where('is_bot_user', 1)->paginate(15);

        return view('bot_users.index', compact('users'));
    }


    public function create(){


        return view("bot_users.create");
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function store(Request $request)
    {


        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $faker = Faker::create();
        $email = $faker->email;
        $data = array_merge($data, ["location" => $this->getRandomCountry(), 'is_bot_user' => 1, 'email' => $email]);

        $user = User::create($data);


        if($user == null){
            return response()->json(['status' => 'error', 'user' => "",'message' => 'something went wrong please try again'], 500); 
        }
        return redirect("/admin/bot_users");
    }
    public function update(Request $request)
    {



        $user = User::findOrFail($request->id);

        $data = $request->validate([
            'name' => 'required|string',
        ]);
        
        if($user->update($data)){
            flash(translate('User information updated successfully'))->success();
            return redirect("/admin/bot_users");
        }else{
            flash(translate('Something went wrong while updated user information'))->error();
            return redirect("/admin/bot_users");

        }
    }





    public function destroy($id)
    {
        
        if(User::destroy($id)){

            flash(translate('User has been deleted successfully'))->success();

            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            return redirect("/admin/bot_users");
        }
        else{
            flash(translate('Something went wrong'))->error();
            return redirect("/admin/bot_users");
        }
    }

    public function admin_bot_user_edit($id){
        
        $user = User::findOrFail($id);


        return view("bot_users.edit", compact('user'));



    }


    

 
    public function createBotUser(Request $request){

        $user = User::create([
            'name' => $request->name,
            'avatar_original' => $request->avatar_url,
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);


        return response()->json(['status' => 'success', 'data' => $user->toArray()], 200);
    }



    public function botsUsers (){
     
        $botsUsers = [];
        $user1 = User::create([
            'name' => "Laura Roman",
            'avatar_original' => 'https://randomuser.me/api/portraits/women/17.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);

        
        $user2 = User::create([
            'name' => "Hrithik Hiremath",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/men/89.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        
        $user3 = User::create([
            'name' => "Aurora Turner",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/women/17.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        $user4 = User::create([
            'name' => "Tammy Warren",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/women/8.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);

        $user5 = User::create([
            'name' => "Kahaan Kumar ",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/men/65.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);

        $user6 = User::create([
            'name' => "Alice Addy ",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/women/63.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        
        $user7 = User::create([
            'name' => "Mario Roybal ",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/men/38.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        
        $user8 = User::create([
            'name' => "Dobroslav Kotik ",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/men/94.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        $user9 = User::create([
            'name' => "Vselyud Shutko ",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/men/23.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        $user10 = User::create([
            'name' => "Purificación Pascual ",
            'avatar_original' => 'https://randomuser.me/api/portraits/med/women/78.jpg',
            'password' => Hash::make('password'),
            'verification_code' => rand(100000, 999999)
        ]);
        $botsUsers[] = $user1;
        $botsUsers[] = $user2;
        $botsUsers[] = $user3;
        $botsUsers[] = $user4;
        $botsUsers[] = $user5;
        $botsUsers[] = $user6;
        $botsUsers[] = $user7;
        $botsUsers[] = $user8;
        $botsUsers[] = $user9;
        $botsUsers[] = $user10;

        return response()->json(['botsUsers' => $botsUsers], 200);
        

    }

    // Function to get a random country name from an array
        protected function getRandomCountry() {
            $countries = array(
                'United States',
                'Canada',
                'Brazil',
                'France',
                'Japan',
                'India',
                'Australia',
                'Germany',
                'Netherlands',
                'South Africa',
                'China',
                'United Kingdom',
                'Mexico',
                'Argentina',
                'Spain',
                'Italy',
                'Russia',
                'Thailand',
                'Indonesia',
                'South Korea'
            );
            $index = array_rand($countries); // Get a random index from the array
            return $countries[$index]; // Return the country name at the random index
        }



}
