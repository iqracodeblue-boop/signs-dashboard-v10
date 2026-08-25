@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #060818 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .timeline { list-style: none; padding: 0; margin: 30px 0 0 0; position: relative; }
    .timeline::before { content: ''; position: absolute; top: 10px; bottom: 10px; left: 28px; width: 2px; background: linear-gradient(to bottom, rgba(59, 130, 246, 0.3), rgba(255, 255, 255, 0.05)); }
    .timeline-item { position: relative; padding-left: 75px; margin-bottom: 35px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; left: 0; top: 0; width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; z-index: 2; box-shadow: 0 8px 20px rgba(0,0,0,0.4); }
    .icon-view { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #000; }
    .icon-click { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #000; }
    .icon-type { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: #000; }
    .icon-submit { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #000; }
    .event-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 20px 24px; }
    .event-badge { padding: 6px 14px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 12px; }
    .badge-view { background: rgba(79, 172, 254, 0.15); color: #4facfe; border: 1px solid rgba(79, 172, 254, 0.2); }
    .badge-click { background: rgba(253, 160, 133, 0.15); color: #fda085; border: 1px solid rgba(253, 160, 133, 0.2); }
    .badge-type { background: rgba(161, 140, 209, 0.15); color: #fbc2eb; border: 1px solid rgba(161, 140, 209, 0.2); }
    .badge-submit { background: rgba(67, 233, 123, 0.15); color: #43e97b; border: 1px solid rgba(67, 233, 123, 0.2); }
    .data-box { background: rgba(79, 172, 254, 0.05); border: 1px solid rgba(79, 172, 254, 0.15); border-radius: 10px; padding: 12px 18px; margin-top: 15px; }
    .time-pill { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #7c8db5; }
    .text-muted { color: #64748b !important; }
    .text-white { color: #ffffff !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">User Journey Details</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">Session: {{ $sessionId }}</p>
    </div>
    <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Analytics
    </a>
</div>

<div class="premium-card p-5">
    @php
        $firstEvent = $activities->first();
        $lastEvent = $activities->last();
        $timeSpent = 0;
        if($firstEvent && $lastEvent) {
            $timeSpent = abs(\Carbon\Carbon::parse($firstEvent->created_at)->diffInSeconds(\Carbon\Carbon::parse($lastEvent->created_at)));
        }
        $isBot = ($lead && ($lead->is_spam == 1 || $lead->is_spam == '1'));
        $userName = $isBot ? 'Anonymous 🤖' : ($lead->name ?? 'Guest');
        $prevTime = null;
    @endphp

    <!-- User Info Header -->
    <div class="d-flex justify-content-between flex-wrap mb-4 pb-4 border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div style="width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; box-shadow: 0 8px 15px rgba(0,0,0,0.3);">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="ms-3">
                <h5 class="fw-bold mb-0 text-white" style="font-size: 20px;">User: {{ $userName }}</h5>
                <small class="text-muted d-block mt-1">IP: {{ $visitor->ip_address ?? 'N/A' }} | Device: {{ $visitor->device ?? 'N/A' }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 10px 18px;">
                <small class="text-muted d-block" style="font-size: 11px;">Total Duration</small><span class="text-white fw-bold">{{ gmdate('i:s', $timeSpent) }} Mins</span>
            </div>
            @if($lead && !$isBot)
            <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn btn-sm btn-success px-3 py-2 rounded-pill" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; color: #000; font-weight: 700;">
                <i class="bi bi-link-45deg me-1"></i> View Lead Data
            </a>
            @endif
        </div>
    </div>

    <!-- Activity Timeline -->
    <ul class="timeline">
        @foreach($activities as $event)
            @php
                $eventName = $event->event_name ?? 'page_view';
                
                // Heartbeat events ko timeline par show nahi karenge taa k clean rahe
                if($eventName == 'heartbeat') continue;
                
                $pageUrl = $event->page_url ?? 'N/A';
                $metadata = isset($event->metadata) ? json_decode($event->metadata, true) : [];
                
                // Page Name Extract Karne Ka 100% Perfect Logic
                $pageTitle = $event->page_title ?? ($metadata['title'] ?? '');
                if(empty($pageTitle) || $pageTitle == 'test' || $pageTitle == 'N/A') {
                    $path = parse_url($pageUrl, PHP_URL_PATH);
                    if($path == '/' || empty($path)) {
                        $pageTitle = 'Home Page';
                    } else {
                        $pageTitle = str_replace(['/', '-', '_'], ' ', trim($path, '/'));
                        $pageTitle = ucwords($pageTitle); // e.g. "pages about-us" -> "Pages About Us"
                    }
                }
                
                $stepTime = 0;
                if($prevTime) {
                    $stepTime = abs(\Carbon\Carbon::parse($event->created_at)->diffInSeconds($prevTime));
                }
                $prevTime = $event->created_at;
                
                $stepTimeString = $stepTime . 's';
                if ($stepTime >= 60) {
                    $minutes = floor($stepTime / 60);
                    $seconds = $stepTime % 60;
                    $stepTimeString = $minutes . 'm ' . $seconds . 's';
                }
            @endphp
            <li class="timeline-item">
                @if($eventName == 'page_view')
                    <div class="timeline-icon icon-view"><i class="bi bi-eye-fill"></i></div>
                @elseif($eventName == 'cta_click')
                    <div class="timeline-icon icon-click"><i class="bi bi-hand-index-thumb-fill"></i></div>
                @elseif($eventName == 'field_filled')
                    <div class="timeline-icon icon-type"><i class="bi bi-keyboard-fill"></i></div>
                @elseif($eventName == 'scroll_depth')
                    <div class="timeline-icon" style="background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); color: #000;"><i class="bi bi-arrows-vertical"></i></div>
                @else
                    <div class="timeline-icon icon-submit"><i class="bi bi-check2-circle"></i></div>
                @endif
                
                <div class="event-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            @if($eventName == 'page_view') <span class="event-badge badge-view">Page View</span>
                            @elseif($eventName == 'cta_click') <span class="event-badge badge-click">Button Click</span>
                            @elseif($eventName == 'field_filled') <span class="event-badge badge-type">Form Typed</span>
                            @elseif($eventName == 'scroll_depth') <span class="event-badge" style="background: rgba(102, 166, 255, 0.15); color: #66a6ff; border: 1px solid rgba(102, 166, 255, 0.2);">Scroll Depth</span>
                            @else <span class="event-badge badge-submit">Form Submit</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <small class="text-white fw-bold d-block" style="font-size: 14px;">{{ \Carbon\Carbon::parse($event->created_at)->format('h:i:s A') }}</small>
                            @if($stepTime > 0)
                                <span class="time-pill mt-1 d-inline-block"><i class="bi bi-stopwatch me-1"></i> {{ $stepTimeString }} later</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Yahan Page ka Naam Clearly Dikhega -->
                    <div class="mb-2">
                        <span class="text-primary fw-bold" style="font-size: 14px;"><i class="bi bi-file-earmark-text me-1"></i> Page:</span>
                        <a href="{{ $pageUrl }}" target="_blank" class="text-white text-decoration-none fw-bold" style="font-size: 16px;">{{ $pageTitle }}</a>
                    </div>
                    
                    @if($eventName == 'cta_click' && !empty($metadata['button_text']))
                        <div class="data-box">
                            <small class="text-warning fw-bold me-2"><i class="bi bi-cursor me-1"></i> Clicked:</small>
                            <span class="text-light">"{{ $metadata['button_text'] }}"</span>
                        </div>
                    @endif
                    
                    @if($eventName == 'scroll_depth' && !empty($metadata['scroll_percentage']))
                        <div class="data-box" style="background: rgba(102, 166, 255, 0.05); border-color: rgba(102, 166, 255, 0.15);">
                            <small class="text-info fw-bold me-2"><i class="bi bi-arrows-vertical me-1"></i> Scrolled to:</small>
                            <span class="text-light fw-bold">{{ $metadata['scroll_percentage'] }}</span>
                        </div>
                    @endif
                    
                    @if($eventName == 'field_filled' && !empty($metadata['field_name']))
                        <div class="data-box">
                            <small class="text-info fw-bold"><i class="bi bi-keyboard me-1"></i> Typed in:</small>
                            <span class="text-white ms-2">"{{ $metadata['field_name'] }}"</span>
                            <small class="text-muted mx-1">→</small>
                            <span class="text-success fw-bold">"{{ $metadata['field_value'] ?? '' }}"</span>
                        </div>
                    @endif
                    
                    @if($eventName == 'form_submitted')
                        <div class="data-box" style="background: rgba(67, 233, 123, 0.05); border-color: rgba(67, 233, 123, 0.15);">
                            <small class="text-success fw-bold fs-6"><i class="bi bi-check-circle me-2"></i> Form Submitted Successfully!</small>
                        </div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection