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

    $type = $_GET['type'] . 's';
    $user = $_SESSION['username'];

    $nextItem = $mysqli->query("SELECT COUNT(*) FROM `{$type}`")->fetch_array()[0] + 1;
    $id = $mysqli->query("SELECT id FROM `{$type}` ORDER BY id DESC LIMIT 1")->fetch_array()[0] + 1;

    $title = ucwords($_GET['type'] . ' ' . $nextItem);
    $url = slugify($_GET['type'] . '-' . $id);

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

    $add->bind_param('sss', $title, $url, $user);
    $add->execute();
    $add->close();

    

    if(!$mysqli->error) {
        echo json_encode([1, $id]);
    }
    else {
        echo json_encode([0, 'Error: Could not amend ' . $_GET['type'] . '.']);
    }

?>