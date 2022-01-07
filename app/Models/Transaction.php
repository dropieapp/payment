<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Transaction extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'transaction_status',
        'transaction_reference',
        'transaction_amount',
        'transaction_date_created',
        'transaction_paid_at',
        'transaction_currency',
        'bank_of_transfer',
        'channel_of_transfer',
        'card_type_on_transfer',
        'customer_payment_id'
    ];
}
