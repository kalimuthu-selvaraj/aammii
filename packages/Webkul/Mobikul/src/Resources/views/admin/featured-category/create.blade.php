@extends('mobikul::admin.layouts.content')

@section('page_title')
    {{ __('mobikul::app.mobikul.category.new-category') }}
@stop

@section('content')
    <div class="content">
        <form method="POST" action="{{ route('mobikul.featured-category.store') }}" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="history.length > 1 ? history.go(-1) : window.location = '{{ url('/admin/featuredcategories') }}';"></i>
                        {{ __('mobikul::app.mobikul.category.new-category') }}
                    </h1>
                </div>
                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('mobikul::app.mobikul.category.create-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">

                <div class="form-container">
                    @csrf()

                    <div class="control-group" :class="[errors.has('image') ? 'has-error' : '']">
                        <label for="image">
                            {{ __('mobikul::app.mobikul.category.featured-category-icon') }}
                        </label>

                        <image-wrapper :button-label="'{{ __('mobikul::app.mobikul.category.image') }}'" input-name="image" :multiple="false" ></image-wrapper>

                        <span class="control-error" v-if="{!! $errors->has('image.*') !!}">
                            @foreach ($errors->get('image.*') as $key => $message)
                                @php echo str_replace($key, 'Image', $message[0]); @endphp
                            @endforeach
                        </span>
                    </div>

                    <div class="control-group" :class="[errors.has('sort_order') ? 'has-error' : '']">
                        <label for="sort_order" class="required">
                            {{ __('mobikul::app.mobikul.banner-image.sort-order') }}
                        </label>

                        <input type="text" id="sort_order" class="control" name="sort_order" v-validate="'required|numeric'"  value="{{ old('sort_order') }}" data-vv-as="&quot;{{ __('mobikul::app.mobikul.banner-image.sort-order') }}&quot;" />

                        <span class="control-error" v-if="errors.has('sort_order')">@{{ errors.first('sort_order') }}</span>
                    </div>

                    <div class="control-group" :class="[errors.has('channels[]') ? 'has-error' : '']" >
                        <label for="channels" class="required">
                            {{ __('mobikul::app.mobikul.notification.store-view') }}
                        </label>

                        <select  v-validate="'required'" id="channels" class="control" name="channels[]" multiple="multiple" data-vv-as="&quot;{{ __('mobikul::app.mobikul.notification.store-view') }}&quot;">
                            @foreach ($channels as $channel)
                                <option value="{{ $channel->id }}">
                                    {{ $channel->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="control-error" v-if="errors.has('channels[]')">@{{ errors.first('channels[]') }}
                        </span>
                    </div>

                    <div class="control-group" :class="[errors.has('status') ? 'has-error' : '']">
                        <label for="status" class="required">
                            {{ __('mobikul::app.mobikul.category.category-status') }}
                        </label>

                        <select class="control" name="status" v-validate="'required'" data-vv-as="&quot;{{ __('mobikul::app.mobikul.category.category-status') }}&quot;">
                            <option value="1">{{ __('mobikul::app.mobikul.notification.status.enabled') }}</option>
                            <option value="0">{{ __('mobikul::app.mobikul.notification.status.disabled') }}</option>
                        </select>
                        <span class="control-error" v-if="errors.has('status')">@{{ errors.first('status') }}</span>
                    </div>

                    <div class="control-group" id="category">
                        <h3>{{ __('mobikul::app.mobikul.category.choose-category') }}</h3>
                        <tree-view value-field="id" name-field="category_id" input-type="radio" items='@json($categories)'></tree-view>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop