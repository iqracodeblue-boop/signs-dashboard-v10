@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #060818 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    .stat-card-sm { background: linear-gradient(145deg, rgba(30, 41, 59, 0.4), rgba(17, 25, 40, 0.6)); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; height: 100%; }
    .stat-icon-sm { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .g-1 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #000; }
    .g-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; }
    .g-3 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #000; }
    .g-4 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
    .table { background-color: transparent !important; color: #ffffff !important; --bs-table-bg: transparent !important; --bs-table-color: #ffffff !important; margin-bottom: 0; }
    .premium-table thead th { background-color: transparent !important; color: #7c8db5 !important; border-bottom: 2px solid rgba(255, 255, 255, 0.08) !important; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 16px; font-weight: 700; }
    .premium-table tbody tr { background-color: transparent !important; border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important; }
    .premium-table tbody tr:hover { background-color: rgba(59, 130, 246, 0.03) !important; }
    .premium-table tbody td { color: #ffffff !important; vertical-align: middle; padding: 16px; }
    .text-muted { color: #64748b !important; }
    .badge { font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 6px; }
    .badge-bg { background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
    .btn-view { background-color: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; transition: 0.2s; }
    .btn-view:hover { background-color: rgba(59, 130, 246, 0.2); color: #fff; }
    
    /* Pagination CSS */
    .pagination { display: flex; list-style: none; padding-left: 0; border-radius: .25rem; gap: 8px; margin-top: 20px; justify-content: center; }
    .page-item .page-link { background-color: #1E293B !important; color: #e2e8f0 !important; border: 1px solid #334155 !important; border-radius: 10px !important; padding: 10px 18px !important; font-size: 14px; font-weight: 600; transition: all 0.3s ease; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .page-item.active .page-link { background-color: #3B82F6 !important; color: #fff !important; border-color: #3B82F6 !important; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }
    .page-item.disabled .page-link { background-color: #111827 !important; color: #475569 !important; border-color: #1E293B !important; box-shadow: none; }
    .page-item .page-link:hover { background-color: #334155 !important; color: #fff !important; border-color: #3B82F6 !important; }
    .page-item .page-link svg { width: 16px !important; height: 16px !important; display: inline-block !important; }
    
    /* Journey Modal CSS */
    .dark-modal { background-color: #0B1120 !important; border: 1px solid rgba(255,255,255,0.08) !important; border-radius: 16px !important; color: #e2e8f0 !important; }
    .modal-info-bar { display: flex; flex-wrap: wrap; gap: 20px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; color: #94a3b8; }
    .info-item strong { color: #fff; font-weight: 500; margin-left: 4px; }
    
    .journey-list { display: flex; flex-direction: column; gap: 20px; }
    .journey-row { display: flex; gap: 15px; align-items: flex-start; }
    .journey-time { font-family: monospace; color: #64748b; font-size: 12px; width: 120px; flex-shrink: 0; padding-top: 4px; }
    .journey-main { flex-grow: 1; font-size: 14px; line-height: 1.4; padding-top: 2px; }
    .journey-title { font-weight: 600; }
    .journey-desc { color: #94a3b8; }
    .journey-duration { font-family: monospace; color: #64748b; font-size: 12px; width: 70px; text-align: right; flex-shrink: 0; padding-top: 4px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Analytics Overview</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">All active and past user sessions on the platform</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
</div>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-1"><i class="bi bi-clock-history"></i></div>
            <div>
                <h4 class="mb-0 fw-bold text-white">{{ number_format($todaySessions ?? 0) }}</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">Sessions Today</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-2"><i class="bi bi-people"></i></div>
            <div>
                <h4 class="mb-0 fw-bold text-white">{{ number_format(isset($allVisitors) ? $allVisitors->count() : 0) }}</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">Total Visitors</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-3"><i class="bi bi-mouse"></i></div>
            <div>
                <h4 class="mb-0 fw-bold text-white">{{ number_format(isset($allActivities) ? $allActivities->count() : 0) }}</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">Total Events</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card-sm">
            <div class="stat-icon-sm g-4"><i class="bi bi-file-earmark-text"></i></div>
            <div>
                <h4 class="mb-0 fw-bold text-white">{{ number_format(isset($allPageViews) ? $allPageViews->count() : 0) }}</h4>
                <p class="text-muted mb-0" style="font-size: 13px;">Page Views</p>
            </div>
        </div>
    </div>
</div>

<div class="premium-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold text-white">User Sessions</h5>
    </div>
    <div class="table-responsive">
        <table class="table premium-table align-middle">
            <thead>
                <tr>
                    <th>Session ID</th>
                    <th>IP Address</th>
                    <th>Last Activity</th>
                    <th>Device / Browser</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions ?? [] as $sid)
                    @php
                        $info = isset($visitorData[$sid]) ? $visitorData[$sid] : (object)['ip_address' => 'N/A', 'user_agent' => 'N/A', 'last_activity' => null];
                        
                        $ua = $info->user_agent ?? 'N/A';
                        $device = 'Unknown Device';
                        if (preg_match('/Windows/i', $ua)) $device = 'Desktop (Windows)';
                        elseif (preg_match('/Mac OS X/i', $ua)) $device = 'Desktop (MacOS)';
                        elseif (preg_match('/Android ([\d\.]+)/i', $ua, $m)) {
                            $device = 'Android ' . ($m[1] ?? '');
                            if (preg_match('/Pixel ([\w]+)/i', $ua, $pm)) $device .= ' (Pixel ' . $pm[1] . ')';
                        }
                        elseif (preg_match('/iPhone/i', $ua)) $device = 'iOS (iPhone)';
                        elseif (preg_match('/iPad/i', $ua)) $device = 'iOS (iPad)';
                        
                        $browser = 'Unknown Browser';
                        if (preg_match('/Chrome\/([\d\.]+)/i', $ua) && !preg_match('/Edg/i', $ua) && !preg_match('/OPR/i', $ua)) $browser = 'Chrome';
                        elseif (preg_match('/Firefox\/([\d\.]+)/i', $ua)) $browser = 'Firefox';
                        elseif (preg_match('/Safari\/([\d\.]+)/i', $ua) && !preg_match('/Chrome/i', $ua)) $browser = 'Safari';
                        elseif (preg_match('/Edg\/([\d\.]+)/i', $ua)) $browser = 'Edge';
                    @endphp
                    <tr>
                        <td class="fw-bold text-white" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ \Illuminate\Support\Str::limit($sid, 12) }}
                        </td>
                        <td>
                            <span class="badge badge-bg">{{ $info->ip_address ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if(!empty($info->last_activity))
                                <span class="text-muted" style="font-size: 13px;">
                                    {{ \Carbon\Carbon::parse($info->last_activity)->diffForHumans() }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size: 13px;">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 13px;">
                                {{ $browser }} <span style="color: #475569 !important; margin: 0 4px;">•</span> {{ $device }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-view" data-bs-toggle="modal" data-bs-target="#journeyModal{{ md5($sid) }}">
                                <i class="bi bi-diagram-3"></i> View Journey
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">No active sessions found matching your criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(method_exists($sessions, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $sessions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Session Journey Modals -->
@foreach($sessions ?? [] as $sid)
    @php
        $modalJourney = isset($journeyData[$sid]) ? $journeyData[$sid] : collect([]);
        $modalInfo = isset($visitorData[$sid]) ? $visitorData[$sid] : (object)['ip_address' => 'N/A', 'user_agent' => 'N/A', 'started_at' => null, 'duration' => '0s'];
    @endphp
    <div class="modal fade" id="journeyModal{{ md5($sid) }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content dark-modal">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-diagram-3 me-2"></i>User Journey Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto; padding: 24px;">
                    
                    <!-- Top Info Bar -->
                    <div class="modal-info-bar">
                        <div class="info-item">User: <strong>Guest</strong></div>
                        <div class="info-item">IP: <strong>{{ $modalInfo->ip_address ?? 'N/A' }}</strong></div>
                        <div class="info-item">Device: <strong>Desktop</strong></div>
                        <div class="info-item">Total Duration: <strong>{{ $modalInfo->duration ?? '0s' }}</strong></div>
                    </div>

                    @if($modalJourney->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-info-circle" style="font-size: 24px;"></i>
                            <p class="mt-2">No activity recorded for this session.</p>
                        </div>
                    @else
                        <div class="journey-list">
                            @php 
                                $uniqueJourney = collect();
                                $lastEventSignature = '';
                                $lastEventTime = null;

                                foreach ($modalJourney as $item) {
                                    $eventName = strtolower($item->event_name ?? '');
                                    if (in_array($eventName, ['heartbeat', 'ping', 'alive', 'scroll'])) {
                                        continue;
                                    }
                                    
                                    $currentTime = isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at) : null;
                                    $signature = ($item->event_name ?? '') . '|' . ($item->event_data ?? '') . '|' . ($item->page_url ?? '');

                                    if ($signature === $lastEventSignature && $lastEventTime && $currentTime) {
                                        $diff = $lastEventTime->diffInSeconds($currentTime);
                                        if ($diff <= 5) {
                                            continue; 
                                        }
                                    }

                                    $uniqueJourney->push($item);
                                    $lastEventSignature = $signature;
                                    $lastEventTime = $currentTime;
                                }
                                $journeyValues = $uniqueJourney->values();
                            @endphp
                            
                            @foreach($journeyValues as $index => $item)
                                @php
                                    $eventName = strtolower($item->event_name ?? '');
                                    $isView = isset($item->page_url) && empty($eventName);
                                    
                                    $titleColor = '#8B5CF6'; 
                                    $title = ucfirst(str_replace('_', ' ', $eventName));
                                    $descHtml = '';
                                    
                                    $time = isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('h:i:s A') : 'N/A';

                                    $durationText = '';
                                    $nextItem = $journeyValues->get($index + 1);
                                    if ($nextItem && isset($nextItem->created_at) && isset($item->created_at)) {
                                        $start = \Carbon\Carbon::parse($item->created_at);
                                        $end = \Carbon\Carbon::parse($nextItem->created_at);
                                        $diffSecs = $start->diffInSeconds($end);
                                        if ($diffSecs < 60) {
                                            $durationText = $diffSecs . 's later';
                                        } else {
                                            $durationText = floor($diffSecs / 60) . 'm ' . ($diffSecs % 60) . 's later';
                                        }
                                    }

                                    if ($isView) {
                                        $titleColor = '#60a5fa'; 
                                        $title = 'Page View';
                                        $path = parse_url($item->page_url, PHP_URL_PATH);
                                        $pageName = $path === '/' ? 'Home Page' : ucfirst(str_replace('-', ' ', trim($path, '/')));
                                        $descHtml = $pageName;
                                    } else {
                                        $descPrefix = "";
                                        $descSuffix = "";

                                        if (strpos($eventName, 'click') !== false) {
                                            $titleColor = '#06B6D4'; 
                                            $title = 'Button Click';
                                            $descPrefix = "Clicked: '";
                                            $descSuffix = "'";
                                        } elseif (strpos($eventName, 'fill') !== false || strpos($eventName, 'input') !== false) {
                                            $titleColor = '#F59E0B'; 
                                            $title = 'Form Fill';
                                            $descPrefix = "Data: ";
                                        } elseif (strpos($eventName, 'submit') !== false) {
                                            $titleColor = '#10B981'; 
                                            $title = 'Form Submitted';
                                        } elseif (strpos($eventName, 'exit') !== false) {
                                            $titleColor = '#EF4444'; 
                                            $title = 'Page Exit';
                                        }
                                        
                                        $extractedDesc = '';

                                        if (strpos($eventName, 'submit') !== false && isset($allLeads)) {
                                            $lead = $allLeads->first(function($l) use ($sid) {
                                                return $l->session_id == $sid;
                                            });
                                            if ($lead) {
                                                $extractedDesc = "Name: {$lead->name} | Email: {$lead->email} | Phone: {$lead->phone}";
                                            }
                                        }
                                        
                                        if (empty($extractedDesc) && !empty($item->event_data)) {
                                            $decoded = json_decode($item->event_data, true);
                                            if (is_array($decoded)) {
                                                if (isset($decoded['page'])) {
                                                    $extractedDesc = "Page: " . $decoded['page'];
                                                    if (isset($decoded['clicked'])) $extractedDesc .= " | Clicked: '" . $decoded['clicked'] . "'";
                                                    elseif (isset($decoded['text'])) $extractedDesc .= " | Clicked: '" . $decoded['text'] . "'";
                                                } else {
                                                    $textKeys = ['text', 'innerText', 'value', 'label', 'content', 'element', 'target', 'id', 'name', 'email', 'phone', 'data'];
                                                    $found = false;
                                                    foreach ($textKeys as $key) {
                                                        if (isset($decoded[$key]) && !empty($decoded[$key]) && is_string($decoded[$key])) {
                                                            $extractedDesc = $decoded[$key];
                                                            $found = true;
                                                            break;
                                                        }
                                                    }
                                                    if (!$found) {
                                                        $extractedDesc = json_encode($decoded);
                                                    }
                                                }
                                            } else {
                                                $extractedDesc = $item->event_data;
                                            }
                                        }
                                        
                                        if (empty($extractedDesc)) {
                                            $possibleCols = ['element_text', 'event_value', 'value', 'label', 'text', 'target', 'details', 'data'];
                                            foreach ($possibleCols as $col) {
                                                if (!empty($item->$col)) {
                                                    $extractedDesc = $item->$col;
                                                    break;
                                                }
                                            }
                                        }
                                        
                                        $descHtml = $descPrefix . ($extractedDesc ?: 'No additional data') . $descSuffix;
                                    }
                                @endphp
                                
                                <div class="journey-row">
                                    <div class="journey-time">{{ $time }}</div>
                                    <div class="journey-main">
                                        <span class="journey-title" style="color: {{ $titleColor }};">{{ $title }}</span>
                                        @if(!empty($descHtml))
                                            <span class="journey-desc"> - {!! $descHtml !!}</span>
                                        @endif
                                    </div>
                                    <div class="journey-duration">{{ $durationText }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection