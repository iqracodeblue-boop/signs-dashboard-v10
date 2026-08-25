<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\LeadController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Traits\GlobalFilters;

// Helper function to use trait in routes easily
if (!function_exists('apply_filters')) {
    function apply_filters($query, $request, $tableName) {
        $filterClass = new class { use GlobalFilters; };
        return $filterClass->applyGlobalFilters($query, $request, $tableName);
    }
}

// Root URL direct Dashboard par jayega
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

  require __DIR__.'/auth.php';

// Logout Route
Route::post('/logout', function () {
    if(Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
})->name('logout');

// Admin Routes (Auth Protected)
Route::prefix('admin')->group(function () {

    // 0. Main Dashboard Route (Stats & Graphs)
    Route::get('/dashboard', function () {
        $totalVisitors = \DB::table('visitors')->count();
        $totalPageViews = \DB::table('page_views')->count();
        $totalEvents = \DB::table('events')->count();

        $totalLeads = \DB::table('leads')->count();
        $todayLeads = \DB::table('leads')->whereDate('created_at', today())->count();
        $desktopLeads = \DB::table('leads')->where('device_type', 'Desktop')->count();
        $mobileLeads = \DB::table('leads')->where('device_type', 'Mobile')->count();

        $leadsTrend = \DB::table('leads')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $trendDates = [];
        $trendCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
            $trendDates[] = \Carbon\Carbon::parse($date)->format('D (d M)');
            $trendCounts[] = $leadsTrend[$date] ?? 0;
        }

        return view('admin.dashboard.index', compact(
            'totalVisitors', 'totalPageViews', 'totalEvents',
            'totalLeads', 'todayLeads', 'desktopLeads', 'mobileLeads',
            'trendDates', 'trendCounts'
        ));
    })->name('admin.dashboard');

    // 1. Leads Management Routes
    Route::get('/leads', [LeadController::class, 'index'])->name('admin.leads.index');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('admin.leads.show');
    Route::post('/leads/{lead}/airtable', [LeadController::class, 'sendToAirtable'])->name('admin.leads.airtable');

    // 2. Analytics Overview (Sessions)
    Route::get('/sessions', function (\Illuminate\Http\Request $request) {
        // FIX: Sort sessions by latest activity so they don't mix up
        $pageViewsSessions = \DB::table('page_views')->whereNotNull('session_id')->where('session_id', '!=', '')->select('session_id', 'created_at')->get();
        $eventsSessions = \DB::table('events')->whereNotNull('session_id')->where('session_id', '!=', '')->select('session_id', 'created_at')->get();
        
        $allSessions = $pageViewsSessions->concat($eventsSessions)->groupBy('session_id')->map(function($items) {
            return $items->max('created_at');
        })->sortDesc()->keys();
        
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        
        $sessions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allSessions->forPage($currentPage, $perPage),
            $allSessions->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        $currentPageSessionIds = $sessions->items();
        
        $allPageViews = \DB::table('page_views')->whereIn('session_id', $currentPageSessionIds)->get();
        
        // FIX: Strictly fetch by Session ID or Visitor ID. NO IP MATCHING to prevent overlapping data on localhost.
        $visitorIds = $allPageViews->pluck('visitor_id')->filter()->unique();
        
        $allEvents = collect();
        if (Schema::hasTable('events')) {
            $eventsQuery = \DB::table('events');
            $eventsQuery->where(function($q) use ($currentPageSessionIds, $visitorIds) {
                if (Schema::hasColumn('events', 'session_id')) {
                    $q->orWhereIn('session_id', $currentPageSessionIds);
                }
                if (Schema::hasColumn('events', 'visitor_id') && $visitorIds->isNotEmpty()) {
                    $q->orWhereIn('visitor_id', $visitorIds);
                }
            });
            $allEvents = $eventsQuery->get();
        }
        
        $journeyData = [];
        $visitorData = [];
        
        foreach($currentPageSessionIds as $sid) {
            $views = $allPageViews->filter(function($item) use ($sid) {
                return $item->session_id == $sid;
            });
            
            $viewVisitorIds = $views->pluck('visitor_id')->filter()->unique();
            
            $evts = $allEvents->filter(function($item) use ($sid, $viewVisitorIds) {
                if (isset($item->session_id) && $item->session_id == $sid) return true;
                if (isset($item->visitor_id) && $viewVisitorIds->contains($item->visitor_id)) return true;
                return false;
            });
            
            $journey = $views->concat($evts)->sortBy(function($item) {
                return $item->created_at ?? ($item->id ?? 0);
            });
            
            $journeyData[$sid] = $journey;
            
            $firstActivity = $journey->first();
            if ($firstActivity) {
                $visitor = null;
                if (isset($firstActivity->visitor_id)) {
                    $visitor = \DB::table('visitors')->where('visitor_id', $firstActivity->visitor_id)->first();
                }
                $lastActivity = $journey->last();
                
                $totalDurationSec = 0;
                $durationText = '0s';
                if (isset($firstActivity->created_at) && isset($lastActivity->created_at)) {
                    $start = \Carbon\Carbon::parse($firstActivity->created_at);
                    $end = \Carbon\Carbon::parse($lastActivity->created_at);
                    $totalDurationSec = $start->diffInSeconds($end);
                    
                    if ($totalDurationSec < 60) {
                        $durationText = $totalDurationSec . ' seconds';
                    } elseif ($totalDurationSec < 3600) {
                        $durationText = $start->diffInMinutes($end) . ' min ' . ($totalDurationSec % 60) . ' sec';
                    } else {
                        $durationText = $start->diffInHours($end) . ' hrs ' . ($start->diffInMinutes($end) % 60) . ' min';
                    }
                }
                
                $visitorData[$sid] = (object)[
                    'ip_address' => $firstActivity->ip_address ?? ($visitor->ip_address ?? 'N/A'),
                    'user_agent' => $firstActivity->user_agent ?? ($visitor->user_agent ?? 'N/A'),
                    'last_activity' => $lastActivity->created_at ?? null,
                    'started_at' => $firstActivity->created_at ?? null,
                    'duration' => $durationText
                ];
            }
        }
        
        $todaySessions = \DB::table('page_views')->whereDate('created_at', today())->distinct()->count('session_id');
        $allVisitors = \DB::table('visitors')->get();
        $allActivities = \DB::table('events')->get();
        $allPageViews = \DB::table('page_views')->get();
        $allLeads = \DB::table('leads')->get();
        $leads = $allLeads; $events = $allActivities; $pageViews = $allPageViews; $visitors = $allVisitors;
        $todayVisitors = \DB::table('visitors')->whereDate('created_at', today())->count();
        $todayLeads = \DB::table('leads')->whereDate('created_at', today())->count();
        
        return view('admin.sessions.index', compact(
            'sessions', 'todaySessions', 'visitors', 'events', 'pageViews', 'leads', 
            'allActivities', 'allPageViews', 'allVisitors', 'allLeads', 'todayVisitors', 'todayLeads',
            'journeyData', 'visitorData'
        ));
    })->name('admin.sessions.index');

    // 2.1 User Journey Dedicated Full Page Route (NEW ADDED)
    Route::get('/sessions/{sessionId}/journey', function ($sessionId) {
        $pageViews = \DB::table('page_views')->where('session_id', $sessionId)->get();
        
        $events = collect();
        if (Schema::hasTable('events')) {
            $events = \DB::table('events')->where('session_id', $sessionId)->get();
        }
        
        // Combine and sort chronologically
        $journey = $pageViews->concat($events)->sortBy(function($item) {
            return $item->created_at ?? ($item->id ?? 0);
        });

        // Fetch visitor info if available
        $firstActivity = $journey->first();
        $visitor = null;
        if ($firstActivity && isset($firstActivity->visitor_id)) {
            $visitor = \DB::table('visitors')->where('visitor_id', $firstActivity->visitor_id)->first();
        }

        return view('admin.sessions.journey', compact('journey', 'sessionId', 'visitor'));
    })->name('admin.sessions.journey');

    // 3. Submitted Forms Analytics
    Route::get('/analytics/submissions', function (\Illuminate\Http\Request $request) {
        $visitorsQuery = \DB::table('visitors');
        $visitorsQuery = apply_filters($visitorsQuery, $request, 'visitors');
        $visitors = $visitorsQuery->get();

        $submittedQuery = \DB::table('events')->where('event_name', 'form_submitted');
        $submittedQuery = apply_filters($submittedQuery, $request, 'events');
        $submittedSessions = $submittedQuery->pluck('session_id')->unique();
        
        $submittedData = \DB::table('events')->whereIn('session_id', $submittedSessions)->where('event_name', 'form_submitted')->orderBy('created_at', 'desc')->get()->groupBy('session_id');
        $submittedLeads = \DB::table('leads')->whereIn('session_id', $submittedSessions)->orderBy('created_at', 'desc')->get();
        $leadsData = \DB::table('leads')->whereIn('session_id', $submittedSessions)->get()->keyBy('session_id');

        $totalSubmitted = $submittedSessions->count();
        $todaySubmitted = \DB::table('events')->where('event_name', 'form_submitted')->whereDate('created_at', today())->count();
        $totalLeadsCount = $leadsData->count();
        $conversionRate = ($totalSubmitted > 0) ? round(($totalLeadsCount / $totalSubmitted) * 100, 2) : 0;
        
        $desktopSubmitted = \DB::table('events')->join('visitors', 'events.visitor_id', '=', 'visitors.visitor_id')->where('events.event_name', 'form_submitted')->where('visitors.device', 'Desktop')->count();
        $mobileSubmitted = \DB::table('events')->join('visitors', 'events.visitor_id', '=', 'visitors.visitor_id')->where('events.event_name', 'form_submitted')->where('visitors.device', 'Mobile')->count();
        
        $events = \DB::table('events')->get(); $pageViews = \DB::table('page_views')->get(); $leads = \DB::table('leads')->get();
        return view('admin.analytics.submissions', compact('submittedData', 'submittedLeads', 'visitors', 'totalSubmitted', 'leadsData', 'todaySubmitted', 'conversionRate', 'desktopSubmitted', 'mobileSubmitted', 'events', 'pageViews', 'leads'));
    })->name('admin.analytics.submissions');

    // 4. Abandoned Forms Analytics
    Route::get('/analytics/abandoned', function (\Illuminate\Http\Request $request) {
        $visitorsQuery = \DB::table('visitors');
        $visitorsQuery = apply_filters($visitorsQuery, $request, 'visitors');
        $visitors = $visitorsQuery->get();

        $filledQuery = \DB::table('events')->where('event_name', 'field_filled');
        $submittedQuery = \DB::table('events')->where('event_name', 'form_submitted');
        $filledQuery = apply_filters($filledQuery, $request, 'events');
        $submittedQuery = apply_filters($submittedQuery, $request, 'events');

        $filledSessions = $filledQuery->pluck('session_id')->unique();
        $submittedSessions = $submittedQuery->pluck('session_id')->unique();
        $abandonedSessionIds = $filledSessions->diff($submittedSessions);
        
        $abandonedEventsQuery = \DB::table('events')->whereIn('session_id', $abandonedSessionIds)->where('event_name', 'field_filled');
        $abandonedEventsQuery = apply_filters($abandonedEventsQuery, $request, 'events');
        $abandonedData = $abandonedEventsQuery->orderBy('created_at', 'desc')->get()->groupBy('session_id');
            
        $abandonedLeads = \DB::table('leads')->whereIn('session_id', $abandonedSessionIds)->orderBy('created_at', 'desc')->get();
        $convertedLeads = \DB::table('leads')->whereIn('session_id', $abandonedSessionIds)->get()->keyBy('session_id');
        
        $totalAbandoned = $abandonedSessionIds->count();
        $totalSubmitted = $submittedSessions->count();
        $abandonedRate = ($totalAbandoned + $totalSubmitted > 0) ? round(($totalAbandoned / ($totalAbandoned + $totalSubmitted)) * 100) : 0;
        $fieldsFilledCount = $filledQuery->count();
        $todayAbandoned = \DB::table('events')->where('event_name', 'field_filled')->whereDate('created_at', today())->count();
        
        $desktopAbandoned = \DB::table('events')->join('visitors', 'events.visitor_id', '=', 'visitors.visitor_id')->where('events.event_name', 'field_filled')->where('visitors.device', 'Desktop')->count();
        $mobileAbandoned = \DB::table('events')->join('visitors', 'events.visitor_id', '=', 'visitors.visitor_id')->where('events.event_name', 'field_filled')->where('visitors.device', 'Mobile')->count();
        
        $events = \DB::table('events')->get(); $pageViews = \DB::table('page_views')->get(); $leads = \DB::table('leads')->get();
        return view('admin.analytics.abandoned', compact('abandonedData', 'abandonedLeads', 'visitors', 'totalAbandoned', 'totalSubmitted', 'abandonedRate', 'fieldsFilledCount', 'convertedLeads', 'todayAbandoned', 'desktopAbandoned', 'mobileAbandoned', 'events', 'pageViews', 'leads'));
    })->name('admin.analytics.abandoned');

    // 5. Total Visitors Analytics
    Route::get('/analytics/visitors', function (\Illuminate\Http\Request $request) {
        $query = \DB::table('visitors');
        $query = apply_filters($query, $request, 'visitors');
        $visitorsData = $query->orderBy('last_seen_at', 'desc')->get();
        $leadsData = \DB::table('leads')->select('visitor_id', 'name', 'is_spam')->get()->keyBy('visitor_id');
        
        $visitors = $visitorsData->map(function($v) use ($leadsData) {
            $lead = $leadsData->get($v->visitor_id);
            $v->lead_name = $lead->name ?? null;
            $v->is_bot = $lead->is_spam ?? null;
            return $v;
        });

        if ($request->filled('status')) {
            $isBot = $request->status === 'Bot' ? 1 : 0;
            $visitors = $visitors->filter(function($v) use ($isBot) { return $v->is_bot == $isBot; });
        }

        $totalVisitors = $visitors->count();
        $desktopUsers = $visitors->where('device', 'Desktop')->count();
        $mobileUsers = $visitors->where('device', 'Mobile')->count();
        $botUsers = $visitors->filter(function($v) { return $v->is_bot == 1 || $v->is_bot == '1'; })->count();
        $humanUsers = $totalVisitors - $botUsers;
        
        $todayVisitors = \DB::table('visitors')->whereDate('created_at', today())->count();
        $desktopVisitors = $desktopUsers; $mobileVisitors = $mobileUsers;
        $events = \DB::table('events')->get(); $pageViews = \DB::table('page_views')->get(); $leads = \DB::table('leads')->get();
        
        return view('admin.analytics.visitors', compact('visitors', 'totalVisitors', 'desktopUsers', 'mobileUsers', 'botUsers', 'humanUsers', 'todayVisitors', 'desktopVisitors', 'mobileVisitors', 'events', 'pageViews', 'leads'));
    })->name('admin.analytics.visitors');

    // 6. Settings Routes
    Route::get('/settings', function () {
        $settings = \DB::table('settings')->pluck('setting_value', 'setting_key')->toArray();
        return view('admin.settings.index', compact('settings'));
    })->name('admin.settings.index');

    Route::post('/settings', function (\Illuminate\Http\Request $request) {
        \DB::table('settings')->where('setting_key', 'airtable_api_token')->update(['setting_value' => $request->airtable_api_token]);
        \DB::table('settings')->where('setting_key', 'airtable_base_id')->update(['setting_value' => $request->airtable_base_id]);
        \DB::table('settings')->where('setting_key', 'airtable_table_name')->update(['setting_value' => $request->airtable_table_name]);
        return redirect()->back()->with('success', 'Settings updated successfully!');
    })->name('admin.settings.update');
});