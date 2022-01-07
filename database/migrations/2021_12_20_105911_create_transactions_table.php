<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users');
            $table->string('transaction_id')->unique();
            $table->enum('transaction_status', ['success', 'failed']);
            $table->string('transaction_reference');
            $table->double('transaction_amount');
            $table->date('transaction_date_created');
            $table->date('transaction_paid_at');
            $table->string('transaction_currency');
            $table->string('bank_of_transfer');
            $table->enum('channel_of_transfer', ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer']);
            $table->enum('card_type_on_transfer', ['mastercard', 'visa', 'verve']);
            $table->string('customer_payment_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
