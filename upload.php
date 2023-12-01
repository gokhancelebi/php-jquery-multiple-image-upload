<?php


require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';


$files = $_FILES;
$order = isset($_POST['order']) ? $_POST['order'] : [];

if (isset($files['file'])){
    $files = $files['file'];
    foreach ($files['name'] as $key => $file) {
        if ($files['error'][$key] === 0) {
            $tmp = $files['tmp_name'][$key];
            $image_order = $order[$key];
            $image_name = $files['name'][$key];
            $image_name = time() . '-' . $image_name;
            $image_name = str_replace(' ', '-', $image_name);
            unset ($order[$key]);
            $order[ $image_name] = $image_order;
            move_uploaded_file($tmp, UPLOAD_DIR . $image_name);
            create_thumbnail(UPLOAD_DIR . $image_name, THUMBNAIL_DIR . $image_name);
            echo 'Uploaded File :  ' . $image_name . PHP_EOL;
        }
    }
}


$files = glob(UPLOAD_DIR . '*');

foreach ($files as $file) {
    if (is_file($file) && $file != BASE_DIR . '/.gitignore') {
        $file_name = str_replace(UPLOAD_DIR, '', $file);
        if (!isset($order[$file_name])) {
            unlink($file);
            unlink(THUMBNAIL_DIR . $file_name);
            echo 'Deleted File : ' . $file_name . PHP_EOL;
        }
    }
}

if (count($order) > 0) {
    file_put_contents(BASE_DIR . '/order.json', json_encode($order,JSON_PRETTY_PRINT));
}else{
    if (file_exists(BASE_DIR . '/order.json')){
        unlink(BASE_DIR . '/order.json');
        // delete all images in upload and thumbnail directory
        $files = glob(UPLOAD_DIR . '*');
        foreach ($files as $file) {
            if (is_file($file) && $file != BASE_DIR . '/.gitignore') {
                unlink($file);
                unlink(THUMBNAIL_DIR . $file_name);
                echo 'Deleted File : ' . $file_name . PHP_EOL;
            }
        }
    }
}

echo 'Images Updated!';