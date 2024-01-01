<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Otp;
use App\Models\Category;
use App\Models\Product;
use Hash;
use DB;
use Closure;
use Http;
use Exception;

class APIController extends Controller
{
    private function checkAuthentication(Request $request)
    {
        if ($request->user()) {
            return response()->json(['message' => 'Authenticated', 'user' => $request->user()], 200);
        } else {
            return response()->json(['message' => 'Invalid user or authentication failed'], 401);
        }
    }
    public function testApiCall()
    {
        return response()->json([
            'status'    => 200,
            'message' => 'Publicly accessible data'
        ]);
    }
    public function jsonResponseGe($code, $status, $message, array $data = [])
    {
        return response()->json([
            'status_code'    => $code,
            'status'    => $status,
            'message' => $message
        ]);
    }
    public function register(Request $request)
    {
        if (isset($request->phone) && is_numeric($request->phone) && strlen($request->phone) == 10) {
            $getUser = User::where('phone', $request->phone)->first();
            if ($getUser != null) {
                return $this->jsonResponseGe('201', 'failure', 'Number Already Registerd With Us');
            } else {
                $otp = $this->rndgen();
                $ids = DB::table('users')->insertGetId(['phone' => $request->phone, 'password' => Hash::make($request->phone)]);
                Otp::create(['otp' => $otp, 'user_id' => $ids]);
                return $this->jsonResponseGe('200', 'success', 'OTP Has to be Sent on Your Mobile Number =' . $otp);
            }
        } else {
            return $this->jsonResponseGe('202', 'error', 'Please provide valid Mobile Number');
        }
    }
    public function login(Request $request)
    {
        if ((isset($request->phone) && is_numeric($request->phone) && strlen($request->phone) == 10) && (isset($request->password) && is_numeric($request->password) && strlen($request->password) == 10)) {
            $getUser = User::where('phone', $request->phone)->first();
            if ($getUser != null) {
                if (Hash::check($request->password, $getUser->password)) {
                    $token = $getUser->createToken('AccessToken')->accessToken;
                    return $this->jsonResponseGe('200', 'success', $token);
                } else {
                    return $this->jsonResponseGe('202', 'error', 'Invalid Login Credentials');
                }
            } else {
                $getUser = User::where('phone', $request->phone)->first();
                if ($getUser != null) {
                    return $this->jsonResponseGe('202', 'error', 'Invalid Login Credentials');
                } else {
                    return $this->jsonResponseGe('202', 'error', 'Number Not Registerd With Us');
                }
            }
        } else {
            return $this->jsonResponseGe('202', 'error', 'Please provide valid Login Details');
        }
    }
    public function rndgen()
    {
        do {
            $num = sprintf('%06d', mt_rand(100, 999989));
        } while (preg_match("~^(\d)\\1\\1\\1|(\d)\\2\\2\\2$|0000~", $num));
        return $num;
    }
    public function validateToken($request)
    {
        try {
            $passportEndpoint = 'your_passport_endpoint_here';
            $client = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $request->header('Authorization')
            ]);
            $response = $client->get($passportEndpoint);
            print_r($response);
            if ($response->status() === 200) {
                $body = $response->object();
                //do some stuff with response here, like setting the global logged in user
            }
        } catch (Exception $exception) {
            print_r($exception);
        }
    }
    public function profileUpdate(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();
            try {
                // Validate the request data
                $request->validate([
                    'full_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . $user->id,
                    // Add more validation rules if needed
                ]);
                // Update user profile
                $user->full_name = $request->input('full_name');
                $user->email = $request->input('email');
                // Add more fields if needed

                $user->save();
                return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
            } catch (ValidationException $e) {
                // Validation failed, return JSON response with errors
                return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
            }
        }
    }
    public function resendOTP(Request $request)
    {
        if (isset($request->phone) && is_numeric($request->phone) && strlen($request->phone) == 10) {
            $getUser = User::where('phone', $request->phone)->first();
            if ($getUser == null) {
                $getUser = DB::table('users')->insertGetId(['phone' => $request->phone, 'password' => Hash::make($request->phone)]);
            } else {
                Otp::where('user_id', $getUser->id)->delete();
                $getUser = $getUser->id;
            }
            $otp = $this->rndgen();
            Otp::create(['otp' => $otp, 'user_id' => $getUser]);
            return $this->jsonResponseGe('200', 'success', 'OTP Has to be Sent on Your Mobile Number =' . $otp);
        } else {
            return $this->jsonResponseGe('202', 'error', 'Please provide valid Mobile Number');
        }
    }
    public function verifyOTP(Request $request)
    {
        if ((isset($request->phone) && is_numeric($request->phone) && strlen($request->phone) == 10) && (isset($request->otp_check) && is_numeric($request->otp_check) && strlen($request->otp_check) == 6)) {
            $getUser = User::where('phone', $request->phone)->first();
            if ($getUser != null) {
                $checkOTP = Otp::where('otp', $request->otp_check)->where('user_id', $getUser->id)->first();
                if ($checkOTP != null) {
                    return $this->jsonResponseGe('200', 'success', 'OTP Verified Successfully.');
                } else {
                    return $this->jsonResponseGe('201', 'failure', 'OTP is Invalid');
                }
            } else {
                return $this->jsonResponseGe('200', 'success', 'Mobile Number Not Registerd');
            }
        } else {
            return $this->jsonResponseGe('202', 'error', 'Please enter OTP received on Mobile Number');
        }
    }
    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->get();
        return response()->json(['message' => 'Categories Listed successfully', 'categories' => $categories], 200);
    }
    public function products()
    {
        $products = Product::orderBy('id', 'DESC')->get();
        return response()->json(['message' => 'Products Listed successfully', 'products' => $products], 200);
    }
}
