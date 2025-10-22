<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Theme Demo</title>
  <link href="{{ asset('css/theme-dashboard.css') }}?v={{ filemtime(public_path('css/theme-dashboard.css')) }}" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{padding:24px;background:transparent;}</style>
</head>
<body>
  <div class="container">
    <h1 class="mb-4" style="color:var(--primary);">UI Theme Demo</h1>

    <div class="metric-row mb-4">
      <div class="metric-card small position-relative" style="flex:1;">
        <div class="metric-number">1,234</div>
        <div class="metric-label">Active Students</div>
        <div class="corner-icon">★</div>
      </div>
      <div class="metric-card small position-relative" style="flex:1;">
        <div class="metric-number">56</div>
        <div class="metric-label">Open Incidents</div>
        <div class="corner-icon">⚠️</div>
      </div>
    </div>

    <div class="glass-panel mb-4">
      <div class="card-body">
        <h3>Chart area</h3>
        <div class="chart-canvas-wrap"><canvas id="demoChart" width="800" height="300"></canvas></div>
      </div>
    </div>

    <div class="glass-table">
      <table class="table mb-0">
        <thead><tr><th>#</th><th>Name</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>1</td><td>Jane Doe</td><td><span class="badge bg-success">Active</span></td></tr>
          <tr><td>2</td><td>John Smith</td><td><span class="badge bg-secondary">Pending</span></td></tr>
        </tbody>
      </table>
    </div>

    <p class="mt-4 text-muted">If this page looks styled with rounded glass cards, gradients and blue accents, the theme is applied correctly.</p>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js"></script>
  <script>
    try{
      var ctx = document.getElementById('demoChart').getContext('2d');
      new Chart(ctx, {type:'line', data:{labels:['Mon','Tue','Wed','Thu','Fri'], datasets:[{label:'Demo', data:[12,19,8,14,20], borderColor:'#2563eb', backgroundColor:'rgba(37,99,235,0.15)', fill:true}]}, options:{responsive:true, maintainAspectRatio:false}});
    }catch(e){console.warn('Chart init failed',e)}
  </script>
</body>
</html>
