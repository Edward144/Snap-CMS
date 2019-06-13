<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $page = $_POST['pageId'];
    $parent = $_POST['parentId'];
    $position = $_POST['position'];
    $level = $_POST['level'];
    $customId = $_POST['customId'];
    $navName = $_POST['customName'];
    $navUrl = $_POST['customUrl'];
    $truncate = $_POST['truncate'];
    
    if($navName == '') {
        $navName = null;
    }
    
    if($truncate == 1) {
        $mysqli->query("TRUNCATE TABLE `navigation`");
        
        echo json_encode('Updating Navigation:');
        
        exit();
    }

    if($page != null) {
        $updateNav = $mysqli->prepare("INSERT INTO `navigation` (page_id, parent_id, position, level, custom_id, nav_name, custom_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $updateNav->bind_param('iiiiiss', $page, $parent, $position, $level, $customId, $navName, $navUrl);
        $updateNav->execute();
        $updateNav->close();

        if(!$updateNav->error) {
            echo json_encode('Level ' . $level . ': Added Page ID ' . $page);
        }
        else {
            echo json_encode('Error: Could not add page ID ' . $page . ' to level ' . $level . '.');
        }
    }
    else {
        echo json_encode('Level ' . $level . ': Could not add null page at position ' . $position . '.');
    }

?>