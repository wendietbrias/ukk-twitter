@extends("layouts.layout")

@section("links")

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />


@endsection

@section("content")

<div class="w-full h-screen flex">
    @include("components.sidebar")
    <section class="w-[80%] profile-container py-5 px-10 h-screen overflow-y-scroll">
        <!-- Modal untuk update data user -->
        <div id="ex1" class="modal">
            <h4 class="text-center font-bold text-xl">Update Profile</h4>
            <form id="form-update-profile" action="{{ route('profile.update') }}" method="POST" class="mt-5 flex flex-col gap-y-2">
                @csrf
                <input type="text" name='username'  value="{{ Auth::user()->username }}" class="w-full border border-gray-200 py-2 px-3 rounded-md outline-none">
                <input type="text" name='display_name'  value="{{ Auth::user()->display_name }}" class="w-full border border-gray-200 py-2 px-3 rounded-md outline-none">
                <input type="email" name='email'  value="{{ Auth::user()->email }}" class="w-full border border-gray-200 py-2 px-3 rounded-md outline-none">
                <textarea  name="bio" class="w-full h-[80px] outline-none border border-gray-200 rounded-md">
                  {{ Auth::user()->bio }}
                </textarea>
                <button type="submit" class="w-full bg-blue-400 text-white font-semiobld mt-7 rounded-md text-sm py-2">Update</button>
            </form>
          </div>
        <!-- Modal untuk update data user -->

        <!-- Modal untuk update tweet user -->

        <div id="ex2" class="modal">
          <h4 class="text-center font-bold text-xl">Update Tweet</h4>
          <form id="form-update-tweet"  method="POST" class="mt-5 flex flex-col gap-y-2">
              @csrf
              <input type="hidden" id="id" name="id">
              <input type="text" max="250" maxlength="250" name='tweet'  id="tweet" class="w-full border py-3 border-gray-200  px-3 rounded-md outline-none">
              <input type="file" name="image_tweet" id="image_tweet" style="display:none"/>
              <label class="w-full mt-3" for="image_tweet">
                <span class="bg-gray-100 cursor-pointer py-2 px-5 mt-3 rounded-full text-gray-600 text-center font-semibold text-sm">
                    <i class="ri-image-edit-line"></i>
                    Upload image
                </span>
            </label>              <button  class="w-full bg-blue-400 text-white font-semiobld mt-7 rounded-md text-sm py-2">Update</button>
          </form>
        </div>

        <!-- Modal untuk update tweet user -->

        <div class="w-full border-b border-gray-300 pb-2">
          <div class="flex items-center">
            <input style="display:none;" id="image" name="image" type="file">
            <label for="image" id="image-preview" class="w-[120px] h-[120px] rounded-full">
                @if(Auth::user()->media == null)
                  <span class="w-full h-full rounded-full text-white bg-blue-400 flex items-center justify-center uppercase text-2xl font-bold">
                    {{ substr(Auth::user()->username  , 0 ,1) }}
                  </span> 
                  @else 
                  <img src="{{ asset("storage/profil_user/" .  Auth::user()->media) }}" class="w-full h-full rounded-full">
                @endif
            </label>
             <section class="ml-7 mt-5">
                <div class="flex items-center gap-x-5">
                    <h5 id='username' class="text-3xl font-semibold">{{ Auth::user()->username }}</h5>
                    <a href="#ex1" rel="modal:open">
                        <i class="ri-edit-2-line text-xl"></i>
                    </a>
                </div>
                 <h5 id='display_name' class="text-sm mt-1 font-medium">{{ Auth::user()->display_name }}</h5>
                 <p id="bio" class="mt-3">{{ Auth::user()->bio ? Auth::user()->bio : "No bio" }}</p>
             </section>
          </div>
          <div class="flex justify-center items-center mt-7 gap-x-5">
             <button class="text-blue-400 font-medium">Tweets</button>
             <button class="font-medium">Saved tweets</button>
             <button class="font-medium">Liked tweets</button>
          </div>
        </div>
        <div class="mt-7 w-[65%] mx-auto" id="tweets"></div>
    </section>
</div>

@endsection

@section("js")

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<script>

    //menginisiasi csrf token pada ajax
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

  $(document).ready(function() {

    function showPosts(){
      return $.ajax({
       type:'GET',
       url:'http://127.0.0.1:8000/profile/tweet',
       beforeSend:function(){

       },
       success:function(response){

          let temp = '';

          //menampilkan data di frontend
          response.map((tweet, idx) => {
              temp += `
              <div class="w-full mt-5">
            <div class="flex items-center ">
                <img class="w-[50px] h-[50px] rounded-full" src="http://127.0.0.1:8000/storage/profil_user/${tweet.user.media}">
                <div class="flex-1 ml-4 flex justify-between items-center">
                  <div>
                    <h4 class="font-semibold text-lg">${tweet.user.username}</h4>
                    <p class="text-gray-500 mt-0">${tweet.user.display_name}</p>
                </div>
                 <div class="flex gap-x-3 items-center">
                  <button data-id="${tweet.id}" class="delete-btn text-red-400"><i class="ri-delete-bin-7-line text-md"></i></button>
                  <button data-user="${tweet.id}, ${tweet.tweet}, ${tweet.tag}" class="show-update-modal-btn text-blue-400"><i class="ri-edit-line text-md"></i></button>
                  </div>
                  </div>
            </div>
            <p class="mt-4">${tweet.tweet} <a href="javascript:void(0)">
                <span class="text-blue-400 font-medium ml-3">${
                    tweet.tag.split(" ").map((data,idx) => {
                        return `#${data}`
                    }).join(" ")
                }</span></a></p>
            <img src="http://127.0.0.1:8000/storage/tweet_image/${tweet.media}" class="w-full h-[350px] rounded-md mt-3">
            <div class="flex items-center gap-x-7 mt-5">
             <div class="flex items-center text-[#545454]">
                <i class="ri-heart-3-line text-2xl"></i>
                <p class="ml-4">${tweet.likes.length}</p>
             </div>
             <div class="flex items-center text-[#545454]">
                <i class="ri-chat-3-line text-2xl"></i>
                <p class="ml-4">${tweet.comments.length}</p>
             </div>
             <div class="flex items-center text-[#545454]">
                <i class="ri-share-forward-box-line text-2xl"></i>
                <p class="ml-4">21</p>
             </div>
            </div>
        </div>
              `;
          });


          $('#tweets').html(temp);
       }
    });

    }

    showPosts();

    $(document).on('click' , '.delete-btn' , function() {
        $.ajax({
            type:'DELETE',
            url:'http://127.0.0.1:8000/profile/tweet/delete/' + $(this).attr('data-id'),
            cache:false,
            beforeSend:function(){
            
            },
            success:function(){
              showPosts();

              alert('success delete data');
            },
        })
    });

    $(document).on('click' , '.show-update-modal-btn' , function() {
 
      const [id,tweet,tag] = $(this).attr('data-user').split(",");

        $('#ex2').modal({
           open:true,
           fadeDuration:500,
        });

       //memformat tag dengan hashtag
        const tagJoin = tag.split(" ").join(" #");

        $('#id').val(id);
        $('#tweet').val(`${tweet} ${tagJoin}`);
    })

    $(document).on('submit' ,'#form-update-tweet' , function(e) {
      e.preventDefault();
 
      $.ajax({
         type:'POST',
         url:'http://127.0.0.1:8000/profile/tweet/update/' + $("#id").val(),
         cache:false,
         processData:false,
         contentType:false,
         data:new FormData(this),
         beforeSend:function(){},
         success:function(response){
           showPosts();

           $('#tweet').val('');

           $('#ex2').modal({
             open:false ,
             fadeDuration:0
           });
         },
      })
    });
    
       $(document).on('submit' , '#form-update-profile' , function(e) {
            e.preventDefault();

           //kirim ke controller

            $.ajax({
                type:"POST",
                url:'http://127.0.0.1:8000/profile/update',
                data:new FormData(this),
                processData:false,
                contentType:false,
                cache:false, 
                beforeSend:function(){
                  $('#ex1').html(`
                  <h4 class="text-center font-semibold text-xl">Loading...</h4>
                  `)
                },
                success:function(data) {
                    $('#bio').html(data.bio);
                    $('#username').html(data.username);
                    $('#display_name').html(data.display_name);

                    $('textarea[name="bio"]').val('');
                    $('input[name="display_name"]').val('');
                    $('input[name="username"]').val('');
                    $('input[name="email"]').val('');

                    $('#ex1').modal({
                      open:false,
                    });

                     $('#ex1').html(`
                     <h4 class="text-center font-semibold text-xl">Success update</h4>
                     `)
                }
            });
       });


       //update foto profil user
       $(document).on('change' , '#image' , function(e) {

        //image preview
           let file = this.files[0];
           let reader =  new FileReader();

           reader.onloadend = function() {
               //menampilkan perurbahan image secara realtime
               $('#image-preview').html(`
                  <img src="${reader.result}" class="w-full h-full rounded-full" alt="avatar">
               `)
           }

           reader.readAsDataURL(file);

           //kirim ke controller
           const formData = new FormData();

           formData.append('image' , file);

           $.ajax({
             type:'POST',
             url:'http://127.0.0.1:8000/profile/update/avatar',
             data:formData,
             cache:false,
             contentType:false,
             processData:false,
             success:function(response){
                return response;
             }

           })
       })

  });

</script>


@endsection