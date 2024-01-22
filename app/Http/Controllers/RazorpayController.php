<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Frontend\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;

class RazorpayController extends Controller
{
    public function razorpay()
    {
        $order = Order::findOrFail(session()->get('order_id'));
        $api = new Api(config('custom.custom.RAZOR_KEY'), config('custom.custom.RAZOR_SECRET'));
        $res = $api->order->create(array('receipt' => $order->order_number, 'amount' => ($order->total_amount * 100), 'currency' => 'INR', 'notes' => array('customer_name' => $order->first_name . ' ' . $order->last_name, 'phone' => $order->phone, 'note' => $order->note)));
        $order->rzp_order_id = $res->id;
        return view('frontend.pages.checkout.razor', compact('order'));
    }

    public function payment(Request $request)
    {
        if (isset($request->payment_response) && $request->payment_response != NULL) {
            $response  = json_decode($request->payment_response);
            if (isset($response->razorpay_payment_id) && $response->razorpay_payment_id != NULL) {
                $request['razorpay_payment_id'] = $response->razorpay_payment_id;
            }
        }
        $payment_detalis = null;
        $api = new Api(config('custom.custom.RAZOR_KEY'), config('custom.custom.RAZOR_SECRET'));
        $payment = $api->payment->fetch($request->razorpay_payment_id);
        if ($payment->status == 'captured') {
            $payment_detalis = json_encode(array('id' => $payment->id, 'method' => $payment->method, 'amount' => $payment->amount, 'currency' => $payment->currency));
        }
        if (isset($request->razorpay_payment_id)  && !empty($request->razorpay_payment_id)) {
            if ($payment_detalis == null) {
                try {
                    $response = $api->payment->fetch($request->razorpay_payment_id)->capture(array('amount' => $payment['amount']));
                    $payment_detalis = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'currency' => $response['currency']));
                } catch (\Exception $e) {
                    \Session::put('error', $e->getMessage());
                    return redirect()->back();
                }
            }
        }
        $checkoutController = new CheckoutController;
        return $checkoutController->checkout_done(Session::get('order_id'), $payment_detalis, 'razor');
    }
}
