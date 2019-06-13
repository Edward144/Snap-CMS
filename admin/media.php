<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <h1><?php adminTItle(); ?></h1>
        
        <div class="formBlock">
            <form id="mediaUpload">                
                <p>
                    <label>Upload File: </label>
                    <input type="file" name="mediaFile">
                    <input type="submit" value="Upload">
                </p>
                
                <p class="message"></p>
            </form>
        </div>
        
        <?php 
            if(isset($_GET['f'])) {
                new mediaTree($_GET['f']); 
            }
            else {
                new mediaTree();
            }
        ?>
    </div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>