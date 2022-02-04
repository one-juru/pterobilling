<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'server_id',
        'total',
        'credit',
        'late_fee',
        'payment_method',
        'due_date',
        'paid',
    ];
}
