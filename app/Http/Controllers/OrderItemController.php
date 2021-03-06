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

        Log::info('Showing all order item');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
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

        Log::info('Showing all order item');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
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

        Log::info('Showing order item by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
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

        Log::info('Showing order item with post comment by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'data.attributes.order_id' => 'required|exists:orders,id',
            'data.attributes.product_id' => 'required|exists:products,id',
            'data.attributes.quantity' => 'required'
        ]);
        
        $data = new OrderItem();
        $data->order_id = $request->input('data.attributes.order_id');
        $data->product_id = $request->input('data.attributes.product_id');
        $data->quantity = $request->input('data.attributes.quantity');
        $data->save();

        Log::info('Adding order item');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => [
                "attributes" => $data
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'data.attributes.order_id' => 'required|exists:orders,id',
            'data.attributes.product_id' => 'required|exists:products,id',
            'data.attributes.quantity' => 'required'
        ]);
        
        $data = OrderItem::find($id);
        if ($data) {
            $data->order_id = $request->input('data.attributes.order_id');
            $data->product_id = $request->input('data.attributes.product_id');
            $data->quantity = $request->input('data.attributes.quantity');
            $data->save();

            Log::info('Updating order item by id');

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
        $data = OrderItem::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting order item by id');

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
}
