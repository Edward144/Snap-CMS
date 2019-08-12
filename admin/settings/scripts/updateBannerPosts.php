<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $postType = $_POST['postType'];
    $json = ['<option value="" selected disabled>--Select Post Name--</option>'];

    $posts = $mysqli->query("SELECT id, name FROM `{$postType}`");

    while($post = $posts->fetch_assoc()) {
        array_push($json, 
            '<option value="' . $post['id'] . '">' . $post['name'] . '</option>'
        );
    }

    echo json_encode(implode($json));

?>