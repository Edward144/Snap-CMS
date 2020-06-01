<?php
    
    //Add query with ? if no other queries
    if(strpos($_POST['returnUrl'], '?') === false) {
        header("Location: " . $_POST['returnUrl'] . '?search=' . str_replace('%', '%25', $_POST['searchTerm']));
    }
    //Add query with & if other queries
    else {
        header("Location: " . explode('?', $_POST['returnUrl'])[0] . '?search=' . str_replace('%', '%25', $_POST['searchTerm']));
    }

?>