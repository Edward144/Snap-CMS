<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content" style="overflow-x: auto;">
        <h1><?php adminTitle(); ?></h1>
        
        <div class="formBlock">
            <form id="catLayout" style="max-width: 100%;">
                <?php new categoryTree(); ?>
            </form>
        </div>
    </div>

    <script src="/admin/settings/scripts/updateCategories.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>