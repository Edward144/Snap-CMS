<?php    
    
    $json = [];
    $tempDir = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/*');
        
    foreach($tempDir as $tempFile) {
        if(is_file($tempFile)) {
            $file = explode('/', $tempFile);
            $count = count($file);
            $file = $file[$count - 1];
                
            array_push($json, 
                '<div class="galleryItem">
                    <img src="/admin/images/tempuploads/' . $file . '" alt="' . $file .'">
                </div>'
            );
        }
    }

    echo json_encode(implode($json));

?>