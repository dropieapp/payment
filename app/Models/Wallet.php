<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Wallet extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'customer_id',
        'amount',
    ];
}
