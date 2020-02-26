<?php
    
    session_start();

    require_once('../../includes/database.php');
    
    $revisionId = $_POST['revisionId'];
    $postId = $_POST['postId'];

    $revert = $mysqli->prepare("
        UPDATE `posts` AS posts
            LEFT OUTER JOIN `post_history` AS history ON posts.id = history.post_id
        SET posts.post_type_id = history.post_type_id,
            posts.short_description = history.short_description,
            posts.content = history.content,
            posts.url = history.url,
            posts.main_image = history.main_image,
            posts.gallery_images = history.gallery_images,
            posts.specifications = history.specifications,
            posts.category_id = history.category_id,
            posts.author = history.author,
            posts.date_posted = history.date_posted,
            posts.visible = history.visible,
            posts.custom_content = history.custom_content
        WHERE history.id = ? AND posts.id = ?
    ");
    $revert->bind_param('ii', $revisionId, $postId);
    $ex = $revert->execute();
    
    if($ex === false) {
        echo json_encode([0, 'Error: Could not revert post']);
        
        exit();
    }

    $revert = $mysqli->prepare("UPDATE `posts` SET last_edited = NOW(), last_edited_by = ? WHERE id = ?");
    $revert->bind_param('ii', $_SESSION['adminid'], $postId);
    $ex = $revert->execute();
    
    if($ex === false) {
        echo json_encode([0, 'Error: Could not revert post']);
        
        exit();
    }

    $mysqli->query(
        "INSERT INTO `post_history` (post_id, post_type_id, name, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible, custom_content) 
        SELECT id, post_type_id, NAME, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible, custom_content
        FROM `posts` WHERE id = {$postId}"
    );

    echo json_encode([1, 1]);

?>