<?php
echo "Current Directory: " . __DIR__ . "<br>";
echo "Directory Contents:<br>";
print_r(scandir(__DIR__));
