<?php
$file = 'app/Services/AiContextService.php';
$content = file_get_contents($file);
$content = preg_replace('/->select\(\[(.*?)\]\)/', '->select([\'poster\', $1])', $content);
file_put_contents($file, $content);
echo "Replaced successfully\n";
