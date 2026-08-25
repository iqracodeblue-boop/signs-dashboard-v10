@extends('layouts.admin') {{/* ya jo bhi aap ka admin layout ho */}}

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">User Journey Details</h2>
            <p class="text-muted mb-0">Detailed timeline and steps taken by the user.</p>
        </div>
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Analytics
        </a>
    </div>

    <!-- Timeline / Journey Steps Container -->
    <div class="card shadow-sm border-0 p-4">
        <div class="timeline-container">
            <!-- Yahan aap apni timeline/steps ka HTML loop chalayein -->
            @isset($session->events)
                @foreach($session->events as $event)
                    <div class="timeline-step mb-3 p-3 border rounded bg-light">
                        <h5 class="text-primary mb-1">{{ $event->title ?? 'Page Visit' }}</h5>
                        <p class="mb-1 text-muted">{{ $event->description ?? 'User navigated to the page' }}</p>
                        <small class="text-secondary"><i class="far fa-clock"></i> {{ $event->created_at ?? now() }}</small>
                    </div>
                @endforeach
            @else
                <p class="text-center text-muted">No journey steps found for this session.</p>
            @endisset
        </div>
    </div>
</div>
@endsection