<?php

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

?>