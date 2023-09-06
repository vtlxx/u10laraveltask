<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    private string $api = 'https://novaposhta.test/api/delivery';

    /*
     * Add delivery for new package
     */
    public function add(Request $request): JsonResponse
    {
        // validating POST input
        $validation = Validator::make($request->all(), [
            'package_width' => ['required', 'numeric', 'min:0'],
            'package_height' => ['required', 'numeric', 'min:0'],
            'package_length' => ['required', 'numeric', 'min:0'],
            'package_weight' => ['required', 'numeric', 'min:0'],
            'customer_full_name' => ['required', 'string', 'min:3', 'max:128'],
            'customer_phone_number' => ['required', 'string'],
            'customer_email' => ['required', 'email'],
            'customer_address' => ['required', 'string', 'min:3', 'max:256']
        ]);

        if($validation->fails())
        {
            return response()->json(['success' => false, 'message' => 'POST fields are not valid']);
        }

        $data = $validation->getData();

        // sending request for creating delivery
        $response = Http::post($this->api, [
            'customer_name' => $data['customer_full_name'],
            'phone_number' => $data['customer_phone_number'],
            'email' => $data['customer_email'],
            'sender_address' => config('delivery.sender_address'),
            'delivery_address' => $data['customer_address']
        ]);

        if(!$response->successful())
        {
            return response()->json(['success' => false, 'message' => 'Error while sending request!']);
        }

        if(!($response->ok() || $response->created()))
        {
            return response()->json(['success' => false, 'message' => $response->json('message')]);
        }

        $data['delivery_ttn'] = $response->json('ttn');
        $data['customer_phone_number'] = str_replace(['+', ' '], ['', ''], $data['customer_phone_number']);

        // getting client id (if exists) or creating new client (if not exists)
        $data['customer_id'] = Delivery::addCustomer($data);

        // inserting new package
        $delivery_id = Delivery::add($data);

        return response()->json(['success' => true, 'id' => $delivery_id, 'ttn' => $data['delivery_ttn']]);
    }

    /*
     * Get all info about existing delivery
     */
    public function get(int $id) : JsonResponse
    {
        if($id < 0)
        {
            return response()->json(['success' => false, 'message' => 'ID must be a positive number']);
        }

        $delivery_info = Delivery::get($id);
        return response()->json(['success' => true, 'result' => $delivery_info]);
    }

    /*
     * Get all packages for client by his phone_number
     */
    public function getClientPackages(Request $request) : JsonResponse
    {
        $phone_number = $request->input('phone_number');


        if(strlen($phone_number) < 10 || strlen($phone_number) > 13)
        {
            return response()->json(['success' => false, 'message' => 'Length of a phone number must be between 10 and 13 characters']);
        }

        if(!is_numeric(str_replace([' ', '+'], ['', ''], $phone_number)))
        {
            return response()->json(['success' => false, 'message' => 'Enter correct phone number!']);
        }

        $client_deliveries = Delivery::getByCustomer($phone_number);
        if(!$client_deliveries)
        {
            return response()->json(['success' => false, 'message' => 'Client not found']);
        }
        return response()->json(['success' => true, 'result' => $client_deliveries]);
    }
}
