@extends('layouts.inquiry')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6">
          <h3>Student Inquiry</h3>
          <form id="form-inquiry">
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Student ID</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" name="student_number" placeholder="Student ID">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Password</label>
              <div class="col-sm-9">
                <input type="password" class="form-control" name="student_password" placeholder="Password">
              </div>
            </div>
            <div class="form-group float-right">
              <button class="btn btn-sm btn-success btn-submit-inquiry" type="submit">Submit</button>
            </div>
          </form>
        </div>    
        <div class="col-sm-6">
          <h3>Contact us</h3>
          <form id="form-contact-us">
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Name</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" name="cu_name" placeholder="Name">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Message</label>
              <div class="col-sm-9">
                <textarea class="form-control" name="cu_content" placeholder="Message"></textarea>
              </div>
            </div>
            <div class="form-group float-right">
              <button class="btn btn-sm btn-success btn-submit-message" type="submit">Submit</button>
            </div>
          </form>
        </div>        
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-6">
            <h3 id="violationListsNotif"></h3>
            <table class="table table-hover table-borderless shadow-sm rounded notif-table" style
            ="display:none">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Code</th>
                  <th scope="col">Remarks</th>
                </tr>
              </thead>
              <tbody id="violationLists">
              </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $.noConflict();
            // $('#list').DataTable();
        } );
        $(function(){

      // Ensure CSRF token is present
      if (!$('meta[name="csrf-token"]').length) {
        $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
      }
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      // Contact Us submit
      $('.btn-submit-message').click(function(e){
        e.preventDefault();
        var data = $('#form-contact-us').serializeArray();
        $.ajax({
          type: 'POST',
          url: '/add-message',
          data: data,
          dataType: 'json',
          success:function(data){
            // Minimal debug log
            if(data.status !== 'success') {
              alert('Error: ' + (data.message || 'Unable to send message.'));
            } else {
              alert('Message sent!');
              location.reload(true);
            }
          },
          error:function(xhr){
            alert('Failed to send message. Please try again.');
          }
        });
      });
      // Student Inquiry submit
      $('.btn-submit-inquiry').click(function(e){
        e.preventDefault();
        var data = $('#form-inquiry').serializeArray();
        $.ajax({
          type: 'POST',
          url: '/inquiryViolation',
          data: data,
          dataType: 'json',
          success:function(data){
            $('#violationLists').empty();
            $('#violationListsNotif').empty();
            if(data.status !== 'success') {
              alert('Error: ' + (data.message || 'Inquiry failed.'));
              $('.notif-table').css('display','none');
              return;
            }
            if(data.records.length > 0){
              $('.notif-table').css('display','block')
              $.each(data.records, function( i, val ) {
                                $('#violationLists').append('<tr><th scope="row">'+(i+1)+'</th>'+
                                  '<td>'+val.vt_code+': '+val.vt_label+':</td>'+
                                  '<td>'+val.violation_actions+'</td>'+
                                '</tr>');
              });
            }
            else
            {
              $('#violationListsNotif').html('No violations.')
              $('.notif-table').css('display','none')
            }
          },
          error:function(xhr){
            alert('Inquiry failed. Please check your Student ID and Password.');
          }
        });
      });

        });   
    </script>
@endpush