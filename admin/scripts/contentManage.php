<?php

    session_start();

    require_once('../../includes/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'admin/includes/classes/admin.resizeimage.class.php');
    require_once('../../includes/functions.php');

    $protocol = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://');
    $url = str_replace('page-', 'pages-', slugify($_POST['url']));
    $posted = date('Y-m-d H:i:s', strtotime($_POST['posted']));

    //Update General
    $general = $mysqli->prepare("
        UPDATE `posts` SET
            name = ?,
            url = ?,
            short_description = ?,
            content = ?,
            category_id = ?,
            author = ?,
            date_posted = ?,
            last_edited = NOW(),
            last_edited_by = ?,
            custom_content = ?
        WHERE id = ?
    ");
    $general->bind_param('ssssissisi', $_POST['name'], $url, $_POST['short'], $_POST['content'], $_POST['category'], $_POST['author'], $posted, $_SESSION['adminid'], $_POST['customFile'], $_POST['id']);
    $ex = $general->execute();

    if($ex === false) {
        echo json_encode('Error: Could not update general content');
        
        exit();
    }

    //Update Images
    $images = $mysqli->prepare("UPDATE `posts` SET gallery = NULLIF(?, 'null') WHERE id = ?");
    $main = null;
    $imageGallery = null;
    $alt = null;
    $imageNum = 0;
    
    $json = [];

    foreach($_POST['images'] as $index => $image) {
        //Check if gallery directory exists for this post id
        $galleryUrl = $_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'images/gallery/' . $_POST['id'] . '/';
        
        if(!file_exists($galleryUrl)) {
            umask(0);
            mkdir($galleryUrl, 0775, true);
        }
        
        if(strpos($image['url'], '/useruploads/') !== false) {
            $imageOri = rawurldecode(explode('/useruploads/', $image['url'])[1]);            
            $newFilename = preg_replace('/\@2x/', '', pathinfo($imageOri)['filename']);
            $extension = '.' . pathinfo($imageOri)['extension'];
            
            $retinaUrl = $galleryUrl . $newFilename . '@2x' . $extension;
            $standardUrl = $galleryUrl . $newFilename . $extension;
            
            //If uploaded image is retina create downscaled copy
            if(strpos($imageOri, '@2x') !== false) {
                copy($image['url'], $retinaUrl);
                
                $resize = new \Gumlet\ImageResize($retinaUrl);
                $resize->scale(50);
                $resize->save($standardUrl);
            }
            //If uploaded image isn't retina create upscaled copy
            else {
                copy($image['url'], $standardUrl);
                
                $resize = new \Gumlet\ImageResize($standardUrl);
                $resize->scale(200);
                $resize->save($retinaUrl);
            }
            chmod($standardUrl, 0664);
            chmod($retinaUrl, 0664);
            
            //Update image url to new gallery url
            $_POST['images'][$index]['url'] = ROOT_DIR . 'images/gallery/' . $_POST['id'] . '/' . $newFilename . $extension;
        }
        
        $imageNum++;
    }    

    $images->bind_param('si', json_encode($_POST['images']), $_POST['id']);
    $ex = $images->execute();

    if($ex === false) {
        echo json_encode('Error: Could not update images');
        
        exit();
    }

    //Update Additional Options
    if($_POST['hasOptions'] == '1') {
        //Update Specs
        $specString = null;
        $additional = $mysqli->prepare("UPDATE `posts` SET specifications = NULLIF(?, 'null') WHERE id = ?");
        $additional->bind_param('si', json_encode($_POST['specs']), $_POST['id']);
        $ex = $additional->execute();
        
        if($ex === false) {
            echo json_encode('Error: Could not additional options');
        
            exit();
        }
    }

    $mysqli->query(
        "INSERT INTO `post_history` (post_id, post_type_id, name, short_description, content, url, main_image, gallery_images, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible, custom_content) 
        SELECT id, post_type_id, NAME, short_description, content, url, gallery, specifications, category_id, author, date_posted, last_edited, last_edited_by, visible, custom_content
        FROM `posts` WHERE id = {$_POST['id']}"
    );

    echo json_encode('Content has been updated');

?>