<!DOCTYPE html>

<?php
    ini_set('display_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE); 
?>

<?php 
    session_start(); 

    include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/functions.php');
?>

<html>
    <head>
        <link href="/admin/templates/admin-style.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto&display=swap" rel="stylesheet">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>        
        <script src="/admin/tinymce/tinymce.min.js"></script>
        
        <script>
            tinymce.init({
                selector:'textarea',
                plugins: 'paste image table code save link moxiemanager',
                menubar: 'file edit format insert table ',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link image | code',
                relative_urls: false,
                remove_script_host: false,
                image_title: true,
                height: 400
                /*automatic_uploads: true,
                image_upload_url: '/admin/settings/scripts/imageUploader.php',
                file_picker_types: 'image',
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    
                    input.onchange = function() {
                        var file = this.files[0];
                        var reader = new FileReader();
                        
                        reader.onload = function() {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            
                            blobCache.add(blobInfo);
                            
                            cb(blobInfo.blobUri(), {title: file.name});
                        }
                        
                        reader.readAsDataURL(file);
                    }
                    
                    input.click();
                }*/
            });
        </script>
    </head>
    
    <body>
        <div class="header">
            <div class="headerInner">
                <div class="sidebarToggle" id="hidden"></div>
                
                <div class="left">
                    <?php 
                        $logo = $mysqli->query("SELECT logo FROM `company_info`")->fetch_array()[0];
                    ?>
                        
                    <?php if($logo) : ?>
                        <a href="/admin/dashboard"><img class="logo" src="<?php echo $logo; ?>" alt="Logo"></a>
                    <?php else : ?>
                        <a href="/admin/dashboard"><img class="logo" src="/admin/images/default-logo.png" alt="Logo"></a>
                    <?php endif; ?>
                </div>
                
                <div class="right">
                    <?php
                        $breadcrumbs = ltrim($_SERVER['REQUEST_URI'], '/');
                        $breadcrumbs = str_replace('/', ' > ', $breadcrumbs);
                        $breadcrumbs = str_replace('_', ' ', $breadcrumbs);
                        $breadcrumbs = explode('?', $breadcrumbs)[0];
                        $breadcrumbs = ucwords($breadcrumbs);
                    
                        $companyName = $mysqli->query("SELECT company_name FROM `company_info`");
                    
                        if($companyName->num_rows > 0) {
                            $companyName = ucwords($mysqli->query("SELECT company_name FROM `company_info`")->fetch_array()[0]);
                            
                            if($companyName == '') {
                                $companyName = 'CMS';
                            }
                            
                            $breadcrumbs = $companyName . ': ' . $breadcrumbs;
                        }
                    ?>
                    
                    <h2 class="breadcrumbs"><?php echo $breadcrumbs; ?></h2>
                </div>
                
            </div>
        </div>
        
        <div class="main">