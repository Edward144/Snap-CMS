<?php 


    if($_POST['file']) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $_POST['file']);
        echo json_encode(1);
    }
    else {
        //Delete All Files In Temp Uploads
        $tempDir = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/*');

        foreach($tempDir as $tempFile) {
            if(is_file($tempFile)) {
                unlink($tempFile);
            }
        }
    }

?>