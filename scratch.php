<?php
function updateConfDir($dir) {
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = $dir . '/' . $f;
        if (is_dir($path)) {
            updateConfDir($path);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'conf' || pathinfo($path, PATHINFO_EXTENSION) === 'default') {
            $content = file_get_contents($path);
            if (stripos($content, 'C:/xampp') !== false || stripos($content, 'C:\\xampp') !== false) {
                $content = str_ireplace('C:/xampp', 'D:/xampp', $content);
                $content = str_ireplace('C:\\xampp', 'D:\\xampp', $content);
                file_put_contents($path, $content);
                echo "Updated: $path\n";
            }
        }
    }
}
updateConfDir('D:/xampp/apache/conf');
echo "Apache conf update complete!\n";




