<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetup extends Model
{
    protected $table = 'company_setup';

    protected $fillable = [
        'name', 'logo', 'address', 'phone', 'email',
        'website', 'gst_no', 'pan_no', 'created_by', 'modified_by',
    ];

    public static function current(): ?self
    {
        return static::first();
    }
}
