<?php 
    
    session_start();

    unset($_SESSION['adminusername']);
    unset($_SESSION['adminid']);
    unset($_SESSION['adminlevel']);

    header('Location: ../../admin-login');

?>