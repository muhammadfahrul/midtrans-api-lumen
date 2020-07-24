<?php

namespace App\Http\Controllers;

use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        Log::info('Showing all author');

        return response()->json([
            "results" => $data
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

        Log::info('Showing author by id');

        return response()->json([
            "results" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required',
            'transaction_id' => 'required',
            'payment_type' => 'required',
            'gross_amount' => 'required',
            'transaction_time' => 'required',
            'transaction_status' => 'required'
        ]);
        
        $data = new Payment();
        $data->order_id = $request->input('order_id');
        $data->transaction_id = $request->input('transaction_id');
        $data->payment_type = $request->input('payment_type');
        $data->gross_amount = $request->input('gross_amount');
        $data->transaction_time = $request->input('transaction_time');
        $data->transaction_status = $request->input('transaction_status');
        $data->save();

        Log::info('Adding author');

        return response()->json([
            "message" => "Success Added",
            "results" => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'order_id' => 'required',
            'transaction_id' => 'required',
            'payment_type' => 'required',
            'gross_amount' => 'required',
            'transaction_time' => 'required',
            'transaction_status' => 'required'
        ]);
        
        $data = Payment::find($id);
        if ($data) {
            $data->order_id = $request->input('order_id');
            $data->transaction_id = $request->input('transaction_id');
            $data->payment_type = $request->input('payment_type');
            $data->gross_amount = $request->input('gross_amount');
            $data->transaction_time = $request->input('transaction_time');
            $data->transaction_status = $request->input('transaction_status');
            $data->save();

            Log::info('Updating author by id');

            return response()->json([
                "message" => "Success Updated",
                "results" => $data
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

            Log::info('Deleting author by id');

            return response()->json([
                "message" => "Success Deleted",
                "results" => $data
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }
}
