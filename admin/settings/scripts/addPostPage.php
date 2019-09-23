<?php

    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    function slugify($url) {
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    $type = $_GET['type'];
    $author = $mysqli->query("SELECT first_name, last_name FROM `users` WHERE username = '{$_SESSION['username']}'")->fetch_assoc();
    $author = $author['first_name'] . ' ' . $author['last_name'];

    $id = $mysqli->query("SELECT id FROM `{$type}` ORDER BY id DESC LIMIT 1")->fetch_array()[0] + 1;

    $title = ucwords(rtrim($_GET['type'], 's') . ' ' . $id);
    $url = slugify(rtrim($_GET['type'], 's') . '-' . $id);

    $add = $mysqli->prepare(
        "INSERT INTO `{$type}` (
            name,
            url,
            author,
            date_posted,
            visible
        ) VALUES (
            ?,
            ?,
            ?,
            NOW(),
            0
        )"
    );

    $add->bind_param('sss', $title, $url, $author);
    $add->execute();
    $add->close();
    
    $id = $mysqli->insert_id;
    
    if($type != 'pages' && $type != 'posts') {
        if($mysqli->query("SHOW TABLES LIKE '{$type}_options'")->num_rows > 0) {
            $mysqli->query("INSERT INTO `{$type}_options` (post_type_id) VALUES({$id})");
        }
    }

    if(!$mysqli->error) {
        echo json_encode([1, $id]);
    }
    else {
        echo json_encode([0, 'Error: Could not amend ' . $_GET['type'] . '.']);
    }

?>
