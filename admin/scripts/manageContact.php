<?php

	require_once('../../includes/database.php');
    require_once('../../includes/functions.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

	if($_POST['method'] == 'createContact') {
		$_SESSION['status'] = 1;
		$unique = rtrim(base64_encode(time()), '=');
		
		$create = $mysqli->prepare("INSERT INTO `contact_forms` (name) VALUES(?)");
		$create->bind_param('s', $unique);
		$ex = $create->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['createmessage'] = 'Could not create contact form';
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
		
		$lastId = $mysqli->insert_id;
		$name = 'Contact Form ' . $lastId;
		
		
		$create = $mysqli->prepare("UPDATE `contact_forms` SET name = ? WHERE id = ?");
        $create->bind_param('si', $name, $lastId);
        $create->execute();
        
        header('Location: ' . explode('?', $_POST['returnUrl'])[0] . '?id=' . $lastId);
	}
	elseif($_POST['method'] == 'updateContact') {
		$update = $mysqli->prepare("UPDATE `contact_forms` SET name = ?, subject = ?, sitekey = ?, secretkey = ?, structure = ? WHERE id = ?");
		$update->bind_param('sssssi', $_POST['name'], $_POST['subject'], $_POST['sitekey'], $_POST['secretkey'], $_POST['structure'], $_POST['id']);
		$ex = $update->execute();
		
		if($ex === false) {
			$_SESSION['status'] = 0;
			$_SESSION['updatemessage'] = 'Could not save contact form'; 
			header('Location: ' . $_POST['returnurl']);
			exit();
		}
		
		$_SESSION['status'] = 1;
		$_SESSION['updatemessage'] = 'Contact form has been saved'; 
		header('Location: ' . $_POST['returnurl']);
		exit();
	}
	elseif($_POST['method'] == 'deleteContact') {
		$delete = $mysqli->prepare("DELETE FROM `contact_forms` WHERE id = ?");
		$delete->bind_param('i', $_POST['id']);
		$ex = $delete->execute();
		
		if($ex === false || $delete->affected_rows <= 0) {
            echo json_encode([0, 'Could not delete contact form']);
            exit();
        }
        
        echo json_encode([1, 'Contact form has been deleted successfully']);
	}
	
?>