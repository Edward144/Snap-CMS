<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $name = $_GET['name'];
    $add1 = $_GET['add1'];
    $add2 = $_GET['add2'];
    $add3 = $_GET['add3'];
    $add4 = $_GET['add4'];
    $postcode = $_GET['postcode'];
    $phone = $_GET['phone'];
    $email = $_GET['email'];
    $fax = $_GET['fax'];
    $vat = $_GET['vat'];
    $reg = $_GET['reg'];
    $country = $_GET['country'];
    $logo = $_GET['logoUrl'];

    $checkCompany = $mysqli->query("SELECT COUNT(*) FROM `company_info`")->fetch_array()[0];

    if($checkCompany > 0) {
        $updateCompany = $mysqli->prepare("UPDATE `company_info` SET company_name = ?, address_1 = ?, address_2 = ?, address_3 = ?, address_4 = ?, postcode = ?, country = ?, phone = ?, email = ?, fax = ?, vat_number = ?, reg_number = ?, logo = ?");
        $updateCompany->bind_param('sssssssssssss', $name, $add1, $add2, $add3, $add4, $postcode, $country, $phone, $email, $fax, $vat, $reg, $logo);
        $updateCompany->execute();
        $updateCompany->close();
    }
    else {
        $createCompany = $mysqli->prepare(
            "INSERT INTO `company_info` (
                company_name,
                address_1,
                address_2,
                address_3,
                address_4,
                postcode,
                country,
                phone,
                email,
                fax,
                vat_number,
                reg_number,
                logo
            ) VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )"
        );
        $createCompany->bind_param('sssssssssssss', $name, $add1, $add2, $add3, $add4, $postcode, $country, $phone, $email, $fax, $vat, $reg, $logo);
        $createCompany->execute();
        $createCompany->close();
    }

    if(!$mysqli->error) {
        echo json_encode('Company details have been updated.');
    }
    else {
        echo json_encode('Error: Could not update company details.');
    }

?>