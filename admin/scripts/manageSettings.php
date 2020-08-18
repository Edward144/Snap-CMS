<?php

    require_once('../../includes/database.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

    if($_POST['method'] == 'updateSettings') {        
        $_POST['hide_posts'] = (isset($_POST['hide_posts']) ? 1 : 0);
        $output = '';
        $_SESSION['status'] = 1;
        
        $updateSettings = $mysqli->prepare("UPDATE `settings` SET settings_value = ? WHERE settings_name = ?");
        
        foreach($_POST as $name => $value) {
            if($name != 'method' && $name != 'returnUrl') {
                $updateSettings->bind_param('ss', $value, str_replace('_', ' ', $name));
                $ex = $updateSettings->execute();
                
                if($ex === false) {
                    $_SESSION['status'] = 0;
                    $output .= '<span>Failed to update ' . $name . '</span><br>';
                }
            }
        }
        
        if($_SESSION['status'] == 1) {
            $output = 'Settings updated successfully';
        }
        
        $_SESSION['settingsmessage'] = $output;
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    elseif($_POST['method'] == 'createPostType') {
        $_SESSION['status'] = 1;
        
        if($_POST['newType'] != null && $_POST['newType'] != '') {
            $postName = preg_replace('/[^a-zA-Z0-9\-]/', '', preg_replace('/\s/', '-', $_POST['newType']));

            $createPost = $mysqli->prepare("INSERT INTO `post_types` (name) VALUES(?)");
            $createPost->bind_param('s', strtolower($postName));
            $ex = $createPost->execute();
            
            if($ex === false) {
                $_SESSION['status'] = 0;
                $_SESSION['createmessage'] = 'Could not create post type';
                header('Location: ' . $_POST['returnUrl']);
                exit();
            }
            
            $_SESSION['createmessage'] = 'Post type created successfully';
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    elseif($_POST['method'] == 'deletePostType') {
        $_SESSION['status'] = 1;
        
        $deletePost = $mysqli->prepare("DELETE FROM `post_types` WHERE name <> 'pages' AND name <> 'posts' AND id > 2 AND id = ?");
        $deletePost->bind_param('i', $_POST['id']);
        $ex = $deletePost->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['custommessage'] = 'Could not delete post type';
            
            header('Location: ' . $_POST['returnUrl']); 
            exit();
        }
        
        if($deletePost->affected_rows <= 0) {
            $_SESSION['status'] = 0;
            $_SESSION['custommessage'] = 'This post type could not be deleted';
            header('Location: ' . $_POST['returnUrl']); 
            exit();
        }
        
        $_SESSION['custommessage'] = 'Post type was deleted successfully';
        header('Location: ' . $_POST['returnUrl']); 
        exit();
    }

?>