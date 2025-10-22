@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
@endpush

@section('content')
<div class="container">
    <h3>Block Management</h3>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-success mb-2 font-weight-bold float-right" data-toggle="modal" data-target="#addBlockModal">
                Add Block
            </button>
            <table id="list" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blocks as $b)
                    <tr>
                        <td>
                            {{ $b->block_name }}
                        </td>
                        <td>
                            {{ $b->block_desc }}
                        </td>
                        <td>
                            @switch($b->block_status)
                                @case('active')
                                    <span class="badge badge-success">Active</span>
                                @break
                                @default
                                    <span class="badge badge-primary">Not Active</span>
                                @break
                            @endswitch
                        </td>
                        <td>
                            <button class="btn badge badge-danger btn-delete-block" data-id="{{ $b->block_id }}" aria-label="Delete block">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 6h18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button data-id="{{ $b->block_id }}" class="btn badge badge-info btn-get-block" data-toggle="modal" data-target="#updateBlockModal" aria-label="Edit block">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 21v-3.6l10.4-10.4 3.6 3.6L6.6 21H3z" fill="#fff" />
                                    <path d="M14.5 7.5l2 2" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="addBlockModal" tabindex="-1" role="dialog" aria-labelledby="addBlockModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Block</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-add-block">
                <div class="modal-body">                    
                    <div class="form-group">
                        <label>Label</label>
                        <input type="text" class="form-control" name="block_name">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="block_desc"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="block_status">
                            <option>Select Status</option>
                            <option value="active">Active</option>
                            <option value="notactive">Not Active</option>
                        </select>
                    </div>                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-add-block">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateBlockModal" tabindex="-1" role="dialog" aria-labelledby="updateBlockModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Block</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-update-block">
                <div class="modal-body">       
                    <input type="hidden" class="form-control" name="block_id">             
                    <div class="form-group">
                        <label>Label</label>
                        <input type="text" class="form-control" name="block_name">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="block_desc"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="block_status">
                            <option>Select Status</option>
                            <option value="active">Active</option>
                            <option value="notactive">Not Active</option>
                        </select>
                    </div>                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-update-block">Save</button>
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
            $('#form-add-block').submit(function(e){
                e.preventDefault();
                var data = $(this).serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/add-block',
                    data: data,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-get-block').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '/get-block/'+id,
                    dataType: 'json',
                    success:function(data){

                        $('#form-update-block [name=block_id]').val(data.block_id);
                        $('#form-update-block [name=block_name]').val(data.block_name);
                        $('#form-update-block [name=block_desc]').val(data.block_desc);
                        $('#form-update-block [name=block_status]').val(data.block_status).change() 
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-update-block').click(function(e){
                e.preventDefault();
                var data = $('#form-update-block').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-block',
                    data: data,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-delete-block').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var c = confirm('Are you sure?');
                if (c === true)
                {
                    $.ajax({
                        type: 'POST',
                        url: '/delete-block/'+id,
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