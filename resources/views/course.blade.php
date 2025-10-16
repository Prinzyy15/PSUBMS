@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
@endpush

@section('content')
<div class="container">
    <h3>Course Management</h3>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-success mb-2 font-weight-bold float-right" data-toggle="modal" data-target="#addCourseModal">
                Add Course
            </button>
            <table id="list" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <!-- <th>Status</th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $c)
                    <tr>
                        <td>
                            {{ $c->course_name }}
                        </td>
                        <!-- <td>
                            {{ $c->course_desc }}
                        </td> -->
                        <td>
                            @switch($c->course_status)
                                @case('active')
                                    <span class="badge badge-success">Active</span>
                                @break
                                @default
                                    <span class="badge badge-primary">Not Active</span>
                                @break
                            @endswitch
                        </td>

                        <td><button class="btn badge badge-danger btn-delete-course" data-id="{{ $c->course_id }}"> <img src="{{ asset('images/trash.png') }}" style="height: 35px;"/> </button> <button data-id="{{ $c->course_id }}" class="btn badge badge-info btn-get-course" data-toggle="modal" data-target="#updateCourseModal"><img src="{{ asset('images/edit.png') }}" style="height: 35px;"/> </button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="addCourseModal" tabindex="-1" role="dialog" aria-labelledby="addCourseModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-add-course">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="course_name">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="course_desc"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="course_status">
                            <option>Select Status</option>
                            <option value="active">Active</option>
                            <option value="notactive">Not Active</option>
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-add-course">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateCourseModal" tabindex="-1" role="dialog" aria-labelledby="updateCourseModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-update-course">
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="course_id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="course_name">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="course_desc"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="course_status">
                            <option>Select Status</option>
                            <option value="active">Active</option>
                            <option value="notactive">Not Active</option>
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-update-course">Save</button>
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
        $(document).ready(function() {
            $.noConflict();
            $('#list').DataTable();
        } );
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });   
            $('.btn-add-course').click(function(e){
                e.preventDefault();
                var data = $('#form-add-course').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/add-course',
                    data: data,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-get-course').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '/get-course/'+id,
                    dataType: 'json',
                    success:function(data){

                        $('#form-update-course [name=course_id]').val(data.course_id);
                        $('#form-update-course [name=course_name]').val(data.course_name);
                        $('#form-update-course [name=course_desc]').val(data.course_desc);
                        $('#form-update-course [name=course_status]').val(data.course_status).change() 
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-update-course').click(function(e){
                e.preventDefault();
                var data = $('#form-update-course').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-course',
                    data: data,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-delete-course').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var c = confirm('Are you sure?');
                if (c === true)
                {
                    $.ajax({
                        type: 'POST',
                        url: '/delete-course/'+id,
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
        });   
    </script>
@endpush