<?php
// Project: Easy and Lightweight WordPress Installer

$zipUrl = 'https://wordpress.org/latest.zip';
$zipFile = 'latest.zip';
$extractDir = __DIR__;
$wordpressDir = $extractDir . '/wordpress';

// دانلود و اکسترکت فایل ZIP
if (!file_exists($zipFile)) {
    echo "Downloading WordPress...\n";
    if (!file_put_contents($zipFile, file_get_contents($zipUrl))) {
        die("Failed to download ZIP file.\n");
    }
    echo "Download completed successfully.\n";
} else {
    echo "ZIP file already exists.\n";
}

$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractDir);
    $zip->close();
    echo "Extraction completed successfully.\n";
} else {
    die("Failed to extract ZIP file.\n");
}

// انتقال محتوا و پاک‌سازی پوشه‌ها
if (is_dir($wordpressDir)) {
    foreach (array_diff(scandir($wordpressDir), ['.', '..']) as $file) {
        rename("$wordpressDir/$file", "$extractDir/$file");
    }
    rmdir($wordpressDir);
    echo "WordPress files moved and folder deleted.\n";
}

unlink($zipFile);

// حذف پلاگین‌ها و تم‌های پیش‌فرض
$itemsToDelete = [
    "$extractDir/wp-content/plugins/akismet",
    "$extractDir/wp-content/plugins/hello.php",
    "$extractDir/wp-content/themes/twentytwentyfour",
    "$extractDir/wp-content/themes/twentytwentythree"
];

foreach ($itemsToDelete as $item) {
    if (is_dir($item)) {
        deleteDirectory($item);
        echo "Removed directory: $item\n";
    } elseif (file_exists($item)) {
        unlink($item);
        echo "Removed file: $item\n";
    }
}

echo "Process completed successfully.\n";

// هدایت به صفحه نصب وردپرس
$siteUrl = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https://' : 'http://';
$siteUrl .= $_SERVER['HTTP_HOST'] . '/wp-admin/install.php';
header("Location: $siteUrl");
exit;

function deleteDirectory($dir) {
    foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
        $path = "$dir/$file";
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}