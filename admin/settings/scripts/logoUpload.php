<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $existingLogo = $mysqli->query("SELECT logo FROM `company_info`")->fetch_array()[0];

    $tmpLogo = $_FILES['file']['tmp_name'];
    $extension = explode('.', $_FILES['file']['name'])[1];
    $logo = 'logo.' . $extension;

    $directory = $_SERVER['DOCUMENT_ROOT'] . '/admin/useruploads/' . $logo;

    //Upload Logo
    if(move_uploaded_file($tmpLogo, $directory)) {
        echo json_encode('/admin/useruploads/' . $logo);
    }
    else {
        echo json_encode($existingLogo);
    }

?>