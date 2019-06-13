<?php    
    
    $tmpFile = $_FILES['file']['tmp_name'];
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
    }

?>