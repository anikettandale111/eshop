@extends('backend.layouts.master')

@section('content')
<!--app-content open-->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- CONTAINER -->
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <h1 class="page-title">Update Product Price</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->
            <!-- ROW-4 -->
            <div class="row">
                <div class="col-md-12">
                    @include('layouts._error_notify')
                </div>
                <div class="col-12 col-sm-12">
                    <div class="card">
                        <div class="card-body pt-4">
                            <div class="grid-margin">
                                <form action="{{route('product.price.update')}}" method="post">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-1">
                                                <label type="text" class="form-control" >Sr.No.</label>
                                            </div>
                                            <div class="col-5">
                                                <label type="text" class="form-control" >Product Name</label>
                                            </div>
                                            <div class="col-3">
                                                <label type="text" class="form-control" >Purchase Price/Unit Type</label>
                                            </div>
                                            <div class="col-3">
                                                <label type="text" class="form-control" >Selling Price</label>
                                            </div>
                                        </div>
                                        @foreach($product AS $key => $prod)
                                        <div class="row mb-4">
                                            <div class="col-1">
                                                <input type="text" class="form-control" value="{{$key+1}}" readonly>
                                            </div>
                                            <div class="col-5">
                                                <input type="hidden" name="prod_ids[]" value="{{$prod->id}}">
                                                <input type="text" class="form-control" value="{{$prod->title}}">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" class="form-control" name="product_unit_price[]" value="{{$prod->unit_price}}">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" class="form-control" name="product_purchase_price[]" value="{{$prod->purchase_price}}">
                                            </div>
                                        </div>
                                        @if(isset($prod->stocks) && count($prod->stocks))
                                        @foreach($prod->stocks AS $k => $st)
                                        <div class="row mb-4">
                                            <div class="col-5">
                                                <input type="hidden" name="stocks_ids[]" value="{{$st->id}}">
                                            </div>
                                            <div class="col-1">
                                                <input type="text" class="form-control" value="{{($key+1)}}.{{($k+1)}}" readonly>
                                            </div>
                                            <div class="col-3">
                                                <input type="text" class="form-control" name="stocks_variant[]" value="{{$st->variant}}">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" class="form-control" name="stocks_price[]" value="{{$st->price}}">
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                        @endforeach
                                        <div class="row mb-4">
                                            <div class="col-9"></div>
                                            <div class="col-3">
                                                <button type="submit" class="btn btn-success" style="width: 100%;">UPDATE</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ROW-4 END -->
        </div>
        <!-- CONTAINER CLOSED -->
    </div>
</div>
<!--app-content closed-->
@endsection