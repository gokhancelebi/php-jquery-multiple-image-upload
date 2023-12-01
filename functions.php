<?php

function preview_images(){

    if (!file_exists(BASE_DIR . '/order.json')){
        return"";
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
        $html .= '<img src="' . UPLOAD_URL . $file . '" class="img-fluid">';
        $html .= '<input type="hidden" name="order[' . $file . ']" value="' . $order . '" class="orders">';
        $html .= '<div class="delete-image">X</div>';
        $html .= '</div>';
    }

    return $html;
}
