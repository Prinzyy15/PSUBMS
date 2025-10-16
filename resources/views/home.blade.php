@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
<style type="text/css">
.avatar {
    vertical-align: middle;
    width: 50px;
    height: 50px;
    border-radius: 3px;
}
.snumber-error {
    font-size: 15px;
    color: red;
    padding: 5px 0;
}
.list-table-container {
    /*border-bottom: 1px dashed #c8c8c8;*/
}
.list-inline li {
    display: inline;
}
.list-inline li a {
    padding: 5px 10px;
    color: #fff;
    background-color: #736e6e;
    border-radius: 5px;

}
</style>
@endpush

@section('content')
<div class="container">
    <h3>Student Management</h3>
    <div class="row mb-5 pb-5 list-table-container">
        <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-success mb-2 font-weight-bold float-right" data-toggle="modal" data-target="#addStudentModal">
                Add Record
            </button>
            <br/><br/>
            <div class="row">
                <div class="col-sm-12">
                    <ul class="list-inline">
                    <li><a href="/home?get=active">Active</a></li>
                    <li><a href="/home?get=notactive">Not Active</a></li>
                    <li><a href="/home?get=dropped">Dropped</a></li>
                    <li><a href="/home?get=graduated">Graduated</a></li>
                    </ul>
                </div>
            </div>

            <table id="list" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>Student #</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Status</th>

                        @if(\Auth::user()->role === 'admin')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                    <tr>
                        <td><a href="/student/{{ $s->student_id }}">#{{ $s->student_number }}</a></td>
                        <td>
                            <p class="mb-0 float-left mr-2"><img src="{{ $s->student_avatar }}" class="img-thumbnail avatar"></p>
                            <p class="mb-0"><a href="/student/{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_mname }} {{ $s->student_lname }}</a></p>
                        </td>
                        <td>{{ $s->course_name }} <small>({{ $s->block_name }})</small></td>
                        <td>
                            @switch($s->student_year)
                                @case(1)
                                    First
                                @break
                                @case(2)
                                    Second
                                @break
                                @case(3)
                                    Third
                                @break
                                @default
                                    Fourth
                                @break
                            @endswitch
                            ({{ $s->student_year }}) Year</td>
                        <td>
                            @switch($s->student_status)
                                @case('active')
                                    <span class="badge badge-success">Active</span>
                                @break
                                @case('notactive')
                                    <span class="badge badge-danger">Not Active</span>
                                @break
                                @case('dropped')
                                    <span class="badge badge-warning">Dropped</span>
                                @break
                                @default
                                    <span class="badge badge-primary">Graduated</span>
                                @break
                            @endswitch
                        </td>
                        @if(\Auth::user()->role === 'admin')
                            <td>
                                <button class="btn badge badge-danger btn-delete-student" data-id="{{ $s->student_id }}"><img src="{{ asset('images/trash.png') }}" style="height: 35px;"/> </button></button>
                                <button class="btn badge badge-info btn-get-student" data-id="{{ $s->student_id }}" data-toggle="modal" data-target="#updateStudentModal"><img src="{{ asset('images/edit.png') }}" style="height: 35px;"/> </button></button>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
<!-- <h3>Violation Statatistics 
        <div class="dropdown float-right">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Dropdown button
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#">Monthly</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Yearly</a>
            </div>
        </div>
    </h3>
    <div class="row">
        <div class="col-sm-12">
            <table id="stats" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>Student #</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Violations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($active_students as $s)
                    <tr>
                        <td><a href="/student/{{ $s->student_id }}">#{{ $s->student_number }}</a></td>
                        <td>
                            <p class="mb-0 float-left mr-2"><img src="{{ $s->student_avatar }}" class="img-thumbnail avatar"></p>
                            <p class="mb-0"><a href="/student/{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_mname }} {{ $s->student_lname }}</a></p>
                        </td>
                        <td>{{ $s->course_name }} <small>({{ $s->block_name }})</small></td>
                        <td>
                            @switch($s->student_year)
                                @case(1)
                                    First
                                @break
                                @case(2)
                                    Second
                                @break
                                @case(3)
                                    Third
                                @break
                                @default
                                    Fourth
                                @break
                            @endswitch
                            ({{ $s->student_year }}) Year</td>
                        <td>
                            @switch($s->student_status)
                                @case('active')
                                    <span class="badge badge-success">Active</span>
                                @break
                                @case('notactive')
                                    <span class="badge badge-danger">Not Active</span>
                                @break
                                @case('dropped')
                                    <span class="badge badge-warning">Dropped</span>
                                @break
                                @default
                                    <span class="badge badge-primary">Graduated</span>
                                @break
                            @endswitch
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-add-student">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Student Number (#2025-0000)</label>
                        <input type="text" class="form-control" name="student_number">
                        <p class="snumber-error"></p>
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="student_fname">
                        <p class="fname-error"></p>
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" class="form-control" name="student_mname">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="student_lname">
                    </div>
                    <hr>
                    <h5>Parent Information</h5>
                    <div class="form-group">
                        <label>Parent First Name</label>
                        <input type="text" class="form-control" name="parent_fname" required>
                    </div>
                    <div class="form-group">
                        <label>Parent Middle Name</label>
                        <input type="text" class="form-control" name="parent_mname">
                    </div>
                    <div class="form-group">
                        <label>Parent Last Name</label>
                        <input type="text" class="form-control" name="parent_lname" required>
                    </div>
                    <div class="form-group">
                        <label>Parent Email (optional)</label>
                        <input type="email" class="form-control" name="parent_email">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select class="form-control" name="student_gender">
                            <option>Select Gender</option>
                            <option value="m">Male</option>
                            <option value="f">Female</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label>Date of Birth <small>(ex. <i>MM/DD/YYY</i>)</small></label>
                                <input type="date" class="form-control" name="student_dob">
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label>Address</label>
                                <textarea class="form-control" name="student_address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label>Course</label>
                                <select class="form-control" name="student_course">
                                    <option>Select Course</option>
                                    @foreach($courses as $c)
                                    <option value="{{ $c->course_id }}">{{ $c->course_name }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label>Year</label>
                                <select class="form-control" name="student_year">
                                    <option>Select Year</option>
                                    <option value="1">First (1) Year</option>
                                    <option value="2">Second (2) Year</option>
                                    <option value="3">Third (3) Year</option>
                                    <option value="4">Fourth (4) Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label>Block #</label>
                                <select class="form-control" name="student_block">
                                    <option>Select Block #</option>
                                    @foreach($blocks as $b)
                                    <option value="{{ $b->block_id }}">{{ $b->block_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label>Status</label>
                                <select class="form-control" name="student_status" required>
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="dropped">Dropped</option>
                                    <option value="graduated">Graduated</option>
                                    <option value="notactive">Not Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo</label>
                        <input type="file" class="form-control-file" name="student_photo" accept="image/*">
                        <small class="form-text text-muted">Upload a custom photo (optional)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-add-student">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateStudentModal" tabindex="-1" role="dialog" aria-labelledby="updateStudentModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudentModal">Update Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-update-student">
                <div class="modal-body">
                    <input type="hidden" name="student_id">
                    <div class="form-group">
                        <label>Student Number (#2025-0000)</label>
                        <input type="text" class="form-control" name="student_number">
                    </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="student_fname">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" class="form-control" name="student_mname">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="student_lname">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select class="form-control" name="student_gender">
                            <option>Select Gender</option>
                            <option value="m">Male</option>
                            <option value="f">Female</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label>Date of Birth <small>(ex. <i>MM/DD/YYY</i>)</small></label>
                                <input type="text" class="form-control" name="student_dob">
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label>Address</label>
                                <textarea class="form-control" name="student_address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label>Course</label>
                                <select class="form-control" name="student_course">
                                    <option>Select Course</option>
                                    @foreach($courses as $c)
                                    <option value="{{ $c->course_id }}">{{ $c->course_name }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label>Year</label>
                                <select class="form-control" name="student_year">
                                    <option>Select Year</option>
                                    <option value="1">First (1) Year</option>
                                    <option value="2">Second (2) Year</option>
                                    <option value="3">Third (3) Year</option>
                                    <option value="4">Fourth (4) Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <label>Block #</label>
                                <select class="form-control" name="student_block">
                                    <option>Select Block #</option>
                                    @foreach($blocks as $b)
                                    <option value="{{ $b->block_id }}">{{ $b->block_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <label>Status</label>
                                <select class="form-control" name="student_status">
                                    <option>Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="dropped">Dropped</option>
                                    <option value="graduated">Graduated</option>
                                    <option value="notactive">Not Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Photo</label>
                        <p><img align='middle' src="/images/user.png" alt="default image" class="avatar"/></p>
                        <input type="file" class="form-control btn-update-photo">
                        <input type="hidden" name="student_avatar">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-update-student">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.js"></script>
    <script>          
            $('.btn-add-student').css('display','none');
            $(document).ready(function() {
                $.noConflict();
                $('#list').DataTable();
                $('#stats').DataTable();  
            });
            $(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });   
                $('#form-add-student [name=student_number]').keyup(function(e){ 
                    e.preventDefault();  
                    var q = $(this);
                    if(q.val().length > 5)
                    {
                        $.ajax({
                            type: 'POST',
                            url: '/check-student',
                            data: {q: q.val()},
                            dataType: 'json',
                            success:function(data){
                                if(data.hit > 0)
                                {
                                    $(q).css({'border':'1px solid red'})
                                    $('.snumber-error').empty().append('Oops! `Student Number` is existing!');
                                    $('.btn-add-student').css('display','none');
                                }
                                else
                                {
                                    $(q).css({'border':'1px solid #ced4da'})
                                    $('.snumber-error').empty();
                                    $('.btn-add-student').css('display','block');
                                }
                            },
                            error:function(data){
                              console.log(data);
                            }
                        });
                    } 
                });
                $('#form-add-student [name=student_fname]').keypress(function(){    
                    // var q = $(this);
                    // checQ(q, 'name');    
                });
                // Use form submit event for AJAX
                $('#form-add-student').submit(function(e){
                    e.preventDefault();
                    var form = document.getElementById('form-add-student');
                    var formData = new FormData(form);
                    $.ajax({
                        type: 'POST',
                        url: '/add-student',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success:function(data){
                            if(data.status === 'success') {
                                location.reload(true);
                            } else {
                                alert(data.message);
                            }
                        },
                        error:function(xhr){
                            console.log(xhr);
                            alert('An error occurred while adding the student.');
                        }
                    });
                });
            $('.btn-get-student').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '/get-student/'+id,
                    dataType: 'json',
                    success:function(data){
                        console.log(data);
                        $('#form-update-student [name=student_id]').val(data.student_id);
                        if(data.student_avatar)
                        {
                            $('.avatar').attr('src', data.student_avatar);
                        }
                        $('#form-update-student [name=student_avatar]').val(data.student_avatar);
                        $('#form-update-student [name=student_number]').val(data.student_number);
                        $('#form-update-student [name=student_password]').val(data.student_password);
                        $('#form-update-student [name=student_fname]').val(data.student_fname);
                        $('#form-update-student [name=student_mname]').val(data.student_mname);
                        $('#form-update-student [name=student_lname]').val(data.student_lname);
                        $('#form-update-student [name=student_gender]').val(data.student_gender).change() 
                        $('#form-update-student [name=student_dob]').val(data.student_dob);
                        $('#form-update-student [name=student_address]').val(data.student_address);
                        $('#form-update-student [name=student_year]').val(data.student_year).change() 
                        $('#form-update-student [name=student_block]').val(data.student_block).change() 
                        $('#form-update-student [name=student_course]').val(data.student_course).change() 
                        $('#form-update-student [name=student_status]').val(data.student_status).change() 

                        // location.reload(true);
                    },
                    error:function(data){
                      console.log(data);
                    }
                });
            });
            // Use form submit event for update student
            $('#form-update-student').submit(function(e){
                e.preventDefault();
                var data = $(this).serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-student',
                    data: data,
                    dataType: 'json',
                    success:function(data){
                        if(data.status === 'success') {
                            location.reload(true);
                        } else {
                            alert(data.message);
                        }
                    },
                    error:function(data){
                        console.log(data);
                        alert('An error occurred while updating the student.');
                    }
                });
            });
            $('.btn-delete-student').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var c = confirm('Are you sure?');
                if (c === true)
                {
                    $.ajax({
                        type: 'POST',
                        url: '/delete-student/'+id,
                        dataType: 'json',
                        success:function(data){
                            console.log(data);
                            location.reload(true);
                        },
                        error:function(data){
                          console.log(data);
                        }
                    });
                }
            });
            
            $('.btn-add-new-photo').change(function (event) {
                readURL(this);
            });
            
            $('.btn-update-photo').change(function (event) {
                readURL(this);
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.avatar').attr('src', e.target.result);
                        $('.avatar').hide();
                        $('.avatar').fadeIn(500);
                        $('[name="student_avatar"]').val(e.target.result);
                        console.log(input)
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        });   
    </script>
@endpush