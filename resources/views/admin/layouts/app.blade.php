<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signs INC - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-main: #060818;
            --bg-sidebar: #0B1120;
            --text-main: #e2e8f0;
            --text-muted: #64748b;
            --accent: #3B82F6;
            --border-color: rgba(255, 255, 255, 0.08);
            --font-primary: 'Inter', system-ui, -apple-system, sans-serif;
            --font-accent: 'DM Serif Display', Georgia, serif;
            --text-xs: 11px;
            --text-sm: 12px;
            --text-base: 14px;
            --text-lg: 16px;
            --text-xl: 20px;
            --text-3xl: 32px;
        }
        * { box-sizing: border-box; }
        body { 
            background-color: var(--bg-main) !important; 
            font-family: var(--font-primary); 
            font-size: var(--text-base);
            color: var(--text-main);
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3, h4 { color: #fff; margin: 0; }
        h1 { font-family: var(--font-accent); font-size: var(--text-3xl); font-weight: 400; }
        h2 { font-size: var(--text-xl); font-weight: 700; }
        h3 { font-size: var(--text-lg); font-weight: 600; }
        p { font-size: var(--text-base); line-height: 1.6; color: var(--text-muted); margin: 0; }
        
        .sidebar { 
            width: 260px; height: 100vh; background: var(--bg-sidebar); 
            position: fixed; top: 0; left: 0; padding-top: 25px; color: #fff; z-index: 1000;
            border-right: 1px solid var(--border-color); display: flex; flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar .logo { 
            padding: 0 25px 30px; font-family: var(--font-accent); font-size: 22px; font-weight: 400; color: #fff; 
            text-decoration: none; display: flex; align-items: center; gap: 10px;
        }
        .nav-menu { flex-grow: 1; padding: 0 15px; overflow-y: auto; overflow-x: hidden; }
        .sidebar a.nav-link-item { 
            color: #94a3b8; text-decoration: none; display: flex; align-items: center; gap: 12px; 
            padding: 12px 16px; font-size: var(--text-base); transition: all 0.3s ease; font-weight: 500;
            border-radius: 10px; margin-bottom: 4px; position: relative; overflow: hidden;
        }
        .sidebar a.nav-link-item::before {
            content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 3px;
            background: var(--accent); transform: scaleY(0); transition: transform 0.3s ease;
            border-radius: 0 4px 4px 0;
        }
        .sidebar a.nav-link-item:hover { background: rgba(255, 255, 255, 0.03); color: #fff; }
        .sidebar a.nav-link-item.active { background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), transparent); color: #fff; }
        .sidebar a.nav-link-item.active::before { transform: scaleY(1); }
        
        .nav-header { padding: 20px 20px 5px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #475569; font-weight: 700; }
        .nav-header:not(:first-child) { margin-top: 10px; }
        
        .logout-btn { background: transparent; border: none; color: #ef4444; text-decoration: none; padding: 0; font-size: var(--text-base); font-weight: 500; display: flex; align-items: center; gap: 10px; cursor: pointer; }
        
        .main-wrapper { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-header {
            background: rgba(11, 17, 32, 0.8); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color); padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center; 
            position: sticky; top: 0; z-index: 999;
        }
        .header-search {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color);
            border-radius: 10px; padding: 8px 15px; color: var(--text-muted);
            display: flex; align-items: center; gap: 10px; width: 350px;
        }
        .header-search input { background: transparent; border: none; color: #fff; outline: none; width: 100%; font-size: var(--text-base); }
        .header-search kbd { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; font-size: var(--text-xs); color: #cbd5e1; }
        
        .header-actions { display: flex; align-items: center; gap: 20px; }
        .icon-btn { background: transparent; border: none; color: #94a3b8; font-size: 20px; position: relative; cursor: pointer; }
        .icon-btn .dot { position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 2px solid var(--bg-main); }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: var(--text-sm); cursor: pointer; }
        
        #liveClock { font-weight: 600; font-size: var(--text-base); color: #fff; padding: 8px 14px; background: rgba(255,255,255,0.05); border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px; }
        
        .btn-filter {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color);
            color: #e2e8f0; padding: 8px 16px; border-radius: 8px; font-size: var(--text-base); font-weight: 600;
            display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.3s ease;
        }
        .btn-filter:hover { background: rgba(59, 130, 246, 0.1); border-color: var(--accent); color: #fff; }
        
        .main-content { padding: 30px; flex-grow: 1; }
        .text-muted-custom { color: var(--text-muted); font-size: var(--text-sm); }
        
        /* Modal Dark Theme */
        .modal-content.dark-modal { background-color: #0B1120; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; color: #e2e8f0; }
        .modal-content.dark-modal .modal-header { border-bottom: 1px solid rgba(255,255,255,0.08); }
        .modal-content.dark-modal .modal-footer { border-top: 1px solid rgba(255,255,255,0.08); }
        .modal-content.dark-modal .modal-body { max-height: 75vh; overflow-y: auto; padding: 24px; }
        .modal-content.dark-modal .form-select,
        .modal-content.dark-modal .form-control {
            background-color: #1E293B; border: 1px solid #334155; color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }
        .modal-content.dark-modal input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.6; cursor: pointer; }
        .modal-content.dark-modal .form-label { color: var(--text-muted); font-size: 12px; letter-spacing: 1px; font-weight: 700; text-transform: uppercase; }
        .modal-content.dark-modal .input-group-text { background-color: #111827; border: 1px solid #334155; color: #94a3b8; }
        .modal-content.dark-modal .form-check-input { background-color: #1E293B; border-color: #334155; }
        .modal-content.dark-modal .form-check-input:checked { background-color: var(--accent); border-color: var(--accent); }
        
        .date-btn { background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #e2e8f0; padding: 8px 16px; border-radius: 8px; font-size: var(--text-base); font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
        .date-btn:hover { border-color: var(--accent); color: #fff; }
        .date-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
        
        .logic-toggle .btn { border: 1px solid #334155; color: #94a3b8; background: transparent; }
        .logic-toggle .btn.active { background: var(--accent); color: #fff; border-color: var(--accent); }
        .filter-divider { border-top: 1px solid var(--border-color); margin: 24px 0; }
        .btn-reset { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #e2e8f0; padding: 10px 24px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s; }
        .btn-reset:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .btn-apply { background: var(--accent); border: none; color: #fff; padding: 10px 24px; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-apply:hover { background: #2563eb; }
        
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; backdrop-filter: blur(2px); }
        .sidebar-overlay.show { display: block; }
        
        /* FIX: Premium Dark Pagination Fix - Applies to all pages */
        .pagination {
            display: flex;
            list-style: none;
            padding-left: 0;
            border-radius: .25rem;
            gap: 8px;
            margin-top: 20px;
            justify-content: center;
        }
        .page-item .page-link {
            background-color: #1E293B !important;
            color: #e2e8f0 !important;
            border: 1px solid #334155 !important;
            border-radius: 10px !important;
            padding: 10px 18px !important;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .page-item.active .page-link {
            background-color: #3B82F6 !important;
            color: #fff !important;
            border-color: #3B82F6 !important;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .page-item.disabled .page-link {
            background-color: #111827 !important;
            color: #475569 !important;
            border-color: #1E293B !important;
            box-shadow: none;
        }
        .page-item .page-link:hover {
            background-color: #334155 !important;
            color: #fff !important;
            border-color: #3B82F6 !important;
        }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); z-index: 1100; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .mobile-toggle { display: block !important; }
            .header-search { display: none; }
        }
        .mobile-toggle { display: none; background: transparent; border: none; color: #fff; font-size: 24px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="logo">
            <i class="bi bi-lightning-charge-fill" style="color: var(--accent); font-size: 24px;"></i> Signs INC
        </a>
        <div class="nav-menu">
            <div class="nav-header">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-item {{ Route::is('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard Overview</a>
            <a href="{{ route('admin.leads.index') }}" class="nav-link-item {{ Route::is('admin.leads.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Leads Management</a>

            <div class="nav-header">Analytics</div>
            <a href="{{ route('admin.analytics.submissions') }}" class="nav-link-item {{ Route::is('admin.analytics.submissions') ? 'active' : '' }}"><i class="bi bi-check2-square"></i> Submitted Forms</a>
            <a href="{{ route('admin.analytics.abandoned') }}" class="nav-link-item {{ Route::is('admin.analytics.abandoned') ? 'active' : '' }}"><i class="bi bi-x-octagon-fill"></i> Abandoned Forms</a>
            <a href="{{ route('admin.analytics.visitors') }}" class="nav-link-item {{ Route::is('admin.analytics.visitors') ? 'active' : '' }}"><i class="bi bi-people"></i> Total Visitors</a>
            <a href="{{ route('admin.sessions.index') }}" class="nav-link-item {{ Route::is('admin.sessions.*') ? 'active' : '' }}"><i class="bi bi-bar-chart-fill"></i> Analytics Overview</a>

            <div class="nav-header">System</div>
            <a href="{{ route('admin.settings.index') }}" class="nav-link-item {{ Route::is('admin.settings.*') ? 'active' : '' }}"><i class="bi bi-gear-fill"></i> Settings</a>
        </div>
        <div style="padding: 20px; border-top: 1px solid var(--border-color);">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="main-wrapper">
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-toggle" id="mobileToggleBtn"><i class="bi bi-list"></i></button>
                <form action="{{ route('admin.leads.index') }}" method="GET" class="header-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" placeholder="Search leads, IPs, visitors..." id="globalSearchInput" value="{{ request('search') }}">
                    <kbd>⌘K</kbd>
                </form>
            </div>
            <div class="header-actions d-flex align-items-center gap-3">
                <button type="button" class="btn-filter" data-bs-toggle="modal" data-bs-target="#globalFilterModal"><i class="bi bi-funnel-fill"></i> Filters</button>
                <div id="liveClock"><i class="bi bi-clock"></i> <span id="clockTime">--:--</span></div>
                <button class="icon-btn"><i class="bi bi-bell-fill"></i><span class="dot"></span></button>
                <div class="user-avatar">AD</div>
            </div>
        </header>
        
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <div class="modal fade" id="globalFilterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content dark-modal">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-sliders me-2"></i>Global Filters</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ strtok(url()->current(), '?') }}" method="GET" id="globalFilterForm">
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Visitor Information</label>
                                <select name="device" class="form-select mb-2">
                                    <option value="">All Devices</option>
                                    <option value="Desktop" {{ request('device') == 'Desktop' ? 'selected' : '' }}>Desktop</option>
                                    <option value="Mobile" {{ request('device') == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                                </select>
                                <select name="os" class="form-select mb-2">
                                    <option value="">All Operating Systems</option>
                                    <option value="Windows" {{ request('os') == 'Windows' ? 'selected' : '' }}>Windows</option>
                                    <option value="MacOS" {{ request('os') == 'MacOS' ? 'selected' : '' }}>MacOS</option>
                                </select>
                                <select name="browser" class="form-select">
                                    <option value="">All Browsers</option>
                                    <option value="Chrome" {{ request('browser') == 'Chrome' ? 'selected' : '' }}>Chrome</option>
                                    <option value="Safari" {{ request('browser') == 'Safari' ? 'selected' : '' }}>Safari</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Engagement & Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Traffic</option>
                                    <option value="Human" {{ request('status') == 'Human' ? 'selected' : '' }}>Human Only</option>
                                    <option value="Bot" {{ request('status') == 'Bot' ? 'selected' : '' }}>Bot Only</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-divider"></div>

                        <label class="form-label">Time & Date</label>
                        <p class="text-muted-custom mb-3">Filter by specific date ranges</p>
                        
                        <input type="hidden" name="date_range" id="dateRangeInput" value="{{ request('date_range', 'last_7_days') }}">
                        
                        <div class="d-flex flex-wrap gap-2 mb-3" id="dateBtnGroup">
                            <button type="button" class="date-btn {{ request('date_range', 'last_7_days') == 'today' ? 'active' : '' }}" data-range="today">Today</button>
                            <button type="button" class="date-btn {{ request('date_range') == 'yesterday' ? 'active' : '' }}" data-range="yesterday">Yesterday</button>
                            <button type="button" class="date-btn {{ request('date_range', 'last_7_days') == 'last_7_days' ? 'active' : '' }}" data-range="last_7_days">Last 7 Days</button>
                            <button type="button" class="date-btn {{ request('date_range') == 'last_30_days' ? 'active' : '' }}" data-range="last_30_days">Last 30 Days</button>
                            <button type="button" class="date-btn {{ request('date_range') == 'custom' ? 'active' : '' }}" data-range="custom">Custom</button>
                        </div>
                        
                        <input type="hidden" name="date_from" id="hiddenDateFrom" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="hiddenDateTo" value="{{ request('date_to') }}">
                        
                        <div id="customDateContainer" style="display: {{ request('date_range') == 'custom' ? 'flex' : 'none' }};" class="gap-3 mb-3">
                            <input type="date" name="date_from" id="customDateFrom" class="form-control" style="max-width: 200px;" value="{{ request('date_from') }}">
                            <input type="date" name="date_to" id="customDateTo" class="form-control" style="max-width: 200px;" value="{{ request('date_to') }}">
                        </div>

                        <div class="filter-divider"></div>

                        <label class="form-label">Geography</label>
                        <p class="text-muted-custom mb-3">Target specific locations</p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="country" class="form-select">
                                    <option value="">All countries</option>
                                    <option value="US" {{ request('country') == 'US' ? 'selected' : '' }}>United States</option>
                                    <option value="AU" {{ request('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="state" class="form-select">
                                    <option value="">Select state</option>
                                    <option value="CA" {{ request('state') == 'CA' ? 'selected' : '' }}>California</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="city" class="form-select">
                                    <option value="">Select city</option>
                                    <option value="LA" {{ request('city') == 'LA' ? 'selected' : '' }}>Los Angeles</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="exclude_location" id="excludeLocation" value="1" {{ request('exclude_location') ? 'checked' : '' }}>
                            <label class="form-check-label" for="excludeLocation">Exclude selection</label>
                        </div>

                        <div class="filter-divider"></div>

                        <label class="form-label">Activity & Path Builder</label>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <select name="min_pages" class="form-select">
                                    <option value="">Minimum pages viewed</option>
                                    <option value="1" {{ request('min_pages') == '1' ? 'selected' : '' }}>1+ pages</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="min_activities" class="form-select">
                                    <option value="">Minimum activities</option>
                                    <option value="1" {{ request('min_activities') == '1' ? 'selected' : '' }}>1+ activities</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="min_clicks" class="form-select">
                                    <option value="">Minimum clicks</option>
                                    <option value="1" {{ request('min_clicks') == '1' ? 'selected' : '' }}>1+ clicks</option>
                                </select>
                            </div>
                        </div>

                        <label class="form-label mt-4">Visited URL Conditions</label>
                        <div id="urlConditionsContainer">
                            <div class="input-group mb-2">
                                <select class="form-select" style="max-width: 140px;" name="url_condition_type[]">
                                    <option value="contains">Contains</option>
                                    <option value="equals">Equals</option>
                                    <option value="starts_with">Starts with</option>
                                </select>
                                <input type="text" class="form-control" name="visited_url[]" placeholder="Enter URL here">
                                <button class="btn btn-outline-danger remove-url-btn" type="button"><i class="bi bi-dash"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addUrlBtn"><i class="bi bi-plus-lg"></i> Add URL</button>
                    </div>
                    
                    <div class="modal-footer">
                        <a href="{{ strtok(url()->current(), '?') }}" class="btn-reset">Reset</a>
                        <button type="submit" class="btn-apply">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Clock
            function updateClock() {
                const now = new Date();
                let h = now.getHours();
                const m = now.getMinutes().toString().padStart(2, '0');
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                document.getElementById('clockTime').innerText = h + ':' + m + ' ' + ampm;
            }
            setInterval(updateClock, 1000); updateClock();

            // Sidebar
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            document.getElementById('mobileToggleBtn')?.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
            overlay?.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });

            // Date Range
            const dateBtns = document.querySelectorAll('.date-btn');
            const dateRangeInput = document.getElementById('dateRangeInput');
            const customDateContainer = document.getElementById('customDateContainer');
            const hiddenDateFrom = document.getElementById('hiddenDateFrom');
            const hiddenDateTo = document.getElementById('hiddenDateTo');
            const customDateFrom = document.getElementById('customDateFrom');
            const customDateTo = document.getElementById('customDateTo');

            function calculateDates(range) {
                let today = new Date();
                let startDate = new Date();
                let endDate = new Date();
                
                if (range === 'today') { startDate = today; endDate = today; } 
                else if (range === 'yesterday') { startDate.setDate(today.getDate() - 1); endDate.setDate(today.getDate() - 1); } 
                else if (range === 'last_7_days') { startDate.setDate(today.getDate() - 7); endDate = today; } 
                else if (range === 'last_30_days') { startDate.setDate(today.getDate() - 30); endDate = today; }
                
                const formatDate = (d) => {
                    let month = '' + (d.getMonth() + 1), day = '' + d.getDate(), year = d.getFullYear();
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    return [year, month, day].join('-');
                };
                return { start: formatDate(startDate), end: formatDate(endDate) };
            }

            function handleDateChange(range) {
                dateBtns.forEach(b => b.classList.remove('active'));
                const activeBtn = document.querySelector(`.date-btn[data-range="${range}"]`);
                if(activeBtn) activeBtn.classList.add('active');
                
                dateRangeInput.value = range;

                if (range === 'custom') {
                    customDateContainer.style.display = 'flex';
                    if(hiddenDateFrom) hiddenDateFrom.disabled = true;
                    if(hiddenDateTo) hiddenDateTo.disabled = true;
                    if(customDateFrom) customDateFrom.disabled = false;
                    if(customDateTo) customDateTo.disabled = false;
                } else {
                    customDateContainer.style.display = 'none';
                    if(hiddenDateFrom) hiddenDateFrom.disabled = false;
                    if(hiddenDateTo) hiddenDateTo.disabled = false;
                    if(customDateFrom) customDateFrom.disabled = true;
                    if(customDateTo) customDateTo.disabled = true;

                    const dates = calculateDates(range);
                    if(hiddenDateFrom) hiddenDateFrom.value = dates.start;
                    if(hiddenDateTo) hiddenDateTo.value = dates.end;
                }
            }

            dateBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    handleDateChange(this.getAttribute('data-range'));
                });
            });

            // Trigger initial calculation on page load if not custom
            const initialRange = dateRangeInput.value || 'last_7_days';
            if(initialRange !== 'custom') {
                handleDateChange(initialRange);
            }

            // URL Builder
            const addUrlBtn = document.getElementById('addUrlBtn');
            const urlContainer = document.getElementById('urlConditionsContainer');
            if(addUrlBtn && urlContainer) {
                addUrlBtn.addEventListener('click', () => {
                    const html = `
                        <div class="input-group mb-2">
                            <select class="form-select" style="max-width: 140px;" name="url_condition_type[]">
                                <option value="contains">Contains</option>
                                <option value="equals">Equals</option>
                                <option value="starts_with">Starts with</option>
                            </select>
                            <input type="text" class="form-control" name="visited_url[]" placeholder="Enter URL here">
                            <button class="btn btn-outline-danger remove-url-btn" type="button"><i class="bi bi-dash"></i></button>
                        </div>`;
                    urlContainer.insertAdjacentHTML('beforeend', html);
                });
                urlContainer.addEventListener('click', (e) => {
                    if(e.target.closest('.remove-url-btn') && urlContainer.children.length > 1) {
                        e.target.closest('.input-group').remove();
                    }
                });
            }

            // Remove empty URLs before submit
            document.getElementById('globalFilterForm')?.addEventListener('submit', () => {
                document.querySelectorAll('input[name="visited_url[]"]').forEach(i => { if(!i.value.trim()) i.closest('.input-group').remove(); });
            });
        });
    </script>
</body>
</html>