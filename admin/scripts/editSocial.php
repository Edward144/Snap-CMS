<?php

    require_once('../../includes/database.php');
    require_once('../../includes/functions.php');
    
    foreach($_POST as $index => $link) {
        $link = despace($link);
        $_SESSION[$index] = $link;
        
        $oLink = $mysqli->prepare("SELECT url FROM `social_links` WHERE name = ?");
        $oLink->bind_param('s', $index);
        $oLink->execute();
        $oLink = $oLink->get_result();
        
        $updateLink = $mysqli->prepare("UPDATE `social_links` SET url = ? WHERE name = ?");
        $updateLink->bind_param('ss', $link, $index);
        $ex = $updateLink->execute();
        
        if($ex === false) {
            $message .= 'Error: Could not update ' . $index . '<br>';
            header('Location: ../company-details');
            
            exit();
        }
        
        
        if($oLink->num_rows > 0) {
            $oLink = $oLink->fetch_array()[0];
            
            if($oLink != $link) {
                $message .= 'Updated ' . ucwords(str_replace('-', ' ', $index)) . '<br>';
            }
        }
        
        $_SESSION['socialmessage'] = $message;
        header('Location: ../company-details');
        
        exit();
    }

?>