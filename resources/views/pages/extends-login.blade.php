{{-- extends login form --}}

@extends('layouts.login-form')

@section('content')
{{-- loginform goes here... --}}

<div class="flex h-screen w-full font-['Inter'] font-bold">

    <div class="w-full md:w-1/2 flex items-center bg-white justify-center">

        <div class="p-8 w-full max-w-md">

            <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 rounded-xl bg-white shadow-lg border-2 border-gray-200">

                <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                    <img src="https://images.vexels.com/media/users/3/137578/isolated/preview/c895a61e637f53ac91d5faf634c84794-cube-logo-geometric-polygonal.png" alt="Your Company" class="mx-auto h-15 w-auto" />
                    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
                </div>

                <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

                    {{-- // This form submits the login data to the route named 'login.post' using the POST method. --}}
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-8">

                        {{-- // In Laravel, csrf is a Blade directive used to protect our application from Cross-Site Request Forgery csrf attacks. --}}
                        @csrf

                        {{-- // Input field for email.--}}
                        <div>

                            <label for="email" class="block text-sm/6 font-bold text-gray-900">Email address</label>

                            <div class="mt-2">
                                <input id="email" type="email" placeholder="Enter your email" value="{{ old('email') }}" name="email" required autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-2 ring-gray-400 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 outline-none sm:text-sm/6" />
                            </div>

                        </div>

                        {{-- // Input field for confirming the password. --}}
                        <div>

                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-sm/6 font-bold text-gray-900">Password</label>
                            </div>

                            <div class="mt-2">
                                <input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-2 ring-gray-400 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 outline-none sm:text-sm/6" />
                            </div>

                        </div>

                        {{-- //This Show Validation Error for email and password. --}}
                        @if($errors->has('email') || $errors->has('password'))

                            <div class="mt-4 p-3 border border-red-500 rounded-md bg-red-50 w-full text-center">
                                <p id="error-message-text" class="text-xs font-bold text-red-500 uppercase">
                                    {{ $errors->first('email') ?: $errors->first('password') }}
                                </p>
                            </div>
                            
                        @endif

                        {{-- // Sign up button --}}
                        <div>
                            <button id="submitBtn" type="submit" class="flex w-full justify-center rounded-md bg-black px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-400 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Sign in</button>
                        </div>

                    </form>

                    {{-- // Sign up button that directs to login page when accessed. --}}
                    <p class="mt-10 text-center text-sm/6 text-gray-500">Don't have an account?<a href="{{ route('register') }}" class="font-semibold text-black hover:text-indigo-600">Sign Up</a>
                    </p>
                </div>

            </div>

        </div>
    </div>

    {{-- // This second container holds an image. it is hidden in mobile mode. --}}
    <div class="hidden md:flex md:w-1/2 bg-white items-center justify-center bg-contain bg-center" style="background-image: url('{{ asset('assets/bgs/bg5.png') }}'); background-size: 80%; background-repeat: no-repeat;">
    </div>

</div>

@if($errors->has('email') && str_contains($errors->first('email'), 'Too many login attempts'))
    @php
        // Extract the exact countdown seconds from the validation error output.
        preg_match('/in (\d+) seconds/', $errors->first('email'), $matches);
        $lockoutSeconds = $matches[1] ?? 0;
    @endphp
    @if($lockoutSeconds > 0)
    <script>
        // Store the exact future timestamp when the lockout expires and their email
        localStorage.setItem('loginLockoutUntil', Date.now() + ({{ $lockoutSeconds }} * 1000));
        localStorage.setItem('loginLockoutEmail', '{!! addslashes(old('email')) !!}');
    </script>
    @endif
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lockoutUntil = localStorage.getItem('loginLockoutUntil');
        const lockoutEmail = localStorage.getItem('loginLockoutEmail');
        
        if (lockoutUntil) {
            let remainingMs = lockoutUntil - Date.now();
            let seconds = Math.ceil(remainingMs / 1000);
            
            if (seconds > 0) {
                const btn = document.getElementById('submitBtn');
                const errorText = document.getElementById('error-message-text');
                const emailInput = document.getElementById('email');
                
                // Repopulate the email field if they navigated away and it's empty
                if (emailInput && !emailInput.value && lockoutEmail) {
                    emailInput.value = lockoutEmail;
                }
                
                btn.disabled = true;
                
                const countdown = setInterval(() => {
                    seconds--;
                    btn.innerHTML = `Please wait ${seconds}s`;
                    if (errorText && errorText.parentElement.style.display !== 'none') {
                        errorText.innerHTML = `Too many login attempts. Please try again in ${seconds} seconds.`;
                    }
                    
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        localStorage.removeItem('loginLockoutUntil');
                        localStorage.removeItem('loginLockoutEmail');
                        btn.disabled = false;
                        btn.innerHTML = 'Sign in';
                        if (errorText) errorText.parentElement.style.display = 'none';
                    }
                }, 1000);
                
                btn.innerHTML = `Please wait ${seconds}s`;
            } else {
                localStorage.removeItem('loginLockoutUntil');
                localStorage.removeItem('loginLockoutEmail');
            }
        }
    });
</script>
@endsection