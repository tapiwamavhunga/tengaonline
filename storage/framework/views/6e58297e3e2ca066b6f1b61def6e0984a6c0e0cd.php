<meta charset="UTF-8">
<title><?php echo $__env->yieldContent('title'); ?></title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<?php if((Request::is('product/details/*') || Request::is('product/customize/*')) && !empty($single_product_details['meta_keywords'])): ?>
<meta name="keywords" content="<?php echo e($single_product_details['meta_keywords']); ?>">
<?php elseif( Request::is('blog/*') && !empty($blog_details_by_slug['meta_keywords'])): ?>
<meta name="keywords" content="<?php echo e($blog_details_by_slug['meta_keywords']); ?>">
<?php elseif(!empty($seo_data) && $seo_data['meta_tag']['meta_keywords']): ?>
<meta name="keywords" content="<?php echo e($seo_data['meta_tag']['meta_keywords']); ?>">
<?php endif; ?>

<?php if(!empty($seo_data) && $seo_data['meta_tag']['meta_description']): ?>
<meta name="description" content="<?php echo e($seo_data['meta_tag']['meta_description']); ?>">
<?php endif; ?>

<?php if((Request::is('product/details/*') || Request::is('product/customize/*')) && !empty($single_product_details['_product_seo_description'])): ?>
<meta name="description" content="<?php echo e($single_product_details['_product_seo_description']); ?>">
<?php endif; ?>

<?php if((Request::is('product/details/*') || Request::is('product/customize/*')) && !empty($single_product_details['post_slug'])): ?>
<link rel="canonical" href="<?php echo e(route('details-page', $single_product_details['post_slug'])); ?>">
<?php endif; ?>

<?php if(Request::is('blog/*') && !empty($blog_details_by_slug['blog_seo_description'])): ?>
<meta name="description" content="<?php echo e($blog_details_by_slug['blog_seo_description']); ?>">
<?php endif; ?>

<?php if(Request::is('blog/*') && !empty($blog_details_by_slug['blog_seo_url'])): ?>
<link rel="canonical" href="<?php echo e(route('blog-single-page', $blog_details_by_slug['blog_seo_url'])); ?>">
<?php endif; ?>

<?php echo HTML::style('resources/assets/tenga/css/bootstrap.min.css'); ?>

<?php echo HTML::style('resources/assets/tenga/css/font-awesome.min.css'); ?>

<?php echo HTML::style('resources/assets/tenga/css/bootstrap-grid.min.css'); ?>

<?php echo HTML::style('resources/assets/tenga/css/bootstrap-reboot.min.css'); ?>

<?php echo HTML::style('resources/assets/tenga/css/font-techmarket.css'); ?>   
<?php echo HTML::style('resources/assets/tenga/css/slick.css'); ?> 
<?php echo HTML::style('resources/assets/tenga/css/techmarket-font-awesome.css'); ?>  
<?php echo HTML::style('resources/assets/tenga/css/slick-style.css'); ?> 
<?php echo HTML::style('resources/assets/tenga/css/animate.min.css'); ?>

<?php echo HTML::style('resources/assets/tenga/css/style.css'); ?>

<?php echo HTML::style('resources/assets/tenga/css/colors/orange.css'); ?>       
<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,900" rel="stylesheet">
<link rel="shortcut icon" href="assets/images/fav-icon.png">
