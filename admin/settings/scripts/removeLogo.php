<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $checkCompany = $mysqli->query("SELECT COUNT(*) FROM `company_info`")->fetch_array()[0];

    if($checkCompany > 0) {
        $mysqli->query('UPDATE `company_info` SET logo = NULL');
        
        echo json_encode(1);
    }
    else {
        echo json_encode('Error: No logo has been uploaded.');
    }

?>