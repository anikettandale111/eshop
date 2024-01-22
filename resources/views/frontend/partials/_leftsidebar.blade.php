<div class="modal left fade mobileMenuModal show" id="mobileMenu" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="left:240px!important">
                <span aria-hidden="true"><i class="bx bx-x"></i></span>
            </button>
            <div class="modal-body">
                <!-- Sign In or Sign Up -->
                <div class="mobile-menu__signin d-flex align-items-center justify-content-between">
                    <span style="color: red;font-size: 15px;font-weight: bold;text-align: center !important;">
                        @if(isset(Auth::user()->full_name) && Auth::user()->full_name != null ) {{Auth::user()->full_name}} @elseif(isset(Auth::user()->phone) && Auth::user()->phone != null ) {{Auth::user()->phone}} @else
                        <!-- <a href='{{route("login")}}' onclick='event.preventDefault(); document.getElementById("logout-form").submit();' class="text-uppercase">Login</a> -->
                        Welcome Dear
                        @endif</span>
                </div>
                <!-- Navigation Menu -->
                <div class="mobile-navigation-menu mt-3">
                    <ul>
                        <li>
                            <a href="{{route('home')}}" class="navigation-item">
                                <i class='bx bx-home-alt bx-tada' style="font-size:35px"></i>
                                <span> Home </span>
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{route('user.dashboard')}}" class="text-uppercase">
                                <i class='bx bxs-dashboard bx-tada' style="font-size:35px"></i><span>Dashboard</span>
                            </a>
                        </li>
                        <li><a id="account-tab" data-toggle="pill" href="#account"><i class="bx bxs-user bx-tada" style="font-size:35px"></i><span>Profile</span></a></li>
                        <li><a class="@if ($_GET != null && $_GET['active'] == 'order') active @endif" id="orders-tab" data-toggle="pill" href="#orders"><i class="bx bxs-layer" style="font-size:35px"></i><span>Orders</span></a></li>
                        <li><a id="address-tab" data-toggle="pill" href="#address"><i class="bx bx-map bx-tada" style="font-size:35px"></i><span>Address</span></a></li>
                        <li><a id="wishlist-tab" data-toggle="pill" href="#wishlist"><i class="bx bxs-heart-circle bx-spin" style="font-size:35px"></i><span>Wishlist</span></a></li>
                        @endauth
                        @php
                        $categories=\App\Models\Category::with('subcategories')->where(['status'=>'active','is_menu'=>1,'level'=>0,'parent_id'=>0])->limit(6)->orderBy('id','DESC')->get()
                        @endphp
                        @if(count($categories)>0)
                        @foreach($categories->sortBy('order') as $key=>$cat)
                        <li>
                            <a href="{{route('product.category',$cat->slug)}}" class="text-uppercase" @if($cat->subcategories->count()>0)
                                data-toggle="modal"
                                data-target="#mobileSubMenu{{$key}}"
                                @endif
                                >
                                <!-- <img src="{{asset($cat->icon_path)}}"> -->
                                <i class='bx bxs-hand-right bx-tada' style="font-size:35px"></i>
                                <span> {{ucfirst($cat->title)}} </span>
                                @if($cat->subcategories->count()>0)
                                <i class='bx bx-chevron-right'></i>
                                @endif
                            </a>
                        </li>
                        @endforeach
                        @endif
                        @auth
                        <li>
                            <a href="{{route('login')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-uppercase">
                                <i class='bx bx-log-out-circle bx-tada' style="font-size:35px"></i><span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                        @else
                        <li>
                            <a href="{{route('login')}}" class="text-uppercase">
                                <i class='bx bx-log-in-circle bx-tada' style="font-size:35px"></i><span>Sign In</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('register')}}" class="text-uppercase">
                                <i class='bx bx-log-in-circle bx-tada' style="font-size:35px"></i><span>Sign Up</span>
                            </a>
                        </li>
                        @endauth
                        <hr>
                        <li style="margin-top: 20px;">
                            <div class="single-footer-widget footer-contact">
                                <ul class="social-link">
                                    <li><a href="{{get_settings('facebook_url')}}" class="d-block" target="_blank"><i class='bx bxl-facebook bx-tada' style="font-size:35px"></i></a></li>
                                    <li><a href="{{get_settings('twitter_url')}}" class="d-block" target="_blank"><i class='bx bxl-twitter bx-tada' style="font-size:35px"></i></a></li>
                                    <li><a href="{{get_settings('instagram_url')}}" class="d-block" target="_blank"><i class='bx bxl-instagram bx-tada' style="font-size:35px"></i></a>
                                    </li>
                                    <li><a href="{{get_settings('youtube_url')}}" class="d-block" target="_blank"><i class='bx bxl-youtube bx-tada' style="font-size:35px"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!----====== Mobile Sidebar Sub-Menu Starts======-->
@if(count($categories)>0)
@foreach($categories as $key=>$cat)
@if($cat->subcategories->count()>0)
<div class="modal left fade mobileMenuModal mobile-submenu show" id="mobileSubMenu{{$key}}" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="menu-title d-flex align-items-center">
                    <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                        <i class='bx bx-arrow-back bx-tada'></i>
                    </a>
                    <h6><a href="{{route('product.category',$cat->slug)}}">{{ucfirst($cat->title)}}</a></h6>
                </div>
                @if($cat->subcategories->count()>0)
                <ul class="sub-menu mt-4">
                    @foreach($cat->subcategories as $subCat)
                    <li>
                        <a href="{{route('product.category',$subCat->slug)}}">
                            {{ucfirst($subCat->title)}}
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endif

<!----====== Mobile Sidebar Online Help Submenu Starts======-->
<div class="modal left fade mobileMenuModal online-help show" id="onlineHelpModal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="menu-title d-flex align-items-center">
                    <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                        <i class='bx bx-arrow-back bx-tada'></i>
                    </a>
                    <h6>Online Help</h6>
                </div>
                <ul class="sub-menu mt-4" style="list-style: none;">
                    <li>
                        <a href="javascript:;" class="d-flex align-items-center">
                            <img src="{{asset('frontend/assets/images/icons/online-chat.svg')}}">
                            Online Chat
                        </a>
                    </li>
                    <li>
                        <a href="{{route('contact.us')}}" class="d-flex align-items-center">
                            <img src="{{asset('frontend/assets/images/icons/mail.svg')}}">
                            Email
                        </a>
                    </li>
                    <li>
                        <a href="tel:{{get_settings('phone')}}" class="d-flex align-items-center">
                            <img src="{{asset('frontend/assets/images/icons/phone-call.svg')}}">
                            Tollfree Call
                        </a>
                    </li>
                    <li>
                        <a href="{{route('order-status')}}" class="d-flex align-items-center">
                            <img src="{{asset('frontend/assets/images/icons/order-status.svg')}}">
                            Order Status
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>