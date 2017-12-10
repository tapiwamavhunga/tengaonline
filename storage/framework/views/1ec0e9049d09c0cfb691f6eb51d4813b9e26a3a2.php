<!doctype html>
<html>
<head>
    <?php echo $__env->make('includes.frontend.head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</head>
<body>
  <div class="wrapper">
    
    <?php if(get_appearance_settings()['general']['custom_css'] == true): ?>
    <?php echo $__env->make('includes.frontend.content-custom-css', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
    
    <?php echo $__env->make('includes.frontend.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    
    <section class="content">
        <?php echo $__env->yieldContent('content'); ?>
    </section>
    
    <?php echo $__env->make('includes.frontend.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    
    
</body>
</html>