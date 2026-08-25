@extends('admin.layouts.app')

@section('content')
<style>
    /* Premium Dark UI for Show Page */
    .premium-card {
        background: rgba(17, 25, 40, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    .data-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .data-row:last-child { border-bottom: none; }
    .data-label { font-weight: 600; color: #64748b; font-size: 14px; }
    .data-value { font-weight: 500; color: #ffffff; font-size: 14px; text-align: right; word-break: break-all; }
    .text-muted { color: #64748b !important; }
    .text-white { color: #ffffff !important; }
    .badge-bot { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 5px 15px; border-radius: 20px; font-weight: 600; }
    .badge-human { background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); padding: 5px 15px; border-radius: 20px; font-weight: 600; }
    a.text-info { color: #4facfe !important; text-decoration: none; }
    a.text-info:hover { text-decoration: underline; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Lead Details: {{ $lead->name }}</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">Full form data and tracking information</p>
    </div>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Leads
    </a>
</div>

<div class="row">
    <!-- Left Column: Form Data -->
    <div class="col-md-7 mb-4">
        <div class="premium-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-white mb-0"><i class="bi bi-file-earmark-text me-2"></i> Form Data</h5>
                @if($lead->is_spam == 1 || $lead->is_spam == '1')
                    <span class="badge-bot">🤖 Bot Detected</span>
                @else
                    <span class="badge-human">👤 Human</span>
                @endif
            </div>
            
            <div class="data-row">
                <span class="data-label">Name</span>
                <span class="data-value">{{ $lead->name ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Email</span>
                <span class="data-value">{{ $lead->email ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Phone</span>
                <span class="data-value">{{ $lead->phone ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Business Name</span>
                <span class="data-value">{{ $lead->business ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Sign Type</span>
                <span class="data-value">{{ $lead->sign_type ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Details / Message</span>
                <span class="data-value" style="max-width: 60%;">{{ $lead->details ?? $lead->message ?? 'N/A' }}</span>
            </div>
            @if(isset($lead->image_url) && $lead->image_url != 'No Image Uploaded' && $lead->image_url != 'Cloudinary Upload Failed' && $lead->image_url != 'Cloudinary Config Error')
            <div class="data-row">
                <span class="data-label">Uploaded Image</span>
                <span class="data-value"><a href="{{ $lead->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View Image</a></span>
            </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Tracking Data -->
    <div class="col-md-5 mb-4">
        <div class="premium-card p-4 h-100">
            <h5 class="fw-bold text-white mb-4"><i class="bi bi-geo-alt me-2"></i> Tracking Data</h5>
            
            <div class="data-row">
                <span class="data-label">IP Address</span>
                <span class="data-value">{{ $lead->ip_address ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Device Type</span>
                <span class="data-value">{{ $lead->device_type ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Referrer URL</span>
                <span class="data-value text-truncate" style="max-width: 200px;"><a href="{{ $lead->referrer_url ?? '#' }}" target="_blank" class="text-info">{{ $lead->referrer_url ?? 'N/A' }}</a></span>
            </div>
            <div class="data-row">
                <span class="data-label">Source</span>
                <span class="data-value">{{ $lead->source ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Visitor ID</span>
                <span class="data-value">{{ $lead->visitor_id ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Session ID</span>
                <span class="data-value">{{ $lead->session_id ?? 'N/A' }}</span>
            </div>
            <div class="data-row">
                <span class="data-label">Submitted At</span>
                <span class="data-value">{{ $lead->created_at->format('M d, Y h:i A') }}</span>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('admin.sessions.index') }}" class="btn btn-primary btn-sm w-100" style="background: linear-gradient(135deg, #3B82F6, #2563EB); border: none;">
                    <i class="bi bi-graph-up me-1"></i> View Full Journey Timeline
                </a>
            </div>
        </div>
    </div>
</div>
@endsection