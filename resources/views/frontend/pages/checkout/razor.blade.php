@extends('frontend.layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center pt-70 pb-70 flex-column align-items-center">
                <h3>Razor Payment</h3><br>
                <!-- <div class="panel-body text-center"> -->
                <form action="{{route('payment')}}" method="POST">
                    <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ env('RAZOR_KEY') }}" data-amount="{{ $order->total_amount * 100 }}" data-buttontext="{{ session('system_default_currency_info')->symbol }}{{ $order->total_amount }} Pay Now" data-currency="{{ session('system_default_currency_info')->code }}" data-name="{{ get_settings('site_title') }}" data-description="Payment" data-prefill.name="{{ $order->first_name }} {{ $order->last_name }}" data-prefill.email="{{ $order->email }}" data-theme.color="hsl(358deg 100% 68%)"></script>
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .razorpay-payment-button {
        background: teal;
        padding: 8px 16px;
        color: white;
        border-radius: 4px;
        border: 0;
    }
</style>
@endpush
@push('scripts')
<script>
    $(document).ready(function() {
        var options = {
            "key": "{{config('custom.custom.RAZOR_KEY')}}", // Enter the Key ID generated from the Dashboard
            "amount": '{{ $order->total_amount * 100 }}', // Amount is in currency subunits. Default currency is INR. Hence, 10 refers to 1000 paise
            "currency": "INR",
            "name": "MACHIWALA",
            "description": "Order Payment",
            "image": "{{url('frontend/assets/images/60-80.svg')}}",
            // "order_id": "{{str_replace(config('custom.custom.order_prefix'),'',$order->order_number)}}", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
            "handler": function(response) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: "{{route('payment')}}",
                    data: {
                        razorpay_payment_id: response.razorpay_payment_id,
                        payment_response: response,
                        order_number: data.booking_number
                    },
                    success: function(data) {
                        // swal("Success", "Thank you for Payment",'success');
                        // window.location.href = 'payment';
                    }
                });
            },
            "prefill": {
                "name": '{{ $order->first_name }} {{ $order->last_name }}',
                "email": '{{ $order->email }}',
                "contact": '{{ $order->phone }}',
                'external': {
                    'UPI': ['paytm']
                }
            },
            "notes": {
                "address": "test test"
            },
            "theme": {
                "color": "#F37254"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    });
</script>
@endpush