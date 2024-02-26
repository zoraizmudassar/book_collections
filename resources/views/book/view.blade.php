@extends('layout.header')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" rel="stylesheet">
<link href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.dataTables_filter > label {
    float: inline-end;
}
.dataTables_wrapper .dataTables_paginate {
    float: right;
}
#spinner.show {
    transition: opacity .5s ease-out, visibility 0s linear 0s;
    visibility: visible;
    opacity: 1;
}
</style>
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 mx-auto py-5">
            <h4 class="text-center">Books List</h4>
            <table id="example" class="table table-striped table-bordered my-2">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr. No</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Published Year</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($books as $index => $book)
                        <tr>
                        <td>{{$index + 1}}</td>
                            <td>{{$book->title}}</td>
                            <td>{{$book->author}}</td>
                            <td>{{$book->genre}}</td>
                            <td>{{$book->published_year}}</td>
                            <td>
                                <i data-id="{{$book}}" class="fa fa-edit fa-lg mx-1 edit-deal-btn" style="cursor: pointer;" title="Edit Book"></i>
                                <i data-id="{{$book->id}}" class="fa fa-trash text-danger fa-lg delete-deal-btn" style="cursor: pointer;" title="Delete Book"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Delete Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Are you sure you want to delete this book?</h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="delete">Delete</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bd-example-modal-lg" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Book</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="edit-deal" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="title">Title</label>
                            <input type="hidden" class="form-control" name="id" id="id">
                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" value="Adventures of Tom Sawyer">
                            <small class="text-danger"></small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" name="author" id="author" placeholder="Enter Aothor" value="Mark Twain">
                            <small class="text-danger"></small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="validationServer03">Published Year </label>
                            <input type="text" class="form-control onlynumber" id="published_year" name="published_year" placeholder="Enter Published Year (e.g. 2022)">
                            <small class="text-danger"></small>
                        </div>
                            <div class="col-md-12 mb-3">
                                <label for="genre">Genre</label>
                                <select class="custom-select" id="genre" name="genre" aria-describedby="genre">
                                    @foreach($genres as $genre)
                                    <option value="{{$genre}}">{{$genre}}</option> 
                                    @endforeach                          
                                </select>
                            </div>
                        </div>     
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary update-restaurant" type="submit">Update</button>
                    </div>  
                </form>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <script>
    $(document).ready(function() {
        function showLoader() {
            $('.loader-container').show(); 
        }
        function hideLoader() {
            $('.loader-container').hide(); 
        }
        hideLoader();
        $('#example').DataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "pageLength": 5,
        });
        var data_id;
        $('.delete-deal-btn').click(function (e){
            data_id = JSON.parse($(this).attr('data-id'));
            $('#confirmModal').modal('show');
        });
        $('#delete').click(function(){
            showLoader();
            var data = { "_token": "{{@csrf_token()}}", "data_id": data_id };
            $.ajax({
                type:'POST',
                url:'{{route('del.book')}}',
                data:data,
                success:function (data){
                    hideLoader();
                    if (data.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: "Deleted",
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            setTimeout(function() {
                                window.location.href = '/';
                            }, 100); 
                        });
                        $('#confirmModal').modal('hide');
                    } else if (data.status === false) {
                        Swal.fire({
                            icon: 'error',
                            title: "Error",
                            text: data.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: "Error",
                            text: "Something went wrong",
                        });
                    }
                },
                error:function (data){
                    hideLoader();
                    console.error('error');
                }
            });
        });
        $('.edit-deal-btn').click(function (e) {
            showLoader();
            var data = JSON.parse($(this).attr('data-id'));
            $('#id').val(data.id);
            $('#title').val(data.title);
            $('#author').val(data.author);
            $('#published_year').val(data.published_year);
            $('#genre').val(data.genre);
            $('#editProductModal').modal('show');
            hideLoader();
            $('.edit-deal').submit(function (e) {
                e.preventDefault();
                showLoader();
                var data = $('.edit-deal');
                data = new FormData(data[0]);
                $('#editProductModal').modal('hide');
                $.ajax({
                    type: 'POST',
                    url: '{{route('update.book')}}',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        hideLoader();
                        if (response.success === true){
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#editProductModal').modal('hide');
                                setTimeout(function() {
                                    window.location.href = '/';
                                }, 100);
                            });
                        } else if (response.success === false) {
                            $('#editProductModal').modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });                     
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoader();
                        $('#editProductModal').modal('show');
                        $('.is-invalid').removeClass('is-invalid');
                        $('.text-danger').text('');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('input[name="' + key + '"]').addClass('is-invalid');
                                $('input[name="' + key + '"]').siblings('.text-danger').text(value);
                            });
                        } else if (xhr.status === 500) {
                            var errorMessage = xhr.responseJSON.message;
                            console.error('Server Error: ' + errorMessage);
                        } else {
                            console.error(xhr.responseText);
                        }
                    }
                });
            });
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
@endsection
