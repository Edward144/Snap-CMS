<?php

    session_start();

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');

    $protocol = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://');
    $url = slugify($_POST['url']);
    $posted = date('Y-m-d H:i:s', strtotime($_POST['posted']));

    //Update General
    $general = $mysqli->prepare("
        UPDATE `posts` SET
            name = ?,
            url = ?,
            short_description = ?,
            content = ?,
            category_id = ?,
            author = ?,
            date_posted = ?,
            last_edited = NOW(),
            last_edited_by = ?,
            custom_content = ?
        WHERE id = ?
    ");
    $general->bind_param('ssssissisi', $_POST['name'], $url, $_POST['short'], $_POST['content'], $_POST['category'], $_POST['author'], $posted, $_SESSION['adminid'], $_POST['customFile'], $_POST['id']);
    $ex = $general->execute();

    if($ex === false) {
        echo json_encode('Error: Could not update general content');
        
        exit();
    }

    //Update Images
    $images = $mysqli->prepare("UPDATE `posts` SET gallery_images = ?, main_image = ? WHERE id = ?");
    $main = null;
    $imageGallery = null;
    $imageNum = 0;

    foreach($_POST['images'] as $index => $image) {
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'images/gallery/' . $_POST['id'])) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'images/gallery/' . $_POST['id'], 755, true);
        }

        if(strpos($image['url'], '/useruploads/') !== false) {
            $imageX = explode('/useruploads/', $image['url'])[1];
            $imageName = explode('/', $imageX);
            $imageCount = count($imageName) - 1;
            $imageName = $imageName[$imageCount];
            
            copy($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'useruploads/' . $imageX, $_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'images/gallery/' . $_POST['id'] . '/' . $imageName);
        }
        else {
            $imageX = explode('/', $image['url']);
            $imageCount = count($imageX) - 1;
            $imageName = $imageX[$imageCount];
        }
        
        if($image['main'] == 1 || $imageNum == 0) {
            $main = $protocol . $_SERVER['SERVER_NAME'] . ROOT_DIR . 'images/gallery/' . $_POST['id'] . '/' . $imageName;
        }
        
        $imageGallery .= '"' . $protocol . $_SERVER['SERVER_NAME'] . ROOT_DIR . 'images/gallery/' . $_POST['id'] . '/' . $imageName . '";';
        
        $imageNum++;
    }

    $images->bind_param('ssi', $imageGallery, $main, $_POST['id']);
    $ex = $images->execute();

    if($ex === false) {
        echo json_encode('Error: Could not update images');
        
        exit();
    }

    //Update Additional Options
    if($_POST['hasOptions'] == '1') {
        //Update Specs
        $specString = null;
        $additional = $mysqli->prepare("UPDATE `posts` SET specifications = ? WHERE id = ?");

        if(!empty($_POST['specs'])) {
            foreach($_POST['specs'] as $index => $spec) {
                if($spec['name'] != '' && $spec['value'] != '') {
                    $specString .= '"' . $spec['name'] . '":"' . $spec['value'] . '";';
                }
            }
        }
        
        $additional->bind_param('si', $specString, $_POST['id']);
        $ex = $additional->execute();
        
        if($ex === false) {
            echo json_encode('Error: Could not additional options');
        
            exit();
        }
    }

    $mysqli->query(
        "INSERT INTO `post_history` (post_id, post_type_id, name, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible, custom_content) 
        SELECT id, post_type_id, NAME, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible, custom_content
        FROM `posts` WHERE id = {$_POST['id']}"
    );

    echo json_encode('Content has been updated');

?>