<?php 

    //Delete All Files In Temp Uploads
    $tempDir = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/*');

    foreach($tempDir as $tempFile) {
        if(is_file($tempFile)) {
            unlink($tempFile);
        }
    }

?>