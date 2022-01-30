<?php
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $fb = $_POST['facebook'];
    $tw = $_POST['twitter'];
    $yt = $_POST['youtube'];
    $ig = $_POST['instagram'];
    $li = $_POST['linkedin'];

    $update = $mysqli->prepare("UPDATE `social_links` SET link_value = ? WHERE link_name = ?");
    
    $linkName = 'Facebook';
    $update->bind_param('ss', $fb, $linkName);
    $update->execute();

    $linkName = 'Twitter';
    $update->bind_param('ss', $tw, $linkName);
    $update->execute();

    $linkName = 'Youtube';
    $update->bind_param('ss', $yt, $linkName);
    $update->execute();

    $linkName = 'Instagram';
    $update->bind_param('ss', $ig, $linkName);
    $update->execute();

    $linkName = 'LinkedIn';
    $update->bind_param('ss', $li, $linkName);
    $update->execute();

    echo json_encode('Social links have been updated.');

?>