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