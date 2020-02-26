<?php require_once('includes/header.php'); ?>

<div id="mediaManager">
    <script>
        moxman.browse({
            path: "<?php echo ROOT_DIR; ?>useruploads/",
            fullscreen: true,
            leftpanel: false
        });
    </script>
    <!--<iframe src="<?php echo ROOT_DIR; ?>admin/scripts/tinymce/plugins/moxiemanager/index" style="width: 100%; height: 98%;"></iframe>-->
</div>

<?php require_once('includes/footer.php'); ?>