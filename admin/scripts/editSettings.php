<?php

    require_once('../../includes/database.php');
    
    $updateSettings = $mysqli->prepare("UPDATE `settings` SET settings_value = ? WHERE settings_name = ?");
    
    if(!array_key_exists('hide_posts', $_POST)) {
        $_POST['hide_posts'] = '0';
    }

    foreach($_POST as $index => $value) {
        $index = str_replace('_', ' ', $index);
        
        $value = ($value == 'on' ? '1' : $value);
        
        $updateSettings->bind_param('ss', $value, $index);
        $ex = $updateSettings->execute();
        
        if($ex === false) {
            $hasErrors = true;
        }
    }
        
    $message = ($hasErrors == true ? 'Some or all settings could not be updated' : 'Settings have been updated');

    $_SESSION['genmessage'] = $message;
    
    header('Location: ../general-settings');
    exit();
    
?>