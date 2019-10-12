<?php 

    require_once('../../includes/database.php');
  
    $json = [];

    $posts = $mysqli->prepare("SELECT id, name FROM `posts` WHERE post_type_id = ?");
    $posts->bind_param('i', $_POST['postType']);
    $posts->execute();
    $result = $posts->get_result();
    
    array_push($json, '<option value="0" selected disabled>--Select Post--</option>');
    
    while($row = $result->fetch_assoc()) {
        array_push($json, '<option value="' . $row['id'] .'">' . $row['name'] . '</option>');
    }

    echo json_encode(implode($json));

?>