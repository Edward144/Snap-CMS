<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <h1><?php adminTItle(); ?></h1>
        
        <iframe src="/admin/tinymce/plugins/moxiemanager/index" style="width: 100%; height: 100%;"></iframe>
    </div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>