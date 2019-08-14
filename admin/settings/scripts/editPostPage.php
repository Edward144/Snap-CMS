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

    if($mysqli->query("SHOW TABLES LIKE '{$type}_options'")->num_rows > 0) {
        if($type != 'posts' && $type != 'pages') {
            $galleryExist = $_POST['galleryExist'];
            $galleryNew = $_POST['galleryNew'];
            $galleryMain = $_POST['galleryMain'];
            $features = $_POST['features'];
            $output = $_POST['output'];
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
                $updateOptions = $mysqli->prepare("UPDATE `{$type}_options` SET gallery_images = ?, gallery_main = ?, features = ?, specifications = ?, output = ? WHERE post_type_id = ?");
                $updateOptions->bind_param('sssssi', $gallery, $galleryMain, $features, $spec, $output, $id);
                $updateOptions->execute();
                $updateOptions->close();
            }
            else {
                $addOptions = $mysqli->prepare("INSERT INTO `{$type}_options` (post_type_id, gallery_images, gallery_main, features, specifications, output) VALUES(?, ?, ?, ?, ?, ?, ?)");
                $addOptions->bind_param('isssss', $id, $gallery, $galleryMain, $features, $spec, $output);
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

    if($mysqli->query("SHOW TABLES LIKE '{$type}_additional'")->num_rows > 0) {
        $update = $mysqli->prepare("UPDATE `{$type}_additional` SET author = ? WHERE post_type_id = ?");
        $update->bind_param('si', $author, $id);
        $update->execute();
        $update->close();
    }

    if(!$mysqli->error) {
        echo json_encode([1, ucfirst($_POST['type']) . ' has been updated.']);
    }
    else {
        echo json_encode([0, 'Error: Could not update ' . $_POST['type'] . '.']);
    }

?>
