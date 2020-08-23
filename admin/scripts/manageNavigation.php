<?php

    require_once('../../includes/database.php');
    
    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }
    
    if($_POST['method'] == 'createMenu') {
        $_SESSION['status'] = 1;
        
        $menuName = preg_replace('/[^a-zA-Z0-9\s]/', '', $_POST['newMenu']);
        
        $new = $mysqli->prepare("INSERT INTO `navigation_menus` (name) VALUES(?)");
        $new->bind_param('s', $menuName);
        $ex = $new->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['createmessage'] = 'Could not create menu';
            header('Location: ' . $_POST['returnurl']);
            exit();
        }
        
        $menuId = $mysqli->insert_id;
    
        $_SESSION['createmessage'] = 'Menu Created Successfully';
        header('Location: ' . ROOT_DIR . 'admin/navigation/' . $menuId);
        exit();
    }
    elseif($_POST['method'] == 'deleteMenu') {
        $_SESSION['status'] = 1;
        
        $delete = $mysqli->prepare("DELETE FROM `navigation_menus` WHERE id = ? AND id > 0");
        $delete->bind_param('i', $_POST['selectMenu']);
        $ex = $delete->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['deletemessage'] = 'Could not delete menu';
            header('Location: ' . $_POST['returnurl']);
            exit();
        }
        
        if($delete->affected_rows <= 0) {
            $_SESSION['status'] = 0;
            $_SESSION['deletemessage'] = 'You do not have permission to delete this menu';
            header('Location: ' . $_POST['returnurl']);
            exit();
        }
        
        $_SESSION['deletemessage'] = 'Menu has been deleted successfully';
        header('Location: ' . $_POST['returnurl']);
        exit();
    }
    elseif($_POST['method'] == 'saveTree') {
        $tree = json_decode($_POST['tree'], true);
        $success = 1;
        $message = '';
        
        $updateTree = $mysqli->prepare("UPDATE `navigation_structure` SET parent_id = ?, position = ?, name = ?, url = ?, image_url = ?, icon = ?, level = ? WHERE menu_id = ? AND id = ?");
        
        foreach($tree as $item) {
            if($item['delete'] == 1) {
                $delete = $mysqli->prepare("DELETE FROM `navigation_structure` WHERE menu_id = ? AND id = ?");
                $delete->bind_param('ii', $_POST['menuId'], $item['id']);
                $ex = $delete->execute();
                
                if($ex === false) {
                    $success = 0;
                    $message .= 'Failed to delete item ' . $item['id'] . ' (' . $item['name'] . ')<br>';
                }
            }
            else {
                $updateTree->bind_param('iissssiii', $item['parentId'], $item['position'], $item['name'], $item['url'], $item['image'], $item['icon'], $item['level'], $_POST['menuId'], $item['id']);
                $ex = $updateTree->execute();
                
                if($ex === false) {
                    $success = 0;
                    $message .= 'Failed to update item ' . $item['id'] . ' (' . $item['name'] . ')<br>';
                }
            }
            
            if($success == 1) {
                $message = 'Menu has been updated successfully.';
            }
        }
        
        echo json_encode([$success, $message]);
    }
	elseif($_POST['method'] == 'updateMenu') {
		$update = $mysqli->prepare("INSERT INTO `navigation_structure` (menu_id, parent_id, name, url, image_url) VALUES(?, ?, ?, ?, ?)");
		$update->bind_param('iisss', $_POST['menuId'], $_POST['parentId'], $_POST['name'], $_POST['url'], $_POST['imageUrl']);
		$ex = $update->execute();
		
		if($ex === false) {
			$_SESSION['status'] = 0;
			$_SESSION['insertmessage'] = 'Could not add item to menu';
			header('Location: ' . $_POST['returnurl']);
			exit();
		}
		
		$_SESSION['status'] = 1;
		$_SESSION['insertmessage'] = 'Item has been added to menu';
		header('Location: ' . $_POST['returnurl']);
		exit();
	}
    elseif($_POST['method'] == 'pullPage') {
        $pull = $mysqli->prepare("SELECT id, name, url FROM `posts` WHERE id = ?");
        $pull->bind_param('i', $_POST['id']);
        $pull->execute();
        $result = $pull->get_result();
        
        if($result->num_rows > 0) {
            $result = $result->fetch_assoc();
            echo json_encode(['name' => $result['name'], 'url' => $result['url']]);
            exit();
        }
    }

?>