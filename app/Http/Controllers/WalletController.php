<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class WalletController extends Controller
{
    public function viewWallet(Request $request, $id)
    {
        //Get the customer ID from the database
        $user = DB::table('users')->where('id', $id)->value('id');
        $walletData = DB::table('wallets')->where('customer_id', $user)->get();

        //Check if the customer has created a wallet
        if ($walletData->isEmpty()) {
            return response()->json([
                'Status' => false,
                'Message' => 'There is no wallet for this customer',
                'Data' => NULL
            ]);
        } else {
            return response()->json([
                'Status' => true,
                'Message' => 'Successful',
                'Data' => $walletData
            ]);
        }
    }

    public function createWallet(Request $request, $id)
    {
        //Get the customer ID from the database
        $customerId = DB::table('users')->where('id', $id)->value('id');

        $data = [
            'customer_id' => $customerId,
        ];

        $createWallet = Wallet::create($data);

        if ($createWallet == true) {
            return response()->json([
                'Status' => true,
                'Message' => 'Customer wallet created successfully',
                'Data' => $createWallet
            ]);
        } else {
            return response()->json([
                'Status' => false,
                'Message' => 'There was an error while creating the wallet',
                'Data' => $createWallet
            ]);
        }
    }

    public function fundWallet(Request $request, $id)
    {
        //Get the customer ID from the database
        $customerId = DB::table('users')->where('id', $id)->get('id');

        $customerEmail = DB::table('users')->where('id', $id)->get('email');

        //Launch the paystack payment gateway
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $request->input('email'),
            'amount' => $request->input('amount'),
        ];
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_test_3d67c9186567201c64426b0e281350d86489cabe",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        // echo $result;
        $returnUrl = json_decode($result)->data->authorization_url;
        return Redirect::away($returnUrl);
    }
}
