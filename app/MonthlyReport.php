<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $table = 'monthly_reports';
    protected $fillable = [
        'admin_id', 'month', 'report'
    ];
}
