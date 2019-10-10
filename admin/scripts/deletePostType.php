<?php

    require_once('../../includes/database.php');

    foreach($_POST as $index => $value) {
        if(strpos($index, 'delete-') !== false) {
            $id = explode('delete-', $index)[1];
            
            $deletePost = $mysqli->prepare("DELETE FROM `post_types` WHERE id = ? AND name <> 'posts' AND name <> 'pages'");
            $deletePost->bind_param('i', $id);
            $ex = $deletePost->execute();
            
            if($ex === false) {
                $_SESSION['pdmessage'] = 'Error: Could not delete post type';
                
                header('Location: ../general-settings');
                exit();
            }
            
            $deletePosts = $mysqli->prepare("DELETE FROM `posts` WHERE post_type_id = ?");
            $deletePosts->bind_param('i', $id);
            $ex = $deletePosts->execute();
            
            if($ex === false) {
                $_SESSION['pdmessage'] = 'Error: Post type has been deleted, but the associated posts could not';
                
                header('Location: ../general-settings');
                exit();
            }
            
            $_SESSION['pdmessage'] = 'Post type has been deleted';
            
            header('Location: ../general-settings');
            exit();
        }
    }

?>