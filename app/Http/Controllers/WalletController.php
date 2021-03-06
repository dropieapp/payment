<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnValue;

class WalletController extends Controller
{
    public $returnRef;

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

        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $request->input('email'),
            'amount' => $request->input('amount'),
            'callback_url' => 'http://127.0.0.1:8000/api/customer/{id}/wallet/verify'
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

        $transaction = json_decode($result);

        #Redirect to the payment page
        // return Redirect::away($transaction->data->authorization_url);

        $refUrl = urldecode($transaction->data->authorization_url);

        return $result;
    }

    public function verifyTransaction(Request $request, $id)
    {
        $customerId = DB::table('wallets')->where('customer_id', $id)->get('customer_id');

        $curl = curl_init();

        if (!empty($_GET["reference"])) {
            # clean the reference code
            $sanitize = filter_var_array($_GET, FILTER_SANITIZE_STRING);
            $reference = rawurldecode($sanitize["reference"]);
        } else {
            return response()->json(['status' => true, 'message' => 'No reference was supplied']);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer sk_test_3d67c9186567201c64426b0e281350d86489cabe",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        # Send data to the database
        $transaction = json_decode($response);

        $storeData = [
            'customer_id' => $id,
            // 'transaction_id' => $transaction->data->id,
            'transaction_id' => '874528',
            'transaction_status' => $transaction->data->status,
            'transaction_reference' => $transaction->data->reference,
            'transaction_amount' => $transaction->data->amount * 0.01,
            'transaction_date_created' => $transaction->data->created_at = date("Y-m-d H:i:s"),
            'transaction_paid_at' => $transaction->data->paid_at = date("Y-m-d H:i:s"),
            'transaction_currency' => $transaction->data->currency,
            'bank_of_transfer' => $transaction->data->authorization->bank,
            'channel_of_transfer' => $transaction->data->authorization->channel,
            'card_type_on_transfer' => $transaction->data->authorization->card_type,
            'customer_payment_id' => $transaction->data->customer->id,
        ];

        # Store the transaction data in the database
        $createTransaction = Transaction::create($storeData);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $createTransaction;
        }
    }

    public function updateWallet(Request $request, $id)
    {
        # Get the last transaction record of the customer
        $lastRecord = DB::table('transactions')->where([
            ['customer_id', $id],
            ['transaction_status', 'success'],
        ])->get('transaction_amount')->last();

        # Get the amount of the customer from the wallet table
        $initialAmount = DB::table('wallets')->where('customer_id', $id)->get('amount');

        $newAmount = $initialAmount[0]->amount + $lastRecord->transaction_amount;

        $updateWallet = DB::table('wallets')->where('customer_id', $id)->update(['amount' => $newAmount]);

        return response()->json([
            'Status' => true,
            'Message' => 'The update was successful',
            'Data' => $updateWallet
        ]);
    }
}
