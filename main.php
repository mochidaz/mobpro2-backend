<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (file_exists(__DIR__ . $path)) {
    return false; 
}

$file = __DIR__ . $path . '.php';
if (file_exists($file)) {
    require $file;
    return true;
}

http_response_code(404);
echo "404 Not Found";
