@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.bootstrap4.min.css"/>
<style type="text/css">
.avatar {
    vertical-align: middle;
    width: 100px;
    height: 100px;
    border-radius: 3px;
    border: 1px solid #c8c8c8;
}
.btn-action-edite {
    border-radius: 5px;
    padding: 0px 5px;
}
@media all {
	#printLetterContainer,
	#printAppointmentContainer{
		display: none;
	}
}
@media print {
	#printLetterContainer,
	#printAppointmentContainer{
		display: block!important;
	}
	#printLetterContainer p.by,
	#printAppointmentContainer p.by{
		text-align: right;
	}
}
</style>
@endpush

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-xl-4 col-lg-4 col-md-5 col-sm-12">				
				<a href="/home" class="btn btn-sm btn-primary mb-2 font-weight-bold" >
					Back to Student's lists
				</a>
				<div class="card">				
					<div class="card-body">
						<h5 class="card-title text-center">
							<img class="responsive-image avatar" src="{{ $student->student_avatar }}">
						</h5>
						<h5 class="card-title text-center">{{ $student->student_fname }} {{ $student->student_mname }} {{ $student->student_lname }}</h5>
						<table class="table table-hover table-borderless shadow-sm rounded">
							<tbody>
								<tr>									
									<td colspan="2">
										<h5>Course / Block</h5>
										<p class="mb-0">{{ $student->course_name }} / {{ $student->block_name }}</p>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<h5>Year</h5>
			                            @switch($student->student_year)
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
			                            ({{ $student->student_year }}) Year</td>
								</tr>
								<tr>
									<td>
										<h5 class="mb-0">Status</h5>
									</td>
									<td>
			                            @switch($student->student_status)
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
								<tr>
									<td colspan="2">
										<h5>Parents</h5>

										@foreach($parents as $p)
											<p class="mb-1">
												<a href="#" class="show-parent-credentials" 
												   data-username="{{ $student->student_number }}" 
												   data-password="{{ $student->student_number }}"
												   data-toggle="modal" data-target="#parentCredentialsModal">
													{{ $p->parent_fname }} {{ $p->parent_mname }} {{ $p->parent_lname }}
												</a>
												<span class="btn-action-edite float-right btn-info btn-get-parent" data-id="{{ $p->parent_id }}" data-toggle="modal" data-target="#updateParentModal"><small class="pl-1 pr-1">EDIT</small></span>
											</p>
										@endforeach
	<!-- Parent Credentials Modal -->
	<div class="modal fade" id="parentCredentialsModal" tabindex="-1" role="dialog" aria-labelledby="parentCredentialsModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="parentCredentialsModalLabel">Parent Account Credentials</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Username</label>
						<input type="text" class="form-control" id="parent-username" readonly>
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="text" class="form-control" id="parent-password" readonly>
					</div>
					<button type="button" class="btn btn-warning" id="reset-parent-password">Reset Password</button>
					<div id="reset-parent-password-msg" class="mt-2"></div>
				</div>
			</div>
		</div>
	</div>
@push('scripts')
<script>
$(document).on('click', '.show-parent-credentials', function(e) {
	e.preventDefault();
	var username = $(this).data('username');
	var password = $(this).data('password');
	$('#parent-username').val(username);
	$('#parent-password').val(password);
	$('#reset-parent-password-msg').html('');
});

$('#reset-parent-password').on('click', function() {
	var username = $('#parent-username').val();
	$.ajax({
		url: '/reset-parent-password',
		method: 'POST',
		data: {
			username: username,
			_token: '{{ csrf_token() }}'
		},
		success: function(res) {
			$('#reset-parent-password-msg').html('<span class="text-success">Password reset to: ' + username + '</span>');
		},
		error: function() {
			$('#reset-parent-password-msg').html('<span class="text-danger">Failed to reset password.</span>');
		}
	});
});
</script>
@endpush
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<h5>Contacts</h5>
										@foreach($contacts as $c)
											<p class="mb-1">
												<span title="{{ $c->contact_number_label }}" class="">{{ $c->contact_number }}</span>
												<span class="btn-action-edite float-right btn-info btn-get-contact" data-id="{{ $c->contact_id }}" data-toggle="modal" data-target="#updateContactModal"><small class="pl-1 pr-1">EDIT</small></span>
											</p>
										@endforeach
									</td>
								</tr>
							</tbody>
						</table>

	                	@if(\Auth::user()->role === 'admin')
						<button type="button" class="btn btn-sm btn-danger font-weight-bold mb-2" data-toggle="modal" data-target="#addRecordModal">
							Add Record
						</button>
			            @endif			            

						<button type="button" class="btn btn-sm btn-warning font-weight-bold mb-2" data-toggle="modal" data-target="#addParentModal">
							Add Parent
						</button>

						<button type="button" class="btn btn-sm btn-warning font-weight-bold mb-2" data-toggle="modal" data-target="#addContactModal">
							Add Contact
						</button>
						<button type="button" class="btn printLetter btn-sm btn-warning font-weight-bold mb-2">
							Print Letter
						</button>

					</div>
				</div>
			</div>
			<div class="col-xl-8 col-lg-8 col-md-7 col-sm-12">
				@if(count($violations) > 2)
				<div class="card mb-3">
					<div class="card-body bg-danger text-white p-1">
						<span class="font-weight-bold">{{ $student->student_fname }} {{ $student->student_mname }} {{ $student->student_lname }}</span> has {{ count($violations) }} violations! Candidate for expulsion.
					</div>
				</div>
				@endif
				<table id="list" class="table table-hover table-borderless shadow-sm rounded">
	                <thead>
	                    <tr>
	                        <th>#</th>
	                        <th>Violations</th>
	                        <th>Remarks</th>
	                        <th>Status</th>
	                        <th>Actions taken</th>
	                        <th>Assisted by</th>
	                        <th>Date</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
                    	@foreach($violations as $k => $v)
	                    <tr>
	                        <td>{{ $k+1 }}</td>
	                        <td>{{ $v->vt_code }} - {{ $v->vt_label }}</td>
							<td>
								<span @if(strtolower($v->violation_status) == 'settled') style="color:green;font-weight:bold;" @endif>
									{{ $v->violation_remarks }}
								</span>
							</td>
							<td><?php echo ($v->violation_status)? ucwords($v->violation_status) : 'N/A' ?></td>
	                        <td>{{ $v->violation_actions }}</td>
	                        <td>{{ $v->name }}</td>
	                        <td><?php echo date('F j, Y, g:i a', strtotime($v->created_at))?></td>
	                        <td>

			                	@if(\Auth::user()->role === 'admin')
			                	
					            @endif
								<button class="btn badge badge-success btn-get-student-violations" data-id="{{ $v->violation_id }}" data-toggle="modal" data-target="#updateRecordModal"><img src="{{ asset('images/edit2.png') }}" style="height: 35px;"/> </button>
								<!-- <button class="btn badge badge-warning btn-set-message" data-id="{{ $v->id }}" data-toggle="modal" data-target="#setMessageModal"><img src="{{ asset('images/mail.png') }}" style="height: 35px;"/> </button> -->
	                        </td>
	                    </tr>
	                    @endforeach
	                </tbody>
	            </table>
	            <hr>
	            <h3>Appointments</h3>
				<table id="list_appointments" class="table table-hover table-borderless shadow-sm rounded">
	                <thead>
	                    <tr>
	                        <th>#</th>
	                        <th>Reason</th>
	                        <th>Date</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
                    	@foreach($appointments as $k => $a)
	                    <tr>
	                        <td>{{ $k+1 }}</td>
	                        <td>{{ $a->appointment_reason }}</td>
	                        <td><?php echo date('F j, Y, g:i a', strtotime($a->appointment_date))?></td>
	                        <td>
	                        	<button class="btn badge badge-warning printAppointment" data-id="{{ $a->appointment_id }}"><img src="{{ asset('images/mail.png') }}" style="height: 35px;"/> </button>
	                        </td>
	                    </tr>
	                    @endforeach
	                </tbody>
	            </table>
			</div>
		</div>
		<div id="printAppointmentContainer">
				<h1 style="margin:0!important">Pangasinan State University</h1>
			<h1 style="margin:0!important">Office of Student Affairs and Discipline</h1>
			<p style="margin:0!important">Address and Contact Number</p>
			<p>Dear Mr. and Mrs.
				@foreach($parents as $p)
					<strong>{{ $p->parent_fname }} {{ $p->parent_mname }} {{ $p->parent_lname }}</strong>, 
				@endforeach
			</p>
			<p>This letter is from the Office of Student Services of Pangasinan State University - ACC. 
				Above is the appointment that your child has incurred in the institution. 
We wrote this letter to inform you about your child's behavior and conduct in the institution. 
This letter serves also as an invitation for you to come to our office regarding on this matter. 
, <strong>{{ $student->student_fname }} {{ $student->student_mname }} {{ $student->student_lname }}</strong>, has been committed violations under School Rule namely;
Thank You 
            </p>
            <p id="setAppointmentDate"></p>
            <br><br><br>
        <!-- <p>Other text.....</p> -->
        <!-- <p style="margin: 50!important"><strong>{{ \Auth::user()->name }}</strong> </p> -->
        <p style="margin: 50!important">Prepared by:</p>
         <p style="margin: 80!important"><strong>{{ \Auth::user()->name }}</strong> </p>
        <p style="margin: 100!important">Guidance Office, Head</p>

		</div>
		<div id="printLetterContainer">
				<h1 style="margin:0!important">Pangsinan State University</h1>
			<h1 style="margin:0!important">Office of Student Services</h1>
			<p style="margin:0!important">Address and Contact Number</p>
			<p>Dear Mr. and Mrs.
				@foreach($parents as $p)
					<strong>{{ $p->parent_fname }} {{ $p->parent_mname }} {{ $p->parent_lname }}</strong>, 
				@endforeach
			</p>
			<p>This letter is from the Office of Student Services of Pangasinan State University - ACC. 
				Above are the violations that your child has incurred in the institution. 
We wrote this letter to inform you about your child's behavior and conduct in the institution. 
This letter serves also as an invitation for you to come to our office regarding on this matter. 
, <strong>{{ $student->student_fname }} {{ $student->student_mname }} {{ $student->student_lname }}</strong>, has been committed violations under School Rule namely;
Thank You 
            </p>
				<table class="table table-hover table-borderless shadow-sm rounded">
	                <thead>
	                    <tr>
	                        <th>Violations</th>
	                        <th>Remarks</th>
	                        <th>Actions taken</th>
	                        <th>Date</th>
	                    </tr>
	                </thead>
            	<tbody>
            		@foreach($violations as $k => $v)
		                <tr>
		                    <td>{{ $v->vt_code }} - {{ $v->vt_label }}</td>
		                    <td>{{ $v->violation_remarks }}</td>
		                    <td>{{ $v->violation_actions }}</td>
		                    <td><?php echo date('F j, Y, g:i a', strtotime($v->created_at))?></td>
		                </tr>
	                @endforeach
                </tbody>
            </table>
            <!-- <p>Other text.....</p> -->
            <!-- <p style="margin: 50!important"><strong>{{ \Auth::user()->name }}</strong> </p> -->
            <p style="margin: 50!important">Prepared by:</p>
             <p style="margin: 80!important"><strong>{{ \Auth::user()->name }}</strong> </p>
            <p style="margin: 100!important">Guidance Office, Head</p>
		</div>
		<div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-labelledby="addRecordModal" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title">New Record</h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		                </button>
		            </div>
		            <div class="modal-body">
						<form id="form-add-student-violation">
							<input type="hidden" name="student_id" value="{{ $id }}">
							<input type="hidden" name="violation_created_by" value="{{ Auth::user()->id }}">
						    <div class="form-group">
						        <label>Violation</label>
						        <select class="form-control" name="violation_type">
									<option value="">Select Violation</option>
                                    @foreach($vt as $v)
                                    <option value="{{ $v->vt_id }}">{{ $v->vt_label }}</option>
                                    @endforeach
						        </select>
						    </div>
							<div class="form-group">
								<label>Remarks <span class="text-muted">(optional)</span></label>
								<textarea class="form-control" name="violation_remarks" placeholder="Optional"></textarea>
							</div>
							<div class="form-group">
								<label>Actions Taken <span class="text-muted">(optional)</span></label>
								<textarea class="form-control" name="violation_actions" placeholder="Optional"></textarea>
							</div>
						</form>
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		                <button type="button" class="btn btn-primary btn-add-student-violation">Save</button>
		            </div>
		        </div>
		    </div>
		</div>
		<div class="modal fade" id="updateRecordModal" tabindex="-1" role="dialog" aria-labelledby="updateRecordModal" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title">Update Record</h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		                </button>
		            </div>
					<form id="form-update-student-violation">
						<div class="modal-body">
							<input type="hidden" name="id">
							<input type="hidden" name="student_id" value="{{ $id }}">
							<input type="hidden" name="violation_created_by" value="{{ \Auth::user()->id }}">
						    <div class="form-group">
						        <label>Violation</label>
						        <select class="form-control" name="violation_type">
						            <option>Select Violation</option>
                                    @foreach($vt as $v)
                                    <option value="{{ $v->vt_id }}">{{ $v->vt_label }}</option>
                                    @endforeach
						        </select>
						    </div>
						    <div class="form-group">
						        <label>Remarks</label>
						        <textarea class="form-control" readonly name="violation_remarks"></textarea>
						    </div>
						    <div class="form-group">
						        <label>Status</label>
						        <select class="form-control" name="violation_status">
						            <option>Select Violation Status</option>
						            <option value="settled">Settled</option>
						            <option value="pending">Pending</option>
						        </select>
						    </div>
						    <div class="form-group">
						        <label>Actions Taken</label>
						        <textarea class="form-control" name="violation_actions"></textarea>
						    </div>
			            </div>
			            <div class="modal-footer">
	                	@if(\Auth::user()->role === 'admin')
                
			            @endif -->
			                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			                <button type="button" class="btn btn-primary btn-update-student-violation">Save</button>
			            </div>
			        </form>
		        </div>
		    </div>
		</div>

	    <div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addParentModal" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="exampleModalLabel">Add Contact</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <form id="form-add-contact">
	                <div class="modal-body">      
	                	<input type="hidden" name="contact_student_id" value="{{ $id }}">                   
	                    <div class="form-group">
	                        <label>Label</label>
	                        <input type="text" class="form-control" name="contact_number_label">
	                    </div>
	                    <div class="form-group">
	                        <label>Number (#09501234567)</label>
	                        <input type="text" class="form-control" name="contact_number">
	                    </div>           
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	                    <button type="button" class="btn btn-primary btn-add-contact">Save</button>
	                </div>
	                </form>
	            </div>
	        </div>
	    </div>

	    <div class="modal fade" id="setMessageModal" tabindex="-1" role="dialog" aria-labelledby="addParentModal" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="exampleModalLabel">Set Appointment</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <form id="form-set-message">
	                <div class="modal-body">      
	                	<input type="hidden" name="contact_student_id" value="{{ $id }}">                   
	                    <div class="form-group">
	                        <label>Message</label>
	                        <textarea class="form-control"></textarea>
	                    </div>    
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	                    <button type="button" class="btn btn-primary btn-add-contact">Save</button>
	                </div>
	                </form>
	            </div>
	        </div>
	    </div>

	    <div class="modal fade" id="updateContactModal" tabindex="-1" role="dialog" aria-labelledby="updateContactModal" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="">Update Contact</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <form id="form-update-contact">
	                <div class="modal-body">      
	                	<input type="hidden" name="contact_id">                   
	                    <div class="form-group">
	                        <label>Label</label>
	                        <input type="text" class="form-control" name="contact_number_label">
	                    </div>
	                    <div class="form-group">
	                        <label>Number (#+63950 1234 567)</label>
	                        <input type="text" class="form-control" name="contact_number">
	                    </div>           
	                </div>
	                <div class="modal-footer">
	                	@if(\Auth::user()->role === 'admin')
						<button type="button" class="btn btn-danger float-left btn-delete-contact" data-id>Delete</button>
	                    @endif
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	                    <button type="button" class="btn btn-primary btn-update-contact">Save</button>
	                </div>
	                </form>
	            </div>
	        </div>
	    </div>

	    <div class="modal fade" id="addParentModal" tabindex="-1" role="dialog" aria-labelledby="addParentModal" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="exampleModalLabel">Add Parent</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
	                <form id="form-add-parent">
	                <div class="modal-body">       
	                	<input type="hidden" name="parent_student_id" value="{{ $id }}">       
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="parent_fname">
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="parent_mname">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="parent_lname">
                        </div>        
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	                    <button type="button" class="btn btn-primary btn-add-parent">Save</button>
	                </div>
	                </form>
	            </div>
	        </div>
	    </div>

	    <div class="modal fade" id="updateParentModal" tabindex="-1" role="dialog" aria-labelledby="updateParentModal" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <h5 class="modal-title" id="">Update Parent</h5>
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                    </button>
	                </div>
					<form id="form-update-parent">
					<div class="modal-body">     
						<input type="hidden" name="parent_id">      
							<div class="form-group">
								<label>First Name</label>
								<input type="text" class="form-control" name="parent_fname">
							</div>
							<div class="form-group">
								<label>Middle Name</label>
								<input type="text" class="form-control" name="parent_mname">
							</div>
							<div class="form-group">
								<label>Last Name</label>
								<input type="text" class="form-control" name="parent_lname">
							</div>
							<hr>
							<div class="form-group">
								<label>Username</label>
								<input type="text" class="form-control" name="parent_username" id="parent-username-edit">
							</div>
							<div class="form-group">
								<label>Password</label>
								<input type="text" class="form-control" name="parent_password" id="parent-password-edit" readonly>
								<button type="button" class="btn btn-warning btn-sm mt-2" id="reset-parent-password-edit">Reset Password to Username</button>
							</div>
	                </div>
	                <div class="modal-footer">
						<button type="button" class="btn btn-danger float-left btn-delete-parent" data-id>Delete</button>
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	                    <button type="button" class="btn btn-primary btn-update-parent">Save</button>
	                </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js" integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg==" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $.noConflict();
            $('#list_appointments').DataTable();
			var creator = "{{ Auth::user()->name }}";
            var student = "<?php echo $student->student_fname .' '. $student->student_mname .' '. $student->student_lname .' (#'.$student->student_number.') '. $student->course_name .' - ('. $student->student_year .') Year / '. $student->block_name ?>";
		    var table = $('#list').DataTable( {
		        lengthChange: false,
		        buttons: [
		            {
		                extend: 'print',
		                name: 'printViolations',
		                messageTop: function () {
		                	return '<h3  style="margin:50px 0;">'+student+'</h3>';
		                },
		                messageBottom: function () {
		                	return '<h6 class="float-left">Print Date: '+new Date()+'</h6><h6 class="float-right" style="margin-top:200px;">Printed by: '+creator+'</h6>';
		                },
				        exportOptions: {
				            columns: ':visible:not(:last-child)'
				        }
		            },
		          //   {
		          //       extend: 'print',
		          //       name: 'printViolationsLetter',
		          //       messageTop: function () {
		          //       	return '<h3  style="margin:50px 0;">'+student+'</h3>';
		          //       },
		          //       messageBottom: function () {
		          //       	return '<h6 class="float-left">Print Date: '+new Date()+'</h6><h6 class="float-right" style="margin-top:200px;">Printed by: '+creator+'</h6>';
		          //       },
				        // exportOptions: {
				        //     columns: ':visible:not(:last-child)'
				        // }
		          //   },

	            ]
		    } );
		 
		    table.buttons().container()
		        .appendTo( '#list_wrapper .col-md-6:eq(0)' );
        } );

        $(function(){
            
        function printData()
		{

		   var divToPrint=document.getElementById("printLetterContainer");
		   // ...existing code...
		   newWin= window.open("");
		   newWin.document.write(divToPrint.outerHTML);
		   newWin.print();
		   newWin.close();
		}

        function printAppointment()
		{

		   var divToPrint=document.getElementById("printAppointmentContainer");
		   // ...existing code...
		   newWin= window.open("");
		   newWin.document.write(divToPrint.outerHTML);
		   newWin.print();
		   newWin.close();
		}

		$('.printLetter').on('click',function(){
		printData();
		})   
		$('.printAppointment').on('click',function(){
			var _id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/get-student-appointment/id/' + _id,
                dataType: 'json',
                success:function(data){
                    var d = new Date(data.appointment_date) 
					// ...existing code...
                    var date = moment(data.appointment_date).format('dddd, MMMM Do YYYY, h:mm:ss a')
                    $('#setAppointmentDate').empty().append('Reason: ' + data.appointment_reason + ' at ' + date)                 
					printAppointment();
                },
                error:function(data){
				  // ...existing code...
                }
            });
		})   
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });   
            $('.btn-add-student-violation').click(function(e){
				e.preventDefault();
				var form = $('#form-add-student-violation');
				var raw = form.serializeArray();
				var data = {};
				// No required fields except violation_type (which can be blank)
				raw.forEach(function(item) {
					if(item.name === 'student_id') data['student_id'] = item.value;
					else if(item.name === 'violation_type') data['violation_type_id'] = item.value;
					else if(item.name === 'violation_remarks') data['violation_remarks'] = item.value || '';
					else if(item.name === 'violation_actions') data['violation_actions'] = item.value || '';
					else if(item.name === 'violation_created_by') data['violation_created_by'] = item.value;
				});
				// If violation_type is blank, still allow save
				if(!('violation_type_id' in data)) data['violation_type_id'] = '';
				if(!('violation_remarks' in data)) data['violation_remarks'] = '';
				if(!('violation_actions' in data)) data['violation_actions'] = '';
				data['violation_status'] = 'pending';
				$.ajax({
					type: 'POST',
					url: '/add-student-violation',
					data: data,
					dataType: 'json',
					success:function(data){
						location.reload(true);
					},
					error:function(data){
						// ...existing code...
					}
				});
			});
            $('.btn-get-student-violations').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
						// ...existing code...
				$.ajax({
					type: 'GET',
					url: '/get-student-violation/'+id,
					dataType: 'json',
					success:function(data){
						if(!data || !data.id) {
							alert('Error: Violation record not found or missing ID. Please refresh the page or contact support.');
							return;
						}
						$('#form-update-student-violation [name=id]').val(data.id);
						$('#form-update-student-violation .btn-delete-violation').data('id', data.id);
						$('#form-update-student-violation [name=violation_type]').val(data.violation_type).change();
						$('#form-update-student-violation [name=violation_remarks]').val(data.violation_remarks);
						$('#form-update-student-violation [name=violation_status]').val(data.violation_status);
						$('#form-update-student-violation [name=violation_actions]').val(data.violation_actions);
						// location.reload(true);
					},
					error:function(data){
						// ...existing code...
					}
				});
            });
            $('.btn-update-student-violation').click(function(e){
				e.preventDefault();
				var form = $('#form-update-student-violation');
				var raw = form.serializeArray();
				var data = {};
				var valid = true;
				raw.forEach(function(item) {
					if(item.name === 'violation_type' && (item.value === '' || isNaN(item.value))) {
						alert('Please select a valid violation type.');
						valid = false;
					}
					if(item.name === 'id') data['id'] = item.value;
					else if(item.name === 'student_id') data['student_id'] = item.value;
					else if(item.name === 'violation_type') data['violation_type_id'] = item.value;
					else if(item.name === 'violation_remarks') data['violation_remarks'] = item.value;
					else if(item.name === 'violation_status') data['violation_status'] = item.value;
					else if(item.name === 'violation_actions') data['violation_actions'] = item.value;
					else if(item.name === 'violation_created_by') data['violation_created_by'] = item.value;
				});
				if(!valid) return;
				$.ajax({
					type: 'POST',
					url: '/update-student-violation',
					data: data,
					dataType: 'json',
					success:function(data){
						// ...existing code...
						location.reload(true);
					},
					error:function(data){
					  // ...existing code...
					}
				});
            });
            $('.btn-delete-violation').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
						// ...existing code...
                        
                $.ajax({
                    type: 'POST',
                    url: '/delete-student-violation/'+id,
                    dataType: 'json',
                    success:function(data){
						// ...existing code...
                        location.reload(true);
                    },
                    error:function(data){
					  // ...existing code...
                    }
                });
            });
            $('.btn-add-parent').click(function(e){
                e.preventDefault();
                var data = $('#form-add-parent').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/add-parent',
                    data: data,
                    dataType: 'json',
                    success:function(data){
						// ...existing code...
                        location.reload(true);
                    },
                    error:function(data){
					  // ...existing code...
                    }
                });
            });
            $('.btn-get-parent').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
								$.ajax({
										type: 'GET',
										url: '/get-parent/'+id,
										dataType: 'json',
										success:function(data){
												$('#form-update-parent [name=parent_id]').val(data.parent_id);
												$('#form-update-parent .btn-delete-parent').data('id', data.parent_id);
												$('#form-update-parent [name=parent_fname]').val(data.parent_fname);
												$('#form-update-parent [name=parent_mname]').val(data.parent_mname);
												$('#form-update-parent [name=parent_lname]').val(data.parent_lname);
												// Populate username and password fields
												$('#parent-username-edit').val(data.parent_username);
												$('#parent-password-edit').val(data.parent_password);
												// location.reload(true);
										},
										error:function(data){
											// ...existing code...
										}
								});
            });
            $('.btn-update-parent').click(function(e){
                e.preventDefault();
                var data = $('#form-update-parent').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-parent',
                    data: data,
                    dataType: 'json',
                    success:function(data){
						// ...existing code...
                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-delete-parent').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: '/delete-parent/'+id,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-add-contact').click(function(e){
                e.preventDefault();
                var data = $('#form-add-contact').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/add-contact',
                    data: data,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-get-contact').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '/get-contact/'+id,
                    dataType: 'json',
                    success:function(data){

                        $('#form-update-contact [name=contact_id]').val(data.contact_id);
                        $('#form-update-contact .btn-delete-contact').data('id', data.contact_id);
                        $('#form-update-contact [name=contact_number_label]').val(data.contact_number_label);
                        $('#form-update-contact [name=contact_number]').val(data.contact_number);

                        // location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-update-contact').click(function(e){
                e.preventDefault();
                var data = $('#form-update-contact').serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '/update-contact',
                    data: data,
                    dataType: 'json',
                    success:function(data){

                        location.reload(true);
                    },
                    error:function(data){

                    }
                });
            });
            $('.btn-delete-contact').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                
                var c = confirm('Are you sure?');
                if (c === true)
                {
	                $.ajax({
	                    type: 'POST',
	                    url: '/delete-contact/'+id,
	                    dataType: 'json',
	                    success:function(data){

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