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
        $levels = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $levelsCount = count($levels) - 1;
        
        if(strpos($levels[$levelsCount], 'page-') !== false ||
           strpos($levels[$levelsCount], 'category-') !== false ||
           strpos($levels[$levelsCount], 'id-') !== false) {
            $levelsCount = $levelsCount - 1;
        }
        
        $level = $levels[$levelsCount];
        
        if($level != trim(ROOT_DIR, '/') && $level != 'admin') {
            $title = ucwords(explode('?', explode('.php', str_replace('-', ' ', $level))[0])[0]);
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
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
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

?>