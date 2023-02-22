@extends("layouts.layout")

@section("links")

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">


@endsection

@section("content")

<div class="w-full h-screen flex">
    <!-- Modal -->
    <div id="ex1" class="modal">
        <p>Thanks for clicking. That felt good.</p>
        <a href="#" rel="modal:close">Close</a>
      </div>
    <!-- Modal -->

    @include("components.sidebar")
    <section class="tweets-container w-[80%] h-screen flex items-center justify-center">
        <div class="w-full">
            
            <h4 class="text-center text-2xl font-bold">Create Tweets</h4>
            <form id="form-tweet" action="{{ route("tweet.create") }}" method="POST" class="mt-7 w-[40%] mx-auto flex flex-col gap-y-2">
                <input type="text" name="tweet" placeholder="Tweet" class="border border-gray-200 py-4 px-3 rounded-md outline-none">
                <input type="file" style="display:none;"  id="image" name="image" class="mt-3" enctype="multipart/formdata">
                <label class="w-full mt-3" for="image">
                    <span class="bg-gray-100 cursor-pointer py-2 px-5 mt-3 rounded-full text-gray-600 text-center font-semibold text-sm">
                        <i class="ri-image-edit-line"></i>
                        Upload image
                    </span>
                </label>
                <button class="bg-blue-400 py-2 mt-10 rounded-full text-white text-center font-semibold text-sm">Create Tweet</button>
            </form>
        </div>
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

//ajax

    $(document).ready(function() {

         $(document).on('submit', '#form-tweet' , function(e) {
              e.preventDefault();

              $.ajax({
                 type:'POST',
                 url:$(this).attr('action'),
                 data:new FormData(this),
                 cache:false,
                 contentType:false,
                 processData:false,
                 beforeSend:function(){
                    $('#ex1').modal({
                        open:true,
                        fadeDuration:500,
                        showClose:false 
                    });

                    $('#ex1').html(`
                       <h4 class="text-center font-bold text-xl uppercase">Loading..</h4>
                    `
                    )
                 },
                 success:function(response){
                     if(response.status == 200) {
                       return  window.location.href = "http://127.0.0.1:8000/";
                     }
                    
                     $('#ex1').modal({
                         fadeDuration:500,
                         showClose:true, 
                         open:true
                     });

                     $('#ex1').html(`
                     <h4 class="text-center font-bold text-xl uppercase">${response.message}</h4>

                     `)
                 }
              })
         });

    });
</script>

@endsection