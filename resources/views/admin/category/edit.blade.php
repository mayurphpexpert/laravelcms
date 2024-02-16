@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Edit Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{ route('categories.index') }}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="categoryForm" name="categoryForm">
                            <div class="card">
                                <div class="card-body">								
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Name</label>
                                                <input value="{{ $category->name }}" type="text" name="name" id="name" class="form-control" placeholder="Name">	
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug">Slug</label>
                                                <input type="text" value="{{ $category->slug }}" readonly name="slug" id="slug" class="form-control" placeholder="Slug">	
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="parent_id">Parent Category</label>
                                                <select name="parent_id" id="parent_id" class="form-control">
                                                    <option value="" selected>Select Parent Category (optional)</option>
                                                    @if ($categories->isNotEmpty())
                                                    @foreach($categories as $cat)
                                                        <option  {{ ($category->parent_id == $cat->id) ? 'selected' : '' }} value="{{ $cat->id }}">
                                                            {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status">Status</label>											
                                                <select name="status" id="status" class="form-control">
                                                    <option {{ ($category->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                                    <option  {{ ($category->status == 0) ? 'selected' : '' }} value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Meta Title</label>
                                                <input type="text" value="{{ $category->meta_title }}" name="meta_title" id="meta_title" class="form-control" placeholder="Meta Title">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Meta Canonical Url</label>
                                                <input type="text" value="{{ $category->meta_canonical_url }}" name="meta_canonical_url" id="meta_canonical_url" class="form-control" placeholder="Meta Canonical Url">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="description">Meta Description</label>
                                                <textarea name="meta_description" id="meta_description" class="form-control" placeholder=" Meta Description">{{ $category->meta_description }}</textarea>
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="description">Meta Keyword</label>
                                                <textarea name="meta_keyword" id="meta_keyword" class="form-control" placeholder=" Meta Keyword">{{ $category->meta_keyword }}</textarea>
                                                <p></p>
                                            </div>
                                        </div>	
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="description" class="summernotecatedit" placeholder="Description">{{ $category->description }}</textarea>
                                                <p></p>
                                            </div>
                                        </div>	
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status">Show on home</label>
                                                <select name="showHome" id="showHome" class="form-control">
                                                    <option {{ ($category->showHome == 'Yes') ? 'selected' : '' }} value="Yes">Yes</option>
                                                    <option {{ ($category->showHome == 'No') ? 'selected' : '' }} value="No">No</option>
                                                </select>
                                            </div>
                                        </div>							
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <input type="hidden" id="image_id" name="image_id" value="">
                                                <label for="image">Image</label>
                                                <div id="image" class="dropzone dz-clickable">
                                                    <div class="dz-message needsclick">    
                                                        <br>Drop files here or click to upload.<br><br>                                            
                                                    </div>
                                                </div>
                                            </div>                                                                                      
                                        </div>
                                        <div class="col-md-6">
                                        @if (!empty($category->image))
                                            <div>
                                                <img width="250" src="{{ asset('uploads/category/thumb/'.$category->image) }}" alt="">
                                            </div>
                                        @endif
                                        </div>
                                    </div>
                                </div>							
                            </div>
                            <div class="pb-5 pt-3">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                            </div>
                        </form>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection

@section('customJs')
<script>
$("#categoryForm").submit(function(event){
    event.preventDefault();
    var element = $(this);
    $("button[type=submit]").prop('disabled',true);
    $.ajax({
        url:'{{ route("categories.update",$category->id) }}',
        type: 'put',
        data: element.serializeArray(),
        dataType: 'json',
        success: function(response){
            $("button[type=submit]").prop('disabled',false);

            if (response["status"] == true) {

                window.location.href="{{ route('categories.index') }}";

                $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                $("#slug").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

            } else {

                if (response['notFound'] == true){
                    window.location.href="{{ route('categories.index') }}";
                }

                var errors = response['errors'];
                if (errors['name']) {
                    $("#name").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['name']);
                } else {
                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                }

                if (errors['slug']) {
                    $("#slug").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['slug']);
                } else {
                    $("#slug").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                }
            }

        }, error: function(jqXHR, exception){
            console.log("Something went wrong");
        }
    })
});

    $("#name").change(function(){
        element = $(this);
        $("button[type=submit]").prop('disabled',true);
        $.ajax({
            url:'{{ route("getSlug") }}',
            type: 'get',
            data: {title: element.val()},
            dataType: 'json',
            success: function(response){
                $("button[type=submit]").prop('disabled',false);
                if (response["status"] == true) {
                    $("#slug").val(response["slug"]);
                }
            }
        });
    });


    Dropzone.autoDiscover = false; 
    $('.summernotecatedit').summernote({
        height: '200px'
    });   
    const dropzone = $("#image").dropzone({ 
        init: function() {
            this.on('addedfile', function(file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
        },
        url:  "{{ route('temp-images.create') }}",
        maxFiles: 1,
        paramName: 'image',
        addRemoveLinks: true,
        acceptedFiles: "image/jpeg,image/png,image/gif",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }, success: function(file, response){
            $("#image_id").val(response.image_id);
            //console.log(response)
        }
    });
   
</script>
@endsection