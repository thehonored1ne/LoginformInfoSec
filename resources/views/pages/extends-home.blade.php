{{-- extends home screen --}}

@extends('layouts.home')

@section('content')
{{-- home screen goes here... --}}

<div class="grid place-items-center h-screen w-full font-['Space_Grotesk'] font-bold">

    <div class="flex justify-center flex-col align-items-center gap-10">

    <div>
        <video autoplay loop class="w-full border-4 border-black">
            <source src="{{ asset('assets/videos/rickroll2.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    
    </div>

        <div>
            {{-- // This form submits the logout data to the route named 'logout.post' using the POST method. --}}
            <form action="{{ route('logout') }}" method="POST">

                {{-- // In Laravel, csrf is a Blade directive used to protect our application from Cross-Site Request Forgery csrf attacks. --}}
                @csrf

                {{-- // Logout button --}}
                <button type="submit" 
                        class="cursor-pointer w-full px-4 py-2 bg-red-400 border-4 border-black font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all uppercase tracking-widest text-sm">
                    Log Out
                </button>
            </form>
        </div>

    </div>

</div>

@endsection