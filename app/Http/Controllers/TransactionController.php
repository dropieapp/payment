<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function viewTransaction(Request $request, $id)
    {
        $userRecord = DB::table('transactions')->where('customer_id', $id)->get();

        return response()->json([
            'Status' => true,
            'Message' => 'Success',
            'Data' => $userRecord
        ]);
    }

    public function completedTransaction(Request $request, $id)
    {
        $userRecord = DB::table('transactions')->where([
            ['customer_id', $id],
            ['transaction_status', 'success'],
        ])->get();

        return $userRecord;
    }

    public function failedTransaction(Request $request, $id)
    {
        $userRecord = DB::table('transactions')->where([
            ['customer_id', $id],
            ['transaction_status', 'failed'],
        ])->get();

        return response()->json([
            'Status' => true,
            'Message' => 'Success',
            'Data' => $userRecord
        ]);
    }
}
