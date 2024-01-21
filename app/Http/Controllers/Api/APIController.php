<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
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
        return response()->json(['status' => 200, 'message' => 'Banners List', 'banners' => $banners], 200);
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
            'status'    => $code,
            'message' => $message
        ]);
    }
    public function register(Request $request)
    {
        if (isset($request->phone) && is_numeric($request->phone) && strlen($request->phone) == 10) {
            $getUser = User::where('phone', $request->phone)->first();
            if ($getUser != null) {
                if($getUser->status == 'inactive'){
                    $otp = $this->rndgen();
                    Otp::updateOrCreate(['user_id' => $getUser->id],['otp' => $otp]);
                    return response()->json(['status' => 200, 'message' => 'OTP send successfully', 'otp' => $otp], 200);
                }
                return response()->json(['status' => 201, 'message' => 'Number Already Registerd With Us'], 200);
            } else {
                $otp = $this->rndgen();
                $ids = DB::table('users')->insertGetId(['phone' => $request->phone,'status'=>'inactive', 'password' => Hash::make($request->phone)]);
                Otp::updateOrCreate(['user_id' => $ids],['otp' => $otp]);
                return response()->json(['status' => 200, 'message' => 'OTP send successfully', 'otp' => $otp], 200);
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
                    User::where('phone', $request->phone)->update(['status' =>'active']);
                    $token = $getUser->createToken('AccessToken')->accessToken;
                    return response()->json(['status' => 200,'message'=>'Login Success','token' => $token,'email'=>$getUser->email,'phone'=>$getUser->phone,'full_name'=>$getUser->full_name], 200);
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
            $num = sprintf('%04d', mt_rand(1000, 9999));
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
                return response()->json(['status' => 200, 'message' => 'Profile updated successfully', 'user' => $user], 200);
            } catch (ValidationException $e) {
                // Validation failed, return JSON response with errors
                return response()->json(['status' => 422, 'message' => 'Validation failed', 'errors' => $e->errors()], 200);
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
            Otp::firstOrNew(['otp' => $otp, 'user_id' => $getUser]);
            return response()->json(['status' => 200, 'message' => 'OTP send successfully', 'otp' => $otp], 200);
        } else {
            return response()->json(['status' => 202, 'message' => 'PLease provide valid Mobile Number'], 202);
        }
    }
    public function verifyOTP(Request $request)
    {
        if ((isset($request->phone) && is_numeric($request->phone) && strlen($request->phone) == 10) && (isset($request->otp_check) && is_numeric($request->otp_check) && strlen($request->otp_check) == 4)) {
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
        return response()->json(['status' => 200, 'message' => 'Categories Listed successfully', 'categories' => $categories], 200);
    }
    public function products()
    {
        $products = Product::orderBy('id', 'DESC')->get();
        return response()->json(['status' => 200, 'message' => 'Products Listed successfully', 'products' => $products], 200);
    }
    public function productByCategory($catid)
    {
        $products = Product::where(['status' => 'active', 'cat_ids' => $catid])->get();
        return response()->json(['status' => 200, 'message' => 'Products Listed successfully', 'products' => $products], 200);
    }
    public function productDetail(Request $request, $pslug)
    {
        $product = Product::with('stocks', 'rel_prods')->where('slug', $pslug)->first();
        $reviews = $product->reviews()->orderBy('id', 'DESC')->get();
        $display_reviews = $product->reviews()->take(2)->latest()->get();
        $recent_view = null;
        if ($product) {
            return response()->json(['status' => 200, 'message' => 'Products Details get successfully', 'display_reviews' => $display_reviews, 'products' => $product, 'reviews' => $reviews], 200);
        } else {
            return response()->json(['status' => 200, 'message' => 'Product detail not found'], 201);
        }
    }
    public function listAddress(Request $request)
    {
        $user_id = $request->user();
        $shippingAddress = ShippingAddress::where('user_id', $user_id->id)->get();
        return response()->json(['status' => 200, 'message' => 'Address Listed successfully', 'address' => $shippingAddress], 200);
    }
    public function cart(Request $request)
    {
        $cart = '';
        if ($request->session()->has('cart_' . Auth::user()->id)) {
            $cart = $request->session()->get('cart_' . Auth::user()->id);
        }
        return response()->json(['status' => 200, 'message' => 'Cart Details', 'cart' => $cart], 200);
    }
    public function cartAdd(Request $request)
    {
        // Make cart empty
        // if ($request->session()->has('cart_'.Auth::user()->id)) {
        //     $request->session()->forget('cart_' . Auth::user()->id);
        // }
        $product = Product::find($request->id);
        if($product == NULL){
            return response()->json(['status' => 200, 'message' => 'Invalid product selected'], 200);
        }
        $data = array();
        $data['id'] = $product->id;
        $str = (isset($request->variant) && $request->variant != null) ? $request->variant : '';
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
        // if (json_decode($product->choice_options)) {
        //     foreach (json_decode($product->choice_options) as $key => $choice) {
        //         //                $data[$choice->name] = $request[$choice->name];
        //         //                $variations[$choice->title] = $request[$choice->name];
        //         if ($str != null) {
        //             $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
        //         } else {
        //             $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
        //         }
        //     }
        // }

        $data['variation'] = $str;
        if ($str) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            if ($product_stock) {
                $price += $product_stock->price;
                $quantity = $product_stock->qty;
            } else {
                return response()->json(['status' => 200, 'message' => 'Sorry This is Out of stock'], 200);
            }
        } else {
            $price += $product->purchase_price;
            $quantity = $product->stock;
        }
        $cart_sub_total = 0;
        
        if ($request->session()->has('cart_' . Auth::user()->id)) {
            if (count($request->session()->get('cart_' . Auth::user()->id)) > 0) {
                foreach ($request->session()->get('cart_' . Auth::user()->id) as $key => $cartItem) {
                    if ($cartItem['id'] == $request['id'] && $cartItem['variation'] == $str) {
                        unset($request->session()->get('cart_' . Auth::user()->id)[$key]);
                        // $cartItem['id'] = $request->quantity;
                        // $response['message'] = '<i  class="fas fa-exclamation-triangle"></i> Oops: you have already added in shopping cart';
                        // $cart = $request->session()->get('cart_'.Auth::user()->id, collect([]));
                        // $response['status'] = $cart;
                        // return json_encode($response);
                    }
                    $cart_sub_total += $cartItem['subtotal'];
                }
            }
        }
        $shipping_id = 1;
        $data['product_id'] = $product->id;
        $data['quantity'] = $request['quantity'];
        $data['slug'] = $product->slug;
        $data['title'] = $product->title;
        $data['discount'] = $data['quantity'] * \Helper::get_product_discount($product, $price);
        $data['image'] = $product->thumbnail_image;
        $data['price'] = $price + ($additional_charge);
        $data['subtotal'] = ($data['quantity'] * $data['price']) - $data['discount'];
        $data['shipping_method_id'] = $shipping_id;
        $data['shipping_cost'] = config('custom.custom.shipping_charges');

        if ($request->session()->has('cart_' . Auth::user()->id)) {
            $cart = $request->session()->get('cart_' . Auth::user()->id, collect([]));
            $cart->push($data);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart_' . Auth::user()->id, $cart);
        }
        return response()->json(['status' => 200, 'message' => 'Product Added to Cart', 'cart' => $cart], 200);
    }
    public function cartUpdate(Request $request)
    {
        $cart = $request->session()->get('cart_' . Auth::user()->id, collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });

        $request->session()->put('cart_' . Auth::user()->id, $cart);

        $this->couponAppliedOnUpdatedCart();
        if ($request->ajax()) {
            $cart_list = view('frontend.layouts._cart-lists')->render();
            $response['status'] = true;
            $response['message'] = "Cart quantity successfully updated";
            $response['cart_list'] = $cart_list;
        }
        return $response;
    }
    public function cartDelete(Request $request, $id = '', $var = '')
    {
        $msg = 'Your cart is Empty';
        $cart = '';
        if ($request->session()->has('cart_' . Auth::user()->id)) {
            $cart = $request->session()->get('cart_' . Auth::user()->id);
            if (count($request->session()->get('cart_' . Auth::user()->id)) > 0) {
                $msg = 'Cart Details';
                if ((isset($id) && $id > 0) && (isset($var) && $var != null)) {
                    foreach ($request->session()->get('cart_' . Auth::user()->id) as $key => $cartItem) {
                        if ($cartItem['id'] == $id && $cartItem['variation'] == $var) {
                            unset($request->session()->get('cart_' . Auth::user()->id)[$key]);
                            $cart = $request->session()->get('cart_' . Auth::user()->id);
                            $msg = 'Product removed from cart.';
                        }
                    }
                } elseif (isset($id) && $id > 0) {
                    foreach ($request->session()->get('cart_' . Auth::user()->id) as $key => $cartItem) {
                        if ($cartItem['id'] == $id) {
                            unset($request->session()->get('cart_' . Auth::user()->id)[$key]);
                            $cart = $request->session()->get('cart_' . Auth::user()->id);
                            $msg = 'Product removed from cart.';
                        }
                    }
                } else {
                    $request->session()->forget('cart_' . Auth::user()->id);
                    $cart = $request->session()->get('cart_' . Auth::user()->id);
                    $msg = 'Your Cart is empty now.';
                }
            }
            return response()->json(['status' => 200, 'message' => $msg, 'cart' => $cart, 'status' => 200], 200);
        }
        return response()->json(['status' => 200, 'message' => $msg, 'cart' => [], 'status' => 200], 200);
        // if ($request->session()->has('cart_'.Auth::user()->id)) {
        //     $cart = $request->session()->get('cart_'.Auth::user()->id, collect([]));
        //     $cart->forget($request->key);
        //     $request->session()->p'status' => 200,ut('cart_'.Auth::user()->id, $cart);
        // }

        // // COUPON UPDATE HERE
        // $this->couponAppliedOnUpdatedCart();

        // if ($request->ajax()) {
        //     $header = view('frontend.layouts.header')->render();
        //     $response['header'] = $header;
        //     $cart_list = view('frontend.layouts._cart-lists')->render();
        //     $response['status'] = true;
        //     $response['message'] = "Cart quantity successfully removed";
        //     $response['cart_list'] = $cart_list;
        // }
        // return $response;
    }
    public function checkoutStore(Request $request)
    {
        if ($request->has('different_address')) {
            $this->validate($request, [
                'address' => 'bail|string|required',
                'country' => 'string|required',
                'saddress' => 'string|required',
                'scountry' => 'bail|string|required',
                'address2' => 'string|nullable',
                'state' => 'string|nullable',
                'postcode' => 'numeric|nullable',
                'note' => 'string|nullable',
                'saddress2' => 'string|nullable',
                'sstate' => 'string|nullable',
                'spostcode' => 'numeric|nullable',
            ], [
                'saddress.required' => 'The shipping address is required',
                'saddress2.string' => 'The shipping address2 must be string',
                'scountry.required' => 'The shipping country is required',
                'sstate.string' => 'The shipping state must be string',
                'spostcode.numeric' => 'The shipping postcode must be numeric',
            ]);
        } else {
            $this->validate($request, [
                'address' => 'bail|string|required',
                'address2' => 'string|nullable',
                'country' => 'string|required',
                'state' => 'string|nullable',
                'postcode' => 'numeric|nullable',
                'note' => 'string|nullable',
            ]);
        }

        $cart = session('cart');
        $ship_to_diff_adr = 0;
        if ($request->has('different-address')) {
            $ship_to_diff_adr = 1;
        }
        $coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
        $order = new Order();
        $order['user_id'] = auth()->user()->id;

        //serial order number
        $orderObj = DB::table('orders')->select('order_number')->latest('id')->first();
        if ($orderObj) {
            $orderNr = $orderObj->order_number;
            $removed1char = substr($orderNr, 6);
            $generateOrder_nr = $stpad = config('custom.custom.order_prefix') . str_pad($removed1char + 1, 3, "0", STR_PAD_LEFT);
        } else {
            $generateOrder_nr = Str::upper(config('custom.custom.order_prefix') . str_pad(1, 4, "0", STR_PAD_RIGHT));
        }

        $order['order_number'] = $generateOrder_nr;

        $order['coupon'] = $coupon_discount;
        $order['quantity'] = count($cart);
        $order['subtotal'] = Order::cart_grand_total($cart);
        $order['total_amount'] = Order::cart_grand_total($cart) - $coupon_discount + Order::total_shipping_cost($cart);
        $order['payment_method'] = $request->payment_method;
        $order['payment_status'] = 'unpaid';
        $order['order_status'] = 'pending';
        $order['delivery_charge'] = Order::total_shipping_cost($cart);
        $order['note'] = $request->note;
        if(isset(Auth::user()->full_name) && Auth::user()->full_name != NULL){
            $user_name = explode(' ',Auth::user()->full_name);
            $order->first_name = (isset($user_name[0])) ? $user_name[0] : 'Guest';
            $order->last_name = (isset($user_name[0])) ? str_replace($user_name[0],'',Auth::user()->full_name) : ((isset(Auth::user()->full_name)) ? Auth::user()->full_name :'User');
        }else{
            $order->first_name = Auth::user()->full_name;
            $order->last_name = Auth::user()->last_name;
        }
        $order->email = Auth::user()->email;
        $order->phone = Auth::user()->phone;
        $order->country = 'India';
        $order->address = $request->address;
        $order->address2 = $request->address2;
        $order->state = $request->state;
        $order->postcode = $request->postcode;

        $order->scountry = $ship_to_diff_adr == '1' ? $request->scountry : $request->country;
        $order->saddress = $ship_to_diff_adr == '1' ? $request->saddress : $request->address;
        $order->saddress2 = $ship_to_diff_adr == '1' ? $request->saddress2 : $request->address2;
        $order->sstate = $ship_to_diff_adr == '1' ? $request->sstate : $request->state;
        $order->spostcode = $ship_to_diff_adr == '1' ? $request->spostcode : $request->postcode;

        if ($order->save()) {
            $subtotal = 0;
            //Order detail storing
            foreach (session()->get('cart') as $key => $cartItem) {
                $product = Product::find($cartItem['id']);
                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $order_detail = new OrderDetail();
                $order_detail->order_id = $order->id;
                $order_detail->product_id = $product->id;
                $order_detail->product_details = $product;
                $order_detail->variation = $cartItem['variation'];
                $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->discount = $cartItem['discount'] * $cartItem['quantity'];
                $order_detail->shipping_method_id = $cartItem['shipping_method_id'];
                $order_detail->save();
            }
            $status = $order->save();
            if ($status) {
                $request->session()->put('order_id', $order->id);
            }
        }
    }
    public function addUpdateaddress(Request $request, $id = '')
    {
        $user_id = Auth::user()->id;
        try {
            $validate = (isset($request->address) && $request->address != NULL) ? $request->address : 'Address Field Required';
            $validate = (isset($request->address2) && $request->address2 != NULL) ? $request->address2 : 'Address Line Two Field Required';
            $validate = (isset($request->postcode) && $request->postcode != NULL && is_numeric($request->postcode)) ? $request->postcode : 'Postcode Required';
            
            $id = isset($request->id) ? $request->id : null;
            if (isset($id) && $id != null) {
                $shippingAddress = ShippingAddress::where('id', $id)->update(['postcode' => $request->postcode, 'address' => $request->address, 'address2' => $request->address2, 'country' => 'India', 'state' => 'Maharashtra']);
                return response()->json(['status' => 200, 'message' => 'Address Updated successfully'], 200);
            } else {
                $shippingAddress = new ShippingAddress;
                $shippingAddress->country = 'India';
                $shippingAddress->state = 'Maharashtra';
                $shippingAddress->user_id = $user_id;
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
                return response()->json(['status' => 200, 'message' => 'New Address Added successfully'], 200);
            }
        } catch (ValidationException $e) {
            // Validation failed, return JSON response with errors
            return response()->json(['status' => 200, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }
    }
}
