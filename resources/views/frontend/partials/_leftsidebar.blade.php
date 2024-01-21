<div class="modal left fade mobileMenuModal show" id="mobileMenu" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close d-none" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="bx bx-x"></i></span>
            </button>
            <div class="modal-body">
                <!-- Sign In or Sign Up -->
                <div class="mobile-menu__signin d-flex align-items-center justify-content-between">

                </div>


                <!-- Navigation Menu -->
                <div class="mobile-navigation-menu mt-5">
                    <ul>
                        <li>
                            <a href="{{route('home')}}" class="navigation-item">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAXhJREFUSEvt1r8vBEEUwPHvNSg0qgsKOv+ATnWFRqUUkVAT/gXuT5AIpYQEnVJUKn+AVkXhRynRSBTsS2bkGbNm3ljZ5rbZu828/bz3dvbddWjp6LTkUgpL3LJL+gT4sBZQAkuMYEsOO3NJmHArHKK+UDNugWXtEbDitHt3nnLnY2A1t+25cFjpAzDnwGtg0tr2HLgO1RWb8RScQv0zlnab8N/gXLQIr4OtqBmPwaWoCQ/hv6LZuIZjw6Ff3WnHOg7deonbVrHfhoyGZTDIENBHk7DcV+b7qXzQ8CJw/s/wAnARwvJ9BhgHrlwCuuIhYLN6X+eBkSDBN+AS2APeI63uAY9Vxbc+ru518r80Gj4E1hLPex/YiMA/HAv8CowCMirvggSmAZleL8BY03CsC97XO9gXE7v2la+l4gGsH/Og1dKNos31DHQLZ3QY9gRMhBfrdvVW9T7uNgSvV0Uc5MKybtaNx+HCBGSMyly+icWn/nMVmumw1uBPg+qCH2ApKdYAAAAASUVORK5CYII=" />
                                <span> Home </span>
                            </a>
                        </li>
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
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAi5JREFUSEvl1kuojVEUwPHfTcpAGCBSksfAQJKBiZQkRVKUd2QmZvIKeRQhMZASMkEeZYIYyEAZyExRilIIIfKcyOtbt320O53r+84993TVXaPvsdb677X3euwOvSQdvcTVU+D+WI2XuImfZQH1BHgejmJsgr3BeRzD064W0Ap4Ao5jVhfOv2IJbjT63x3wIOzFOvRLTh9hPT5iIdZgVPq3Dfvr4c2AQzcchpPhydFn7Elb/SNzPhgnsTh9i/M/k8OrgqfiNCYn4984i01F5G//kUhXMb/YjXeF/jh8qemWgUfgEFZmzu9jLe6VZS6G4DGG4TA2loGjPDZgBwYm5feI8zqFiLiqRHbH+X/LfDWs49iaIxifPEdNnkjQT1VpmV74iy0PGVPkybN4yLd6aAE7hzl1zl/hSTeANZMBmJZeFtQWkYO3pzJpgVFquhUH6yOOsthZatqaQjB2/zfgWMmuCgFFLa4qcuFu1lAqmHWqtBTxJSxNpMiRaCCRmFWkIbhqxBeKprA8A8coHJnef+EKnmMuYpDkUhkcE+YDRmfW0Z+3pHqcnuq79nsmbqeXaELXMTuzrQSOlcdY+47NOFCylw8xqU5nES43C16Gi8koevbrdoHr6zj69L4Eiy29UwKO852YhkJNNTrhimYjjlF2rRgWcabRRsNpmYTuLbzAjCLRplRJrr7XuSKZ4nbYTvmbsPl0iue42sStoR0S/eBB7RJRdvVpxwI6ffY98B9/+3Uf+nc6QAAAAABJRU5ErkJggg==" />
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
                            <a href="{{route('user.dashboard')}}" class="text-uppercase">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAQ5JREFUSEvtlz8OAUEUxn/b4AhEohCVRuIA/p1BXAGdYzgCtcohBNFrVRQScQGFaNiRkeyu7MzsZJLdYqfc+d778v5989YjpeOlxEumiGtAXZGJJ3AEXhJTANpASWFzAa7B+2jEFeAG2kwsgYl0tADGmpK9gaqPu/9wUeIesDWo+x4QWHF2vk3XwKYvsV9oTpynOq5nMtFca2Bk0NVN4ORynBrAUCMgZ2ClEhCb5jII9h/iYo6dEJeDsqbwKGRyKu9nfprnQFGj1S3gEVdj8T3pI7EBBgZhd4CDitjARwjiRKttInZCbPMsOiG2GaecOK4580UglBmbrnay7NnMsZP1NqlqWeMz9SdhHUUSww91L2EfFzmZfAAAAABJRU5ErkJggg==" /><span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('login')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-uppercase">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAltJREFUSEvV1snLjWEYBvDfV7IwFEmRuYREiAxl2oqyEpIomTeKBdnwB5AMC8VCSdkgU2LDRokyZIgVxUrJkEiJ9/56jt7vdb7vPGcouet06pzrea7nnq777vKPrOsf8foviYdgNAbjXfr8zI1gsx6vxGosxqgKyQ/cx00cxce+HpFLPBZnsSjToy8F7gAO94bPIZ6L6xhWuuQxrhXev8AHTMYCLMOgEu4UtuGvFOQQL8QNDEwP2IcnvXgyADuwH1EDYUG+uYrPIY4zQT4DJzJDPQYXMCfhgzge8MdyiTP5esCG4i6m4BPGpe9uUCPi4diNva0wYzYepLMHU8E1JI5DV/AKS1skjmNXizZbjteYULunN4/Xp5z0x502iTeV8jsdT+uFul9q/u2V1tnVhMe3K9ipeJZ+W4PzVeLI5+WideY3QVIPWo1iSOrnBIx66RaVMmhkymnkth2rEoeghJKF7Sm0/VC9UEdOj1caPlSqnVBPwstEvA7n+mqnLYVEHkMnimtncib4ZuFRoz6OXEfOn7dZ1Q8xE28wvlE71f6PvEdeoihasbW10Bb1lC0grRCVz4xI0QrpDMmM+f011+MaLuZw5CcGfI7FkLiYJDPwGwvVOlM+2EirA7skjcMYeZcQhfe+D/YNxUg8UhqLJ9NM7nEkh7i6CMTgj20kZvS30m3TCoValdai2s+nsbXVRSAumZjI5uXEOQlGFFO3WNSzHI/L51Yg9DZyHntY2b4Xq8893EoLQ0eWvXqPjlUo+jK0+G1ab39lRqThIpB7T9O4ZkPdNEGnctwx4t8ku2AfLgil4AAAAABJRU5ErkJggg==" /><span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                        @else

                        <li>
                            <a href="{{route('login')}}" class="text-uppercase">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAkNJREFUSEvl1surjVEYBvDfmbpFJspdUhQRkVzKTBEzlwwYEGHGRCLnD3ANRRSRMiGFSBm7DEgkM4ooKZJIxHpPa+s729n7rL3tnOStr6++71nrWetd73qet8sARdcA8foniYdjDIbiVX6+l2aw1R2vxGosxug6kq+4h1s4gvfNFlFKPAlnsKhwRx+xB4cb4UuI5+EaRlYmeYjrafdP8Q5TMR9LMaSCO4UtaRG/HUEJ8ULcwOBMtguPGuxkELZiN6IGIoJ8Uz2+hDjGBPkMHC9M9biEv4TZGR/EsYBfUUpcyNcLNiIX22R8wPj87gG1S7wAUXDn+lnRHNzPmO703lfDt0u8JF2p2ziB7fjWZAFXE/kyPMfEThHHPHewAm8bkMf5nsz/puNxfaonpLsaT0nMxMEK8DVCXGpprc4xDU/yhzXp6l2sJ44z2FvC2gATyrWxj3MfVimqHTjwt4hDUELJInYmbd/fyVS/yakOra6PKXiWP67DhT+5TrWqLimubTiaiWch5Lbte9zKdXqAKMYX1eJt9x6XCsjaWmrTJjsiICXFPyq7V5hFSGb496dWBSR8OM4nDL4kxib8lTwm8BuSap2tDixJdXQb4b1hi5exuYlKxdzrkyUeqthiyGp4cq8oIZ6biWuNQBj/+WT8N/G5MlvI4apsobXPp/NC22oEYpKwtiCLbqQkQjCimHrEoq8o2XF13HKE3saZh9lX4wvu5mbvWKeavb4WHWcephLt7cvc3v4oSUdgWt1x6bz94v4/4p+7eWgf9H+zMwAAAABJRU5ErkJggg==" /><span>Sign In</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{route('register')}}" class="text-uppercase">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAAXNSR0IArs4c6QAAAltJREFUSEvV1snLjWEYBvDfV7IwFEmRuYREiAxl2oqyEpIomTeKBdnwB5AMC8VCSdkgU2LDRokyZIgVxUrJkEiJ9/56jt7vdb7vPGcouet06pzrea7nnq777vKPrOsf8foviYdgNAbjXfr8zI1gsx6vxGosxqgKyQ/cx00cxce+HpFLPBZnsSjToy8F7gAO94bPIZ6L6xhWuuQxrhXev8AHTMYCLMOgEu4UtuGvFOQQL8QNDEwP2IcnvXgyADuwH1EDYUG+uYrPIY4zQT4DJzJDPQYXMCfhgzge8MdyiTP5esCG4i6m4BPGpe9uUCPi4diNva0wYzYepLMHU8E1JI5DV/AKS1skjmNXizZbjteYULunN4/Xp5z0x502iTeV8jsdT+uFul9q/u2V1tnVhMe3K9ipeJZ+W4PzVeLI5+WideY3QVIPWo1iSOrnBIx66RaVMmhkymnkth2rEoeghJKF7Sm0/VC9UEdOj1caPlSqnVBPwstEvA7n+mqnLYVEHkMnimtncib4ZuFRoz6OXEfOn7dZ1Q8xE28wvlE71f6PvEdeoihasbW10Bb1lC0grRCVz4xI0QrpDMmM+f011+MaLuZw5CcGfI7FkLiYJDPwGwvVOlM+2EirA7skjcMYeZcQhfe+D/YNxUg8UhqLJ9NM7nEkh7i6CMTgj20kZvS30m3TCoValdai2s+nsbXVRSAumZjI5uXEOQlGFFO3WNSzHI/L51Yg9DZyHntY2b4Xq8893EoLQ0eWvXqPjlUo+jK0+G1ab39lRqThIpB7T9O4ZkPdNEGnctwx4t8ku2AfLgil4AAAAABJRU5ErkJggg==" /><span>Sign Up</span>
                            </a>
                        </li>
                        @endauth
                        <hr>

                        <!-- <li class="accordion">
                            <a class="accordion-item">
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
                                <a class="accordion-title" href="javascript:void(0)">
                                    <img src="{{asset('frontend/assets/images/icons/USD.svg')}}">
                                    <span>USD</span>
                                    <i class="bx bx-chevron-down float-right"></i>
                                </a>

                                <div class="accordion-content mt-3" style="display: none;">
                                    @foreach(\App\Models\Currency::where('status','active')->orderBy('id','ASC')->get() as $key=>$currency)
                                        <div class="form-check {{$key==0 ? '' : 'mt-2'}}">
                                            <input onclick="currency_change('{{$currency['code']}}')" class="form-check-input" type="radio" name="exampleRadios"
                                                   id="exampleRadios{{$key}}" value="option2">
                                            <label class="form-check-label" for="exampleRadios{{$key}}" style="font-size: 12px;line-height: 1.5;">
                                                {{\Illuminate\Support\Str::upper($currency->name)}} ({{\Illuminate\Support\Str::upper($currency->code)}}) <img style="height: 1rem" src="{{$currency['flag_path']!=null ? asset($currency['flag_path']) : Helper::DefaultImage()}}">
                                            </label>
                                        </div>
                                    @endforeach

                                </div>
                            </a>
                        </li> -->

                        <!-- <li>
                            <a href="javascript:void(0)" class="navigation-item" data-toggle="modal" data-target="#onlineHelpModal">
                                <img src="{{asset('frontend/assets/images/icons/information.svg')}}">
                                <span> Online Help </span> <i class='bx bx-chevron-right'></i>
                            </a>
                        </li> -->

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!----====== Mobile Sidebar Menu Starts======-->

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