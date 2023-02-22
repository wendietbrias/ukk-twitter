<section class="sidebar w-[20%] h-screen border-r border-gray-200 py-5 px-7 bg-white flex flex-col justify-between">
 <div class="main">
    <a href="{{ route("home.view") }}" class="no-underline">
        <div class="flex items-center gap-x-3">
            <i class="ri-twitter-fill text-blue-400 text-3xl"></i>
            <p class="font-bold text-xl">Twitter</p>
        </div>
   
    </a>
    <ul class="mt-10 flex flex-col gap-y-5">
       <a  href="{{ route("home.view") }}" class="flex items-center @if(Request::path() == "/") opacity-50 @endif">
           <i class="ri-home-line text-2xl"></i>
           <p  class="text-md font-semibold ml-4">Home</p>
       </a>
       <a href="{{ route("home.createTweet.view") }}" class="flex items-center @if(Request::path() == "tweet") opacity-50 @endif">
           <i class="ri-add-box-line text-2xl"></i>
           <p class="text-md font-semibold ml-4">Create Tweets</p>
       </a>
       <a href="{{ route("home.profile.view") }}" class="flex items-center @if(Request::path() == "profile") opacity-50 @endif">
           <i class="ri-user-line text-2xl "></i>
           <p  class="text-md font-semibold ml-4">Profile</p>
       </a>
    </ul>
 </div>
 <form action="{{ route("auth.logout") }}" method="POST">
    @csrf
    <button class="flex items-center">
        <i class="ri-logout-box-r-line text-2xl"></i>
        <p class="font-semibold text-md ml-4">Logout</p>
    </button>
 </form>
</section>