@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Parent Dashboard</h2>
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <strong>👨‍🎓 Linked Students</strong>
        </div>
        <div class="card-body" id="linked-students">
            <div class="text-muted">No students linked to this account.</div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <strong>📩 Messages</strong>
        </div>
        <div class="card-body" id="parent-messages">
            <div class="text-muted">No messages at this time.</div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>📅 Monthly Reports</strong>
        </div>
        <div class="card-body" id="monthly-reports">
            <div class="text-muted">No monthly reports available.</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load linked students
    $.get('/parent/linked-students', function(data) {
        if (data.length > 0) {
            let html = '<div class="row">';
            data.forEach(function(student) {
                html += `<div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">${student.student_fname} ${student.student_mname} ${student.student_lname}</h5>
                            <p class="card-text mb-1"><strong>Course:</strong> ${student.course_name || ''}</p>
                            <p class="card-text mb-1"><strong>Block:</strong> ${student.block_name || ''}</p>
                            <p class="card-text mb-1"><strong>Status:</strong> ${student.student_status}</p>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            $('#linked-students').html(html);
        }
    });
    // Load parent messages (formal violation messages)
    $.get('/parent/messages', function(data) {
        if (data.length > 0) {
            let html = '';
            data.forEach(function(msg) {
                html += `<div class="alert alert-danger mb-3">
                    <strong>Message:</strong> ${msg.message}
                </div>`;
            });
            $('#parent-messages').html(html);
        }
    });
    // Load monthly reports
    $.get('/parent/monthly-reports', function(data) {
        if (data.length > 0) {
            let html = `<div class="table-responsive"><table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Student</th>
                        <th>Month</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>`;
            data.forEach(function(report) {
                html += `<tr>
                    <td>${report.student_name}</td>
                    <td>${report.month ? report.month : '-'}</td>
                    <td>${report.message}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            $('#monthly-reports').html(html);
        }
    });
});
</script>
@endpush
