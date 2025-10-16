@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Submit Monthly Behavior Report</h2>
    <!-- Toast Notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055; right: 0; top: 0;">
        <div id="reportToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>Report Submitted</strong>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('monthly-reports.store') }}" id="monthlyReportForm">
        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning-subtle">
                        <h5 class="modal-title" id="confirmSubmitModalLabel">Confirm Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <strong>Are you sure you want to submit this monthly behavior report?</strong><br>
                        Please double-check your selections before proceeding.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="confirmSubmitBtn">Yes, Submit</button>
                    </div>
                </div>
            </div>
        </div>
        @csrf
        <div class="form-group">
            <label for="month">Month</label>
            <input type="month" class="form-control" id="month" name="month" required>
        </div>
        <div class="form-group">
            <label>All Students</label>
            <div id="studentsTableWrapper">
                <p>Loading students...</p>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Submit Report</button>
        <a href="{{ route('monthly-reports.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function() {
    // Intercept form submit for confirmation modal
    var confirmed = false;
    $('#monthlyReportForm').on('submit', function(e) {
        if (!confirmed) {
            e.preventDefault();
            var form = this;
            var modal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
            modal.show();
            $('#confirmSubmitBtn').off('click').on('click', function() {
                confirmed = true;
                modal.hide();
                setTimeout(function() { form.submit(); }, 200); // slight delay to allow modal to hide
            });
        } else {
            confirmed = false; // reset for next submit
        }
    });
    // Set default value for month input to current month and year
    var now = new Date();
    var month = (now.getMonth() + 1).toString().padStart(2, '0');
    var year = now.getFullYear();
    $('#month').val(year + '-' + month);

    function loadAllStudents() {
        $('#studentsTableWrapper').html('<p>Loading students...</p>');
        $.ajax({
            url: '/monthly-reports/all-students',
            method: 'GET',
            success: function(data) {
                if (data.students && data.students.length > 0) {
                    let table = '<table class="table table-bordered"><thead><tr>' +
                        '<th><input type="checkbox" id="markAll"></th>' +
                        '<th>Student Number</th><th>Name</th><th>Course</th><th>Year</th><th>Block</th></tr></thead><tbody>';
                    data.students.forEach(function(student) {
                        table += '<tr>' +
                            '<td><input type="checkbox" class="student-checkbox" name="selected_students[]" value="' + student.student_id + '"></td>' +
                            '<td>' + student.student_number + '</td>' +
                            '<td>' + student.student_fname + ' ' + student.student_lname + '</td>' +
                            '<td>' + student.course_name + '</td>' +
                            '<td>' + student.student_year + '</td>' +
                            '<td>' + student.block_name + '</td>' +
                            '</tr>';
                    });
                    table += '</tbody></table>';
                    $('#studentsTableWrapper').html(table);
                } else {
                    $('#studentsTableWrapper').html('<p>No students found.</p>');
                }
            },
            error: function() {
                $('#studentsTableWrapper').html('<p class="text-danger">Failed to load students.</p>');
            }
        });
    }

    loadAllStudents();

    $('#month').on('change', function() {
        loadAllStudents();
    });

    // Mark all functionality
    $(document).on('change', '#markAll', function() {
        $('.student-checkbox').prop('checked', this.checked);
    });
    $(document).on('change', '.student-checkbox', function() {
        if (!this.checked) {
            $('#markAll').prop('checked', false);
        } else if ($('.student-checkbox:checked').length === $('.student-checkbox').length) {
            $('#markAll').prop('checked', true);
        }
    });

    // Show toast on successful form submit (handled by backend redirect)
});
</script>
@endsection
