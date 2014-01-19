<?php

function shailan_get_file($url, $target){
	if(FALSE !== file_put_contents($target . "/" . basename($url), file_get_contents($url))) {
		return $target . "/" . basename($url);
	}
}

function shailan_dir_URL() {
    $dirURL = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
    $dirURL .= ( ($_SERVER["SERVER_PORT"] == "80") ? $_SERVER["SERVER_NAME"] : $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] );
    $dirURL .= dirname( $_SERVER['PHP_SELF'] ) . "/";
 
    return $dirURL;
}

$dir = dirname(__FILE__);
$current_url = shailan_dir_URL();

if (@filetype($dir . "/wp-config-sample.php") == "file" || @filetype($dir . "/wp-config.php") == "file") {
	die();
}

if(is_writable($dir) === false){
	die();
}

$availfiles = array();

if($dh = opendir($dir)){
	while(($file = readdir($dh)) !== false){
		if(filetype($dir."/".$file)=="file" && substr($file, 0, 9)=="wordpress"){
			if(substr($file, strlen($file)-3)==".gz" || substr($file, strlen($file)-4)==".zip"){
				$availfiles[] = $file;
			}
		}
	}
	closedir($dh);
}

if(count($availfiles)==0){
	$availfiles[] = shailan_get_file( 'http://wordpress.org/latest.tar.gz', dirname(__FILE__) );
}elseif(count($availfiles)>1){
	die();
}

// echo "<br /><h3>Extracting..</h3><br />";
// system("tar -zxvf " . $availfiles[0] , $buff);

// echo "<br /><h3>Moving..</h3><br />";
// system("mv -f " . $dir . "/wordpress/* " . $dir . "", $buff2);

// echo "<br /><h3>Removing unnecessary files..</h3><br />";
// system("rm -rf wordpress", $buff3);

// echo "<br /><h3>Done.</h3><br />";

// echo "<br /><h3>Install WP : <a href=\"" . $current_url ."wp-admin/install.php\">Proceed..</a></h3><br />";


$file = $availfiles[0];

// get the absolute path to $file
$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

$zip = new ZipArchive;
$res = $zip->open($file);
var_dump($res);
die();
if ($res === TRUE) {
  // extract it to the path we determined above
  $zip->extractTo($path);
  $zip->close();
  echo "WOOT! $file extracted to $path";
} else {
    echo "Doh! I couldn't open $file";
}