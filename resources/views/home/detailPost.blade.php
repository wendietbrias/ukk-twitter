@extends("layouts.layout")

@section("links")

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

@endsection
 
@section('content')

<div class="w-full flex">
    @include("components.sidebar")
    <section class=" h-screen w-[80%]  overflow-y-scroll flex justify-center items-center flex-col ">
        <div id="ex1" class="modal">
            <h4 class="text-center font-semibold text-lg">Update comment</h4>
            <form id="form-update-comment" action="{{ route('comment.update') }}" class="mt-5">
                <input type="hidden" name="id_update" id="id_update">
                <input class="w-full outline-none py-2 px-3 rounded-md border border-gray-400" type="text" name="comment" id="comment">
                <input type="file" name='image_update' class="mt-3" id='image_update' enctype="multipart/formdata">
                <button class="w-full mt-5 bg-blue-400 text-white text-md font-medium rounded-md py-2">Update</button>
            </form>
          </div>
        <input type='hidden' name='id_user' value="{{ Auth::user()->id }}" id='id_user'>
        <input type='hidden' name='tweet_id' value="{{ $data->id }}"id='tweet_id'>
        <div class="w-[600px]">
            <img class="w-full rounded-md h-[300px]" src="{{ asset("storage/tweet_image/" . $data->media ) }}" alt="{{ $data->tweet }}">
            <h4 class="font-bold text-lg mt-5">{{ $data->user->display_name }} Tweets</h4>
            <p class="text-sm text-gray-500">{{ date_format(date_create($data->created_at) , "Y M D") }}</p>

               <div class="mt-2">
                <p class="text-md  font-normal">{{ $data->tweet }}</p>
            </div>
            <form id="comment-form" method="POST" action="{{ route('comment.create') }}" class="mt-3 border-t pt-5 flex items-center gap-x-3">
                @csrf 
                <input type="hidden" name="tweet_id" value="{{ $data->id }}">
                <input type="text" placeholder="your comments" name="comment" id='comment-post' class="outline-none flex-1 border border-gray-300 py-1 px-3 rounded-md">
                <input type="file" style="display:none;" name="image" id="image" enctype="multipart/formdata">
                <label for="image">
                    <span class="cursor-pointer bg-blue-400 text-white text-md  py-1 px-3 rounded-md">
                        <i class="ri-camera-line"></i>
                    </span>
                </label>
                <button type="submit" class="bg-blue-400 py-1 text-sm px-4 text-white font-medium rounded-md">Posts</button>
            </form>
            <div class=" border-gray-300 mt-5">
                <h5 id="count_comment">{{ count($data->comments) }} Comments</h5>
                <div id="comment-container" class="flex py-5 flex-col gap-y-4 h-[200px] overflow-y-scroll">
                 
                </div>
            </div>
        </div>
    </section>
</div>

@endsection 

@section('js')

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

     function showComments() {
        $.ajax({
        type:'GET',
        url:'http://127.0.0.1:8000/comment/all/' + $('#tweet_id').val(),
        success:function(response){
             //mengecek field comments dari object relasi tweet
              console.log(response);
             
             if(response.comments){
                 $('#count_comment').html(`${response.comments.length} Comments`);
                let temp = '';
                response.comments.map((data,idx) => {
                    temp += `
                    <div class="flex items-center">
                        <div class="flex items-start">
                            <img class="w-[40px] h-[40px] mt-2 rounded-full" src="http://127.0.0.1:8000/storage/profil_user/${data.user.media}">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center gap-x-3">
                                    <h5 class="font-semibold">${data.user.display_name}</h5>
                                    <p class="text-gray-500 text-sm">${data.tag.split(" ").map((item)=>`#${item}`).join(" ")}</p>
                                     ${$('#id_user').val() == data.user_id ?  
                                     `
                                       <div class="flex items-center ml-3">
                                        <button data-id="${data.id}" class="delete-btn ml-3 text-red-400 mr-auto"><i class="ri-delete-bin-7-line text-md"></i></button>
                                        <button data-comment="${data.id},${data.comment} , ${data.tag}" class="edit-btn ml-3 text-blue-400 mr-auto"><i class="ri-edit-line"></i></button>

                                        </div>
                                     ` : ''}
                                  
                                </div>
                                <p class="text-sm">${data.comment}</p>
                                ${data.media != null && data.media != '' ? `
                                     <img src="http://127.0.0.1:8000/storage/comment_image/${data.media}" class="w-full h-[100px] rounded-md mt-2">
                                    ` : ''} 
                            </div>
                        </div>
                     </div>
                    `;
                });

                $('#comment-container').html(temp);
            }
        }
    })
     }
     
     showComments();

     //delete comment
     $(document).on('click' , '.delete-btn' , function() {
            $.ajax({
                type:'POST',
                url:'http://127.0.0.1:8000/comment/delete/' + $(this).attr('data-id'),
                success:function(response){
                    showComments();
                }
            })
     });

     //tampilkan modal untuk update comment
     $(document).on('click' , '.edit-btn' , function() {
         $('#ex1').modal({
             open:true, 
             fadeDuration:500
         });

         //memisahkan isi array
          const [id,comment,tag] = $(this).attr('data-comment').split(",");

          //membuat format tag dengan hashtag
          const tagJoin = tag.split(" ").join(" #");

          $('#id_update').val(id);
         $('#comment').val(`${comment} ${tagJoin}`);
     })

     $(document).on('submit' , '#form-update-comment' , function(e) {
          e.preventDefault();

          $.ajax({
             type:'POST',
             url:$(this).attr('action'),
             cache:false,
             data:new FormData(this),
             processData:false,
             contentType:false ,
             beforeSend:function(){
                $('#ex1').html('<h4 class="text-center font-semibold text-xl">Loading...</h4>');
                $('#exl').modal({
                     open:true ,
                     fadeDuration:500
                })

             },
             success:function(response){
                showComments();
                $('#ex1').modal({
                    open:false 
                });
                $('#ex1').html(`
                <h4 class="text-center font-semibold text-lg">Update comment</h4>
            <form id="form-update-comment" action="{{ route('comment.update' , $data->id) }}" class="mt-5">
                <input class="w-full outline-none py-2 px-3 rounded-md border border-gray-400" type="text" name="comment" id="comment">
                <button class="w-full mt-5 bg-blue-400 text-white text-md font-medium rounded-md py-2">Update</button>
            </form>
                `)
                $('#comment').val('');
             },
          })
     })
      
     //submit dan membuat comment
       $(document).on('submit' , '#comment-form' , function(e){
        e.preventDefault();

           $.ajax({
              type:"POST",
              url:$(this).attr('action'),
              data:new FormData(this),
              cache:false,
              processData:false,
              contentType:false ,
              beforeSend:function(){
              },
              success:function(response){
                  showComments();
                  $('#comment-post').val('');
              }
           });
       });

  });

</script>

@endsection