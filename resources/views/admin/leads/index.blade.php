@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #060818 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card { background: rgba(17, 25, 40, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.06) !important; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    
    .table { background-color: transparent !important; color: #ffffff !important; --bs-table-bg: transparent !important; --bs-table-color: #ffffff !important; margin-bottom: 0; }
    .premium-table thead th { background-color: transparent !important; color: #7c8db5 !important; border-bottom: 2px solid rgba(255, 255, 255, 0.08) !important; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 16px; font-weight: 700; }
    .premium-table tbody tr { background-color: transparent !important; border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important; }
    .premium-table tbody tr:hover { background-color: rgba(59, 130, 246, 0.03) !important; }
    .premium-table tbody td { color: #ffffff !important; vertical-align: middle; padding: 16px; }
    .text-muted { color: #64748b !important; }
    
    .badge { font-size: 11px; font-weight: 700; padding: 6px 12px; border-radius: 6px; }
    .badge-bg { background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
    .badge-success { background: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-danger { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .btn-view { background-color: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; transition: 0.2s; }
    .btn-view:hover { background-color: rgba(59, 130, 246, 0.2); color: #fff; }
    
    .btn-approve { background-color: rgba(245, 158, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; }
    .btn-approve:hover { background-color: rgba(245, 158, 11, 0.2); color: #fff; }
    
    .btn-airtable { background-color: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); padding: 8px 14px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; text-decoration: none; }
    .btn-airtable:hover { background-color: rgba(16, 185, 129, 0.2); color: #fff; }
    
    .form-control, .form-select { background-color: #1E293B !important; color: #ffffff !important; border: 1px solid #334155 !important; border-radius: 10px; }
    .form-control::placeholder { color: #64748b; }
    .form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15); border-color: #3B82F6; background-color: #1E293B !important; color: #fff !important; }
    
    /* Pagination CSS */
    .pagination { display: flex; list-style: none; padding-left: 0; border-radius: .25rem; gap: 8px; margin-top: 20px; justify-content: center; }
    .page-item .page-link { background-color: #1E293B !important; color: #e2e8f0 !important; border: 1px solid #334155 !important; border-radius: 10px !important; padding: 10px 18px !important; font-size: 14px; font-weight: 600; transition: all 0.3s ease; text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .page-item.active .page-link { background-color: #3B82F6 !important; color: #fff !important; border-color: #3B82F6 !important; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }
    .page-item.disabled .page-link { background-color: #111827 !important; color: #475569 !important; border-color: #1E293B !important; box-shadow: none; }
    .page-item .page-link:hover { background-color: #334155 !important; color: #fff !important; border-color: #3B82F6 !important; }
    .page-item .page-link svg { width: 16px !important; height: 16px !important; display: inline-block !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">Leads Management</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">View and manage all submitted leads</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-pill py-2 px-4 mb-4 d-flex align-items-center" role="alert"><i class="bi bi-check-circle me-2"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-pill py-2 px-4 mb-4 d-flex align-items-center" role="alert"><i class="bi bi-x-circle me-2"></i> {{ session('error') }}</div>
@endif

<div class="premium-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold text-white">All Leads Management</h5>
        <form action="{{ route('admin.leads.index') }}" method="GET" class="d-flex gap-2">
            <!-- Preserve Global Filters -->
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            @if(request('device')) <input type="hidden" name="device" value="{{ request('device') }}"> @endif
            @if(request('os')) <input type="hidden" name="os" value="{{ request('os') }}"> @endif
            @if(request('browser')) <input type="hidden" name="browser" value="{{ request('browser') }}"> @endif
            @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
            @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

            <input type="text" name="search" class="form-control shadow-sm" placeholder="Search name, email, phone..." value="{{ request('search') }}" style="width: 250px;">
            <select name="source" class="form-select shadow-sm" onchange="this.form.submit()" style="width: 180px;">
                <option value="">All Sources</option>
                <option value="Free Mockup Form (Hero)" @if(request('source') == 'Free Mockup Form (Hero)') selected @endif>Home Form</option>
                <option value="Get Quote Form (Sign Types)" @if(request('source') == 'Get Quote Form (Sign Types)') selected @endif>Sign Types Form</option>
            </select>
            <button class="btn btn-primary btn-sm shadow-sm px-3 d-flex align-items-center" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table premium-table align-middle">
            <thead>
                <tr>
                    <th>User Info</th>
                    <th>Contact</th>
                    <th>Source</th>
                    <th>Device & IP</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px; font-weight: 700; color: #fff;">
                                    {{ strtoupper(substr($lead->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-white">{{ $lead->name ?? 'N/A' }}</div>
                                    <div style="font-size: 12px; color: #64748b;">{{ $lead->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-white" style="font-size: 14px;">{{ $lead->phone ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge badge-bg">{{ $lead->source ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="text-white" style="font-size: 13px;">{{ $lead->device_type ?? 'N/A' }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ $lead->ip_address ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="text-white" style="font-size: 13px;">{{ $lead->created_at ? $lead->created_at->format('M d, Y') : 'N/A' }}</div>
                            <div style="font-size: 12px; color: #64748b;">{{ $lead->created_at ? $lead->created_at->format('h:i A') : '' }}</div>
                        </td>
                        <td>
                            @if(isset($lead->is_spam) && ($lead->is_spam === 1 || $lead->is_spam === '1'))
                                <span class="badge badge-danger"><i class="bi bi-robot me-1"></i> BOT</span>
                            @else
                                <span class="badge badge-success"><i class="bi bi-person-check me-1"></i> HUMAN</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="btn-view"><i class="bi bi-eye me-1"></i> View</a>
                                
                                {{-- FIX: Link directly to the Airtable Base (Prevents 404 Not Found) --}}
                                @if($lead->airtable_id)
                                    @php
                                        // Getting Base ID directly from .env
                                        $baseId = env('AIRTABLE_BASE_ID');
                                        $airtableUrl = $baseId ? "https://airtable.com/" . $baseId : "https://airtable.com";
                                    @endphp
                                    <a href="{{ $airtableUrl }}" target="_blank" class="btn-airtable"><i class="bi bi-cloud-check me-1"></i> Airtable</a>
                                @else
                                    <form action="{{ route('admin.leads.airtable', $lead->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-approve"><i class="bi bi-cloud-upload me-1"></i> Approve</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No leads found matching your criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $leads->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection