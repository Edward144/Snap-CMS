<?php    

    $imageUrls = $_POST['imageUrls'];
    $json = [];

    foreach($imageUrls as $image) {
        $image = urldecode($image);
        
        $file = explode('/', $image);
        $fileCount = count($file);
        $file = $file[$fileCount - 1];
        
        if(!copy($_SERVER['DOCUMENT_ROOT'] . $image, $_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $file)) {
            array_push($json, $file . ': Upload failed, unknown error.<br>');
        }
    }

    echo json_encode(implode($json));

?>