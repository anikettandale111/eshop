<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Otp;
use App\Models\Banner;
use App\Models\AttributeValue;
use App\Models\ShippingAddress;
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
    public function listBanners()
    {
        $banners = Banner::where(['banner_type' => 'home', 'status' => 'active'])->orderBy('id', 'DESC')->get();
        return response()->json(['message' => '', 'banners' => $banners], 200);
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
    public function productByCategory($catid)
    {
        $products = Product::where(['status' => 'active', 'cat_ids' => $catid])->first();
        return response()->json(['message' => 'Products Listed successfully', 'products' => $products], 200);
    }
    public function productDetail(Request $request, $pslug)
    {
        $product = Product::with('stocks','rel_prods')->where('slug', $pslug)->first();
        $reviews = $product->reviews()->orderBy('id', 'DESC')->get();
        $display_reviews = $product->reviews()->take(2)->latest()->get();
        $recent_view = null;
        if ($product) {
            return response()->json(['message' => 'Products Details get successfully', 'display_reviews' => $display_reviews, 'products' => $product, 'reviews' => $reviews], 200);
        } else {
            return response()->json(['message' => 'Product detail not found'], 201);
        }
    }
    public function listAddress(Request $request)
    {
        $user_id = $request->user();
        $shippingAddress = ShippingAddress::where('user_id', $user_id->id)->get();
        return response()->json(['message' => 'Address Listed successfully', 'address' => $shippingAddress], 200);
    }
    public function cartAdd(Request $request)
    {
        $product = Product::find($request->id);
        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $additional_charge = 0;

        //check the color enabled or disabled for the product
        if ($request->has('color')) {
            $data['color'] = $request['color'];
            $str = AttributeValue::where('color_code', $request['color'])->first()->name;
            $variations['color'] = str_replace(' ', '_', $str);
        }
        //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
        if (json_decode($product->choice_options)) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                //                $data[$choice->name] = $request[$choice->name];
                //                $variations[$choice->title] = $request[$choice->name];
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }

        $data['variation'] = $str;
        if ($str) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            $price += $product_stock->price;
            $quantity = $product_stock->qty;
        } else {
            $price += $product->purchase_price;
            $quantity = $product->stock;
        }

        if ($request->session()->has('cart')) {
            if (count($request->session()->get('cart')) > 0) {
                foreach ($request->session()->get('cart') as $key => $cartItem) {
                    if ($cartItem['id'] == $request['id'] && $cartItem['variation'] == $str) {
                        $response['message'] = '<i  class="fas fa-exclamation-triangle"></i> Oops: you have already added in shopping cart';
                        $response['status'] = 'already';
                        return $response;
                    }
                }
            }
        }
        $shipping_id = 1;
        $shipping_cost = 0;
        $data['product_id'] = $product->id;
        $data['quantity'] = $request['quantity'];
        $data['slug'] = $product->slug;
        $data['title'] = $product->title;
        $data['discount'] = \Helper::get_product_discount($product, $price);
        $data['image'] = $product->thumbnail_image;
        $data['price'] = $price + ($additional_charge);
        $data['subtotal'] = $data['quantity'] * $data['price'];
        $data['shipping_method_id'] = $shipping_id;
        $data['shipping_cost'] = $shipping_cost;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->push($data);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return response()->json(['message' => 'Address Listed successfully', 'address' => $cart], 200);
    }
    public function cartUpdate(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });

        $request->session()->put('cart', $cart);

        $this->couponAppliedOnUpdatedCart();
        if ($request->ajax()) {
            $cart_list = view('frontend.layouts._cart-lists')->render();
            $response['status'] = true;
            $response['message'] = "Cart quantity successfully updated";
            $response['cart_list'] = $cart_list;
        }
        return $response;
    }
    public function cartDelete(Request $request)
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        // COUPON UPDATE HERE
        $this->couponAppliedOnUpdatedCart();

        if ($request->ajax()) {
            $header = view('frontend.layouts.header')->render();
            $response['header'] = $header;
            $cart_list = view('frontend.layouts._cart-lists')->render();
            $response['status'] = true;
            $response['message'] = "Cart quantity successfully removed";
            $response['cart_list'] = $cart_list;
        }
        return $response;
    }
    public function addUpdateaddress(Request $request, $id = '')
    {
        $user_id = $request->user();
        try {
            $this->validate($request, [
                // 'country' => 'string|nullable',
                'address' => 'string|required',
                'address2' => 'string|nullable',
                // 'state' => 'string|nullable',
                'postcode' => 'numeric|nullable'
            ]);
            $id = isset($request->id) ? $request->id : null;
            if (isset($id) && $id != null) {
                $shippingAddress = ShippingAddress::where('id', $id)->update(['postcode' => $request->postcode, 'address' => $request->address, 'address2' => $request->address2, 'country' => 'India', 'state' => 'Maharashtra']);
                return response()->json(['message' => 'Address Updated successfully'], 200);
            } else {
                $shippingAddress = new ShippingAddress;
                $shippingAddress->country = 'India';
                $shippingAddress->state = 'Maharashtra';
                $shippingAddress->user_id = $user_id->id;
                $shippingAddress->postcode = $request->postcode;
                $shippingAddress->address = $request->address;
                $shippingAddress->address2 = $request->address2;
                $shippingAddress->scountry = $request->address;
                $shippingAddress->spostcode  = $request->spostcode;
                $shippingAddress->sstate  = $request->sstate;
                $shippingAddress->saddress  = $request->saddress;
                $shippingAddress->saddress  = $request->saddress;
                $shippingAddress->saddress2  = $request->saddress2;
                $shippingAddress->save();
                return response()->json(['message' => 'New Address Added successfully'], 200);
            }
        } catch (ValidationException $e) {
            // Validation failed, return JSON response with errors
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }
}
