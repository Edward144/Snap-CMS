<?php

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');

    //Prevent access if user is not logged in
    if(!isset($_SESSION['adminid'])) {
        header('Location: ' . ROOT_DIR . 'admin-login');
    }

    if($_POST['method'] == 'changeVisibility') {
        $status = ($_POST['action'] == '1' ? 1 : 0);
        
        $visibility = $mysqli->prepare("UPDATE `posts` SET visible = ? WHERE id = ?");
        $visibility->bind_param('ii', $status, $_POST['id']);
        $visibility->execute();
        
        echo json_encode($status);
    }
    elseif($_POST['method'] == 'deleteContent') {
        $delete = $mysqli->prepare("DELETE FROM `posts` WHERE id = ?");
        $delete->bind_param('i', $_POST['id']);
        $ex = $delete->execute();
        
        if($ex === false) {
            echo json_encode([0, 'Could not delete content']);
            exit();
        }
        
        if($delete->affected_rows <= 0) {
            echo json_encode([0, 'Could not delete content']);
            exit();
        }
        
        echo json_encode([1, 'Content has been deleted successfully']);
    }
    elseif($_POST['method'] == 'createContent') {
        $_SESSION['status'] = 1;
        $unique = rtrim(base64_encode(time()), '=');
        
        $create = $mysqli->prepare("INSERT INTO `posts` (post_type_id, url, last_edited_by) VALUES(?, ?, ?)");
        $create->bind_param('isi', $_POST['postTypeId'], $unique, $_SESSION['adminid']);
        $ex = $create->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['createmessage'] = 'Could not create content';
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $lastId = $mysqli->insert_id;
        
        $postType = $mysqli->prepare("SELECT name FROM `post_types` WHERE id = ?");
        $postType->bind_param('i', $_POST['postTypeId']);
        $postType->execute();
        $result = $postType->get_result();
        
        
        $postType = $result->fetch_array()[0];
        $name = ucwords(str_replace('-', ' ', $postType)) . ' ' . $lastId;
        $url = slugify($postType . '-' . $lastId);
        
        $create = $mysqli->prepare("UPDATE `posts` SET name = ?, url = ? WHERE id = ?");
        $create->bind_param('ssi', $name, $url, $lastId);
        $create->execute();
        
        header('Location: ' . explode('?', $_POST['returnUrl'])[0] . '?id=' . $lastId);
    }
    elseif($_POST['method'] == 'saveContent') {
        $formData = [];
	parse_str($_POST['formData'], $formData);

	$carouselData = (json_encode($_POST['carouselData']) == 'null' ? null : json_encode($_POST['carouselData']));

	$update = $mysqli->prepare("UPDATE `posts` SET name = ?, short_description = ?, content = ?, url = ?, gallery = ?, author = ?, date_posted = ?, last_edited = NOW(), last_edited_by = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, meta_author = ? WHERE id = ? AND post_type_id = ?");
	$update->bind_param('sssssssissssii', $formData['title'], $formData['shortDesc'], $formData['content'], slugify($formData['url']), $carouselData, $formData['author'], $formData['datePosted'], $_SESSION['adminid'], $formData['metaTitle'], $formData['metaDescription'], $formData['metaKeywords'], $formData['metaAuthor'], $formData['id'], $formData['postTypeId']);
	$ex = $update->execute();

	if($ex === false) {
		echo json_encode([0, 'Could not save content']);
		exit();
	}

	echo json_encode([1, 'Content has saved successfully']);
    }
    elseif($_POST['method'] == 'updateLanding') {
        $_SESSION['status'] = 1;
        
        $landing = $mysqli->prepare("UPDATE `post_types` SET title = ?, image_url = ?, content = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, meta_author = ? WHERE id = ?");
        $landing->bind_param('sssssssi', $_POST['title'], $_POST['coverImage'], $_POST['content'], $_POST['metaTitle'], $_POST['metaDescription'], $_POST['metaKeywords'], $_POST['metaAuthor'], $_POST['postTypeId']);
        $ex = $landing->execute();
        
        if($ex === false) {
            $_SESSION['status'] = 0;
            $_SESSION['landingmessage'] = 'Could not update landing page';
            header('Location: ' . $_POST['returnUrl']);
            exit();
        }
        
        $_SESSION['landingmessage'] = 'Landing page has been updated successfully';
        header('Location: ' . $_POST['returnUrl']);
    }
    
?>
