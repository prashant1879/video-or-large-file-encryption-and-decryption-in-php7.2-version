<?php
error_reporting(E_ALL);
ini_set('display_errors', true);


$encKey = 'FCAcEA0HBAoRGyALBQIeCAcaDxYWEQQPBxcXHgAFDgY=';
$encIV = 'DB4gHxkcBQkKCxoRGBkaFA==';

// $file = "OG-video-file.mp4";
$file = "OG-video-file-e.himu";
$inPath = "/var/www/html/video-or-large-file-encryption-and-decryption-in-php7.2-version/" . $file;
$outPath = "/var/www/html/video-or-large-file-encryption-and-decryption-in-php7.2-version/";

function encryptFile($encKey, $encIV, $inPath, $outPath)
{
  $sourceFile = file_get_contents($inPath);
  $key = base64_decode($encKey);
  $iv = base64_decode($encIV);
  $path_parts = pathinfo($inPath);
  $fileName = $path_parts['filename'];
  $outFile = $outPath . $fileName . '-e.himu';
  $encrypter = 'aes-256-cbc';
  $encryptedString = openssl_encrypt($sourceFile, $encrypter, $key, 0, $iv);
  if (file_put_contents($outFile, $encryptedString) != false) return 1;
  else return 0;
}

function decryptFile($encKey, $encIV, $inPath, $outPath)
{
  $encryptedString = file_get_contents($inPath);
  $key = base64_decode($encKey);
  $iv = base64_decode($encIV);
  $path_parts = pathinfo($inPath);
  $fileName = $path_parts['filename'];
  $outFile = $outPath . $fileName . '-d.mp4';
  $encrypter = 'aes-256-cbc';
  $decrypted = openssl_decrypt($encryptedString, $encrypter, $key, 0, $iv);
  if (file_put_contents($outFile, $decrypted) != false) return 1;
  else return 0;
}

// encryptFile($encKey, $encIV, $inPath, $outPath);
// echo "encrypt done >>";
decryptFile($encKey, $encIV, $inPath, $outPath);
echo "decrypt done >>";
exit;
