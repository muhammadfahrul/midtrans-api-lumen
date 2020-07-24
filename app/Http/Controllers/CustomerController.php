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

        Log::info('Showing all author');

        return response()->json([
            "results" => $data
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

        Log::info('Showing author by id');

        return response()->json([
            "results" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'full_name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
        ]);
        
        $data = new Customer();
        $data->full_name = $request->input('full_name');
        $data->username = $request->input('username');
        $data->email = $request->input('email');
        $data->phone_number = $request->input('phone_number');
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
            'full_name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
        ]);
        
        $data = Customer::find($id);
        if ($data) {
            $data->full_name = $request->input('full_name');
            $data->username = $request->input('username');
            $data->email = $request->input('email');
            $data->phone_number = $request->input('phone_number');
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
        $data = Customer::find($id);
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
