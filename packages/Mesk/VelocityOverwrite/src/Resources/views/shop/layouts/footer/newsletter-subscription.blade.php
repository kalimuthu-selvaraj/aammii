
	@if (
    $velocityMetaData
    && $velocityMetaData->subscription_bar_content
    || core()->getConfigData('customer.settings.newsletter.subscription')
)

@php
    $reviews = app('Webkul\Velocity\Helpers\Helper')->getShopRecentReviews(4);
    $reviewCount = count($reviews);
@endphp

@if(Route::current()->getName()=="shop.home.index")

<img loading="lazy" class="width-100 mobile-top-space" alt="frame" src="{{ asset('/themes/velocity/assets/images/frame.png') }}">
<div class="container-fluid reviews-container">

	<h2 class="text-center text-white pt-80 mobile-flex"><img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/dark-title-bg-left.png') }}"> What our client says  <img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/dark-title-bg-right.png') }}"></h2>
	
	
    <div class="slideshow-container">
@if ($reviewCount)

	@foreach ($reviews as $key => $review)
<div class="mySlides">
	<div class="row">
		<div class="col-md-1 testimonials-name">
			<span class="customer-name fs20 display-inbl">
				{{ strtoupper(substr( $review['name'], 0, 1 )) }}
			</span>
		</div>
		
		<div class="col-md-3">
			<div class="star-ratings fs16">
				<span class="author">{{ $review['name'] }}</span> <star-ratings :ratings="{{ $review['rating'] }}"></star-ratings>
            </div>
			<span class="brown">{{ __('velocity::app.products.reviewed') }}- <a class="remove-decoration link-color" href="{{ route('shop.productOrCategory.index', $review->product->url_key) }}">{{$review->product->name}}</a></span>
		</div>
		<div class="col-md-8">
			<q class="fs17">{{ $review['comment'] }}</q>
		</div>
	</div>
</div>


<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

@endforeach
@endif
</div>
</div>



@push('scripts')

<script>
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>

@endpush
@endif

    <div class="newsletter-subscription">
        <div class="newsletter-wrapper row">

            @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                <div class="subscribe-newsletter col-lg-6 text-left">
                    <div class="form-container">
                        <form action="{{ route('shop.subscribe') }}">
                            <div class="subscriber-form-div">
                                <div class="control-group">
                                    <span class="newsletter-text">Get latest updates</span>
                                    <input
                                        type="email"
                                        name="subscriber_email"
                                        class="control subscribe-field"
                                        placeholder="{{ __('velocity::app.customer.login-form.your-email-address') }}"
                                        aria-label="Newsletter"
                                        required />

                                    <button class="theme-btn subscribe-btn fw6 mobile-top-10">
                                        {{ __('shop::app.subscription.subscribe') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @if ($velocityMetaData && $velocityMetaData->subscription_bar_content)
                {!! $velocityMetaData->subscription_bar_content !!}
            @endif
        </div>
    </div>
@endif

<img loading="lazy" class="width-100 mt-top" alt="frame2" src="{{ asset('/themes/velocity/assets/images/frame2.png') }}">

@push('scripts')

<script>
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>

@endpush

