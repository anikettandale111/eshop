<!-- Top Header Bar Starts -->
<div class="top-header d-none d-lg-block">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 col-md-12">
        <ul class="header-contact-info">
          <!-- <li class="text-uppercase">Express Delivery and Free Turns Within {{get_settings('delivery_time')}} Days</li> -->
          <!-- Search Form -->
          <li>
            <div class="float-right">
              <form class="search-form" data-toggle="validator" action="{{route('search.products')}}" method="GET" style="height: 0;width:auto">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="submit" class="bg-white border-0 input-group-text"> <i class='bx bx-search'></i> </button>
                  </div>
                  <input name="q" type="text" class="input-search border-0 search-bar-input" placeholder="Search by product name.." required autocomplete="off">
                </div>
                <div id="validator-search" class="form-result"></div>
              </form>
            </div>
          </li>
        </ul>
      </div>
      @php
      Helper::currency_load();
      $currency_code = session('currency_code');
      $currency_symbol= session('currency_symbol');
      $currency_img= session('flag_path');

      if ($currency_symbol=="")
      {
      $system_default_currency_info = session('system_default_currency_info');
      $currency_symbol = $system_default_currency_info->symbol;
      $currency_code = $system_default_currency_info->code;
      $currency_img = $system_default_currency_info->flag_path;
      }
      @endphp
      <div class="col-lg-6 col-md-12">
        <ul class="header-top-menu">
          <li>
            <div class="content-wrapper d-inline-block">
              <i class='bx bx-phone-call bx-tada'></i>
              <span class="text-uppercase mr-1">Phone:</span>
              <a href="tel:{{get_settings('phone')}}">
                {{get_settings('phone')}}
              </a>
            </div>
          </li>
          <!-- <li>
            <i class='bx bx-time'></i>
            <span class="">{{strtoupper(get_settings('office_time'))}}</span>
          </li> -->
          @auth
          <li class="">
            <a href="{{route('login')}}">
              <i class='bx bx-user-circle bx-tada'></i>
              @php
              $name=explode(' ',auth()->user()->full_name);
              @endphp
              <span>{{auth()->user()->username ?? ucfirst($name[0])}}</span>
            </a>
          </li>
          @else
          <li class="">
            <a href="{{route('login')}}">
              <i class='bx bx-log-in-circle bx-tada'></i>
              <span>Login</span>
            </a>
          </li>
          @endif
          <li class="">
            <a href="{{route('cart')}}" class="cart-link">
              <i class='bx bx-cart-alt bx-tada'></i>
              <span class="count-badge">{{session()->has('cart') ? count(session()->get('cart')) : 0}}</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Top Header Bar Ends -->