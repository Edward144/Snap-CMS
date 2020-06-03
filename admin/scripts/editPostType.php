<?php 
    
    require_once('../../includes/database.php');

    $details = $mysqli->prepare("UPDATE `post_types` SET title = ?, content = ?, image_url = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, meta_author = ? WHERE id = ?");
    $details->bind_param('sssssssi', $_POST['title'], $_POST['content'], $_POST['imageUrl'], $_POST['metaTitle'], $_POST['metaDescription'], $_POST['metaKeywords'], $_POST['metaAuthor'], $_POST['typeId']);
    $ex = $details->execute();

    if($ex === false) {
        $_SESSION['detailsmessage'] = 'Error: Could not update details';
        
        header('Location: ' . $_POST['returnUrl']);
        exit();
    }

    $_SESSION['detailsmessage'] = 'Details have been updated';
        
    header('Location: ' . $_POST['returnUrl']);
    exit();
    
?>