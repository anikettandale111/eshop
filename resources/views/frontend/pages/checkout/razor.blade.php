@extends('frontend.layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center pt-70 pb-70 flex-column align-items-center">
                <h3>Razor Payment</h3><br>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(document).ready(function() {
        var options = {
            "key": "{{config('custom.custom.RAZOR_KEY')}}", // Enter the Key ID generated from the Dashboard
            "amount": '{{ $order->total_amount * 100 }}', // Amount is in currency subunits. Default currency is INR. Hence, 10 refers to 1000 paise
            "currency": "INR",
            "name": "MACHIWALA",
            "description": "Order Payment",
            "image": "{{url('frontend/assets/images/60-80.svg')}}",
            "order_id": "{{str_replace(config('custom.custom.order_prefix'),'',$order->rzp_order_id)}}", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
            "handler": function(response) {
                var token = "{{ csrf_token() }}";
                $.ajax({
                    type: 'POST',
                    url: "{{route('payment')}}",
                    data: {
                        _token: token,
                        payment_response: JSON.stringify(response)
                    },
                    success: function(data) {
                        window.location.href = "{{url('order-confirmed')}}";
                    }
                });
            },
            "prefill": {
                "name": '{{ $order->first_name }} {{ $order->last_name }}',
                "email": '{{ $order->email }}',
                "contact": '{{ $order->phone }}'
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