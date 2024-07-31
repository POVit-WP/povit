<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function registerview(){
        return view('register');
    }

    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'check box' => 'required|accepted',

        ]);
        // dd($request);
        // Create a new user
        $user = User::create([
            'name' => "{$request->firstname} {$request->lastname}",
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('login_page')->with('success', 'You have been registered successfully');
    }

    public function check(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['status' => 'authenticated', 'user' => Auth::user()]);
        }
        return response()->json(['status' => 'not authenticated']);
    }

    public function loginview()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember'); // Check if "Remember Me" is checked

        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session to prevent fixation
            $request->session()->regenerate();

            // Set the session lifetime based on "Remember Me" status
            $lifetime = $remember ? 1440 : 1; // 1440 minutes = 1 day, 1 minute = 60 seconds
            config(['session.lifetime' => $lifetime]);

            return redirect()->route('home');
        }

        // If authentication fails
        return back()->withErrors([
            'email' => 'Failed to login. Please check your credentials and try again.'
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login_page')->with('success', 'You have been logged out');
    }

    public function updateProfile(Request $request){
    $request->validate([
        'profile_pics' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    if ($request->hasFile('profile_pics')) {
        $user = Auth::user();

        if ($user->profile_pics && $user->profile_pics != 'default.png') {
            $existingImagePath = public_path('user_profile/' . $user->profile_pics);
            if (File::exists($existingImagePath)) {
                File::delete($existingImagePath);
            }
        }

        $photo_file = $request->file('profile_pics');
        $extension = $photo_file->extension();
        $imageName = date('dmyHis') . uniqid() . '.' . $extension;
        $photo_file->move(public_path('user_profile'), $imageName);

        $user->profile_pics = $imageName;
        $user->save();

        return redirect()->back()->with('success', 'Profile picture updated successfully');
    }

    return redirect()->back()->with('error', 'No image file selected');
}


    public function updateUsername(Request $request){
        // dd($request->all());

        $request->validate([
            'username' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->input('username');
        $user->save();
        return redirect()->back();
    }

    public function updateProfileDesc(Request $request){
        $request->validate([
            'profile_desc' => 'sometimes|nullable|string|max:50',
        ]);

        $user = Auth::user();
        $user->profile_desc = $request->input('profile_desc');
        $user->save();
        return redirect()->back();
    }

    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(){
        $user = Socialite::driver('google')->user();
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            Auth::login($existingUser, true);
            return redirect()->route('home');
        }

        $newUser = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'google_id' => $user->id,
            'password' => bcrypt('password'),
        ]);

        Auth::login($newUser, true);
        return redirect()->route('home');
    }
}
