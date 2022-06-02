<?php 
const CUSTOM_CHUNK_SIZE = 8192;
$inputFilename = "./OG-video-file.mp4";
$outputEnptFilename = "./enpt-video-file.mp4";
$outputDnptFilename = "./dnpt-video-file.mp4";

/**
 * @ref https://stackoverflow.com/q/11716047
 */
function encryptFile(string $inputFilename, string $outputFilename, string $key): bool
{
    $iFP = fopen($inputFilename, 'rb');
    $oFP = fopen($outputFilename, 'wb');

    [$state, $header] = sodium_crypto_secretstream_xchacha20poly1305_init_push($key);

    fwrite($oFP, $header, 24); // Write the header first:
    $size = fstat($iFP)['size'];
    for ($pos = 0; $pos < $size; $pos += CUSTOM_CHUNK_SIZE) {
        $chunk = fread($iFP, CUSTOM_CHUNK_SIZE);
        $encrypted = sodium_crypto_secretstream_xchacha20poly1305_push($state, $chunk);
        fwrite($oFP, $encrypted, CUSTOM_CHUNK_SIZE + 17);
        sodium_memzero($chunk);
    }

    fclose($iFP);
    fclose($oFP);
    return true;
}

/**
 * @ref https://stackoverflow.com/q/11716047
 */
function decryptFile(string $inputFilename, string $outputFilename, string $key): bool
{
    $iFP = fopen($inputFilename, 'rb');
    $oFP = fopen($outputFilename, 'wb');

    $header = fread($iFP, 24);
    $state = sodium_crypto_secretstream_xchacha20poly1305_init_pull($header, $key);
    $size = fstat($iFP)['size'];
    $readChunkSize = CUSTOM_CHUNK_SIZE + 17;
    for ($pos = 24; $pos < $size; $pos += $readChunkSize) {
        $chunk = fread($iFP, $readChunkSize);
        [$plain, $tag] = sodium_crypto_secretstream_xchacha20poly1305_pull($state, $chunk);
        fwrite($oFP, $plain, CUSTOM_CHUNK_SIZE);
        sodium_memzero($plain);
    }
    fclose($iFP);
    fclose($oFP);
    return true;
}

// $key = random_bytes(32);
$key = '12345678901234567890123456789012';
// echo $key;
// exit;
echo "ENPT " . encryptFile($inputFilename, $outputEnptFilename, $key);
echo "<br/>";
echo "DNTP " .decryptFile($outputEnptFilename, $outputDnptFilename, $key);
