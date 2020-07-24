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

    public function showAllJoin()
    {
        $data = Payment::with(array('order'=>function($query){
            $query->select();
        }))->get();
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

    public function showIdJoin($id)
    {
        $findId = Payment::find($id);
        $data = Payment::where('id', $id)->with(array('order'=>function($query){
            $query->select();
        }))->get();
        if(!$findId) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing payment with post comment by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            'data.attributes.bank' => 'required',
            'data.attributes.order_id' => 'required|exists:orders,id'
        ]);
        
        $payment = new Payment();
        $payment->payment_type = $request->input('data.attributes.payment_type');
        $payment->gross_amount = $request->input('data.attributes.gross_amount');
        $payment->bank = $request->input('data.attributes.bank');
        $payment->order_id = $request->input('data.attributes.order_id');
        $payment->save();

        $orderitem = OrderItem::with(array('product'=>function($query){
            $query->select();
        }))->get();

        $item_list[] = [
            'id' => $orderitem['data']['product']['id'],
            'price' => $orderitem['data']['product']['price'],
            'quantity' => $orderitem['data']['quantity'],
            'name' => $orderitem['data']['product']['name']
        ];

        $item_details = $item_list;

        Log::info('Adding payment');

        Config::$serverKey = 'SB-Mid-server-VbqKS4xIPoo0ZR3Qu3xKt8Jj';
        if(!isset(Config::$serverKey))
        {
            return "Please set your payment server key";
        }

        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        $enable_payments = array('bank_transfer');

        $transaction = array(
            'enabled_payments' => $payment->payment_type,
            'transaction_details' => $payment,
            'item_details' => $item_details,
        );

        try {
            $snapToken = Snap::createTransaction($transaction);

            return response()->json([
                'message' => 'Transaction successfully',
                'status' => true,
                'results' => $snapToken,
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            dd($e);
            return ['code' => 0 , 'message' => 'failed'];
        }

        // return response()->json([
        //     "message" => "Success Added",
        //     "status" => true,
        //     "data" => [
        //         "attributes" => $data
        //     ]
        // ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'data.attributes.order_id' => 'required|exists:orders,id',
            'data.attributes.transaction_id' => 'required',
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            'data.attributes.transaction_time' => 'required',
            'data.attributes.transaction_status' => 'required'
        ]);
        
        $data = Payment::find($id);
        if ($data) {
            $data->order_id = $request->input('data.attributes.order_id');
            $data->transaction_id = $request->input('data.attributes.transaction_id');
            $data->payment_type = $request->input('data.attributes.payment_type');
            $data->gross_amount = $request->input('data.attributes.gross_amount');
            $data->transaction_time = $request->input('data.attributes.transaction_time');
            $data->transaction_status = $request->input('data.attributes.transaction_status');
            $data->save();

            Log::info('Updating payment by id');

            return response()->json([
                "message" => "Success Updated",
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

    public function delete($id)
    {
        $data = Payment::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting payment by id');

            return response()->json([
                "message" => "Success Deleted",
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
