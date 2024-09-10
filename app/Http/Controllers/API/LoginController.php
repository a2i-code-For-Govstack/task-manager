<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function clientLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'client_id' => 'required',
                'password' => 'required',
            ])->validate();

            if (!($request->client_id == config('constants.client_id') && $request->password == config('constants.client_pass'))) {
                throw new \Exception('Client ID or Client Password is not matching. Please provide valid credentials.');
            }

            $token_data = [
                'client_id' => $request->client_id,
                'client_password' => $request->password,
            ];

            $token_response = $this->generateToken($token_data);
            $response = ['status' => 'success'];
            $response['data']['token'] = $token_response;
            return response()->json($response);
        } catch (\Exception $ex) {
            return response()->json(responseFormat('error', __('Technical Error Happen. Error.'), ['details' => $ex->getMessage(), 'code' => $ex->getCode()]), 500);
        }
    }
}





