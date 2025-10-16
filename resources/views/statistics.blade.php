@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css"/>
@endpush

@section('content')
<div class="container py-4">
	<div class="card shadow rounded-4 border-0 mb-4" style="background: #fff;">
		<div class="card-body">
			<h2 class="mb-4" style="color:#2563eb;font-weight:700;">Statistics</h2>
            <div class="d-flex justify-content-center align-items-center" style="min-height:400px;">
                <canvas id="myChart" width="1000" height="400"></canvas>
			</div>
            @if(empty($years) || empty($violationsDatasetsCntPerYear))
                <div class="alert alert-info mt-4 text-center" style="font-size:1.2rem;">No statistics data available for the selected period.</div>
            @endif
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js"></script>
<script>
$(document).ready(function() {
    $.noConflict();
    var ctx = document.getElementById('myChart').getContext('2d');
    var chartLabels = {!! json_encode($years) !!};
    var chartDatasets = {!! json_encode($violationsDatasetsCntPerYear) !!};
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: chartDatasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Number of Students'
                    },
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        stepSize: 1
                    },
                }],
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Months'
                    },
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }],
            },
            legend: {
                position: 'top',
            },
        }
    });
});
</script>
@endpush