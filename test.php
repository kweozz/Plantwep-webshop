<?php
echo "Current Directory: " . __DIR__ . "<br>";
echo "Contents of 'classes':<br>";
$files = scandir(__DIR__ . '/classes');
print_r($files);
?>

