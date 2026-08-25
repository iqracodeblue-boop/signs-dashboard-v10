<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\GlobalFilters;

class LeadController extends Controller
{
    use GlobalFilters;

    public function index(Request $request)
    {
        $query = Lead::query();

        // 1. Source Filter (Specific to Leads only)
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // 2. Apply Global Filters via Trait (Search, Device, OS, Date, Country, URL etc.)
        $query = $this->applyGlobalFilters($query, $request, 'leads');

        $leads = $query->latest()->paginate(15);
        
        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead)
    {
        return view('admin.leads.show', compact('lead'));
    }

    public function sendToAirtable(Lead $lead)
    {
        if ($lead->airtable_id) {
            return redirect()->back()->with('error', 'Already in Airtable.');
        }

        try {
            $airtableResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('AIRTABLE_API_TOKEN'),
                'Content-Type' => 'application/json',
            ])->post('https://api.airtable.com/v0/' . env('AIRTABLE_BASE_ID') . '/' . env('AIRTABLE_TABLE_NAME'), [
                'records' => [
                    [
                        'fields' => [
                            'Name' => $lead->name ?? 'N/A',
                            'Email' => $lead->email ?? 'N/A',
                            'Phone' => $lead->phone ?? 'N/A',
                            'Sign Type' => $lead->sign_type ?? 'N/A',
                            'Source' => $lead->source ?? 'N/A',
                            'SpamStatus' => ($lead->is_spam == '1') ? 'true' : 'false', 
                            'Laravel ID' => $lead->id
                        ]
                    ]
                ]
            ]);

            if ($airtableResponse->successful()) {
                $airtableData = $airtableResponse->json();
                $airtableId = $airtableData['records'][0]['id'] ?? null;
                
                if ($airtableId) {
                    $lead->airtable_id = $airtableId;
                    $lead->save();
                }
                return redirect()->back()->with('success', 'Lead sent to Airtable successfully!');
            }

            $errorMsg = $airtableResponse->json('error.message') ?? 'Unknown Airtable Error';
            Log::error('Airtable API Error: ' . $errorMsg);
            return redirect()->back()->with('error', 'Failed: ' . $errorMsg);

        } catch (\Exception $e) {
            Log::error('Airtable Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }
}