@extends('layouts.admin')

@section('title')
{{ __('dashboard.Products') }}
@endsection

@section('content')
<style>
   .countdown-div{
      margin-left: 28%;
      font-style: italic;
      font-size: 11px;
      margin-top: 5px;
      color: #A9A9A9;
   }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- croppie library links -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.css" integrity="sha512-2eMmukTZtvwlfQoG8ztapwAH5fXaQBzaMqdljLopRSA0i6YKM8kBAOrSSykxu9NN9HrtD45lIqfONLII2AFL/Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" integrity="sha512-zxBiDORGDEAYDdKLuYU9X/JaJo/DPzE42UubfBw9yg8Qvb2YRRIQ8v4KsGHOx2H1/+sdSXyXxLXv5r7tHc9ygg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="page-content">
   <div class="container-fluid">
      <!-- page title -->
      @include('admin.includes.page_title', ['supTitle2' => __('dashboard.Menu'), 'supTitle' => __('dashboard.Products')])

      <!-- Content -->
      <div class="row">
         <!-- sync data btn -->
         <div class="col-md-10">
            <button id="syncData" class="btn btn-success waves-effect waves-light mrB20">
               <i id="syncIcon" class="fas fa-sync-alt mr-1"></i>
               <span id="syncbtn">{{ __('dashboard.Sync_data_with_ERP') }}</span>
            </button>
         </div>
         <span id="edit_success" style="text-align: center; line-height: 0px;"></span>
         @if($updated)
            <div class="col-md-12">
               <div class="alert alert-danger" id="must_synchronize" style="width: max-content">{{ __('dashboard.must_synchronize') }}</div>
            </div>
         @endif

         <!-- products data -->
         <div class="col-lg-12">
            <div class="card">
               <div class="card-body">
                  <table id="records_table" class="table table-bordered dt-responsive nowrap text-center table-editable"
                     style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                     <thead>
                        <tr>
                           <th>{{ __("dashboard.Sort") }}</th>
                           <th width="10%" class="textCenter">{{ __('dashboard.Image') }}</th>
                           <th>ID</th>
                           <th>{{ __('dashboard.Code') }}</th>
                           <th>{{ __('dashboard.Name') }}</th>
                           <th>{{ __('dashboard.Description') }}</th>
                           <th>{{ __('dashboard.Name') }} visible</th>
                           <th>{{ __('dashboard.Description') }} visible</th>
                           <th>{{ __('dashboard.Price') }}</th>
                           <th>{{ __('dashboard.Active') }}</th>
                           <th>{{ __('dashboard.Action') }}</th>
                        </tr>
                     </thead>

                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal: Add | Edit -->
<div id="formModal" class="modal fade" role="dialog">
   <div class="modal-dialog" style="width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" align="center">{{ __('dashboard.Edit_Product') }}</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <span id="form_result"></span>
            <form method="post" id="sample_form" class="form-horizontal">
               <input type="hidden" name="hidden_id" id="hidden_id" />
               <input type="hidden" name="hidden_image" id="hidden_image" />
               <div class="form-group row mb-3">
                  <label for="name" class="col-sm-3 col-form-label">{{ __('dashboard.Name') }}</label>
                  <div class="col-sm-9">
                     <input type="text" name="name" class="form-control" id="name">
                  </div>
               </div>

               <div class="form-group row mb-3">
                  <label for="description" class="col-sm-3 col-form-label">{{ __('dashboard.Description') }}</label>
                  <div class="col-sm-9">
                     <input type="text" name="description" class="form-control" id="description" data-limit="90">
                  </div>
                  <div class="countdown-div"><span id="countdown" class="countdown">90 </span> letras</div>
               </div>

               <div class="form-group row mb-3">
                  <label class="col-sm-3 col-form-label">{{ __('dashboard.Image') }}</label>
                  <div class="col-sm-9">
                     <div class="custom-file" style="margin-bottom: 5px">
                        <span id="edit_image">add and preview image using editor</span>
                        <!-- <input name="image" type="file" class="custom-file-input" id="customFile" onchange="loadFile(event)">
                        <label class="custom-file-label" for="customFile">{{ __('dashboard.Upload_image') }}</label> -->
                     </div>
                     <span id="store_image"></span>
                     
                  </div>
               </div>

               <div class="form-group row mb-3">
                  <label class="col-sm-3 col-form-label">{{ __('dashboard.Active') }}</label>
                  <div class="col-sm-9">
                     <input name="active" type="checkbox" id="switch4" switch="success" checked />
                     <label for="switch4" data-on-label="{{ __("dashboard.Yes") }}" data-off-label="{{ __("dashboard.No") }}" style="margin-top: 10px"></label>
                  </div>
               </div>

               <div class="form-group row justify-content-end">
                  <div class="col-sm-9">
                     <input type="submit" name="action_button" id="action_button" class="btn btn-light" value="{{ __('dashboard.Save') }}"
                        style="padding:8px 40px;" />
                     <div class="spinner-grow text-secondary m-1" role="status" style="display: none">
                        <span class="sr-only">Loading...</span>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>


<!-- Modal: Edit Image -->
<div id="ImageformModal" class="modal fade" role="dialog">
   <div class="modal-dialog" style="width: 95%; max-width:95%;">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" align="center">{{ __('dashboard.Edit_Product') }}</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
         </div>
         <div class="modal-body">
            <span id="form_result"></span>
            <form method="post" id="sample_image_form" class="form-horizontal">
               <input type="hidden" name="product_hidden_id" id="product_hidden_id" />
               <input type="hidden" name="product_hidden_image" id="image_file" />
               <div class="form-group col mb-3">
                  <label for="description" class="col-sm-3 col-form-label">{{ __('dashboard.Description') }}</label>
                  <div class="col-sm-9">
                     <img id="product_hidden_image" src="" width="100">
                  </div>
               </div>
               <!-- test -->
               <div class="form-group" style="display:flex; justify-content:space-between;">
                  <div class="col-md-4">
                     <div id="image-preview"></div>
                  </div>
                  <div class="col-md-4" style="padding:75px; border-right:1px solid #ddd;">
                     <p><label>Select Image</label></p>
                     <input type="file" name="upload_image" id="upload_image" />
                     <br />
                     <br />
                     <button class="btn btn-success crop_image">Crop & view Image</button>
                  </div>
                  <div class="col-md-4" style="padding:75px;background-color: #333">
                     <div id="uploaded_image" align="center"></div>
                  </div>
               </div>
               <!--  -->
               <div class="form-group row justify-content-end">
                  <div class="col-sm-9">
                     <input type="submit" name="action_button" id="action_button" class="btn btn-light" value="{{ __('dashboard.Save') }}"
                        style="padding:8px 40px;" />
                     <div class="spinner-grow text-secondary m-1" role="status" style="display: none">
                        <span class="sr-only">Loading...</span>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<!-- --- -->

<!-- Success Notification after Sync data with ERP -->
<div class="alert alert-success" id="success-alert-sync" style="display:none;">
   <i class="bx bx-check-double font-size-16 align-middle mr-1"></i>
   <span class="success_msg">Success Message</span> &nbsp;
</div>

<!-- Error Notification after Sync data with ERP -->
<div class="alert alert-danger" id="error-alert-sync" style="display:none;">
   <i class="bx bx-error font-size-16 align-middle mr-1"></i>
   <span class="error_msg">Error Message</span> &nbsp;
</div>
@endsection

@push('AJAX')
<!-- script for cropping image -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" integrity="sha512-Gs+PsXsGkmr+15rqObPJbenQ2wB3qYvTHuJO6YJzPe/dTLvhy0fmae2BcnaozxDo5iaF8emzmCZWbQ1XXiX2Ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.js" integrity="sha512-vUJTqeDCu0MKkOhuI83/MEX5HSNPW+Lw46BA775bAWIp1Zwgz3qggia/t2EnSGB9GoS2Ln6npDmbJTdNhHy1Yw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
   $(document).ready(function () {
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

      // sync data with ERP
      $('#syncData').click(function () {
         event.preventDefault();
         $.ajax({
            url: "{{ route('admin.syncData', $slug) }}",
            beforeSend: function () {
               $('#syncIcon').addClass('fa-spin');
               $('#syncbtn').text("{{ __('dashboard.Syncing') }}");
            },
            complete: function () {
               $('#syncIcon').removeClass('fa-spin');
               $('#syncbtn').text("{{ __('dashboard.Sync_data_with_ERP') }}");
            },
            method: "POST",
            dataType: "json",
            success: function (data) {
               if (data.errors) {
                  $('.error_msg').html(data.error);
                  $("#error-alert-sync").fadeIn(500);
                  setTimeout(function() { $("#error-alert-sync").fadeOut(1500); }, 1500);
               }
               if (data.success) {
                  $('#records_table').DataTable().ajax.reload();
                  $('.success_msg').html(data.success);
                  $("#success-alert-sync").fadeIn(500);
                  $("#must_synchronize").fadeOut(500);
                  setTimeout(function() { $("#success-alert-sync").fadeOut(1500); }, 1500);
               }
            }
         })
      });

      // Show tabel
      var table = $('#records_table').DataTable({
         processing: true,
         serverSide: true,
         retrieve: true,
         "order": [[ 0, "asc" ]],
         "lengthMenu": [
         [25, 50, 100, 200, -1],
         [25, 50, 100, 200, "All"]
         ],

         ajax: {
            url: "{{ route('admin.products.index', $slug) }}",
         },
         columns: [
            {
               data: 'order',
               orderable: false,
               searchable: false,
               render: function (data, type, full, meta) {
                  return `<i class="fa fa-sort"></i>`;
               },
            },
            {
               data: 'image',
               orderable: false,
               searchable: false,
            },
            {
               data: 'id',
               render: function (data, type, full, meta) {
                  return `<strong class='row_id'>${data}</strong>`;
               },
            },
            {data: 'code'},
            {data: 'name'},
            {data: 'description'},
            {data: 'name_peyz',
               render: function (data, type, full, meta) {
                  return `
                     <span class="tabledit-span">${data}</span>
                     <input class="tabledit-input form-control input-sm" type="text" name="name_${full.id}" value="${data}" style="display: none;" disabled="">
                  `;   
               },
            },
            {data: 'description_peyz',
               render: function (data, type, full, meta) {
                  return `
                     <span class="tabledit-span">${data}</span>
                     <input class="tabledit-input form-control input-sm" type="text" name="description_${full.id}" value="${data}" style="display: none;" disabled="">
                  `;
               },
            },
            {data: 'price',
               render: function (data, type, full, meta) {
                  return `${data}€`;
            }
            },

            {
               data: 'active',
               render: function (data, type, full, meta) {
                  let yes_val = "{{__("dashboard.Yes")}}";
                  let no_val = "{{__("dashboard.No")}}";
                  var checked = data == '{{ __("dashboard.active") }}' ? 'checked' : '';
                  return `<input name="active" type="checkbox" id="switch-${full['id']}" class="check-data" switch="success" ${checked} data-pid="${full['id']}"/> <label for="switch-${full['id']}" data-on-label="${yes_val}" data-off-label="${no_val}" style="margin-top: 10px"></label>`
               },
               orderable: false
            },
            {data: 'action', orderable: false, searchable: false}
         ],
         columnDefs: [{ 
            targets: '_all',
            "createdCell": function (td, data) {
               $(td).addClass('tabledit-view-mode');
            }
         }],
         initComplete:function (){
            $('[id^="switch-"]').on('change', function toggle(){
               var id = this.dataset.pid;
               $.ajax({
                  type: "POST",
                  url: "{{ route('admin.change_product_state' , $slug ) }}",
                  data: {id}, // serializes the form's elements.
                  success: function(data)
                  {
                     if(!$('#alert-box').is(':visible')) {
                        $('#alert-box').show();
                     }
                     $('#edit_success').html('<div class="alert alert-success"><i class="bx bx-check-double font-size-16 align-middle mr-1"></i>state updated!!' +
                        '</div>');
                     setTimeout(function (){
                        $('#edit_success').html('');
                     } , 2000);
                  }
               });
            });
         }
      });

      // Ediatable
      $('#records_table').Tabledit({
         url: "{{ route('admin.products.store', $slug) }}",
         editButton: false,
         saveButton:false,
         deleteButton: false,
         hideIdentifier: false,
         columns: {
            identifier: [2, 'id'],
            editable: [ [6, 'name'],[7, 'description']]
         },
         onSuccess: function(action, serialize){
            // $('#records_table').DataTable().ajax.reload();
         },
      }); 

      // Submit Form | Edit Product
      $('#sample_form').on('submit', function (event) {
         event.preventDefault();
         var id = $('#hidden_id').val();
         $.ajax({
            url: "{{ route('admin.products.store', $slug) }}",
            beforeSend: function () {
               $('#action_button').hide();
               $('.spinner-grow').show();
            },
            method: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
               var html = '';
               if (data.errors) {
                  html = '<div class="alert alert-danger">';
                  for (var count = 0; count < data.errors.length; count++) {
                        html += '<p><i class="bx bx-error font-size-16 align-middle mr-1"></i>' + data.errors[count] + '</p>';
                  }
                  html += '</div>';
               }
               else{
                  html = '<div class="alert alert-success"><i class="bx bx-check-double font-size-16 align-middle mr-1"></i>' + data.success +
                        '</div>';
                  // $('#sample_form')[0].reset();
                  $('#records_table').DataTable().ajax.reload();
                  setTimeout(function(){$('#formModal').modal('hide');}, 1000);
               }
               $('#form_result').html(html);
               $('#action_button').show();
               $('.spinner-grow').hide();
               $('#alert-box').show();
            }
         });
      });

      // Edit Button
      $(document).on('click', '.edit', function () {
         var id = $(this).attr('id');
         $('#form_result').html('');
         reset_form();

         $.ajax({
            url: "{{ route('admin.products.index', $slug) }}" + '/' + id + '/edit',
            dataType: "json",
            success: function (json) {
               $('#name').val(json.data.name_peyz);
               $('#description').val(json.data.description_peyz);
               $('#hidden_id').val(json.data.id);
               $('#product_hidden_id').val(json.data.id);
               if(json.data.image != null){
                  $('#store_image').html(`<img src="${json.data.image}" width='70' class='img-thumbnail' />`);
                  $('#hidden_image').val(json.originImage);
                  $('#product_hidden_image').attr('src',json.originImage);
               }
               else{
                  $('#store_image').html("");
                  $('#hidden_image').val("");
                  $('#product_hidden_image').attr('src',"");
               }
               if(json.data.active == "{{ __('dashboard.active') }}") $("#switch4").prop('checked', true);
               else $("#switch4").prop('checked', false);

               $('#formModal').modal('show');
               checkInput();
            }
         })
      });

      // upload image
      $(document).on('change', '.product_photo', function () {
         event.preventDefault();
         let pro_id = $(this).attr('data');
         var form = $(`.pro_form_${pro_id}`)[0];
         
         var url= "{{ route('admin.products.upload_img',[$slug, ':pro_id']) }}";
         url = url.replace(':pro_id', pro_id);

         $.ajax({
            url: url,
            method: "POST",
            data: new FormData(form),
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            success: function (data) {
               if(data.success){
                  $('#records_table').DataTable().ajax.reload();
               }
            }
         });
      });

      // delete image
      $(document).on('click', '.delete-img', function () {
         let del_btn = $(this);
         let pro_id = $(this).attr('data');

         var url= "{{ route('admin.products.delete_img',[$slug, ':pro_id']) }}";
         url = url.replace(':pro_id', pro_id);

         $.ajax({
            url: url,
            beforeSend: function () {
               del_btn.removeClass('fa-times');
               del_btn.addClass('fa-spinner fa-spin');
            },
            method: "POST",
            success: function (data) {
               $('#records_table').DataTable().ajax.reload();
               // del_btn.addClass('fa-times');
               // del_btn.removeClass('fa-spinner fa-spin');
            }
         });
      });

      // clear input file
      function reset_form(){
         $('#customFile').val(null);
      }
      // re-ordering rows
      $("#records_table tbody").sortable({
         items: "tr",
         cursor: 'move',
         opacity: 0.6,
         update: function() {
            sendOrderToServer();
         }
      });
   });

   // re-ordering rows
   function sendOrderToServer() {
      var order = [];
      var token = $('meta[name="csrf-token"]').attr('content');
      $('tr.odd, tr.even').each(function(index,element) {
         var row_id = $(element).find('.row_id').html();
         order.push({
            id: row_id,
            position: index+1
         });
      });

      $.ajax({
         type: "POST",
         dataType: "json",
         url: "{{ route('admin.order_products', $slug) }}",
         data: {
            order: order,
            _token: token
         }
      });
   }

   // preview image before upload it
   var loadFile = function(event) {
      $('#store_image').html(`<img src="${URL.createObjectURL(event.target.files[0])}" width='70' class='img-thumbnail' />`);
   };

   // Countdown for description input
   $(document).ready(function () {
      $('#description').on("load propertychange keyup input paste",
      function () {     
        var limit = $(this).data("limit");     
        var remainingChars = limit - $(this).val().length;      
        if (remainingChars <= 0) {
            $(this).val($(this).val().substring(0, limit));
        }
        $(".countdown").text(remainingChars<=0?0:remainingChars);
      });
  
   $('#description').trigger('load');
   });
   function checkInput(){
      var value = $('#description').val();
      var length = value.length;
      var limit = $('#description').data("limit");
      var remainingChars = limit - length;
      $(".countdown").text(remainingChars<=0?0:remainingChars);
   } 


   

   // ////
   $image_crop = $('#image-preview').croppie({
    enableExif:true,
    viewport:{
      width:375,
      height:439,
      type:'square'
    },
    boundary:{
      width:450,
      height:450
    }
  });

  // show edit image modal
  $('#edit_image').click(function(){
      $('#ImageformModal').modal('show');
      var src = $('#product_hidden_image').attr('src');
      console.log(src);
      
      // $image_crop.croppie('bind', {
      //       url:src
      //    }).then(function(){
      //       console.log('jQuery bind complete');
      //    });
     
   });

  $('#upload_image').change(function(){
    var reader = new FileReader();

    reader.onload = function(event){
      $image_crop.croppie('bind', {
        url:event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
    reader.readAsDataURL(this.files[0]);
  });

  $('.crop_image').click(function(event){
   event.preventDefault();
    $image_crop.croppie('result', {
      type:'canvas',
      size:'viewport'
    }).then(function(response){
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      var _token = $('input[name=_token]').val();
      $.ajax({
        url:'{{ route("admin.products.upload",[$slug]) }}',
        type:'post',
        data:{"image":response, _token:_token},
        dataType:"json",
        beforeSend: function () {
           
         },
        success:function(data)
        {
            //var src =  "{!! asset('images') !!}"; 
            var crop_image =  '<img src="'+data.path+'" />';
            $('#uploaded_image').html(crop_image);
            console.log(data.path);
            $('#records_table').DataTable().ajax.reload();
            //setTimeout(function(){$('#ImageformModal').modal('hide');}, 1000);
        }
        //event.preventDefault();
      });
    });
  });

  // clear input file
  function reset_image_form(){
      $('#upload_image').val(null);
   }
   // ////

</script>
@endpush

