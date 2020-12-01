<?php

	require_once(__DIR__ . '/settings.php');

    function companyName() {
        global $mysqli; 
        
        $companyName = $mysqli->query("SELECT name FROM `company_info` WHERE name IS NOT NULL AND name <> '' LIMIT 1");
        
        if($companyName->num_rows == 1) {
            return $companyName->fetch_array()[0];
        }
        else {
            return 'Snap CMS';
        }
    }

	function companyDetails() {
		global $mysqli; 
        
        $companyDetails = $mysqli->query("SELECT * FROM `company_info` LIMIT 1");
		
		if($companyDetails->num_rows == 1) {
            return $companyDetails->fetch_assoc();
        }
	}

	function baseDir($url) {
		if(isset($url)) {
			return $url;
		}
		else {
			return (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ROOT_DIR;
		}
	}
	
	function slugify($url) {
        return preg_replace('/[^a-zA-Z0-9\:\/\-\?\=\#\.]/', '', $url);
    }

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

	function checkContent($url) {   
        global $mysqli;
        global $_postType;
        global $_postUrl;
        global $postDetails;
		global $homepage;
        
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

	function metaData($metaTitle, $metaDesc, $metaKeywords, $metaAuthor) {
		$metaData = '';
		$metaData .= (!empty($metaTitle) ? '<title>' . $metaTitle . '</title>' : '');
		$metaData .= (!empty($metaTitle) ? '<meta name="description" content="' . $metaDesc . '">' : '');
		$metaData .= (!empty($metaTitle) ? '<meta name="keywords" content="' . $metaKeywords . '">' : '');
		$metaData .= (!empty($metaTitle) ? '<meta name="author" content="' . $metaAuthor . '">' : '');
		
		return $metaData;
	}

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

	$classes = scandir($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'includes/classes');

    foreach($classes as $class) {
        if(strpos($class, '.class') !== false) {
            include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'includes/classes/' . $class);
        }
    }

?>