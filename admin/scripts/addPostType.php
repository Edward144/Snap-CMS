<?php
    
    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');

    $name = slugify($_POST['postName']);
    $options = (isset($_POST['hasOptions']) ? 1 : 0);
    $_SESSION['postName'] = $name;

    echo $name . ' ' . $options;
    
    $addPost = $mysqli->prepare("INSERT INTO `post_types` (name, has_options) VALUES (?, ?)");
    $addPost->bind_param('si', $name, $options);
    $ex = $addPost->execute();

    if($ex === false) {
        $_SESSION['pamessage'] = 'Error: Could not create post type';
        
        header('Location: ../general-settings');
        exit();
    }

    $_SESSION['pamessage'] = 'Post type has been created';
        
    header('Location: ../general-settings');
    exit();

?>