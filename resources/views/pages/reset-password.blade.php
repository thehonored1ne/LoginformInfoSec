@extends('layouts.login-form')

@section('content')

<div class="flex h-screen w-full font-['Inter'] font-bold">

    <div class="w-full md:w-1/2 flex items-center bg-white justify-center">

        <div class="p-8 w-full max-w-md">

            <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 rounded-xl bg-white shadow-lg border-2 border-gray-200">

                <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                    <img src="https://images.vexels.com/media/users/3/137578/isolated/preview/c895a61e637f53ac91d5faf634c84794-cube-logo-geometric-polygonal.png" alt="Your Company" class="mx-auto h-15 w-auto" />
                    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Reset your password</h2>
                </div>

                <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

                    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div>
                            <label for="email" class="block text-sm/6 font-bold text-gray-900">Email address</label>
                            <div class="mt-2">
                                <input id="email" type="email" placeholder="Enter your email" value="{{ old('email', $email) }}" name="email" required autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-2 ring-gray-400 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 outline-none sm:text-sm/6" />
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm/6 font-bold text-gray-900">New Password</label>
                            <div class="mt-2">
                                <input id="password" type="password" name="password" placeholder="Enter new password" required autocomplete="new-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-2 ring-gray-400 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 outline-none sm:text-sm/6" />
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm/6 font-bold text-gray-900">Confirm Password</label>
                            <div class="mt-2">
                                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm new password" required autocomplete="new-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 ring-2 ring-gray-400 placeholder:text-gray-300 focus:ring-2 focus:ring-indigo-600 outline-none sm:text-sm/6" />
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="mt-4 p-3 border border-red-500 rounded-md bg-red-50 w-full text-center">
                                <ul class="text-xs font-bold text-red-500 uppercase list-none">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <button type="submit" class="flex w-full justify-center rounded-md bg-black px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-400 cursor-pointer">Reset Password</button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

    <div class="hidden md:flex md:w-1/2 bg-white items-center justify-center bg-contain bg-center" style="background-image: url('{{ asset('assets/bgs/bg5.png') }}'); background-size: 80%; background-repeat: no-repeat;">
    </div>

</div>

@endsection
