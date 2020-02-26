<?php

    //Breadrumbs Admin
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

    //Meta Data
    function metaData($mysqli) {
        $companyName = $mysqli->query("SELECT name FROM `company_info` LIMIT 1");
        $meta = [];
        
        if($companyName->num_rows > 0) {
            $companyName = $companyName->fetch_array()[0];
            
            if($companyName != null) {
                $metaCompany = ucwords($companyName);
            }
            else {
                $metaCompany = 'Snap CMS';
            }
        }
        else {
            $metaCompany = 'Snap CMS';
        }
        
        if(isset($_GET['url'])) {
            $post = $mysqli->query("SELECT name, short_description, author FROM `posts` WHERE url = '{$_GET['url']}'");
            
            if($post->num_rows > 0) {
                $post = $post->fetch_assoc();
                
                $metaTitle = $post['name'];
                $metaDesc = $post['short_description'];
                $metaAuthor = $post['author'];
            }
        }
        elseif(isset($_GET['post-type'])) {
            $metaTitle = ucwords(str_replace('-', ' ', $_GET['post-type']));
            $metaDesc = ucwords(str_replace('-', ' ', $_GET['post-type']));
            $metaAuthor = $metaCompany;
        }
        elseif($_SERVER['REQUEST_URI'] == ROOT_DIR) {
            $metaTitle = 'Home';
            $metaDesc = 'Welcome to ' . $metaCompany;
            $metaAuthor = $metaCompany;
        }
        
        if(strlen($metaTitle) > 0) {
            $metaTitle = $metaTitle . ' | ';
        }
        
        $meta['title'] = $metaTitle . ' ' . $metaCompany;
        $meta['description'] = $metaDesc;
        $meta['author'] = $metaAuthor;
        
        return $meta;
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
                gtag("config", ' . $trackingCode . ');
            </script>';
        }
    }

?>