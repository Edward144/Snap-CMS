<?php

    //Admin Functions

    function adminBreadcrumbs() {
        $breadcrumbs = '';
        $levels = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $levelsCount = count($levels) - 1;

        foreach ($levels as $level) {
            if($level != trim(ROOT_DIR, '/') && $level != 'admin') {
                $breadName = ucwords(explode('?', explode('.php', str_replace('-', ' ', $level))[0])[0]);

                $breadcrumbs .= ' <span>' . $breadName . '</span> >';
            }
            elseif($level == 'admin') {
                $breadcrumbs .= ' <span>Admin Dashboard</span> >';
            }
        }

        return $breadcrumbs = trim($breadcrumbs, '>');
    }

    function adminTitle() {
        $levels = explode('?', $_SERVER['REQUEST_URI'])[0];
        $levels = explode('/', trim($levels, '/'));
        $levelsCount = count($levels) - 1;
        
        if(strpos($levels[$levelsCount], 'page-') !== false ||
           strpos($levels[$levelsCount], 'category-') !== false ||
           strpos($levels[$levelsCount], 'id-') !== false ||
           strpos($levels[$levelsCount - 1], 'categories') !== false ||
           strpos($levels[$levelsCount - 1], 'navigation') !== false) {
            $levelsCount = $levelsCount - 1;
        }
        
        $level = $levels[$levelsCount];
        
        if($level != trim(ROOT_DIR, '/') && $level != 'admin') {
            $title = ucwords(explode('&', explode('?', explode('.php', str_replace('-', ' ', $level))[0])[0])[0]);
        }
        elseif($level == 'admin') {
            $title = 'Dashboard';
        }
        
        return $title;
    }

    function cmsName($mysqli) {
        $companyName = $mysqli->query("SELECT name FROM `company_info` LIMIT 1");
        
        if($companyName->num_rows > 0) {
            $companyName = $companyName->fetch_array()[0];
            
            if($companyName != null) {
                $cmsName = ucwords($companyName) . ' CMS';
            }
            else {
                $cmsName = 'Snap CMS';
            }
        }
        else {
            $cmsName = 'Snap CMS';
        }
        
        return $cmsName;
    }

    function checkAccess($userlevel, $requiredLevel = 0) {
        global $mysqli;
        
        if($userlevel != $requiredLevel) {
            $admin = $mysqli->query("SELECT * FROM `users` WHERE access_level = 0 ORDER BY id ASC LIMIT 1")->fetch_assoc();
            
            echo 
                '<h1 style="color: red">Your current access level does not permit you to view this page.</h1>
                <h2>If you feel that this is incorrect please contact your administrator. <a href="mailto: ' . $admin['email'] . '">' . $admin['email'] .'</a></h2>
                <h3><a href="' . ROOT_DIR . 'admin">Return to the dashboard</a></h3>';
            
            include_once('../admin/includes/footer.php');
            
            exit();
        }
    }

    //Frontend Functions

    //Meta Data
    function metaData() {
        global $mysqli;

        $companyName = $mysqli->query("SELECT name FROM `company_info` WHERE name IS NOT NULL AND name <> ''");
        $companyName = ucwords(($companyName->num_rows == 1 ? $companyName->fetch_array()[0] : ''));

        if($_GET['url']) {
            //UGC Page
            $url = explode('/', $_GET['url']);

            if(count($url) >= 2) {
                //Single Page
                $postType = $url[0];
                $postName = '%' . $url[count($url) - 1];

                $data = $mysqli->prepare("
                    SELECT post_types.name AS post_type, posts.meta_title, posts.meta_description, posts.meta_keywords, posts.meta_author, posts.name, posts.short_description, posts.author FROM `posts` 
                        LEFT OUTER JOIN post_types ON posts.post_type_id = post_types.id
                    WHERE posts.url LIKE ?
                ");
                $data->bind_param('s', $postName);
                $data->execute();
                $meta = $data->get_result()->fetch_assoc();
            }
            else {                        
                //Check if Single or List
                $singleCheck = $mysqli->prepare("SELECT meta_title, meta_description, meta_keywords, meta_author, name, short_description, author FROM `posts` WHERE url = ?");
                $singleCheck->bind_param('s', $url[0]);
                $singleCheck->execute();
                $singleResults = $singleCheck->get_result();

                $listCheck = $mysqli->prepare("SELECT meta_title, meta_description, meta_keywords, meta_author, name FROM `post_types` WHERE name = ?");
                $listCheck->bind_param('s', $url[0]);
                $listCheck->execute();
                $listResults = $listCheck->get_result();

                if($singleResults->num_rows > 0 && $listResults->num_rows <= 0) {
                    //Single
                    $meta = $singleResults->fetch_assoc();
                }
                elseif($listResults->num_rows > 0 && $singleResults->num_rows <= 0) {
                    //List
                    $meta = $listResults->fetch_assoc();
                }
            }                    
        }
        elseif($_SERVER['REQUEST_URI'] == ROOT_DIR) {
            //Index Page
            $homepage = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'homepage'")->fetch_array()[0];
            $data = $mysqli->prepare("SELECT meta_title, meta_description, meta_keywords, meta_author, name, short_description, author FROM `posts` WHERE id = ?");
            $data->bind_param('i', $homepage);
            $data->execute();
            $meta = $data->get_result()->fetch_assoc();
        }
        else {
            $meta = [
                'meta_title' => $companyName,
                'meta_author' => $companyName,
                'meta_description' => '',
                'meta_keywords' => ''
            ];
        }
        
        $metaTitle = ($meta['meta_title'] != null && $meta['meta_title'] != '' ? $meta['meta_title'] : $meta['name']);
        
        $title = ucwords(($metaTitle != null && $metaTitle != '' ? $metaTitle : '') . 
                 ($metaTitle != null && $metaTitle != '' && $companyName != null && $companyName != '' ? ' | ' : '') .
                 ($companyName != null && $companyName != '' ? $companyName : ''));
        $description = ($meta['meta_description'] != null && $meta['meta_description'] != '' ? $meta['meta_description'] : (isset($meta['short_description']) ? $meta['short_description'] : ''));
        $keywords = ($meta['meta_keywords'] != null && $meta['meta_keywords'] != '' ? $meta['meta_keywords'] : '');
        $author = ucwords(($meta['meta_author'] != null && $meta['meta_author'] != '' ? $meta['meta_author'] : (isset($meta['author']) ? $meta['author'] : '')));

        return 
            (strlen($title) > 0 ? '<title>' . $title . '</title>' : '') .
            (strlen($description) > 0 ? '<meta name="description" content="' . $description . '">' : '') .
            (strlen($keywords) > 0 ? '<meta name="keywords" content="' . $keywords . '">' : '') .
            (strlen($author) > 0 ? '<meta name="author" content="' . $author . '">' : '');
    }

    //Remove Spaces
    function despace($string) {
        $string = preg_replace('/\s+/', '', $string);
        
        return $string;
    }

    //Remove Special Characters & Spaces
    function cleanstring($string) {
        $string = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $string));
        
        return $string;
    }

    //Convert String to URL Slug
    function slugify($url) {
        $url = preg_replace('~[^\pL\d\/\.]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w\/\.]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    function slugifyslash($url) {
        $url = preg_replace('~[^\pL\d\/]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w\/]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    //Convert Bytes to Readable Format
    function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        }
        else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    //Check That URL Exists
    function checkContent($url) {   
        global $mysqli;
        global $_postType;
        global $_postUrl;
        global $postDetails;
        
        //Get Homepage and Check If Posts Are Hidden
        $homepage = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'homepage'")->fetch_array()[0]; 
        $hidePosts = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'hide posts'")->fetch_array()[0]; 
        
        //Get Available Post Types
        $postTypes = $mysqli->query("SELECT name FROM `post_types`");
        $postList = [];
        
        while($type = $postTypes->fetch_array()) {
            array_push($postList, $type[0]);
        }

        if(isset($url) && strlen($url) > 0) {
            $customUrl = explode('/', $url);
            
            if(in_array($customUrl[0], $postList)) {
                $_postType = $customUrl[0];

                if(isset($customUrl[1])) {
                    unset($customUrl[0]);
                    $_postUrl = implode('/', $customUrl);
                }
            }
            else {
                $_postType = 'pages';
                $_postUrl = implode('/', $customUrl);
            }
        }
        else {
            $_postType = 'pages';
            $_postUrl = $mysqli->query("SELECT url FROM `posts` WHERE id = {$homepage}")->fetch_array()[0];
        }
        
        //Go to posts if no type set
        if(!isset($_postType)) {
            http_response_code(404);
            include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');

            exit();
        }
        
        //404 If Trying To Access Hidden Posts
        if(($hidePosts == 1 && $_postType == 'posts') || ($_postType == 'pages' && !isset($_postUrl))) {
            http_response_code(404);
            include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');

            exit();
        }

        $postDetails = $mysqli->query("SELECT * FROM `post_types` WHERE name = '{$_postType}'");
        
        //Go to posts if type does not exist
        if($postDetails->num_rows <= 0) {
            http_response_code(404);
            include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');

            exit();
        }
        else {
            $postDetails = $postDetails->fetch_assoc();
        }
    }

    //Insert Google Analytics Tracking Code
    function googleAnalytics() {
        global $mysqli;
        
        $googleAnalytics = $mysqli->query("SELECT settings_value FROM `settings` WHERE settings_name = 'google analytics' AND settings_value <> '' AND settings_value IS NOT NULL LIMIT 1");
        
        if($googleAnalytics->num_rows == 1) {
            $trackingCode = $googleAnalytics->fetch_array()[0];
            
            echo '<!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=' . $trackingCode . '"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag("js", new Date());
                gtag("config", "' . $trackingCode . '");
            </script>';
        }
    }

?>