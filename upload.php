<?php


$files = $_FILES;
$order = isset($_POST['order']) ? $_POST['order'] : [];

$uploaded = [];


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
            $order['uploads/' . $image_name] = $image_order;
            echo 'uploads/' . $image_name . PHP_EOL;
            move_uploaded_file($tmp, 'uploads/' . $image_name);
            echo 'uploaded ' . $image_name . PHP_EOL;
        }
    }
}


$files = glob('uploads/*');

foreach ($files as $file) {
    if (is_file($file) && $file != 'uploads/.gitignore') {
        if (!isset($order[$file])) {
            unlink($file);
            echo 'deleted ' . $file . PHP_EOL;
        }
    }
}


if (count($order) > 0) {
    file_put_contents(__DIR__ . '/order.json', json_encode($order,JSON_PRETTY_PRINT));
}else{
    if (file_exists(__DIR__ . '/order.json')){
        unlink(__DIR__ . '/order.json');
    }
}

echo 'Images Updated!';