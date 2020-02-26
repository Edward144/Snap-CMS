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
            posts.gallery = history.gallery,
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

    echo json_encode([1, 1]);

?>