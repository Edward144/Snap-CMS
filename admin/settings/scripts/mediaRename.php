<?php  
    
    function slugify($url) {
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }
    
    $newName = slugify($_POST['newName']);
    $existingUrl = $_POST['existingUrl'];
    $currUrl = $_POST['currUrl'];

    if(strpos($existingUrl, '.') !== false) {
        $newName = $newName . '.' . explode('.', $existingUrl)[1];
    }

    $newUrl = $currUrl . '/' . $newName;

    $oldUrl = $_SERVER['DOCUMENT_ROOT'] . '/admin/' . $existingUrl;
    $newUrl = $_SERVER['DOCUMENT_ROOT'] . '/admin/' . $currUrl . '/' . $newName;

    if(rename($oldUrl, $newUrl)) {
        echo json_encode(1);
    }
    else {
        echo json_encode('Error: Colud not rename.');
    }

?>