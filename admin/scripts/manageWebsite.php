<?php

    require_once('../../includes/database.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

    if($_POST['method'] == 'updateDetails') {
        $checkDetails = $mysqli->query("SELECT COUNT(*) FROM `company_info`");
        
        if($checkDetails->fetch_row()[0] > 0) {
            $updateDetails = $mysqli->prepare(
                "UPDATE `company_info` SET 
                    name = ?, 
                    address_1 = ?, 
                    address_2 = ? , 
                    address_3 = ?, 
                    address_4 = ?, 
                    county = ?, 
                    postcode = ?,
                    country = ?,
                    phone = ?,
                    email = ?,
                    fax = ?,
                    registration_number = ?,
                    vat_number = ?,
                    logo = ?
                ORDER BY id DESC LIMIT 1"
            );
        }
        else {
            $updateDetails = $mysqli->prepare("INSERT INTO `company_info` (name, address_1, address_2, address_3, address_4, county, postcode, country, phone, email, fax, registration_number, vat_number, logo) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        }
        
        $updateDetails->bind_param('ssssssssssssss', $_POST['name'], $_POST['address1'], $_POST['address2'], $_POST['address3'], $_POST['address4'], $_POST['county'], $_POST['postcode'], $_POST['country'], $_POST['phone'], $_POST['email'], $_POST['fax'], $_POST['registrationNumber'], $_POST['vatNumber'], $_POST['logo']);
        $ex = $updateDetails->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['detailsmessage'] = 'Could not update website details';
            
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $_SESSION['status'] = 1;
        $_SESSION['detailsmessage'] = 'Website details updated successfully';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    elseif($_POST['method'] == 'updateSocial') {
        $output = '';
        $_SESSION['status'] = 1;
        $updateSocial = $mysqli->prepare("UPDATE `social_links` SET url = ? WHERE name = ?");
        
        foreach($_POST as $name => $url) {
            if($name != 'method' && $name != 'returnUrl') {
                $updateSocial->bind_param('ss', $url, $name);
                $ex = $updateSocial->execute();
                
                if($ex === false) {
                    $output .= '<span>Failed to save ' . ucwords('name') . '</span><br>';
                    $_SESSION['status'] = 0;
                }
            }
        }
        
        $_SESSION['socialmessage'] = ($_SESSION['status'] == 1 ? 'Social links have updated successfully' : $output);
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }

?>