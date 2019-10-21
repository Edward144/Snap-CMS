<?php 
    
    require_once('../../includes/database.php');

    $details = $mysqli->prepare("UPDATE `post_types` SET title = ?, content = ? WHERE id = ?");
    $details->bind_param('sssi', $_POST['title'], $_POST['content'], $_POST['typeId']);
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