@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
@endpush

@section('content')
<div class="container">
    <h3>Violations/Complaint Management</h3>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-success mb-2 font-weight-bold float-right" data-toggle="modal" data-target="#addViolationModal">
                Add Violation
            </button>
            <div class="glass-table">
                <div class="table-tools d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button class="btn btn-sm btn-outline-secondary">Filter</button>
                        <button class="btn btn-sm btn-outline-secondary">Export</button>
                    </div>
                    <div>
                        <select class="form-control form-control-sm">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                    </div>
                </div>
                <table id="list" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Desc</th>
                        <th>Violators</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($violations as $v)
                    <tr>
                        <td>
                            {{ $v->vt_label }}
                        </td>
                        <td>
                            {{ $v->vt_desc }}
                        </td>
                        <td>
                            <a href="/get-violators/{{ $v->vt_id }}" class="btn btn-sm btn-warning">Violators</a>
                        </td>
                        <td>
                            <button class="btn badge badge-inverse btn-get-violation" data-id="{{ $v->vt_id }}"  data-toggle="modal" data-target="#updateViolationModal" aria-label="Edit violation">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 21v-3.6l10.4-10.4 3.6 3.6L6.6 21H3z" fill="#fff" />
                                    <path d="M14.5 7.5l2 2" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                        
                        <td>
                            <button class="btn badge badge-danger btn-delete-violation" data-id="{{ $v->vt_id }}" aria-label="Delete violation">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M3 6h18" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 6v14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="updateViolationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Complaint/Violation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-update-violation">
                <input type="hidden" class="form-control" name="vt_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" class="form-control" name="vt_code" placeholder="ex. 1001">
                        <p class="code-error"></p>
                    </div>
                    <div class="form-group">
                        <label>Label</label>
                        <input type="text" class="form-control" name="vt_label">
                    </div>
                    <div class="form-group">
                        <label>Description (optional)</label>
                        <textarea class="form-control" name="vt_desc" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-update-violation">Save</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addViolationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Complaint/Violation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-add-violation">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" class="form-control" name="vt_code" placeholder="ex. 1001">
                        <p class="code-error"></p>
                    </div>
                    <div class="form-group">
                        <label>Label</label>
                        <input type="text" class="form-control" name="vt_label">
                    </div>
                    <div class="form-group">
                        <label>Description (optional)</label>
                        <textarea class="form-control" name="vt_desc" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-add-violation">Save</button>
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

            $('#form-add-violation [name="vt_code"]').keyup(function(e){ 
            e.preventDefault();  
                var q = $(this);
                console.log(q.val())
                if(q.val().length > 3)
                {
                    $.ajax({
                        type: 'POST',
                        url: '/check-violation-code',
                        data: {q: q.val()},
                        dataType: 'json',
                        success:function(data){
                            console.log(data);
                            if(data.hit > 0)
                            {
                                $(q).css({'border':'1px solid red'})
                                $('.code-error').empty().append('Oops! ` Violation Code` is existing!');
                            }
                            else
                            {
                                $(q).css({'border':'1px solid #ced4da'})
                                $('.code-error').empty()
                            }

                        },
                        error:function(data){
                          console.log(data);
                        }
                    });
                } 
            });
            $('.btn-add-violation').click(function(e){
                e.preventDefault();
                var data = {
                    vt_code: $('#form-add-violation [name="vt_code"]').val(),
                    vt_label: $('#form-add-violation [name="vt_label"]').val(),
                    vt_desc: $('#form-add-violation [name="vt_desc"]').val()
                };
                $.ajax({
                    type: 'POST',
                    url: '/add-violation',
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
            $('.btn-update-violation').click(function(e){
                e.preventDefault();
                var data = $('#form-update-violation').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-violation',
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
            $('.btn-delete-violation').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var c = confirm('Are you sure?');
                if (c === true)
                {
                    $.ajax({
                        type: 'POST',
                        url: '/delete-violation/'+id,
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
            $('.btn-get-violation').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                console.log(id)
                $.ajax({
                    type: 'GET',
                    url: '/get-violation/'+id,
                    dataType: 'json',
                    success:function(data){
                        console.log(data);
                        $('#form-update-violation [name=vt_id]').val(data.vt_id);
                        $('#form-update-violation [name=vt_label]').val(data.vt_label);
                        $('#form-update-violation [name=vt_code]').val(data.vt_code);
                        $('#form-update-violation [name=vt_desc]').val(data.vt_desc);
                    },
                    error:function(data){
                      console.log(data);
                    }
                });
            });
        });   
    </script>
@endpush