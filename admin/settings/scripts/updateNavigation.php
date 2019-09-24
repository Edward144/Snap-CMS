<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $menuStructure = $_POST['menuArray'];
    $menuId = $_POST['menuId'];
    $json = [];
    
    $delMenu = $mysqli->prepare("DELETE FROM `navigation` WHERE menu_id = ?");
    $delMenu->bind_param('i', $menuId);
    $delMenu->execute();
    $delMenu->close();
    
    $updateNav = $mysqli->prepare("INSERT INTO `navigation` (item_id, menu_id, parent_id, position, display_name, page_url, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach($menuStructure as $index => $value) {
        $findParent = $mysqli->query("SELECT id FROM `navigation` WHERE item_id = {$value['parent_id']}");
        
        if(($findParent->num_rows >= 1 && $value['parent_id'] > 0) || ($value['parent_id'] == 0)) {
            $updateNav->bind_param('iiiisss', $index, $menuId, $value['parent_id'], $value['position'], $value['display_name'], $value['page_url'],     $value['image_url']);
            $updateNav->execute();
        }        
    }

    $updateNav->close();

    echo json_encode(implode($json));

?>