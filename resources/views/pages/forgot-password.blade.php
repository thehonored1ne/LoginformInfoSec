@extends('layouts.login-form')

@section('content')

<div class="flex h-screen w-full font-['Inter'] font-bold">

    <div class="w-full md:w-1/2 flex items-center bg-white justify-center">

        <div class="p-8 w-full max-w-md">

            <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 rounded-xl bg-white shadow-lg border-2 border-gray-200">

                <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                    <img src="https://images.vexels.com/media/users/3/137578/isolated/preview/c895a61e637f53ac91d5faf634c84794-cube-logo-geometric-polygonal.png" alt="Your Company" class="mx-auto h-15 w-auto" />
                    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Forgot your password?</h2>
                    <p class="mt-2 text-center text-sm text-gray-600">No problem. Just let us know your email address and we will email you a password reset link.</p>
                </div>

                <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

                    @if(session('status'))
                        <div class="mb-4 p-3 border border-green-500 rounded-md bg-green-50 w-full text-center">
                            <p class="text-xs font-bold text-green-500 uppercase">
                                {{ session('status') }}
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST" class="space-y-8">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm/6 font-bold text-gray-900">Email address</label>
                            <div class="mt-2">
                                <input id="email" type="email" placeholder="Enter your email" value="{{ old('email') }}" name="email" required autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-2 ring-gray-400 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 outline-none sm:text-sm/6" />
                            </div>
                        </div>

                        @if($errors->has('email'))
                            <div class="mt-4 p-3 border border-red-500 rounded-md bg-red-50 w-full text-center">
                                <p id="email-error-text" class="text-xs font-bold text-red-500 uppercase">
                                    {{ $errors->first('email') }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <button id="submitBtn" type="submit" class="flex w-full justify-center rounded-md bg-black px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-400 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Email Password Reset Link</button>
                        </div>
                    </form>

                    <p class="mt-10 text-center text-sm/6 text-gray-500">
                        <a href="{{ route('login') }}" class="font-semibold text-black hover:text-indigo-600">Back to Login</a>
                    </p>
                </div>

            </div>

        </div>
    </div>

    <div class="hidden md:flex md:w-1/2 bg-white items-center justify-center bg-contain bg-center" style="background-image: url('{{ asset('assets/bgs/bg5.png') }}'); background-size: 80%; background-repeat: no-repeat;">
    </div>

</div>

@if($errors->has('email') && str_contains($errors->first('email'), 'Too many reset attempts'))
    @php
        preg_match('/in (\d+) seconds/', $errors->first('email'), $matches);
        $lockoutSeconds = $matches[1] ?? 0;
    @endphp
    @if($lockoutSeconds > 0)
    <script>
        localStorage.setItem('resetLockoutUntil', Date.now() + ({{ $lockoutSeconds }} * 1000));
        localStorage.setItem('resetLockoutEmail', '{!! addslashes(old('email')) !!}');
    </script>
    @endif
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lockoutUntil = localStorage.getItem('resetLockoutUntil');
        const lockoutEmail = localStorage.getItem('resetLockoutEmail');
        
        if (lockoutUntil) {
            let remainingMs = lockoutUntil - Date.now();
            let seconds = Math.ceil(remainingMs / 1000);
            
            if (seconds > 0) {
                const btn = document.getElementById('submitBtn');
                const errorText = document.getElementById('email-error-text');
                const emailInput = document.getElementById('email');
                
                if (emailInput && !emailInput.value && lockoutEmail) {
                    emailInput.value = lockoutEmail;
                }
                
                btn.disabled = true;
                
                const countdown = setInterval(() => {
                    seconds--;
                    btn.innerHTML = `Please wait ${seconds}s`;
                    if (errorText && errorText.parentElement.style.display !== 'none') {
                        errorText.innerText = `Too many reset attempts. Please try again in ${seconds} seconds.`;
                    }
                    
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        localStorage.removeItem('resetLockoutUntil');
                        localStorage.removeItem('resetLockoutEmail');
                        btn.disabled = false;
                        btn.innerHTML = 'Email Password Reset Link';
                        if (errorText) errorText.parentElement.style.display = 'none';
                    }
                }, 1000);
                
                btn.innerHTML = `Please wait ${seconds}s`;
            } else {
                localStorage.removeItem('resetLockoutUntil');
                localStorage.removeItem('resetLockoutEmail');
            }
        }
    });
</script>


@endsection
