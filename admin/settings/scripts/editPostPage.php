<?php
    
    header('Content-type: application/json');
    
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
    
    $id = $_POST['id'];
    $type = $_POST['type'] . 's';
    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $url = slugify($_POST['url']);
    $author = $_POST['author'];
    $datetime = $_POST['datetime'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $image = $_POST['imageUrl'];

    if($type == 'posts') {
        $update = $mysqli->prepare(
            "UPDATE `{$type}` SET 
                name = ?,
                description = ?,
                url = ?,
                author = ?,
                date_posted = ?,
                content = ?,
                category_id = ?,
                image_url = ?
            WHERE id = ?"
        );
        $update->bind_param('ssssssisi', $title, $desc, $url, $author, $datetime, $content, $category, $image, $id);
    }
    else {
        $update = $mysqli->prepare(
            "UPDATE `{$type}` SET 
                name = ?,
                description = ?,
                url = ?,
                author = ?,
                date_posted = ?,
                content = ?,
                image_url = ?
            WHERE id = ?"
        );
        $update->bind_param('sssssssi', $title, $desc, $url, $author, $datetime, $content, $image, $id);
    }
    $update->execute();
    $update->close();

    if(!$mysqli->error) {
        echo json_encode([1, ucfirst($_POST['type']) . ' has been updated.']);
    }
    else {
        echo json_encode([0, 'Error: Could not update ' . $_POST['type'] . '.']);
    }

?>