<?php

    require_once('../../includes/database.php');
    
    $mysqli->query("TRUNCATE TABLE `company_info`");

    foreach($_POST as $index => $value) {
        if($value == '') {
            $_POST[$index] = NULL;
        }
    }

    $updateCompany = $mysqli->prepare("INSERT INTO `company_info` (name, address_1, address_2, address_3, address_4, postcode, county, country, 
    phone, email, fax, vat_number, registration_number, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $updateCompany->bind_param('ssssssssssssss', $_POST['name'], $_POST['add1'], $_POST['add2'], $_POST['add3'], $_POST['add4'], $_POST['postcode'], $_POST['county'], $_POST['country'], $_POST['phone'], $_POST['email'], $_POST['fax'], $_POST['vat'], $_POST['reg'], $_POST['logo']);
    $ex = $updateCompany->execute();

    if($ex === false) {
        $_SESSION['compmessage'] = 'Error: Could not update company details';
        
        header('Location: ../company-details');
        exit();
    }

    $_SESSION['compmessage'] = 'Company details have been updated';
        
    header('Location: ../company-details');
    exit();

?>