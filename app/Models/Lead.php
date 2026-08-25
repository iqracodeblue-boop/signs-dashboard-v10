<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
   protected $fillable = [
    'uuid', 'name', 'email', 'phone', 'business', 'message', 
    'source', 'sign_type', 'dimensions', 'details',
    'is_spam', 'company_website', 'bot_reason',
    'image_url', 'referrer_url', 'ip_address', 'user_agent', 
    'device_type', 'browser', 'os', 'airtable_id',
    'visitor_id', 'session_id' // Naye columns add kiye
];
}