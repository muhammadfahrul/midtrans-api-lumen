<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderItem;
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

    // public function showAll()
    // {
    //     $data = Order::all();
    //     if(!$data) {
    //         return response()->json([
    //             "message" => "Data Not Found"
    //         ]);
    //     }

    //     Log::info('Showing all order');

    //     return response()->json([
    //         "message" => "Success retrieve data",
    //         "status" => true,
    //         "data" => $data
    //     ]);
    // }

    public function showAllJoin()
    {
        $data = Order::with(array('orderitem'=>function($query){
            $query->select();
        }))->get();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all order');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    // public function showId($id)
    // {
    //     $data = Order::find($id);
    //     if(!$data) {
    //         return response()->json([
    //             "message" => "Parameter Not Found"
    //         ]);
    //     }

    //     Log::info('Showing order by id');

    //     return response()->json([
    //         "message" => "Success retrieve data",
    //         "status" => true,
    //         "data" => $data
    //     ]);
    // }

    public function showIdJoin($id)
    {
        $findId = Order::find($id);
        $data = Order::where('id', $id)->with(array('orderitem'=>function($query){
            $query->select();
        }))->get();
        if(!$findId) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing order with post comment by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'data.attributes.user_id' => 'required|exists:customers,id'
        ]);
        
        $order = new Order();
        $order->user_id = $request->input('data.attributes.user_id');
        $order->status = "created";
        $order->save();

        $order_detail = $request->input('data.attributes.order_detail');

        for ($i=0; $i < count($order_detail); $i++) { 
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $request->input('data.attributes.order_detail.'.$i.'.product_id');
            $order_item->quantity = $request->input('data.attributes.order_detail.'.$i.'.quantity');
            $order->orderitem()->save($order_item);
        }

        Log::info('Adding order');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => [
                "attributes" => $order
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'data.attributes.user_id' => 'required|exists:customers,id'
        ]);
        
        $order = Order::find($id);
        if ($order) {
            $order->user_id = $request->input('data.attributes.user_id');
            $order->status = "created";
            $order->save();

            $order_detail = $request->input('data.attributes.order_detail');

            for ($i=0; $i < count($order_detail); $i++) { 
                $order_item = OrderItem::where('order_id', $id)->first();
                $order_item->product_id = $request->input('data.attributes.order_detail.'.$i.'.product_id');
                $order_item->quantity = $request->input('data.attributes.order_detail.'.$i.'.quantity');
                $order->orderitem()->save($order_item);
            }

            Log::info('Updating order by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => true,
                "data" => [
                    "attributes" => $order
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
        $order = Order::find($id);
        if($order) {
            $order->delete();

            $order_item = OrderItem::where('order_id', $id)->delete();

            Log::info('Deleting order by id');

            return response()->json([
                "message" => "Success Deleted",
                "status" => true,
                "data" => [
                    "attributes" => $order
                ]
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }
}
