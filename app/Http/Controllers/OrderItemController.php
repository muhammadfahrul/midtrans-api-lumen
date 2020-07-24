<?php

namespace App\Http\Controllers;

use App\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
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
        $data = OrderItem::all();
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

    public function showAllJoin()
    {
        $data = OrderItem::with(array('order'=>function($query){
            $query->select();
        }))->with(array('product'=>function($query){
            $query->select();
        }))->get();
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
        $data = OrderItem::find($id);
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

    public function showIdJoin($id)
    {
        $findId = OrderItem::find($id);
        $data = OrderItem::where('id', $id)->with(array('order'=>function($query){
            $query->select();
        }))->with(array('product'=>function($query){
            $query->select();
        }))->get();
        if(!$findId) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing author with post comment by id');

        return response()->json([
            "results" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required'
        ]);
        
        $data = new OrderItem();
        $data->order_id = $request->input('order_id');
        $data->product_id = $request->input('product_id');
        $data->quantity = $request->input('quantity');
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
            'product_id' => 'required',
            'quantity' => 'required'
        ]);
        
        $data = OrderItem::find($id);
        if ($data) {
            $data->order_id = $request->input('order_id');
            $data->product_id = $request->input('product_id');
            $data->quantity = $request->input('quantity');
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
        $data = OrderItem::find($id);
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
