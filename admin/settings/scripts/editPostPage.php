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
    $type = $_POST['type'];
    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $url = slugify($_POST['url']);
    $author = $_POST['author'];
    $datetime = $_POST['datetime'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $image = $_POST['imageUrl'];
    $custom = $_POST['custom'];

    if($mysqli->query("SHOW TABLES LIKE '{$type}_options'")->num_rows > 0) {
        if($type != 'posts' && $type != 'pages') {
            $galleryExist = $_POST['galleryExist'];
            $galleryNew = $_POST['galleryNew'];
            $galleryMain = $_POST['galleryMain'];
            $features = $_POST['features'];
            $spec = $_POST['spec'];

            if($galleryExist == '" ";') {
                $galleryExist = '';
            }

            if($galleryNew == '" ";') {
                $galleryNew = '';
            }

            $gallery = $galleryExist . $galleryNew;

            if($galleryNew != null && $galleryNew != '" ";') {
                if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/gallery/' . $type . '/' . $id)) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . '/gallery/' . $type . '/' . $id, 0777, true);
                }

                $tempFiles = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/*');

                foreach($tempFiles as $tempFile) {            
                    if(is_file($tempFile)) {
                        $tempFile = explode('/', $tempFile);
                        $count = count($tempFile);
                        $tempFile = $tempFile[$count - 1];

                        rename($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $tempFile, $_SERVER['DOCUMENT_ROOT'] . '/gallery/' . $type . '/' . $id . '/' . $tempFile);
                    }
                }
            }

            if($galleryMain == null || $galleryMain == '') {
                $galleryMain = ltrim(explode('";', $gallery)[0], '"');
            }

            $checkOptions = $mysqli->query("SELECT COUNT(*) FROM `{$type}_options` WHERE post_type_id = {$id}")->fetch_array()[0];

            if($checkOptions > 0) {
                $updateOptions = $mysqli->prepare("UPDATE `{$type}_options` SET gallery_images = ?, gallery_main = ?, features = ?, specifications = ? WHERE post_type_id = ?");
                $updateOptions->bind_param('ssssi', $gallery, $galleryMain, $features, $spec, $id);
                $updateOptions->execute();
                $updateOptions->close();
            }
            else {
                $addOptions = $mysqli->prepare("INSERT INTO `{$type}_options` (post_type_id, gallery_images, gallery_main, features, specifications) VALUES(?, ?, ?, ?, ?, ?)");
                $addOptions->bind_param('issss', $id, $gallery, $galleryMain, $features, $spec);
                $addOptions->execute();
                $addOptions->close();
            }
        }
    }

    if($type != 'pages') {
        $update = $mysqli->prepare(
            "UPDATE `{$type}` SET 
                name = ?,
                description = ?,
                url = ?,
                author = ?,
                date_posted = ?,
                content = ?,
                category_id = ?,
                image_url = ?,
                custom_content = ?
            WHERE id = ?"
        );
        $update->bind_param('ssssssissi', $title, $desc, $url, $author, $datetime, $content, $category, $image, $custom, $id);
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
                image_url = ?,
                custom_content = ?
            WHERE id = ?"
        );
        $update->bind_param('ssssssssi', $title, $desc, $url, $author, $datetime, $content, $image, $custom, $id);
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
