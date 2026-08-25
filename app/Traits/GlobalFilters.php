<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

trait GlobalFilters
{
    public function applyGlobalFilters($query, $request, $tableName)
    {
        // 1. Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search, $tableName) {
                if (Schema::hasColumn($tableName, 'name')) $q->orWhere('name', 'like', "%{$search}%");
                if (Schema::hasColumn($tableName, 'ip_address')) $q->orWhere('ip_address', 'like', "%{$search}%");
                if (Schema::hasColumn($tableName, 'email')) $q->orWhere('email', 'like', "%{$search}%");
                if (Schema::hasColumn($tableName, 'phone')) $q->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 2. Device, OS, Browser & Status Filters
        if ($request->filled('device')) {
            if (Schema::hasColumn($tableName, 'device_type')) $query->where('device_type', $request->device);
            elseif (Schema::hasColumn($tableName, 'device')) $query->where('device', $request->device);
        }
        if ($request->filled('os') && Schema::hasColumn($tableName, 'os')) $query->where('os', $request->os);
        if ($request->filled('browser') && Schema::hasColumn($tableName, 'browser')) $query->where('browser', $request->browser);
        
        if ($request->filled('status') && Schema::hasColumn($tableName, 'is_spam')) {
            $query->where('is_spam', $request->status === 'Bot' ? 1 : 0);
        }

        // 3. Time & Date Filter (FIX: Safe for sessions table using last_activity)
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $startDate = Carbon::parse($request->date_from)->startOfDay();
            $endDate = Carbon::parse($request->date_to)->endOfDay();
            
            if (Schema::hasColumn($tableName, 'created_at')) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (Schema::hasColumn($tableName, 'last_activity')) {
                // Sessions table uses last_activity as timestamp
                $query->whereBetween('last_activity', [$startDate->timestamp, $endDate->timestamp]);
            }
        }

        // 4. Geography Filters
        $excludeLocation = $request->filled('exclude_location');
        $operator = $excludeLocation ? '!=' : '=';
        
        if ($request->filled('country') && Schema::hasColumn($tableName, 'country')) $query->where('country', $operator, $request->country);
        if ($request->filled('state') && Schema::hasColumn($tableName, 'state')) $query->where('state', $operator, $request->state);
        if ($request->filled('city') && Schema::hasColumn($tableName, 'city')) $query->where('city', $operator, $request->city);

        // 5. Activity & Path Builder Minimums
        if ($request->filled('min_pages') && Schema::hasColumn($tableName, 'pages_viewed')) $query->where('pages_viewed', '>=', $request->min_pages);
        if ($request->filled('min_activities') && Schema::hasColumn($tableName, 'activities_count')) $query->where('activities_count', '>=', $request->min_activities);
        if ($request->filled('min_clicks') && Schema::hasColumn($tableName, 'clicks_count')) $query->where('clicks_count', '>=', $request->min_clicks);

        // 6. Visited URL Filter
        if ($request->filled('visited_url') && Schema::hasColumn($tableName, 'session_id')) {
            $urls = $request->input('visited_url', []);
            $types = $request->input('url_condition_type', []);
            
            $query->where(function($q) use ($urls, $types) {
                foreach ($urls as $index => $url) {
                    if (!empty($url)) {
                        $type = $types[$index] ?? 'contains';
                        $q->orWhereIn('session_id', function($sub) use ($url, $type) {
                            $sub->select('session_id')->from('page_views');
                            if ($type == 'equals') $sub->where('page_url', $url);
                            elseif ($type == 'starts_with') $sub->where('page_url', 'like', "{$url}%");
                            else $sub->where('page_url', 'like', "%{$url}%");
                        });
                    }
                }
            });
        }

        return $query;
    }
}