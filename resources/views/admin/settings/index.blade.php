@extends('admin.layouts.app')

@section('content')
<style>
    body { background-color: #0B1120 !important; font-family: 'Inter', sans-serif; color: #e2e8f0; }
    .premium-card {
        background: rgba(17, 25, 40, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 16px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    .form-control {
        background-color: #1E293B !important;
        color: #ffffff !important;
        border: 1px solid #334155 !important;
        border-radius: 10px;
        padding: 12px;
    }
    .form-control:focus { box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15); border-color: #3B82F6; }
    .text-muted { color: #64748b !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5 mt-4">
    <div>
        <h3 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px; font-size: 28px;">System Settings</h3>
        <p class="text-muted mb-0" style="font-size: 15px;">Manage your API integrations and configurations</p>
    </div>
    <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-sm">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-pill py-2 px-4 mb-4 d-flex align-items-center" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="premium-card p-5">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon me-3" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #000;">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">Airtable Integration</h5>
                        <small class="text-muted">Enter your Airtable API credentials here to sync leads automatically.</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-semibold">Airtable API Token</label>
                    <input type="text" name="airtable_api_token" class="form-control" value="{{ $settings['airtable_api_token'] ?? '' }}" placeholder="patXXXXXX...">
                    <small class="text-muted mt-1 d-block">Generate this from your Airtable account settings.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-semibold">Airtable Base ID</label>
                    <input type="text" name="airtable_base_id" class="form-control" value="{{ $settings['airtable_base_id'] ?? '' }}" placeholder="appXXXXXX...">
                    <small class="text-muted mt-1 d-block">Found in the URL of your Airtable base.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white fw-semibold">Airtable Table Name</label>
                    <input type="text" name="airtable_table_name" class="form-control" value="{{ $settings['airtable_table_name'] ?? '' }}" placeholder="Leads">
                    <small class="text-muted mt-1 d-block">The exact name of the table where you want to send leads.</small>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-lg px-5 rounded-pill text-white" style="background: linear-gradient(135deg, #3B82F6, #2563EB); border: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                        <i class="bi bi-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="col-md-4">
        <div class="premium-card p-4 h-100">
            <h5 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-2"></i> How it works?</h5>
            <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                When a user submits a lead form on the Shopify store, the data is sent to Laravel. 
                <br><br>
                When you click the <span class="badge bg-success bg-opacity-10 text-success">Approve</span> button on a lead in the dashboard, Laravel uses these API keys to securely send the lead data to your Airtable base.
                <br><br>
                If you change your Airtable credentials, simply update them here. No need to touch the code!
            </p>
        </div>
    </div>
</div>
@endsection