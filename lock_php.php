<?php
    $path = '/var/www/doolox-new/users/';
    $cd = getcwd();
    $user = str_replace($path, '', $cd);
    $user = explode('/', $user);
    $user = $user[0];
    ini_set ('upload_tmp_dir', $path . '/' . $user . '/tmp/'); 
    ini_set ('open_basedir', $path . '/' . $user . '/:/tmp/');
?>
