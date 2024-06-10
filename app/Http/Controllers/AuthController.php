<?php

namespace App\Http\Controllers;

use App\Events\UserSubscribed;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    // Register user
    public function register(Request $request){
        // Validate
        $fields = $request->validate([
            'username'  => ['required' , 'max:255'],
            'email'     => ['required' , 'max:255' , 'email' , 'unique:users'],
            'password'  => ['required' , 'min:3' , 'confirmed']
        ]);

        // Register
        $user = User::create($fields);

        // Login
        Auth::login($user);

        event(new Registered($user));

        if($request->subscribe){
            event(new UserSubscribed($user));
        }

        // Redirect
        return redirect()->route('dashboard');
    }

    // Veify Email Notice Handler
    public function verifyNotice(){
        return view('auth.verify-email');
    }

    // The Email Verification Handler
    public function verifyEmail(EmailVerificationRequest $request) {
        $request->fulfill();
     
        return redirect()->route('dashboard');
    }

    // Resending the Verification Email
    public function verifyHandler(Request $request) {
        $request->user()->sendEmailVerificationNotification();
     
        return back()->with('message', 'Verification link sent!');
    }

    // Login user
    public function login(Request $request){
        // Validate
        $fields = $request->validate([
            'email'     => ['required' , 'max:255' , 'email'],
            'password'  => ['required']
        ]);

        // Try to login user
        if(Auth::attempt($fields, $request->remember)){
            return redirect()->intended('dashboard');
        }else{
            return back()->withErrors([
                'failed' => 'The provided credentials do not match the records.'
            ]);
        }
    }

    // Logout
    public function logout(Request $request){
        // Logout the user
        Auth::logout();

        // Invalidate user's session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to home page
        return redirect('/');
    }

}
