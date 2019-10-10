<?php 

    session_start();
    
    unset($_SESSION['adminusername']);
    unset($_SESSION['adminid']);
    
    header('Location: ../../login');
    
?>