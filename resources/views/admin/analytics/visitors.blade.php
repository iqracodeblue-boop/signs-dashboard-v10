@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #060818 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .stat-card-sm { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(17, 25, 40, 0.6)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%; }
    .stat-icon-sm { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .g-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .g-2 { background: linear-gradient(135deg, #0093E9 0%, #80D0C7 100%); color: #fff; }
    .g-3 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #fff; }
    .g-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #fff; }
    .chart-container { position: relative; height: 320px; width: 100%; }
    
    .table { background-color: transparent !important; color: #ffffff !important; --bs-table-bg: transparent !important; --bs-table-color: #ffffff !important; margin-bottom: 0; }
    .premium-table thead th { background-color: transparent !important; color: #7c8db5 !important; border-bottom: 2px solid rgba(255, 255, 255, 0.08) !important; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 16px; font-weight: 700; }
    .premium-table tbody tr { background-color: transparent !important; border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important; }
    .premium-table tbody tr:hover { background-color: rgba(59, 130, 246, 0.03) !important; }
    .premium-table tbody td { color: #ffffff !important; vertical-align: middle; }
    
    /* Avatar Styling */
    .visitor-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 16px; flex-shrink: 0; }
    .av-guest { background: linear-gradient(135deg, #475569, #334155); }
    .av-human { background: linear-gradient(135deg, #667eea, #764ba2); }
    .av-bot { background: linear-gradient(135deg, #f5576c, #f093fb); }
    
    .text-muted { color: #64748b !important; font-size: 11px; }
    .text-white { color: #ffffff !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Total Visitors Analytics</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">All unique visitors tracked across the website</p>
    </div>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-1"><i class="bi bi-people-fill"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Total Visitors</p><h4 class="mb-0 fw-bold text-white">{{ $totalVisitors }}</h4></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-2"><i class="bi bi-laptop"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Desktop Users</p><h4 class="mb-0 fw-bold text-white">{{ $desktopUsers }}</h4></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-3"><i class="bi bi-phone"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Mobile Users</p><h4 class="mb-0 fw-bold text-white">{{ $mobileUsers }}</h4></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-4"><i class="bi bi-shield-check"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Human vs Bot</p><h4 class="mb-0 fw-bold text-white">{{ $humanUsers }} / {{ $botUsers }}</h4></div>
        </div>
    </div>
</div>

<!-- Graph Row -->
<div class="row mb-5">
    <div class="col-md-6 mb-3">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Device Breakdown</h5>
                <i class="bi bi-pie-chart-fill text-muted"></i>
            </div>
            <div class="chart-container"><canvas id="deviceChart"></canvas></div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Human vs Bot Traffic</h5>
                <i class="bi bi-shield-fill text-muted"></i>
            </div>
            <div class="chart-container"><canvas id="trafficChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Visitors Table -->
<div class="premium-card p-4 mb-5">
    <h5 class="fw-bold text-white mb-4">All Visitors Data</h5>
    <div class="table-responsive">
        <table class="table premium-table align-middle">
            <thead>
                <tr>
                    <th>Visitor Name / Status</th>
                    <th>IP Address</th>
                    <th>Device / OS</th>
                    <th>Browser</th>
                    <th>First Seen</th>
                    <th>Last Active</th>
                </tr>
            </thead>
            <tbody>
                @if($visitors->isEmpty())
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox d-block mb-2" style="font-size: 40px; opacity: 0.5;"></i>No visitors tracked yet.</td></tr>
                @else
                    @foreach($visitors as $visitor)
                        @php
                            // Logic: Agar form fill kiya hai to naam dikhao, warna Guest
                            $isBot = ($visitor->is_bot == 1 || $visitor->is_bot == '1');
                            
                            if ($isBot) {
                                $displayName = 'Anonymous 🤖';
                                $initial = '?';
                                $avatarClass = 'av-bot';
                            } elseif (!empty($visitor->lead_name)) {
                                $displayName = $visitor->lead_name;
                                $initial = strtoupper(substr($visitor->lead_name, 0, 1));
                                $avatarClass = 'av-human';
                            } else {
                                $displayName = 'Guest';
                                $initial = 'G';
                                $avatarClass = 'av-guest';
                            }
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="visitor-avatar {{ $avatarClass }} me-3">{{ $initial }}</div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-white" style="font-size: 15px;">{{ $displayName }}</h6>
                                        <small class="text-muted">{{ $visitor->visitor_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-white">{{ $visitor->ip_address ?? 'N/A' }}</td>
                            <td class="text-white">{{ $visitor->device ?? 'N/A' }} / {{ $visitor->os ?? 'N/A' }}</td>
                            <td class="text-white">{{ $visitor->browser ?? 'N/A' }}</td>
                            <td class="text-white">{{ isset($visitor->first_seen_at) ? \Carbon\Carbon::parse($visitor->first_seen_at)->format('M d, Y h:i A') : 'N/A' }}</td>
                            <td class="text-warning fw-semibold">{{ isset($visitor->last_seen_at) ? \Carbon\Carbon::parse($visitor->last_seen_at)->diffForHumans() : 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart 1: Device Breakdown
    const ctx1 = document.getElementById('deviceChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Desktop Users', 'Mobile Users'],
            datasets: [{
                data: [{{ $desktopUsers }}, {{ $mobileUsers }}],
                backgroundColor: ['#4facfe', '#43e97b'],
                borderWidth: 4, borderColor: '#111927'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom', labels: { color: '#64748b', font: { weight: '500', size: 14 }, padding: 20 } } } }
    });

    // Chart 2: Human vs Bot Traffic
    const ctx2 = document.getElementById('trafficChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Human Traffic', 'Bot Traffic'],
            datasets: [{
                data: [{{ $humanUsers }}, {{ $botUsers }}],
                backgroundColor: ['#43e97b', '#f5576c'],
                borderWidth: 4, borderColor: '#111927'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom', labels: { color: '#64748b', font: { weight: '500', size: 14 }, padding: 20 } } } }
    });
</script>
@endsection