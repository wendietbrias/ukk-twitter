@extends("layouts.layout")

@section("links")

@endsection

@section("content")

 <div class="w-full h-screen flex justify-center items-center bg-[#EDEDED]">
    <div class="bg-white py-5 px-5 rounded-md w-[35%]">
        @if($errors->any() && str_contains($errors->first() , "Authentication")) 

        <div class="w-full mb-3 bg-red-50 py-2 px-3 rounded-md">
            <p class="text-red-400 font-semibold text-sm">
                {{ $errors->first() }}
            </p>
        </div>

        @endif
        <h4 class="text-center font-bold text-2xl">Login Twitter</h4>
        <form class="mt-5" action="{{ route("auth.login") }}" method="POST">
            @csrf 
            <input value="{{ old('email') }}" name="email" placeholder="Email" type="email" class=" w-full py-1 px-2 outline-none rounded-md border border-gray-300">
            @error('email')
             <p class="text-red-400 font-medium text-sm my-2">{{$message}}</p>
            @enderror
            <input name="password" placeholder="Password" type="password" class="mt-2 w-full py-1 px-2 outline-none rounded-md border border-gray-300">
            @error('password')
            <p class="text-red-400 font-medium text-sm my-2">{{$message}}</p>
           @enderror
            <button class="w-full mt-5 bg-blue-400 text-white text-center font-medium text-sm capitalize rounded-full py-2 ">sign in</button>
        </form>
        <p class="text-center mt-3 font-normal text-gray-500 text-sm">
            Don't have account? <a href="{{ route("auth.register.view") }}" class="text-blue-400">Register</a>
        </p>
    </div>
 </div>

@endsection

@section("js")

@endsection