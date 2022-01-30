<?php  
    
    $url = $_POST['url'];
    $type = $_POST['type'];
    $delete = $_SERVER['DOCUMENT_ROOT'] . '/admin/' . $url;

    if($type == 'directory') {        
        if(rmdir($delete)) {
            echo json_encode(1);
        }
        else {
            echo json_encode('Error: Could not delete folder. Files exist within this folder.');
        }
    }
    elseif($type == 'file') {
        if(unlink($delete)) {
            echo json_encode(1);
        }
        else {
            echo json_encode('Error: Could not delete file.');
        }
    }

?>