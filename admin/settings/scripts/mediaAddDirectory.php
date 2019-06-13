<?php 
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php'); 
    
    function slugify($url) {
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    $dirName = slugify($_POST['dirName']);
	$directory = $_SERVER['DOCUMENT_ROOT'] . '/admin/' .$_POST['directory'] . '/' . $dirName;
    
    if(mkdir($directory)) {
        echo json_encode(1);
    }
    else {
        echo json_encode('Error: Could not create folder.');
    }

    /*if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/' .$_POST['directory'])) {
        echo json_encode($_SERVER['DOCUMENT_ROOT'] . '/admin/' .$_POST['directory']);
        
        exit();
    }

    if(move_uploaded_file($tmpFile, $directory)) {
        echo json_encode('File uploaded successfully.');
    }
    else {
        echo json_encode('Error ' . $_FILES['file']['error'] . ': Could not upload File.');
    }*/

?>