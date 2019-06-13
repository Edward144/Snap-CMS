<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content" style="overflow-x: auto;">
        <h1><?php adminTitle(); ?></h1>

        <div class="formBlock">
            <form id="navLayout" style="max-width: 100%;">                
                <?php new navigationTree(); ?>
            </form>
        </div>
    </div>

    <script src="settings/scripts/updateNav.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>