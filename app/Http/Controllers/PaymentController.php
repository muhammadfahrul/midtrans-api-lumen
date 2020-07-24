<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Midtrans\Config;

// Midtrans API Resources
use App\Http\Controllers\Midtrans\Transaction;

// Plumbing
use App\Http\Controllers\Midtrans\ApiRequestor;
use App\Http\Controllers\Midtrans\SnapApiRequestor;
use App\Http\Controllers\Midtrans\Notification;
use App\Http\Controllers\Midtrans\CoreApi;
use App\Http\Controllers\Midtrans\Snap;

// Sanitization
use App\Http\Controllers\Midtrans\Sanitizer;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function showAll()
    {
        $data = Payment::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all payment');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    // public function showAllJoin()
    // {
    //     $data = Payment::with(array('order'=>function($query){
    //         $query->select();
    //     }))->get();
    //     if(!$data) {
    //         return response()->json([
    //             "message" => "Data Not Found"
    //         ]);
    //     }

    //     Log::info('Showing all payment');

    //     return response()->json([
    //         "message" => "Success retrieve data",
    //         "status" => true,
    //         "data" => $data
    //     ]);
    // }

    public function showId($id)
    {
        $data = Payment::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing payment by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    // public function showIdJoin($id)
    // {
    //     $findId = Payment::find($id);
    //     $data = Payment::where('id', $id)->with(array('order'=>function($query){
    //         $query->select();
    //     }))->get();
    //     if(!$findId) {
    //         return response()->json([
    //             "message" => "Parameter Not Found"
    //         ]);
    //     }

    //     Log::info('Showing payment with post comment by id');

    //     return response()->json([
    //         "message" => "Success retrieve data",
    //         "status" => true,
    //         "data" => $data
    //     ]);
    // }

    public function add(Request $request)
    {
        // $this->validate($request, [
        //     'data.attributes.payment_type' => 'required',
        //     'data.attributes.gross_amount' => 'required',
        //     'data.attributes.bank' => 'required',
        //     'data.attributes.order_id' => 'required|exists:orders,id'
        // ]);
        
        // $data = new Payment();
        // $data->payment_type = $request->input('data.attributes.payment_type');
        // $data->gross_amount = $request->input('data.attributes.gross_amount');
        // $data->bank = $request->input('data.attributes.bank');
        // $data->order_id = $request->input('data.attributes.order_id');
        // $data->save();

        // Log::info('Adding payment');

        // return response()->json([
        //     "message" => "Success Added",
        //     "status" => true,
        //     "data" => [
        //         "attributes" => $data
        //     ]
        // ]);
        
        $item_list = array();
        $amount = 0;
        Config::$serverKey = 'SB-Mid-server-VbqKS4xIPoo0ZR3Qu3xKt8Jj';
        if (!isset(Config::$serverKey)) {
            return "Please set your payment server key";
        }
        Config::$isSanitized = true;

        // Enable 3D-Secure
        Config::$is3ds = true;
        
        // Required

        $item_list[] = [
                'id' => "151",
                'price' => 20000,
                'quantity' => 4,
                'name' => "Apple"
        ];

        $transaction_details = array(
            'order_id' => rand(),
            'gross_amount' => 20000, // no decimal allowed for creditcard
        );


        // Optional
        $item_details = $item_list;

        // Optional
        $billing_address = array(
            'first_name'    => "Andri",
            'last_name'     => "Litani",
            'address'       => "Mangga 20",
            'city'          => "Jakarta",
            'postal_code'   => "16602",
            'phone'         => "081122334455",
            'country_code'  => 'IDN'
        );

        // Optional
        $shipping_address = array(
            'first_name'    => "Obet",
            'last_name'     => "Supriadi",
            'address'       => "Manggis 90",
            'city'          => "Jakarta",
            'postal_code'   => "16601",
            'phone'         => "08113366345",
            'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => "Andri",
            'last_name'     => "Litani",
            'email'         => "andri@litani.com",
            'phone'         => "081122334455",
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );

        // Optional, remove this to display all available payment methods
        $enable_payments = array('bank_transfer');

        // Fill transaction details
        $transaction = array(
            'enabled_payments' => $enable_payments,
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );
        // return $transaction;
        try {
            $snapToken = Snap::getSnapToken($transaction);

            $data = new Payment();
            $data->payment_type = $request->input('data.attributes.payment_type');
            $data->gross_amount = $request->input('data.attributes.gross_amount');
            // $data->bank = $request->input('data.attributes.bank');
            $data->order_id = $request->input('data.attributes.order_id');
            $data->transaction_id = $transaction_details['order_id'];
            $data->transaction_time = "";
            $data->transaction_status = "";
            $data->save();

            // return response()->json($snapToken);
            return response()->json([
                "message" => "Transaction added successfully",
                "status" => true,
                "results" => $snapToken,
                // "data" => [
                //     "attributes" => $data
                // ]
            ]);
        } catch (\Exception $e) {
            dd($e);
            // return ['code' => 0 , 'message' => 'failed'];
            return response()->json([
                "message" => "failed",
                "status" => false,
                // "results" => $snapToken,
                // "data" => [
                //     "attributes" => $data
                // ]
            ]);
        }

    }

    public function update(Request $request, $id)
    {
        // $this->validate($request, [
        //     'data.attributes.payment_type' => 'required',
        //     'data.attributes.gross_amount' => 'required',
        //     'data.attributes.bank' => 'required',
        //     'data.attributes.order_id' => 'required|exists:orders,id'
        // ]);
        
        // $data = Payment::find($id);
        // if ($data) {
        //     $data->payment_type = $request->input('data.attributes.payment_type');
        //     $data->gross_amount = $request->input('data.attributes.gross_amount');
        //     $data->bank = $request->input('data.attributes.bank');
        //     $data->order_id = $request->input('data.attributes.order_id');
        //     $data->save();

        //     Log::info('Updating payment by id');

        //     return response()->json([
        //         "message" => "Success Updated",
        //         "status" => true,
        //         "data" => [
        //             "attributes" => $data
        //         ]
        //     ]);        
        // }else {
        //     return response()->json([
        //         "message" => "Parameter Not Found"
        //     ]);
        // }

        $item_list = array();
        $amount = 0;
        Config::$serverKey = 'SB-Mid-server-VbqKS4xIPoo0ZR3Qu3xKt8Jj';
        if (!isset(Config::$serverKey)) {
            return "Please set your payment server key";
        }
        Config::$isSanitized = true;

        // Enable 3D-Secure
        Config::$is3ds = true;
        
        // Required

        $item_list[] = [
                'id' => "124",
                'price' => 12000,
                'quantity' => 5,
                'name' => "Mango"
        ];

        $transaction_details = array(
            'order_id' => rand(),
            'gross_amount' => 12000, // no decimal allowed for creditcard
        );


        // Optional
        $item_details = $item_list;

        // Optional
        $billing_address = array(
            'first_name'    => "Hujani",
            'last_name'     => "Yunani",
            'address'       => "Hukal 20",
            'city'          => "Jakarta",
            'postal_code'   => "51268",
            'phone'         => "083256472910",
            'country_code'  => 'IDN'
        );

        // Optional
        $shipping_address = array(
            'first_name'    => "Tukaki",
            'last_name'     => "Rekosil",
            'address'       => "Gujahi 90",
            'city'          => "Jakarta",
            'postal_code'   => "24115",
            'phone'         => "085152726491",
            'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => "Budi",
            'last_name'     => "Badi",
            'email'         => "buba@gmail.com",
            'phone'         => "081245162718",
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );

        // Optional, remove this to display all available payment methods
        $enable_payments = array('bank_transfer');

        // Fill transaction details
        $transaction = array(
            'enabled_payments' => $enable_payments,
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );
        // return $transaction;
        try {
            $snapToken = Snap::getSnapToken($transaction);

            $data = Payment::find($id);
            $data->payment_type = $request->input('data.attributes.payment_type');
            $data->gross_amount = $request->input('data.attributes.gross_amount');
            // $data->bank = $request->input('data.attributes.bank');
            $data->order_id = $request->input('data.attributes.order_id');
            $data->transaction_id = $transaction_details['order_id'];
            $data->transaction_time = "";
            $data->transaction_status = "";
            $data->save();

            // return response()->json($snapToken);
            return response()->json([
                "message" => "Transaction updated successfully",
                "status" => true,
                "results" => $snapToken,
                // "data" => [
                //     "attributes" => $data
                // ]
            ]);
        } catch (\Exception $e) {
            dd($e);
            // return ['code' => 0 , 'message' => 'failed'];
            return response()->json([
                "message" => "failed",
                "status" => false,
                // "results" => $snapToken,
                // "data" => [
                //     "attributes" => $data
                // ]
            ]);
        }

    }

    public function delete($id)
    {
        $data = Payment::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting payment by id');

            return response()->json([
                "message" => "Transaction deleted successfully",
                "status" => true,
                "data" => [
                    "attributes" => $data
                ]
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }

    public function midtransPush(Request $request)
    {
        
    }
}
