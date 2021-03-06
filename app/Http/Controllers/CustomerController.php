<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
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
        $data = Customer::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all customer');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function showId($id)
    {
        $data = Customer::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing customer by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'data.attributes.full_name' => 'required',
            'data.attributes.username' => 'required',
            'data.attributes.email' => 'required|email',
            'data.attributes.phone_number' => 'required',
        ]);
        
        $data = new Customer();
        $data->full_name = $request->input('data.attributes.full_name');
        $data->username = $request->input('data.attributes.username');
        $data->email = $request->input('data.attributes.email');
        $data->phone_number = $request->input('data.attributes.phone_number');
        $data->save();

        Log::info('Adding customer');

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
            'data.attributes.full_name' => 'required',
            'data.attributes.username' => 'required',
            'data.attributes.email' => 'required|email',
            'data.attributes.phone_number' => 'required',
        ]);
        
        $data = Customer::find($id);
        if ($data) {
            $data->full_name = $request->input('data.attributes.full_name');
            $data->username = $request->input('data.attributes.username');
            $data->email = $request->input('data.attributes.email');
            $data->phone_number = $request->input('data.attributes.phone_number');
            $data->save();

            Log::info('Updating customer by id');

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
        $data = Customer::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting customer by id');

            $results = array(
                "data" => array("attributes")
            );

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
