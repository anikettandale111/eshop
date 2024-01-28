@if(Helper::isDeviceMobile() == false)
<footer class="footer-area">
  <div class="footer-menu-area py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6 d-block d-md-none">
          <div class="single-footer-widget footer-contact">
            <ul class="social-link">
              <li><a href="{{get_settings('facebook_url')}}" class="d-block" target="_blank"><i class='bx bxl-facebook'></i></a></li>
              <li><a href="{{get_settings('twitter_url')}}" class="d-block" target="_blank"><i class='bx bxl-twitter'></i></a></li>
              <li><a href="{{get_settings('instagram_url')}}" class="d-block" target="_blank"><i class='bx bxl-instagram'></i></a>
              </li>
              <li><a href="{{get_settings('youtube_url')}}" class="d-block" target="_blank"><i class='bx bxl-youtube'></i></a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
          <div class="single-footer-widget pl-4 footer__about-us">
            <h3>About Us</h3>
            <div class="d-flex align-items-center">
              <ul class="customer-support">
                <li><a href="{{route('about.us')}}">About {{env('APP_NAME')}}</a></li>
                <li><a href="{{route('frontend.return.policy')}}">Return Policy</a></li>
                <li><a href="{{route('frontend.privacy.policy')}}">Privacy Policy</a></li>
                <li><a href="{{route('frontend.terms.conditions')}}">Terms and Conditions</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 d-none d-md-block">
          <div class="single-footer-widget footer-customer-center">
            <h3>Customer Center</h3>
            <ul class="customer-support">
              <li><a href="{{route('contact.us')}}" class="d-inline-block d-none">Contact Us</a></li>
              <li><a href="{{route('contact.us')}}" class="d-md-none d-inline-block">Contact us</a></li>
              <li><a href="{{route('frontend.cancellation.policy')}}">Cancellation Policy</a></li>
              <li><a href="{{route('frontend.faq')}}">FAQ</a></li>
              <li><a href="{{route('frontend.shipping.payment')}}">Shipping & Payment</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 d-none d-md-block">
          <div class="single-footer-widget footer-contact">
            <h3>Contact Us</h3>
            <ul class="footer-contact-info">
              <li class="d-flex"><i class='bx bx-phone-call pr-1'></i>
                <p>
                  <span>{{get_settings('phone')}}</span>
                  <small>(Phone)</small>
                </p>
              </li>
            </ul>
            <ul class="footer-contact-info">
              <li class="d-flex">
                <i class='bx bx-envelope pr-1'></i>
                <p>
                  <span>{{get_settings('email')}}</span>
                </p>
              </li>
            </ul>
            <ul class="footer-contact-info">
              <li class="d-flex">
                <i class='bx bx-map pr-1'></i>
                <p>
                  <span>{{get_settings('address')}}</span>
                </p>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 d-none d-md-block">
          <div class="single-footer-widget footer-contact">
            <h3>Follow us</h3>
            <ul class="social-link">
              <li><a href="{{get_settings('facebook_url')}}" class="d-block" target="_blank"><i class='bx bxl-facebook'></i></a></li>
              <li><a href="{{get_settings('twitter_url')}}" class="d-block" target="_blank"><i class='bx bxl-twitter'></i></a></li>
              <li><a href="{{get_settings('instagram_url')}}" class="d-block" target="_blank"><i class='bx bxl-instagram'></i></a>
              </li>
              <li><a href="{{get_settings('youtube_url')}}" class="d-block" target="_blank"><i class='bx bxl-youtube'></i></a></li>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-copyright">
    <p>© Copyrights {{date('Y')}}. All rights reserved.  </p>
  </div>
</footer>
@endif
@section('scripts')

{{--Subscriber--}}

<script>
  'use strict';
  (function($) {
    $(document).on('click', '.subscribe-btn', function() {
      var email = $('input[name="s_email"]').val();
      $.ajax({
        url: "{{ route('subscribe') }}",
        method: "POST",
        data: {
          email: email,
          _token: "{{csrf_token()}}"
        },
        success: function(response) {
          if (response.status) {
            $('input[name="s_email"]').val(" ");
            $.notify({
              title: '<strong>Success: </strong>',
              message: response['msg'],
            }, {
              type: 'info',
              allow_dismiss: false,
              delay: 2800,
              animate: {
                enter: 'animated flipInY',
                exit: 'animated flipOutX'
              },
              onShow: function() {
                this.css({
                  'width': 'auto',
                  'height': 'auto'
                });
              },
            });
          } else {
            $.notify({
              title: '<strong>Sorry: </strong>',
              message: response['msg'],
            }, {
              type: 'danger',
              allow_dismiss: false,
              delay: 2800,
              animate: {
                enter: 'animated flipInY',
                exit: 'animated flipOutX'
              },
              onShow: function() {
                this.css({
                  'width': 'auto',
                  'height': 'auto'
                });
              },
            })
          }
        }
      });

    });
  })(jQuery)
</script>


@endsection