<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function view(Request $request, $id)
    {
        $userId = DB::table('users')->where('id', $id)->get();

        dd($userId);
    }

    public function getPaymentResponse()
    {
        $storeResult = new WalletController();
    }
}
