@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Monthly Behavior Reports</h2>
    <a href="{{ route('monthly-reports.create') }}" class="btn btn-primary mb-3">Submit New Report</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div aria-live="polite" aria-atomic="true" class="position-relative">
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
                <div id="warningToast" class="toast align-items-center text-bg-warning border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <strong>Warning:</strong> {{ session('warning') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('warningToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
        </script>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Month</th>
                <th>Submitted By</th>
                <th>Report</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->month }}</td>
                <td>{{ $report->admin_id }}</td>
                <td>{{ $report->report }}</td>
                <td>{{ $report->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
