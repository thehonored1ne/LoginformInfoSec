{{-- extends login form --}}

@extends('layouts.login-form')

@section('content')
{{-- loginform goes here... --}}

<div class="flex h-screen w-full font-['Space_Grotesk'] font-bold">

    <div class="w-full md:w-1/2 flex items-center justify-center bg-gray-100 ">
        <div class="p-8 w-full max-w-md">


            <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 border-2 border-black rounded-2xl border-r-8 border-b-8 bg-white">
                <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                    <img src="https://images.vexels.com/media/users/3/137578/isolated/preview/c895a61e637f53ac91d5faf634c84794-cube-logo-geometric-polygonal.png" alt="Your Company" class="mx-auto h-15 w-auto" />
                    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
                </div>

                <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

                    {{-- // This form submits the login data to the route named 'login.post' using the POST method. --}}
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">

                        {{-- // In Laravel, csrf is a Blade directive used to protect our application from Cross-Site Request Forgery csrf attacks. --}}
                        @csrf

                        {{-- // Input field for email.--}}
                        <div>
                            <label for="email" class="block text-sm/6 font-bold text-gray-900">Email address</label>
                            <div class="mt-2">
                            <input id="email" type="email" value="{{ old('email') }}" name="email" required autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-2 -outline-offset-1 outline-black placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>

                        {{-- // Input field for confirming the password. --}}
                        <div>
                            <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm/6 font-bold text-gray-900">Password</label>
                            </div>
                            <div class="mt-2">
                            <input id="password" type="password" name="password" required autocomplete="current-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-2 -outline-offset-1 outline-black placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            </div>
                        </div>

                        {{-- //This Show Validation Error for email and password. --}}
                        @if($errors->has('email') || $errors->has('password'))
                            <div class="mt-4 p-3 border rounded border-red-700 bg-red-100 w-full text-center">
                                <p class="text-xs font-bold text-red-700 uppercase">
                                    {{ $errors->first('email') ?: $errors->first('password') }}
                                </p>
                            </div>
                        @endif

                        {{-- // Sign up button --}}
                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md bg-black px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-400 cursor-pointer">Sign in</button>
                        </div>

                    </form>

                    {{-- // Sign up button that directs to login page when accessed. --}}
                    <p class="mt-10 text-center text-sm/6 text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-black hover:text-indigo-600">Sign Up</a>
                    </p>
                </div>
            </div>



        </div>
    </div>

    {{-- // This second container holds an image. it is hidden in mobile mode. --}}
    <div class="hidden md:flex md:w-1/2 bg-gray-100 items-center justify-center bg-[url('https://media.newyorker.com/photos/67634c5e2d3906fa2b10d193/16:10/w_2560%2Cc_limit/r45067.jpg')] bg-cover bg-center">
    </div>

</div>

@endsection