<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Customer;
use App\Order;
use App\OrderItem;
use App\Product;
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
        $this->validate($request, [
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            // 'data.attributes.bank' => 'required',
            'data.attributes.order_id' => 'required|exists:orders,id'
        ]);
        
        $data = new Payment();
        $data->payment_type = $request->input('data.attributes.payment_type');
        $data->gross_amount = $request->input('data.attributes.gross_amount');
        // $data->bank = $request->input('data.attributes.bank');
        $data->order_id = $request->input('data.attributes.order_id');
        $data->transaction_id = 1;
        $data->transaction_time = "";
        $data->transaction_status = "";
        $data->save();

        Log::info('Adding payment');

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
        
        $orderitem = OrderItem::where('order_id', $data->order_id)->with(array('product'=>function($query){
            $query->select();
        }))->get();
        $array_item = [];
        for ($i=0; $i < count($orderitem); $i++) { 
            $array_item['id'] = $orderitem[$i]['product']['id'];
            $array_item['price'] = $orderitem[$i]['product']['price'];
            $array_item['quantity'] = $orderitem[$i]['quantity'];
            $array_item['name'] = $orderitem[$i]['product']['name'];
        }

        // Required
        $item_details[] = $array_item;

        $transaction_details = array(
            'order_id' => $data->order_id,
            'gross_amount' => $data->gross_amount, // no decimal allowed for creditcard
        );

        $order = Order::find($data->order_id);
        $customer = Customer::find($order->user_id);

        // Optional
        $customer_details = array(
            'full_name' => $customer->full_name,
            'username' => $customer->username,
            'email' => $customer->email,
            'phone_number' => $customer->phone_number
        );

        // Optional, remove this to display all available payment methods
        $enable_payments = array($data->payment_type);

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

            // return response()->json($snapToken);
            return response()->json([
                "message" => "Transaction added successfully",
                "status" => true,
                "results" => $snapToken,
                "data" => [
                    "attributes" => $data
                ]
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
        $this->validate($request, [
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            // 'data.attributes.bank' => 'required',
            'data.attributes.order_id' => 'required|exists:orders,id'
        ]);
        
        $data = Payment::find($id);
        if ($data) {
            $data->payment_type = $request->input('data.attributes.payment_type');
            $data->gross_amount = $request->input('data.attributes.gross_amount');
            // $data->bank = $request->input('data.attributes.bank');
            $data->order_id = $request->input('data.attributes.order_id');
            $data->transaction_id = 1;
            $data->transaction_time = "";
            $data->transaction_status = "";
            $data->save();

            Log::info('Updating payment by id');

            // return response()->json([
            //     "message" => "Success Updated",
            //     "status" => true,
            //     "data" => [
            //         "attributes" => $data
            //     ]
            // ]);        
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        $item_list = array();
        $amount = 0;
        Config::$serverKey = 'SB-Mid-server-VbqKS4xIPoo0ZR3Qu3xKt8Jj';
        if (!isset(Config::$serverKey)) {
            return "Please set your payment server key";
        }
        Config::$isSanitized = true;

        // Enable 3D-Secure
        Config::$is3ds = true;
        
        $orderitem = OrderItem::where('order_id', $data->order_id)->with(array('product'=>function($query){
            $query->select();
        }))->get();
        $array_item = [];
        for ($i=0; $i < count($orderitem); $i++) { 
            $array_item['id'] = $orderitem[$i]['product']['id'];
            $array_item['price'] = $orderitem[$i]['product']['price'];
            $array_item['quantity'] = $orderitem[$i]['quantity'];
            $array_item['name'] = $orderitem[$i]['product']['name'];
        }

        // Required
        $item_details[] = $array_item;

        $transaction_details = array(
            'order_id' => $data->order_id,
            'gross_amount' => $data->gross_amount, // no decimal allowed for creditcard
        );

        $order = Order::find($data->order_id);
        $customer = Customer::find($order->user_id);

        // Optional
        $customer_details = array(
            'full_name' => $customer->full_name,
            'username' => $customer->username,
            'email' => $customer->email,
            'phone_number' => $customer->phone_number
        );

        // Optional, remove this to display all available payment methods
        $enable_payments = array($data->payment_type);

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

            // return response()->json($snapToken);
            return response()->json([
                "message" => "Transaction updated successfully",
                "status" => true,
                "results" => $snapToken,
                "data" => [
                    "attributes" => $data
                ]
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
