<?php

function preview_images()
{

    if (!file_exists(BASE_DIR . '/order.json')) {
        return "";
    }

    $files = file_get_contents(BASE_DIR . '/order.json');

    $files = json_decode($files, true);

    // order files by order

    uasort($files, function ($a, $b) {
        return $a > $b;
    });

    $html = "";

    foreach ($files as $file => $order) {
        $html .= '<div class="image">';
        $src = UPLOAD_URL . $file;
        if (file_exists(THUMBNAIL_DIR  . $file)) {
            $src = UPLOAD_URL . 'thumbnails/' . $file;
        }
        $html .= '<img src="' . $src . '" class="img-fluid">';
        $html .= '<input type="hidden" name="order[' . $file . ']" value="' . $order . '" class="orders">';
        $html .= '<div class="delete-image">X</div>';
        $html .= '</div>';
    }

    return $html;
}

function create_thumbnail($source, $dest)
{
    // create 250x250 thumbnail based on pure php7.4 code
    $source_image_type = exif_imagetype($source);
    $source_image = null;

    switch ($source_image_type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $source_image = imagecreatefromgif($source);
            break;
    }

    if ($source_image === null) {
        return false;
    }

    $source_width = imagesx($source_image);

    $source_height = imagesy($source_image);

    $thumbnail_width = 250;

    $thumbnail_height = 250;

    // create 250x250 thumbnail based on pure php7.4 code

    $thumbnail_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);

    imagecopyresampled($thumbnail_image, $source_image, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $source_width, $source_height);

    imagejpeg($thumbnail_image, $dest, 100);
}
