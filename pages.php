<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/header.php'); ?>

    <?php $homepage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0]; ?>
     
        <?php if(isset($_GET['url'])) : ?>
            <?php $page = $mysqli->query("SELECT * FROM `pages` WHERE url = '{$_GET['url']}'"); ?>
            
            <?php if($page->num_rows > 0) : ?>
                <?php while($row = $page->fetch_assoc()) : ?>
                    <?php 
                        if(isset($homepage) && $row['id'] == $homepage) {
                            header('HTTP/1.1 301 Moved Permenantly');
                            header('Location: /');
                            exit();
                        } 
                    ?>
        
                    <?php if($row['visible'] == 1) : ?>
            
                        <div class="hero" style="<?php echo ($row['image_url'] != null && $row['image_url'] != '' ? 'background-image: url(\'' . $row['image_url'] . '\')' : ''); ?>">
                            <h1><?php echo $row['name']; ?></h1>
                        </div>

                        <div class="mainInner">
                            <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/templates/sidebar.php'); ?>
                            
                            <div class="content">
                                <div class="pageContent">
                                    <?php echo $row['content']; ?>
                                </div>
                            </div>
                        </div>  
                    <?php else : ?>
                        <?php 
                            http_response_code(404); 
                            header("Location: /404"); 
                        ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else : ?>
                <?php 
                    http_response_code(404); 
                    header("Location: /404"); 
                ?>
            <?php endif; ?>
        <?php else : ?>
            <?php header('Location: /'); ?>
        <?php endif; ?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/footer.php'); ?>