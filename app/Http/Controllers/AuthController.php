<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// ! laravel jetstream is for more than one authentications
class AuthController extends Controller
{
    function register()
    {
        return view('auth.register');
    }

    function store()
    {
        $validated = request()->validate([
            'name' => 'required',
            // ! users is the table and email is the row:
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('dashboard')->with('success', 'User registered successfully.');
    }

    function login()
    {
        return view('auth.login');
    }

    function authenticate()
    {
        /*
            todo: this is to see if the parameters that we send to route is working or not:
            dd(request()->all());
        */

        $validated = request()->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // ! auth() => is a helper of laravel to check if the data that is inserted is real or not (the way it work is that check the data if it is true log in the user and give true, if not will give false):
        if (auth()->attempt($validated)) {
            // ! this will help in past loged in:
            request()->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Loged-in successfully.');
        } else {
            return redirect()->route('login')->with('failed', 'Login failed');

            /*
                ? another way to give errors:
                return redirect()->route('login')->withErrors([
                    'email' => 'Email or password is not valid.'
                ]);
            */
        }
    }

    function logout()
    {
        // ! to do the logout process we need to clear cookies and sessions:
        auth()->logout();

        request()->session()->regenerateToken();

        return redirect()->route('dashboard')->with('success', 'Logged out successfully.');
    }
}