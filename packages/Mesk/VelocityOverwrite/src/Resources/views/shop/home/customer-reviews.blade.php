@php
    $reviews = app('Webkul\Velocity\Helpers\Helper')->getShopRecentReviews(4);
    $reviewCount = count($reviews);
@endphp

<!--<img loading="lazy" class="width-100" alt="frame" src="{{ asset('/themes/velocity/assets/images/frame.png') }}">
<div class="container-fluid reviews-container">

	<h2 class="text-center text-white pt-80"><img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-white.png') }}"> What our client says <img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-white.png') }}"></h2>
	
	
    <div class="slideshow-container">
@if ($reviewCount)

	@foreach ($reviews as $key => $review)
<div class="mySlides">
	<div class="row">
		<div class="col-md-1">
			<span class="customer-name fs20 display-inbl">
				{{ strtoupper(substr( $review['name'], 0, 1 )) }}
			</span>
		</div>
		
		<div class="col-md-4">
			<div class="star-ratings fs16">
				<span class="author">{{ $review['name'] }}</span> <star-ratings :ratings="{{ $review['rating'] }}"></star-ratings>
            </div>
			<span class="brown">{{ __('velocity::app.products.reviewed') }}- <a class="remove-decoration link-color" href="{{ route('shop.productOrCategory.index', $review->product->url_key) }}">{{$review->product->name}}</a></span>
		</div>
		<div class="col-md-7">
			<q class="fs17">{{ $review['comment'] }}</q>
		</div>
	</div>
</div>


<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

@endforeach
@endif
</div>
</div>-->



