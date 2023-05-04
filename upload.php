<?php


$files = $_FILES['file'];
$order = $_POST['order'];

foreach ($files['name'] as $key => $file) {
    if ($files['error'][$key] === 0) {
        $tmp = $files['tmp_name'][$key];
        $filename = time() . '-' . $files['name'][$key];
        $image_order = $order[$key];
        $destination = 'uploads/'.$image_order.'-' . $filename;
        move_uploaded_file($tmp, $destination);
    }
}

echo 'Uploaded';

