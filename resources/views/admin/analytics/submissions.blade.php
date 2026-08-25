@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #060818 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .stat-card-sm { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(17, 25, 40, 0.6)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%; }
    .stat-icon-sm { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .g-1 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #000; }
    .g-2 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; }
    .g-3 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #fff; }
    .g-4 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .chart-container { position: relative; height: 320px; width: 100%; }
    .submit-card { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(10, 15, 28, 0.9)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 24px; margin-bottom: 24px; transition: all 0.3s ease; }
    .submit-card:hover { border-color: rgba(67, 233, 123, 0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: translateY(-2px); }
    .user-avatar { width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 22px; color: #000; flex-shrink: 0; box-shadow: 0 4px 10px rgba(67, 233, 123, 0.3); }
    .timeline { list-style: none; padding: 0; margin: 20px 0 0 0; position: relative; }
    .timeline::before { content: ''; position: absolute; top: 10px; bottom: 10px; left: 18px; width: 2px; background: rgba(255,255,255,0.1); }
    .timeline-item { position: relative; padding-left: 50px; margin-bottom: 25px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; left: 0; top: 0; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; z-index: 2; }
    .ic-view { background: #4facfe; color: #000; }
    .ic-click { background: #fda085; color: #000; }
    .ic-submit { background: #43e97b; color: #000; }
    .text-muted { color: #64748b !important; }
    .text-white { color: #ffffff !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Submitted Forms Analytics</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">Complete journey of users who successfully submitted forms</p>
    </div>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm g-1"><i class="bi bi-check2-circle"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Total Submitted</p><h4 class="mb-0 fw-bold text-white">{{ $totalSubmitted }}</h4></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm g-2"><i class="bi bi-calendar-day"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Today's Submitted</p><h4 class="mb-0 fw-bold text-white">{{ $todaySubmitted }}</h4></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm g-3"><i class="bi bi-percent"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Conversion Rate</p><h4 class="mb-0 fw-bold text-white">{{ $conversionRate }}%</h4></div></div></div>
    <div class="col-md-3 mb-3"><div class="stat-card-sm"><div class="stat-icon-sm g-4"><i class="bi bi-laptop"></i></div><div><p class="text-muted mb-0 fw-semibold" style="font-size: 13px;">Desktop / Mobile</p><h4 class="mb-0 fw-bold text-white">{{ $desktopSubmitted }} / {{ $mobileSubmitted }}</h4></div></div></div>
</div>

<!-- Graph Row -->
<div class="row mb-5">
    <div class="col-md-12 mb-3">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-white">Submission Engagement</h5>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Won Leads</span>
            </div>
            <div class="chart-container"><canvas id="submitChart"></canvas></div>
        </div>
    </div>
</div>

<!-- Submitted Users List -->
<div class="premium-card p-4 mb-5">
    <h5 class="fw-bold text-white mb-4">User Submission Journeys</h5>
    
    @if($submittedLeads->isEmpty())
        <div class="text-center py-5"><i class="bi bi-inbox text-muted" style="font-size: 50px; opacity: 0.5;"></i><h5 class="text-white mt-3">No Submissions Found</h5></div>
    @else
        @foreach($submittedLeads as $lead)
            @php
                $sessionId = $lead->session_id;
                $activities = $allActivities[$sessionId] ?? collect([]);
                $visitorId = $lead->visitor_id ?? 'N/A';
                $visitor = $visitors[$visitorId] ?? null;
                $initial = strtoupper(substr($lead->name ?? 'U', 0, 1));
                $isBot = ($lead->is_spam == 1 || $lead->is_spam == '1');
            @endphp
            <div class="submit-card">
                <div class="d-flex justify-content-between flex-wrap">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <div class="user-avatar">{{ $initial }}</div>
                        <div class="ms-3">
                            <h6 class="fw-bold text-white mb-1">{{ $lead->name }} @if($isBot)<span class="badge bg-danger ms-2">Bot 🤖</span>@endif</h6>
                            <div style="font-size: 13px; color: #64748b;"><i class="bi bi-envelope me-1"></i> {{ $lead->email }} | <i class="bi bi-phone me-1"></i> {{ $lead->phone }}</div>
                            <div style="font-size: 12px; color: #475569; margin-top: 5px;"><i class="bi bi-fingerprint"></i> {{ $sessionId }}</div>
                        </div>
                    </div>
                    <div class="text-end d-flex flex-column align-items-end justify-content-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill"><i class="bi bi-clock-check me-1"></i> Submitted {{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</span>
                        <!-- LINK TO LEADS MANAGEMENT -->
                        <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn btn-sm btn-primary rounded-pill px-3" style="background: var(--accent); border: none; color: #fff;">
                            <i class="bi bi-arrow-right-short me-1"></i> View in Leads Management
                        </a>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#journey-{{ $sessionId }}" aria-expanded="false" aria-controls="journey-{{ $sessionId }}">
                            <i class="bi bi-eye-fill me-1"></i> View Full Journey
                        </button>
                    </div>
                </div>
                
                <!-- Collapsible Journey -->
                <div class="collapse" id="journey-{{ $sessionId }}">
                    <div class="mt-4 p-3" style="background: rgba(0,0,0,0.2); border-radius: 12px;">
                        <ul class="timeline">
                            @foreach($activities as $event)
                                @php
                                    $eventName = $event->event_name ?? 'page_view';
                                    if($eventName == 'heartbeat') continue;
                                    $pageTitle = $event->page_title ?? (json_decode($event->metadata ?? '[]', true)['title'] ?? 'N/A');
                                    if(empty($pageTitle) || $pageTitle == 'test' || $pageTitle == 'N/A') {
                                        $path = parse_url($event->page_url ?? '', PHP_URL_PATH);
                                        $pageTitle = ($path == '/' || empty($path)) ? 'Home Page' : ucwords(str_replace(['/', '-', '_'], ' ', trim($path, '/')));
                                    }
                                @endphp
                                <li class="timeline-item">
                                    @if($eventName == 'page_view') <div class="timeline-icon ic-view"><i class="bi bi-eye-fill"></i></div>
                                    @elseif($eventName == 'cta_click') <div class="timeline-icon ic-click"><i class="bi bi-hand-index-thumb-fill"></i></div>
                                    @else <div class="timeline-icon ic-submit"><i class="bi bi-check2-circle"></i></div>
                                    @endif
                                    
                                    <div>
                                        <span class="text-white fw-bold" style="font-size: 14px;">{{ $pageTitle }}</span>
                                        <small class="text-muted d-block"><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($event->created_at)->format('h:i:s A') }} ({{ \Carbon\Carbon::parse($event->created_at)->diffForHumans() }})</small>
                                        @if($eventName == 'cta_click')
                                            <small class="text-warning">Clicked: "{{ json_decode($event->metadata ?? '[]', true)['button_text'] ?? '' }}"</small>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx1 = document.getElementById('submitChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Total Submitted', "Today's Submitted", 'Desktop Users', 'Mobile Users'],
            datasets: [{
                label: 'Count',
                data: [{{ $totalSubmitted }}, {{ $todaySubmitted }}, {{ $desktopSubmitted }}, {{ $mobileSubmitted }}],
                backgroundColor: ['#43e97b', '#4facfe', '#667eea', '#fda085'],
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b' } }, x: { grid: { display: false }, ticks: { color: '#64748b' } } } }
    });
</script>
@endsection