<?php
$recaptcha_secret_key = '';
$recaptcha_site_key   = '';

if(\Schema::hasTable('users') && \Schema::hasTable('roles') && \Schema::hasTable('role_user') && \Schema::hasTable('options')){
  $get_data = get_recaptcha_data();
  
  if(count($get_data) > 0){
    $recaptcha_secret_key = $get_data['recaptcha_secret_key'];
    $recaptcha_site_key   = $get_data['recaptcha_site_key'];
  }
}

return [
    'secret'  => $recaptcha_secret_key,
    'sitekey' => $recaptcha_site_key
];