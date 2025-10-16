@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
@endpush

@section('content')
<div class="container">
    <h3>User Management</h3>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-success mb-2 font-weight-bold float-right" data-toggle="modal" data-target="#addUserModal">
                Add User
            </button>
            <table id="list" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <p class="mb-0">{{ $u->name }}</p>
                            <p class="mb-0 text-muted">{{ $u->email }}</p>
                            <p class="mb-0 text-muted">
                                    
                                @switch($u->status)
                                    @case('active')
                                        <span class="badge badge-success">Active</span>
                                    @break
                                    @default
                                        <span class="badge badge-danger">Not Active</span>
                                    @break
                                @endswitch
                            </p>
                        </td>
                        <td>{{ ($u->role === 'admin')? 'Admin Role' : 'Parent' }}</td>
                        <td>
                            @if(\Auth::user()->role === 'admin' && \Auth::user()->id !== $u->id )
                               <button class="btn badge badge-danger btn-delete-user" data-id="{{ $u->id }}"><img src="{{ asset('images/trash.png') }}" style="height: 35px;"/> </button>
                            @endif
                            <button data-toggle="modal" data-target="#updateUserModal" class="btn badge badge-info btn-get-user" data-id="{{ $u->id }}"><img src="{{ asset('images/edit.png') }}" style="height: 50px;"/> </button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-user">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <label>Role</label>
                                    <select class="form-control" name="role">
                                        <option>Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option>Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="notactive">Not Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-add-user">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-update-user">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <label>Role</label>
                                    <select class="form-control" name="role">
                                        <option>Select Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="staff">Staff</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <label>Status</label>
                                    <select class="form-control" name="status">
                                        <option>Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="notactive">Not Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-update-user">Save</button>
                </div>
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
            $('.btn-add-user').click(function(e){
                e.preventDefault();
                var data = $('#form-add-user').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/add-user',
                    data: data,
                    dataType: 'json',
                    success:function(data){
                        console.log(data);
                        location.reload(true);
                    },
                    error:function(data){
                      console.log(data);
                    }
                });
            });
            $('.btn-get-user').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    type: 'GET',
                    url: '/get-user/'+id,
                    dataType: 'json',
                    success:function(data){
                        console.log(data);
                        $('#form-update-user [name=id]').val(data.id);
                        $('#form-update-user [name=name]').val(data.name);
                        $('#form-update-user [name=email]').val(data.email);
                        $('#form-update-user [name=role]').val(data.role).change() 
                        $('#form-update-user [name=status]').val(data.status).change() 
                    },
                    error:function(data){
                      console.log(data);
                    }
                });
            });
            $('.btn-update-user').click(function(e){
                e.preventDefault();
                var data = $('#form-update-user').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-user',
                    data: data,
                    dataType: 'json',
                    success:function(data){
                        console.log(data);
                        location.reload(true);
                    },
                    error:function(data){
                      console.log(data);
                    }
                });
            });
            $('.btn-delete-user').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var c = confirm('Are you sure?');
                if (c === true)
                    {
                    $.ajax({
                        type: 'POST',
                        url: '/delete-user/'+id,
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