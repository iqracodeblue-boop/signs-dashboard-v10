@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #0B1120 !important; font-family: 'Inter', system-ui, sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); transition: all 0.3s ease; }
    .premium-card:hover { border-color: rgba(59, 130, 246, 0.3) !important; box-shadow: 0 15px 30px rgba(0,0,0,0.4), 0 0 0 1px rgba(59, 130, 246, 0.1); }
    .stat-card-big { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(17, 25, 40, 0.6)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 24px; display: flex; align-items: center; justify-content: space-between; height: 100%; }
    .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 8px 15px rgba(0,0,0,0.3); }
    .gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .gradient-2 { background: linear-gradient(135deg, #0093E9 0%, #80D0C7 100%); color: #fff; }
    .gradient-3 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #fff; }
    .stat-card-sm { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(17, 25, 40, 0.6)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 16px; display: flex; align-items: center; gap: 16px; }
    .stat-icon-sm { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .gradient-4 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; }
    .gradient-5 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; }
    .gradient-6 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #fff; }
    .chart-container { position: relative; height: 340px; width: 100%; }
    .text-muted { color: #64748b !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Dashboard Overview</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">Real-time analytics & lead management metrics</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.leads.index') }}" class="btn btn-lg px-4 rounded-pill text-white d-flex align-items-center" style="background: linear-gradient(135deg, #10B981, #059669); border: none; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
            <i class="bi bi-people-fill me-2"></i> Leads
        </a>
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-lg px-4 rounded-pill text-white d-flex align-items-center" style="background: linear-gradient(135deg, #3B82F6, #2563EB); border: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
            <i class="bi bi-graph-up-arrow me-2"></i> Analytics
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card-big">
            <div>
                <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Total Visitors Tracked</p>
                <h2 class="fw-bold mb-0 text-white">{{ $totalVisitors ?? 0 }}</h2>
            </div>
            <div class="stat-icon gradient-1"><i class="bi bi-people-fill"></i></div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card-big">
            <div>
                <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Total Page Views</p>
                <h2 class="fw-bold mb-0 text-white">{{ $totalPageViews ?? 0 }}</h2>
            </div>
            <div class="stat-icon gradient-2"><i class="bi bi-eye-fill"></i></div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card-big">
            <div>
                <p class="text-muted mb-1 fw-semibold" style="font-size: 14px;">Total Clicks & Events</p>
                <h2 class="fw-bold mb-0 text-white">{{ $totalEvents ?? 0 }}</h2>
            </div>
            <div class="stat-icon gradient-3"><i class="bi bi-mouse-fill"></i></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12 mb-3">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Leads Generation Trend (Last 7 Days)</h5>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-graph-up-arrow me-1"></i> Live Tracking</span>
            </div>
            <div class="chart-container" style="height: 350px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Leads & User Engagement</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Live Data</span>
            </div>
            <div class="chart-container"><canvas id="engagementChart"></canvas></div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="premium-card p-4 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Device Breakdown</h5>
                <i class="bi bi-pie-chart-fill text-muted"></i>
            </div>
            <div class="chart-container flex-grow-1 d-flex align-items-center justify-content-center"><canvas id="deviceChart"></canvas></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm gradient-4"><i class="bi bi-file-earmark-text-fill"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 14px;">Total Leads</p><h4 class="mb-0 fw-bold text-white">{{ $totalLeads }}</h4></div></div></div>
    <div class="col-md-4 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm gradient-5"><i class="bi bi-calendar-check-fill"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 14px;">Today's Leads</p><h4 class="mb-0 fw-bold text-white">{{ $todayLeads }}</h4></div></div></div>
    <div class="col-md-4 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm gradient-6"><i class="bi bi-laptop-fill"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 14px;">Desktop / Mobile</p><h4 class="mb-0 fw-bold text-white">{{ $desktopLeads }} / {{ $mobileLeads }}</h4></div></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx3 = document.getElementById('trendChart').getContext('2d');
    const gradient = ctx3.createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: {{ Js::from($trendDates) }},
            datasets: [{
                label: 'Total Leads',
                data: {{ Js::from($trendCounts) }},
                borderColor: '#3B82F6',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#3B82F6',
                pointBorderColor: '#fff',
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#3B82F6',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(11, 17, 32, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(59, 130, 246, 0.2)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return ` Leads: ${context.raw}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { color: '#64748b', font: { weight: '500' }, stepSize: 1 }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#64748b', font: { weight: '500' } }
                }
            }
        }
    });

    const ctx1 = document.getElementById('engagementChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Visitors', 'Page Views', 'Events', 'Total Leads', 'Today Leads'],
            datasets: [{
                label: 'Count',
                data: [{{ $totalVisitors ?? 0 }}, {{ $totalPageViews ?? 0 }}, {{ $totalEvents ?? 0 }}, {{ $totalLeads ?? 0 }}, {{ $todayLeads ?? 0 }}],
                backgroundColor: ['#667eea', '#0093E9', '#fda085', '#f5576c', '#4facfe'],
                borderRadius: 10,
                barThickness: 45
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b', font: { weight: '500' } } },
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { weight: '500' } } }
            }
        }
    });

    const ctx2 = document.getElementById('deviceChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Desktop Users', 'Mobile Users'],
            datasets: [{
                data: [{{ $desktopLeads ?? 0 }}, {{ $mobileLeads ?? 0 }}],
                backgroundColor: ['#4facfe', '#43e97b'],
                borderWidth: 4,
                borderColor: '#111927'
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { color: '#64748b', font: { weight: '500' }, padding: 20 } }
            }
        }
    });
</script>
@endsection