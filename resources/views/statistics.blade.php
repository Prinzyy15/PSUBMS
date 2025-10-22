@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css"/>
@endpush

@section('content')
<div class="container py-4">
    <div class="glass-panel shadow rounded-4 border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0" style="color:#2563eb;font-weight:700;">Analytics</h2>
                <div class="chart-controls" role="tablist" aria-label="Chart range controls">
                    <button class="btn btn-sm" data-range="week" role="tab" aria-pressed="false">Week</button>
                    <button class="btn btn-sm active" data-range="month" role="tab" aria-pressed="true">Month</button>
                    <button class="btn btn-sm" data-range="year" role="tab" aria-pressed="false">Year</button>
                </div>
            </div>
            <div class="chart-canvas-wrap d-flex justify-content-center align-items-center">
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
    var canvas = document.getElementById('myChart');
    var ctx = canvas.getContext('2d');
    // safe JSON echo to avoid parser issues
    var chartLabels = <?php echo json_encode($years); ?>;
    var chartDatasets = <?php echo json_encode($violationsDatasetsCntPerYear); ?>;

    function createGradient(ctx, height){
        var g = ctx.createLinearGradient(0, 0, 0, height);
        g.addColorStop(0, 'rgba(37,99,235,0.36)');
        g.addColorStop(0.6, 'rgba(6,182,212,0.18)');
        g.addColorStop(1, 'rgba(255,255,255,0.02)');
        return g;
    }

    // normalize datasets: set type to 'line' and apply gradient
    if(Array.isArray(chartDatasets)){
        chartDatasets.forEach(function(ds){
            ds.type = 'line';
            ds.tension = ds.tension || 0.4; // smooth curve
            ds.pointRadius = ds.pointRadius || 3;
            ds.borderWidth = ds.borderWidth || 2;
            ds.fill = true;
            if(!ds.backgroundColor){
                // use canvas height so gradient scales responsively
                var h = canvas.height || 400;
                ds.backgroundColor = createGradient(ctx, h);
            }
            if(!ds.borderColor){ ds.borderColor = ds.borderColor || '#00BFFF'; }
        });
    }

    var myChart = new Chart(ctx, {
        type: 'line',
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
                    },
                }],
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Period'
                    }
                }],
            },
            legend: {
                position: 'top',
            },
        }
    });

    // client-side control wiring: fetch datasets for selected range and update chart
    $('.chart-controls .btn').on('click', function(){
        var range = $(this).data('range');
        $('.chart-controls .btn').removeClass('active');
        $(this).addClass('active');
        // accessibility: update aria-pressed
        $('.chart-controls .btn').attr('aria-pressed', 'false');
        $(this).attr('aria-pressed', 'true');
        // fetch data from server
        $.ajax({
            url: '/statistics/data',
            data: { range: range },
            dataType: 'json',
            success: function(res){
                if(!res || !res.labels || !res.datasets) return;
                // map datasets to Chart.js format and apply gradients
                var datasets = res.datasets.map(function(ds, idx){
                    var color = ds.borderColor || '#00BFFF';
                    var background = createGradient(ctx, canvas.height || 400);
                    return {
                        label: ds.label,
                        data: ds.data,
                        type: 'line',
                        borderColor: color,
                        backgroundColor: background,
                        tension: 0.4,
                        pointRadius: 3,
                        borderWidth: 2,
                        fill: true
                    };
                });
                myChart.data.labels = res.labels;
                myChart.data.datasets = datasets;
                myChart.update();
            },
            error: function(err){ console.log('Failed to load stats data', err); }
        });
    });
});
</script>
@endpush