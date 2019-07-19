<?php    
    

    if(empty($_FILES['file'])) {
        echo json_encode('No files selected.');
    }
    else {
        $files = $_FILES['file']['name'];
        $json = [];

        foreach($_FILES['file']['tmp_name'] as $index => $file) {
            //$file = str_replace("'", "", $file);
            //$file = preg_replace('/\s+/', '_', $file);
            
            /*if(move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/admin/useruploads/' . $_FILES['file']['name'])) {
                echo json_encode(1);
            }
            else {
                echo json_encode(0);
            }*/
            
            array_push($json, $index . ' ' . $file);
        }

        echo json_encode(implode($_FILES['file']));
        
        
    }

    /*$tmpFile = $_FILES['file']['tmp_name'];
	$file = $_FILES['file']['name'];
	$file = str_replace("'", "", $file);
	$file = preg_replace('/\s+/', '_', $file);
	$directory = $_SERVER['DOCUMENT_ROOT'] . '/admin/' .$_POST['directory'] . '/' . $file;
    
    if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/' .$_POST['directory'])) {
        echo json_encode($_SERVER['DOCUMENT_ROOT'] . '/admin/' .$_POST['directory']);
        
        exit();
    }

    if(move_uploaded_file($tmpFile, $directory)) {
        echo json_encode(1);
    }
    else {
        echo json_encode('Error ' . $_FILES['file']['error'] . ': Could not upload File.');
    }*/

?>