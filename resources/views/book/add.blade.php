@extends('layout.header')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 mx-auto py-5">
            <h4 class="text-center">Add Book</h4>
            <form class="add-book p-4" id="addBookForm" style="border: 1px solid gainsboro; border-radius: 10px;"> 
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title">
                        <small class="text-danger" id="titleError"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="author">Author</label>
                        <input type="text" class="form-control" name="author" id="author" placeholder="Enter Author">
                        <small class="text-danger" id="authorError"></small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="validationServer03">Published Year</label>
                        <input type="text" class="form-control onlynumber" id="published_year" name="published_year" placeholder="Enter Published Year (e.g. 2022)">
                        <small class="text-danger" id="publishedYearError"></small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="genre">Genre</label>
                        <select class="custom-select" id="genre" name="genre" aria-describedby="genre">
                            @foreach($genres as $genre)
                                <option value="{{$genre}}">{{$genre}}</option> 
                            @endforeach                          
                        </select>
                        <small class="text-danger" id="genreError"></small>
                    </div>
                </div>
                <button class="btn btn-primary w-100 submit" type="button" id="submitButton">Add</button>            
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function showLoader() {
            $('.loader-container').show();
        }
        function hideLoader() {
            $('.loader-container').hide();
        }
        $(".onlynumber").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                $('input[name="published_year"]').addClass('is-invalid');
                $('input[name="published_year"]').siblings('.text-danger').text('The published year field must be an integer');
            }
        });
        hideLoader();
        const submitBtn = document.querySelector('.submit');
        submitBtn.addEventListener('click', function() {
            document.querySelectorAll('.text-danger').forEach(function(element) {
                element.textContent = '';
            });
            var title = document.getElementById('title').value.trim();
            var author = document.getElementById('author').value.trim();
            var publishedYear = document.getElementById('published_year').value.trim();
            var genre = document.getElementById('genre').value.trim();            
            var isValid = true;
            if (title === '') {
                document.getElementById('titleError').textContent = 'Title is required';
                isValid = false;
            }
            if (author === '') {
                document.getElementById('authorError').textContent = 'Author is required';
                isValid = false;
            }
            if (publishedYear === '') {
                document.getElementById('publishedYearError').textContent = 'Published Year is required';
                isValid = false;
            }
            if (genre === '') {
                document.getElementById('genreError').textContent = 'Genre is required';
                isValid = false;
            }
            if (isValid) {
                addBook();
            }
            function addBook() {
                showLoader();
                var data = $('.add-book');
                data = new FormData(data[0]);
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url:'{{route('add.book')}}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    processData: false,
                    contentType: false, 
                    data: data, 
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
                                $('.add-book')[0].reset();
                                setTimeout(function() {
                                    window.location.href = '/';
                                }, 100);
                            });
                        } else if (response.success === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 3000
                            });                     
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoader();
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
            }
        });
    });
</script>
@endsection