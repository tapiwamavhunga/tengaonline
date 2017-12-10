<!doctype html>
<html>
<head>
    @include('includes.frontend.head')
</head>
<body>
  <div class="wrapper">
    
    @if(get_appearance_settings()['general']['custom_css'] == true)
    @include('includes.frontend.content-custom-css')
    @endif
    
    @include('includes.frontend.header')
    
    <section class="content">
        @yield('content')
    </section>
    
    @include('includes.frontend.footer')
    
    
</body>
</html>