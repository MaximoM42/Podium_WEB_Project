<?php
/**
 * PODIUM - Generador de CAPTCHA
 * 
 * Genera una imagen CAPTCHA y guarda el código en sesión
 * 
 * Endpoint: GET /backend/api/auth/captcha.php
 */

require_once __DIR__ . '/../../config/config.php';

// Generar código aleatorio
$code = '';
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Sin caracteres confusos
$length = 6;

for ($i = 0; $i < $length; $i++) {
    $code .= $characters[rand(0, strlen($characters) - 1)];
}

// Guardar en sesión
$_SESSION['captcha'] = $code;
$_SESSION['captcha_time'] = time();

// Crear imagen
$width = 200;
$height = 60;
$image = imagecreatetruecolor($width, $height);

// Colores
$bgColor = imagecolorallocate($image, 240, 240, 240);
$textColor = imagecolorallocate($image, 50, 50, 50);
$lineColor = imagecolorallocate($image, 200, 200, 200);
$dotColor = imagecolorallocate($image, 150, 150, 150);

// Fondo
imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

// Líneas de ruido
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), 
              rand(0, $width), rand(0, $height), $lineColor);
}

// Puntos de ruido
for ($i = 0; $i < 100; $i++) {
    imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);
}

// Escribir texto
$fontSize = 5; // Tamaño de fuente (1-5 para fuentes incorporadas)
$x = 30;
$y = 25;

// Escribir cada carácter con variación
for ($i = 0; $i < strlen($code); $i++) {
    $char = $code[$i];
    $charColor = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
    
    // Usar imagestring para escribir el carácter
    imagestring($image, $fontSize, $x + ($i * 25), $y + rand(-5, 5), $char, $charColor);
}

// Headers
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Generar imagen
imagepng($image);
imagedestroy($image);

?>
