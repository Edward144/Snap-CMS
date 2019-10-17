<?php

    header('Location: ' . explode('&', $_POST['returnUrl'])[0] . '&search=' . str_replace('%', '%25', $_POST['searchTerm']));
    exit();

?>