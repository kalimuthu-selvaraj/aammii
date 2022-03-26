@php
    $isRendered = false;
    $advertisementFour = null;
    $isLazyLoad = ! isset($lazyload) ? true : ( $lazyload ? true : false );
@endphp

@if ($velocityMetaData && $velocityMetaData->advertisement)
    @php
        $advertisement = json_decode($velocityMetaData->advertisement, true);

        if (isset($advertisement[4]) && is_array($advertisement[4])) {
            $advertisementFour = array_values(array_filter($advertisement[4]));
        }
    @endphp

    @if ($advertisementFour)
        @php
            $isRendered = true;
        @endphp
		
		<div class="product-policy-container home-bg-image">
		<img loading="lazy" class="head-bg" alt="head-bg" src="{{ asset('/themes/velocity/assets/images/head-bg.png') }}">
		<div class="row col-12 remove-padding-margin p-60">
<div class="col-lg-3 col-sm-12 product-policy-wrapper">
<div class="card">
<div class="policy">
<div class="left"><img loading="lazy" class="" alt="k1" src="{{ asset('/themes/velocity/assets/images/k1.png') }}"></div>
<div class="right"><span class="font-setting fs23 fwb tamil-font-46">கற்க 
</span> <br /><div class="text-width"><span class="fs16"> We provide trainings on the entire process of making each of our products which will help in making/ creating them by yourself or even sell further as per your requirement.
These trainings can be taken either in person at the places where they are held or through the videos shared below.</span></div><span><a :href="`${$root.baseUrl}/page/how-to-make`">[ Read More ]</a></span></div>
</div>
</div>
</div>
<div class="col-lg-3 col-sm-12 product-policy-wrapper">
<div class="card">
<div class="policy">
<div class="left"><img loading="lazy" class="" alt="k1" src="{{ asset('/themes/velocity/assets/images/k2.png') }}"></div>
<div class="right"><span class="font-setting fs23 fwb tamil-font-46">காண</span> <br /><div class="text-width"><span  class="fs16">Rare products or inventions which we come across will be at display in our AAMMII store / at training venue like in a museum. This is done with the intent of getting maximum and quick coverage for these products.</span></div><span><a :href="`${$root.baseUrl}/page/events`">[ Read More ]</a></span></div>
</div>
</div>
</div>
<div class="col-lg-3 col-sm-12 product-policy-wrapper">
<div class="card">
<div class="policy">
<div class="left"><img loading="lazy" class="" alt="k1" src="{{ asset('/themes/velocity/assets/images/k3.png') }}"></div>
<div class="right"><span class="font-setting fs23 fwb tamil-font-46">விற்க  </span> <br /><div class="text-width"><span  class="fs16">This is a great opportunity for all the small scale businesses/ home makers who could sell their self made products through AAMMII.</span></div><span><a :href="`${$root.baseUrl}/page/how-to-sell`">[ Read More ]</a></span></div>
</div>
</div>
</div>
<div class="col-lg-3 col-sm-12 product-policy-wrapper">
<div class="card">
<div class="policy">
<div class="left"><img loading="lazy" class="" alt="k1" src="{{ asset('/themes/velocity/assets/images/k4.png') }}"></div>
<div class="right"><span class="font-setting fs23 fwb tamil-font-46">வாங்க </span> <br /><div class="text-width"><span  class="fs16">You can purchase and reap the benefits from AAMMII’s Traditional, Handcrafted, Natural and Chemical-free products.</span></div><span><a :href="`${$root.baseUrl}/page/how-to-buy`">[ Read More ]</a></span></div>
</div>
</div>
</div>

<div class="container">
<div class="col-lg-11 col-sm-12 product-policy-wrapper text-right theme-color tamil-text tamil-font">
நமது பாரம்பரிய மற்றும் மரபு சார்ந்த பொருட்களை மீட்டெடுத்து ஆவணமாகும் முயற்சியே </br>- <span class="fw6 tamil-font">அம்மி தற்சார்பு சந்தை</span>
</div>
</div>
</div>
</div>
		

		<div class="container-fluid mt-50">		
		<div class="container-fluid advertisement-four-container">
	<div class="row text-center">	
			<h2 class="width-100 mobile-flex mobile-header mobile-top-25"><img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/right-title-bg.png') }}"> Discover More <img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/right-title-bg.png') }}"></h2>
			</div>

        <div class="row mt-50 mobile-top-10">
		<div class="col-md-8">
			<div class="row">
			
			<div class="col-lg-6 col-12 advertisement-container-block offers-ct-panel">
                <a @if (isset($two)) href="{{ $two }}" @endif class="row col-12 remove-padding-margin" aria-label="Advertisement">
                    <img
                        class="col-12 offers-ct-top {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/WUYJbYJBhMWFtze8Zxocqn2cpACZpu8C4LsCJMvv.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/seasons.webp') }}" alt="" />
                </a>
			</div>
			<div class="col-lg-6 col-12 advertisement-container-block offers-ct-panel">
                <a @if (isset($three)) href="{{ $three }}" @endif class="row col-12 remove-padding-margin" aria-label="Advertisement">
                    <img
                        class="col-12 offers-ct-bottom {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/ADtfN4y5Qt0iO1Hz8FyJbrBUoW1OVZ62DS230ZA7.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/deals.webp') }}" alt="" />
                </a>
				</div>
			
			
		
			<div class="col-lg-12 col-12 advertisement-container-block no-padding pr-16">
                <a @if (isset($one)) href="{{ $one }}" @endif class="col-lg-4 col-12 no-padding" aria-label="Advertisement">
                    <img
                        class="col-12 {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/8C1eyUwCZdkheIfP2aLF65QmqpQt4aXbcD83L3wd.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/8C1eyUwCZdkheIfP2aLF65QmqpQt4aXbcD83L3wd.png') }}" alt="" />
                </a>
            </div>
			</div>	
			</div>	
	
	
	
            <div class="col-lg-4 col-12 advertisement-container-block no-padding">
                <a @if (isset($four)) href="{{ $four }}" @endif class="col-lg-4 col-12 no-padding" aria-label="Advertisement">
                    <img
                        class="col-12 {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/x0UefoBfcWpFwBne6DIBj2EkuJfU3IMLPGehCbFl.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/x0UefoBfcWpFwBne6DIBj2EkuJfU3IMLPGehCbFl.png') }}" alt="" />
                </a>
            </div>
        </div>
    </div>
    @endif
@endif

@if (! $isRendered)
    <div class="container-fluid advertisement-four-container">
	<div class="row text-center">	
			<h2 class="mobile-header"><img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}"> Discover More <img loading="lazy" class="discover-image" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}"></h2>
			</div>

        <div class="row">
		<div class="col-md-8">
			<div class="row">
			
			<div class="col-lg-6 col-12 advertisement-container-block offers-ct-panel">
                <a @if (isset($two)) href="{{ $two }}" @endif class="row col-12 remove-padding-margin" aria-label="Advertisement">
                    <img
                        class="col-12 offers-ct-top {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/WUYJbYJBhMWFtze8Zxocqn2cpACZpu8C4LsCJMvv.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/WUYJbYJBhMWFtze8Zxocqn2cpACZpu8C4LsCJMvv.png') }}" alt="" />
                </a>
			</div>
			<div class="col-lg-6 col-12 advertisement-container-block offers-ct-panel">
                <a @if (isset($three)) href="{{ $three }}" @endif class="row col-12 remove-padding-margin" aria-label="Advertisement">
                    <img
                        class="col-12 offers-ct-bottom {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/ADtfN4y5Qt0iO1Hz8FyJbrBUoW1OVZ62DS230ZA7.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/ADtfN4y5Qt0iO1Hz8FyJbrBUoW1OVZ62DS230ZA7.png') }}" alt="" />
                </a>
				</div>
			
			
		
			<div class="col-lg-12 col-12 advertisement-container-block no-padding pr-16">
                <a @if (isset($one)) href="{{ $one }}" @endif class="col-lg-4 col-12 no-padding" aria-label="Advertisement">
                    <img
                        class="col-12 {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/8C1eyUwCZdkheIfP2aLF65QmqpQt4aXbcD83L3wd.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/8C1eyUwCZdkheIfP2aLF65QmqpQt4aXbcD83L3wd.png') }}" alt="" />
                </a>
            </div>
			</div>	
			</div>	
	
	
	
            <div class="col-lg-4 col-12 advertisement-container-block no-padding">
                <a @if (isset($four)) href="{{ $four }}" @endif class="col-lg-4 col-12 no-padding" aria-label="Advertisement">
                    <img
                        class="col-12 {{ $isLazyLoad ? 'lazyload' : '' }}"
                        @if (! $isLazyLoad) src="{{ asset('/themes/velocity/assets/images/x0UefoBfcWpFwBne6DIBj2EkuJfU3IMLPGehCbFl.png') }}" @endif
                        data-src="{{ asset('/themes/velocity/assets/images/x0UefoBfcWpFwBne6DIBj2EkuJfU3IMLPGehCbFl.png') }}" alt="" />
                </a>
            </div>
        </div>
    </div>
@endif


<!--<div class="container">
<div class="row">
	<div class="col-md-4">
		<h2 class="mb-60">Top Rated <img loading="lazy" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}"></h2>
		<div>
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
			
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="col-md-4">
		<h2 class="mb-60">Best Selling <img loading="lazy" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}"></h2>
		<div>
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
			
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<div class="col-md-4">
		<h2 class="mb-60">Aammii's Special <img loading="lazy" alt="wave" src="{{ asset('/themes/velocity/assets/images/wave-1.png') }}"></h2>
		<div>
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
			
			<div class="row mb-60">
				<div class="col-md-4">
					<a title="Yoga mat" href="#" class="product-image-container">
					<img alt="" src="http://localhost:8000/cache/large/product/146/mQpwKOSirrwU5tOuZXsQlloyiR3F3GxnI5tsJDIw.png" class="card-img-top"></a>
				</div>
				<div class="col-md-8">
				<div class="product-details-content pr0">
			<div class="row item-title no-margin">
				<a href="#" title="" class="unset col-12 no-padding">
				<span class="fs20 link-color">Yoga mat</span></a></div>
			<div class="row col-12 no-padding no-margin">
				<div class="product-price d-inline-block"><span class="price-bold theme-color mr-5">$200.00</span> <a href="#" class="fs14 align-top unset top-product">
				<i class="material-icons star-icon-ratings">star</i> 4.5</a></div>
			</div>
			<div class="no-padding col-12 cursor-pointer fs16">
			<div class="d-inline-block">
			<a href="#" class="remove-btn unset add-cart"><span class="">ADD TO CART</span></a></div>
			</div>
			</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>-->