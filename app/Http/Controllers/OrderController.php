<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
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
        $data = Order::all();
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
        $data = Order::find($id);
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
            'user_id' => 'required',
            'status' => 'required'
        ]);
        
        $data = new Order();
        $data->user_id = $request->input('user_id');
        $data->status = $request->input('status');
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
            'user_id' => 'required',
            'status' => 'required'
        ]);
        
        $data = Order::find($id);
        if ($data) {
            $data->user_id = $request->input('user_id');
            $data->status = $request->input('status');
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
        $data = Order::find($id);
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
