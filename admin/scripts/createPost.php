<?php

    session_start();

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');
    
    $mysqli->query("INSERT INTO `posts` VALUES ()");
    $newId = $mysqli->insert_id;

    $name = ucwords(str_replace('-', ' ', rtrim($_POST['postType'], 's'))) . ' ' . $newId;
    $url = slugify($name);
    $visible = 0;
    $author = $mysqli->query("SELECT first_name, last_name FROM `users` WHERE id = {$_SESSION['adminid']}")->fetch_assoc();
    $author = ucwords($author['first_name'] . ' ' . $author['last_name']);
    $lastEdit = $_SESSION['adminid'];

    $setValues = $mysqli->prepare("UPDATE `posts` SET post_type_id = ?, name = ?, url = ?, author = ?, visible = ?, last_edited_by = ? WHERE id = ?");
    $setValues->bind_param('isssiii', $_POST['postTypeId'], $name, $url, $author, $visible, $lastEdit, $newId);
    $ex = $setValues->execute();


    if($ex === false) {
        $_SESSION['createmessage'] = 'Error: Could not create post';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }
    else {
        $mysqli->query(
            "INSERT INTO `post_history` (post_id, post_type_id, NAME, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible) 
            SELECT id, post_type_id, NAME, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible
            FROM `posts` WHERE id = {$newId}"
        );
        
        $returnUrl = '../content-manager/' . $_POST['postType'] . '/id-' . $newId;
        
        header('Location: ' . $returnUrl);
        exit();
    }

?>