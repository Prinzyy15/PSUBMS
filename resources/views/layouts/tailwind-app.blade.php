<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BMS - Tailwind Prototype</title>
  <link href="{{ asset('css/tailwind.css') }}" rel="stylesheet">
  @if(config('ui.theme_enabled'))
    <link href="{{ asset('css/theme-dashboard.css') }}?v={{ filemtime(public_path('css/theme-dashboard.css')) }}" rel="stylesheet">
  @endif
</head>
<body class="antialiased">
  <div class="min-h-screen flex bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <aside class="w-72 bg-gradient-to-b from-primary to-indigo-900 text-white p-6 hidden md:block">
      <div class="mb-8 text-2xl font-bold">BMS</div>
      <nav class="space-y-2">
        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10">Dashboard</a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10">Behavior Reports</a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10">Students</a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10">Analytics</a>
        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10">Settings</a>
      </nav>
      <div class="mt-auto pt-6">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-white/20 rounded-full"></div>
          <div>
            <div class="font-semibold">Jane Doe</div>
            <div class="text-sm opacity-80">Admin</div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-6">
      <header class="flex items-center gap-4 mb-6">
        <div class="flex-1">
          <input class="w-full max-w-xl rounded-full border border-gray-200 p-3 shadow-sm" placeholder="Search..." />
        </div>
        <div class="flex items-center gap-4">
          <button id="darkToggle" class="p-2 rounded-full bg-white shadow">☾</button>
          <div class="w-10 h-10 rounded-full bg-gray-200"></div>
        </div>
      </header>

      <section class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <div class="card">
          <div class="text-sm text-gray-500">Students Monitored</div>
          <div class="text-3xl font-extrabold mt-2">245</div>
        </div>
        <div class="card">
          <div class="text-sm text-gray-500">Incidents Logged</div>
          <div class="text-3xl font-extrabold mt-2">56</div>
        </div>
        <div class="card">
          <div class="text-sm text-gray-500">Behavior Progress</div>
          <div class="text-3xl font-extrabold mt-2">78%</div>
        </div>
        <div class="card">
          <div class="text-sm text-gray-500">Pending Interventions</div>
          <div class="text-3xl font-extrabold mt-2">12</div>
        </div>
      </section>

      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-2 card">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Analytics</h3>
            <div class="flex gap-2">
              <button class="px-3 py-1 rounded-lg bg-gray-100">Week</button>
              <button class="px-3 py-1 rounded-lg bg-primary text-white">Month</button>
              <button class="px-3 py-1 rounded-lg bg-gray-100">Year</button>
            </div>
          </div>
          <div class="h-64 bg-gradient-to-b from-white to-gray-100 rounded-lg"></div>
        </div>

        <div class="card">
          <h3 class="text-lg font-bold mb-4">Overview</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <div class="text-sm text-gray-500">Positive</div>
              <div class="font-semibold">150</div>
            </div>
            <div class="flex justify-between">
              <div class="text-sm text-gray-500">Negative</div>
              <div class="font-semibold">45</div>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-6">
        @yield('content')
      </section>
    </main>
  </div>
  <!-- Toast container -->
  <div id="toast-container" class="fixed bottom-6 right-6 space-y-3 z-50"></div>

  <script>
    // Dark mode toggle (persist in localStorage)
    (function(){
      var btn = document.getElementById('darkToggle');
      var root = document.documentElement;
      var stored = localStorage.getItem('bms-dark');
      if(stored === '1') root.classList.add('dark');
      btn && btn.addEventListener('click', function(){
        root.classList.toggle('dark');
        if(root.classList.contains('dark')) localStorage.setItem('bms-dark','1'); else localStorage.removeItem('bms-dark');
      });

      // simple toast helper
      window.showToast = function(message, type){
        var container = document.getElementById('toast-container');
        var el = document.createElement('div');
        el.className = 'px-4 py-2 rounded-lg shadow-md bg-white';
        if(type === 'error') el.className += ' border-l-4 border-red-500';
        if(type === 'success') el.className += ' border-l-4 border-green-500';
        el.textContent = message;
        container.appendChild(el);
        setTimeout(function(){ el.classList.add('opacity-0'); setTimeout(function(){ container.removeChild(el); },400); }, 3500);
      }
    })();
  </script>
  <!-- Floating Action Buttons -->
  <div class="fab-stack" style="position:fixed; right:24px; bottom:80px; z-index:60;">
    <button title="New record" class="fab" onclick="showToast('New record action');">
      <!-- plus icon -->
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <button title="Open quick" class="fab" onclick="showToast('Quick open');">
      <!-- grid icon -->
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="7" height="7" rx="1" stroke="#fff" stroke-width="1.4"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="#fff" stroke-width="1.4"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="#fff" stroke-width="1.4"/><rect x="14" y="14" width="7" height="7" rx="1" stroke="#fff" stroke-width="1.4"/></svg>
    </button>
  </div>
</body>
</html>
