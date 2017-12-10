<meta charset="UTF-8">
<title>@yield('title')</title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<meta name="csrf-token" content="{{ csrf_token() }}">

@if((Request::is('product/details/*') || Request::is('product/customize/*')) && !empty($single_product_details['meta_keywords']))
<meta name="keywords" content="{{ $single_product_details['meta_keywords'] }}">
@elseif( Request::is('blog/*') && !empty($blog_details_by_slug['meta_keywords']))
<meta name="keywords" content="{{ $blog_details_by_slug['meta_keywords'] }}">
@elseif(!empty($seo_data) && $seo_data['meta_tag']['meta_keywords'])
<meta name="keywords" content="{{ $seo_data['meta_tag']['meta_keywords']}}">
@endif

@if(!empty($seo_data) && $seo_data['meta_tag']['meta_description'])
<meta name="description" content="{{ $seo_data['meta_tag']['meta_description'] }}">
@endif

@if((Request::is('product/details/*') || Request::is('product/customize/*')) && !empty($single_product_details['_product_seo_description']))
<meta name="description" content="{{ $single_product_details['_product_seo_description'] }}">
@endif

@if((Request::is('product/details/*') || Request::is('product/customize/*')) && !empty($single_product_details['post_slug']))
<link rel="canonical" href="{{ route('details-page', $single_product_details['post_slug']) }}">
@endif

@if(Request::is('blog/*') && !empty($blog_details_by_slug['blog_seo_description']))
<meta name="description" content="{{ $blog_details_by_slug['blog_seo_description'] }}">
@endif

@if(Request::is('blog/*') && !empty($blog_details_by_slug['blog_seo_url']))
<link rel="canonical" href="{{ route('blog-single-page', $blog_details_by_slug['blog_seo_url']) }}">
@endif

{!! HTML::style('resources/assets/tenga/css/bootstrap.min.css') !!}
{!! HTML::style('resources/assets/tenga/css/font-awesome.min.css') !!}
{!! HTML::style('resources/assets/tenga/css/bootstrap-grid.min.css') !!}
{!! HTML::style('resources/assets/tenga/css/bootstrap-reboot.min.css') !!}
{!! HTML::style('resources/assets/tenga/css/font-techmarket.css') !!}   
{!! HTML::style('resources/assets/tenga/css/slick.css') !!} 
{!! HTML::style('resources/assets/tenga/css/techmarket-font-awesome.css') !!}  
{!! HTML::style('resources/assets/tenga/css/slick-style.css') !!} 
{!! HTML::style('resources/assets/tenga/css/animate.min.css') !!}
{!! HTML::style('resources/assets/tenga/css/style.css') !!}
{!! HTML::style('resources/assets/tenga/css/colors/orange.css') !!}       
<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,900" rel="stylesheet">
<link rel="shortcut icon" href="assets/images/fav-icon.png">
