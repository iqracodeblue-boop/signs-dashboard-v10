@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #060818 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .stat-card-sm { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(17, 25, 40, 0.6)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%; }
    .stat-icon-sm { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .g-1 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; }
    .g-2 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; }
    .g-3 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #fff; }
    .chart-container { position: relative; height: 320px; width: 100%; }
    .table { background-color: transparent !important; color: #ffffff !important; --bs-table-bg: transparent !important; --bs-table-color: #ffffff !important; margin-bottom: 0; }
    .premium-table thead th { background-color: transparent !important; color: #7c8db5 !important; border-bottom: 2px solid rgba(255, 255, 255, 0.08) !important; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 16px; font-weight: 700; }
    .premium-table tbody tr { background-color: transparent !important; border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important; }
    .premium-table tbody tr:hover { background-color: rgba(59, 130, 246, 0.03) !important; }
    .premium-table tbody td { color: #ffffff !important; vertical-align: top; }
    .data-box { background: rgba(79, 172, 254, 0.05); border: 1px solid rgba(79, 172, 254, 0.15); border-radius: 8px; padding: 8px 12px; margin-bottom: 5px; display: inline-block; margin-right: 5px; }
    .text-muted { color: #64748b !important; }
    .text-white { color: #ffffff !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Abandoned Forms</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">Users who typed their data but didn't hit submit</p>
    </div>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-1"><i class="bi bi-x-octagon"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Total Abandoned</p><h4 class="mb-0 fw-bold text-white">{{ $totalAbandoned }}</h4></div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-2"><i class="bi bi-check2-square"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Total Submitted</p><h4 class="mb-0 fw-bold text-white">{{ $totalSubmitted }}</h4></div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-3"><i class="bi bi-percent"></i></div>
            <div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Abandon Rate</p><h4 class="mb-0 fw-bold text-white">{{ $abandonedRate }}%</h4></div>
        </div>
    </div>
</div>

<!-- Graph Row -->
<div class="row mb-5">
    <div class="col-md-6 mb-3">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Submission Status</h5>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Lost Leads</span>
            </div>
            <div class="chart-container"><canvas id="statusChart"></canvas></div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="premium-card p-4 h-100 d-flex align-items-center justify-content-center">
            <div class="text-center">
                <i class="bi bi-keyboard-fill text-primary" style="font-size: 40px; opacity: 0.5;"></i>
                <h1 class="display-4 fw-bold text-white mb-0 mt-2">{{ $fieldsFilledCount }}</h1>
                <p class="text-muted mt-2 mb-0">Data Entries Captured from Users</p>
            </div>
        </div>
    </div>
</div>

<!-- Abandoned Data Table -->
<div class="premium-card p-4 mb-5">
    <h5 class="fw-bold text-white mb-4">Abandoned Form Details</h5>
    <div class="table-responsive">
        <table class="table premium-table align-middle">
            <thead>
                <tr>
                    <th>Session ID</th>
                    <th>IP Address</th>
                    <th>Device / OS</th>
                    <th>Data Typed by User</th>
                    <th>Lead Status</th>
                    <th>Last Activity</th>
                </tr>
            </thead>
            <tbody>
                @if($abandonedData->isEmpty())
                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox d-block mb-2" style="font-size: 40px; opacity: 0.5;"></i>No abandoned forms found yet.</td></tr>
                @else
                    @foreach($abandonedData as $sessionId => $events)
                        @php
                            $firstEvent = $events->first();
                            $visitorId = $firstEvent->visitor_id ?? 'N/A';
                            $visitor = $visitors[$visitorId] ?? null;
                            $lastEvent = $events->last();
                            
                            // Check if converted to lead
                            $convertedLead = $convertedLeads->get($sessionId);
                        @endphp
                        <tr>
                            <td class="text-white fw-bold">{{ $sessionId }}</td>
                            <td class="text-white">{{ $visitor->ip_address ?? 'N/A' }}</td>
                            <td class="text-white"><i class="bi bi-display"></i> {{ $visitor->device ?? 'N/A' }}<br><small class="text-muted">{{ $visitor->os ?? 'N/A' }}</small></td>
                            <td>
                                @foreach($events as $event)
                                    @php $metadata = json_decode($event->metadata, true); @endphp
                                    <div class="data-box">
                                        <small class="text-info fw-bold text-capitalize">{{ $metadata['field_name'] ?? 'Unknown Field' }}:</small>
                                        <span class="text-success fw-bold ms-1">"{{ $metadata['field_value'] ?? '' }}"</span>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @if($convertedLead)
                                    <a href="{{ route('admin.leads.show', $convertedLead->id) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                        <i class="bi bi-check2-circle me-1"></i> Converted to Lead
                                    </a>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Abandoned</span>
                                @endif
                            </td>
                            <td class="text-warning fw-semibold">{{ \Carbon\Carbon::parse($lastEvent->created_at)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx1 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Abandoned', 'Submitted'],
            datasets: [{
                data: [{{ $totalAbandoned }}, {{ $totalSubmitted }}],
                backgroundColor: ['#f5576c', '#43e97b'],
                borderWidth: 4, borderColor: '#111927'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom', labels: { color: '#64748b', font: { weight: '500', size: 14 }, padding: 20 } } } }
    });
</script>
@endsection