@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
<style type="text/css">
</style>
@endpush

@section('content')
<div class="container">
    <h3>Messages</h3>
    <div class="d-flex justify-content-end mb-2 top-controls">
        <div class="avatar-circle" aria-hidden="false">
            <svg role="img" aria-label="User avatar" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;object-fit:cover;">
                <circle cx="12" cy="8" r="4" fill="#e0e6ef"/>
                <path d="M3 21c0-3.866 3.582-7 9-7s9 3.134 9 7" fill="#e0e6ef"/>
            </svg>
        </div>
        <button class="btn btn-sm" title="Toggle theme">🌙</button>
    </div>
    <div class="row mb-5 pb-5 list-table-container">
        <div class="col-sm-12">
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
                        <th>#</th>
                        <th>Name</th>
                        <th>Content/Message</th>
                        <th>Date</th>
                        <!-- <th>Status</th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $m)
                    <tr>
                        <td>
                            #{{$m->cu_id}}
                        </td>
                        <td>
                            <p>{{$m->cu_name}}</p>
                        </td>
                        <td>
                            <span @if(isset($m->status) && strtolower($m->status) == 'settled') style="color:green;font-weight:bold;" @endif>
                                @if(isset($m->student_name) && isset($m->violation_committed))
                                    <strong>Student:</strong> {{ $m->student_name }}<br>
                                    <strong>Violation:</strong> {{ $m->violation_committed }}<br>
                                    <strong>Message:</strong> {{ $m->cu_content }}
                                @else
                                    {{ $m->cu_content }}
                                @endif
                            </span>
                        </td>
                        <td>
                            <p>Received: {{ date('F j, Y, g:i a', strtotime($m->created_at)) }}</p>
                        </td>
                        <td>
                            <button class="btn badge badge-info btn-get-student" data-id="{{ $m->cu_id }}" data-toggle="modal" data-target="#addAppointmentModal" aria-label="Add appointment for message {{ $m->cu_id }}">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                                    <path d="M3 21v-3.6l10.4-10.4 3.6 3.6L6.6 21H3z" stroke="#fff" stroke-width="0" fill="#fff"/>
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

    <div class="modal fade" id="addAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="updateStudentModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudentModal">Add Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-add-appointment" role="form" aria-label="Add appointment form">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Student</label>
                        <select class="form-control" name="appointment_student_id">
                            <option>Select Student</option>
                            @foreach($students as $s)
                            <option value="{{ $s->student_id }}">{{ $s->student_fname }} {{ $s->student_lname }}</option>
                            @endforeach

                        </select>

                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="datetime-local" class="form-control" name="appointment_date">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea class="form-control" name="appointment_reason"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-submit-appointment">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="fab">
    <button class="btn-fab" title="New Message">+</button>
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
            $('.btn-submit-appointment').click(function(e){
                e.preventDefault();
                var data = $('#form-add-appointment').serializeArray();
                $.ajax({
                  type: 'POST',
                  url: '/add-appointment',
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
        });   
    </script>
@endpush