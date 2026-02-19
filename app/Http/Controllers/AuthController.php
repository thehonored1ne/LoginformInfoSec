<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // Importing the base Controller class to extend it for our AuthController.
use Illuminate\Http\Request; // Importing the Request class to handle incoming HTTP requests and access form data.
use Illuminate\Support\Facades\Auth; // Importing the Auth facade to manage authentication and sessions.
use App\Models\UserModel as User; // Importing the User model to interact with the users table in the database.

class AuthController extends Controller
{
    // This function returns the login view when the user visits the root URL. It is named 'login' for easy reference in routes and redirects.
    public function showLogin()
    {
        return view('pages.extends-login');
    }

    // This function returns the registration view when the user visits the '/register' URL. It is named 'register' for easy reference in routes and redirects.
    public function showRegister()
    {
        return view('pages.extends-register');
    }

    // this function returns the home screen view when the user visits the '/home' URL. It is protected by the 'auth' middleware, which means only authenticated users can access it.
    public function showHomeScreen()
    {
        return view('pages.extends-home');
    }


    // --- Logic Methods goes here... ---


    // This function handles the authentication logic when a user submits the login form. It performs several steps to ensure secure authentication, including normalizing input, validating credentials, and managing sessions.
    public function authenticate(Request $request)
    {
        // Normalize: Removes spaces and forces lowercase so login isn't case-sensitive.
        $email = strtolower(trim($request->email));
        $password = $request->password;

        // Step 1: Search for the user in the database
        $user = User::where('email', $email)->first();

        // Step 2: If email doesn't exist, return a clear error.
        if (!$user) {
            return back()->withErrors([
                'email' => 'This email address is not registered.',
            ])->withInput();
        }

        // Step 3: Re-create the hash using the password + the salt found in the DB.
        $hashedInput = hash('sha256', $password . $user->salt);

        // Step 4: Compare the generated hash with the one in the database
        if ($hashedInput !== $user->password) {
            return back()->withErrors([
                'password' => 'The password you entered is incorrect.',
            ])->withInput();
        }

        // Step 5: Start the session and regenerate for security
        Auth::login($user);
        $request->session()->regenerate();

        // Step 6: Redirect to intended page or homescreen
        return redirect()->intended('home');
    }

    /**
     * Handle Registration Logic with Manual Salting
     */

    // This function handles the registration logic when a user submits the registration form. It validates the input, generates a unique salt, hashes the password with the salt, and saves the new user to the database.
    public function storeRegister(Request $request)
    {
        // Step 1: Validate input fields
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Normalize: Removes spaces and forces lowercase so login isn't case-sensitive.
        $email = strtolower(trim($request->email));
        $password = $request->password;
        
        // Step 2: Generate a unique 16-character salt for this user.
        $salt = bin2hex(random_bytes(16)); 
        
        // Step 3: Combine password + salt and hash it using SHA-256 algorithm.
        $manualHash = hash('sha256', $password . $salt);

        // Step 4: Save to the database
        User::create([
            'name' => explode('@', $email)[0], // Fills name using the email prefix
            'email' => $email,
            'password' => $manualHash, // full pasword with salt and has been hashed.
            'salt' => $salt, // save the salt in its own column for later use during login.
        ]);

        // Step 5: Redirect to login page with success message.
        return redirect()->route('login')->with('success', 'Registration successful!');
    }

    /**
     * Handle Logout and Session Destruction
     */

    // This function handles the logout process. It logs the user out, invalidates the session to prevent reuse, regenerates the CSRF token for security, and redirects the user back to the login page.
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Security: Invalidate the session so it can't be reused.
        $request->session()->invalidate();
        
        // Security: Reset CSRF token.
        $request->session()->regenerateToken();

        // Redirect to login page after logout.
        return redirect('/');
    }
}