<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" rel="stylesheet" href="assets/bootstrap/dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="assets/jquery-ui/dist/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="form mb-4">
            <form action="<?=UPLOAD_PHP_URL?>" method="post" enctype="multipart/form-data" class="upload-form">
                <button type="button" class="upload-button btn btn-primary"> + Add Images</button>
                <input type="file" name="fileUpload" multiple accept="image/*" id="multiple-image">
                <button type="submit" class="btn btn-success submit-button">Save</button>
            </form>
        </div>
        <p>
            <strong>Note:</strong> You can drag and drop to change order.<br>
            <strong>Note:</strong> First image will be thumbnail.<br>
        </p>
        <div class="image-container">
            <div class="image-previews row">
                <?php
                if (file_exists(BASE_DIR . '/order.json')){
                    $files = file_get_contents(BASE_DIR . '/order.json');

                    $files = json_decode($files, true);

                    // order files by order

                    uasort($files, function ($a, $b) {
                        return $a > $b;
                    });

                    foreach ($files as $file => $order) {
                        echo '<div class="image">';
                        $src = UPLOAD_URL . $file;
                        if (file_exists(THUMBNAIL_DIR  . $file)) {
                            $src = UPLOAD_URL . 'thumbnails/' . $file;
                        }
                        echo '<img src="' . $src . '" class="img-fluid">';
                        echo '<input type="hidden" name="order[' . $file . ']" value="' . $order . '" class="orders">';
                        echo '<div class="delete-image">X</div>';
                        echo '</div>';
                    }
                }else{
                    $files = [];
                }
                ?>
            </div>
            <?php
            if (count($files) == 0) {
                echo '<div class="information">Drop Images Here</div>';
            }
            ?>
        </div>
    </div>
</div>
<script src="assets/jquery/dist/jquery.min.js"></script>
<script src="assets/jquery-ui/dist/jquery-ui.min.js"></script>
<script>
    $(function () {

        var form_data = new FormData();
        var last_order = 1;

        function upload_images(files) {
            var html = '';
            for (var i = 0; i < files.length; i++) {
                let objectUrl = URL.createObjectURL(files[i]);
                html += '<div class="image">';
                html += '<img src="' + URL.createObjectURL(files[i]) + '" class="img-fluid">';
                html += '<input type="hidden" name="order[' + objectUrl + ']" value="' + last_order + '" class="orders">';
                html += '<div class="delete-image">X</div>';
                html += '</div>';
                // insert file data in form data
                form_data.append('file[' + objectUrl + ']', files[i]);

                last_order++;
            }
            $('.image-previews').append(html);
            $('.image-container .information').remove();
        }

        $('.image-container .information').on('click', function () {
            $('#multiple-image').click();
        });

        $('.upload-button').on('click', function () {
            $('#multiple-image').click();
        });
        $('#multiple-image').on('change', function () {
            var files = $(this)[0].files;
            upload_images(files);
            $(this).val('');
        });

        $('.upload-form').on('submit', function (e) {

            e.preventDefault();

            let order = 1;

            $('.orders').each(function () {
                form_data.delete($(this).attr('name'));
                form_data.append($(this).attr('name'), order);
                order++;
            });

            $('.upload-button').attr('disabled', true);
            $('.submit-button').attr('disabled', true);


            $.ajax({
                url: '<?=UPLOAD_PHP_URL?>',
                method: 'post',
                data: form_data,
                contentType: false,
                processData: false,
                success: function (data) {
                    form_data = new FormData();
                    $('.image-previews').html('');
                    $.get('<?=PREVIEW_PHP_URL?>', function (data) {
                        $('.image-previews').html(data);
                    });
                    alert(data);
                    $('.upload-button').text('+ Add Images');
                    $('.upload-button').attr('disabled', false);
                    $('.submit-button').attr('disabled', false);

                },
                error: function (err) {
                    console.log(err);
                },
                // uploading process for each image
                xhr: function () {
                    var xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('.upload-button').text('Uploading: ' + percent + '%');
                        }
                    });
                    return xhr;
                }
            });
        });

        $(".image-previews").sortable(
            {
                update: function (event, ui) {
                    var order = 1;
                    $('.image-previews .image').each(function () {
                        $(this).find('input').val(order);
                        order++;
                    });
                }
            }
        );

        $(".image-previews,.information").on('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $(".image-previews,.information").on('dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

        $(".image-previews,.information").on('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.originalEvent.dataTransfer) {
                if (e.originalEvent.dataTransfer.files.length) {
                    var files = e.originalEvent.dataTransfer.files;
                    var html = '';
                    upload_images(files);
                    $('.image-previews').append(html);
                    $('.information').remove()
                }
            }
        });

        $(document).on('click', '.delete-image', function () {
            $(this).parent().remove();
        });

    });

</script>
</body>
</html>
