<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <div class="mainInner">
        <div class="content">
            <h1>Categories</h1>

            <?php
                $categories = new categories();

                if(isset($_GET['c'])) {
                    $categories->listCategories($_GET['c']);
                }
                else {
                    $categories->listCategories();
                }

            ?>
        </div>
    </div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>