<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    function slugify($url) {
        $url = preg_replace('~[^\pL\d]+~u', '-', $url);
        $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
        $url = preg_replace('~[^-\w]+~', '', $url);
        $url = trim($url, '-');
        $url = preg_replace('~-+~', '-', $url);
        $url = strtolower($url);
        
        return $url;
    }

    $name = slugify(strtolower($_POST['name']));
    $name = str_replace('-', '_', $name);

    $checkExisting = $mysqli->prepare("SELECT COUNT(*) FROM `custom_posts` WHERE name = ?");
    $checkExisting->bind_param('s', $name);
    $checkExisting->execute();
    $checkResult = $checkExisting->get_result();

    if($checkResult->fetch_array()[0] > 0) {
        echo json_encode($name . ' skipped, already Exists.<br>');
    }
    else {
        //Insert Into Custom Posts Table
        $addCustom = $mysqli->prepare("INSERT IGNORE INTO `custom_posts` (name) VALUES(?)");
        $addCustom->bind_param('s', $name);
        $addCustom->execute();
        
        //Create Post Type Table
        $mysqli->query("CREATE TABLE `{$name}s` LIKE `posts`");
        
        //Create Post Type Categories
        $mysqli->query("CREATE TABLE `{$name}s_categories` LIKE `categories`");
        
        //Create Post Type Options
        $mysqli->query(
            "CREATE TABLE IF NOT EXISTS `{$name}s_options` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_type_id INT UNIQUE,
                gallery_images TEXT DEFAULT NULL,
                main_gallery VACHAR(255) DEFAULT NULL,
                features VARCHAR(255) DEFAULT NULL,
                specifications TEXT DEFAULT NULL,
                output VARCHAR(255) DEFAULT NULL,
                options VARCHAR(255) DEFAULT NULL
            )"
        );
        
        //Add Links To Admin Sidebar        
        if($mysqli->query("SELECT COUNT(*) FROM `admin_sidebar` WHERE name = '{$name}s'")->fetch_array()[0] == 0) {
            $mysqli->query("INSERT IGNORE INTO `admin_sidebar` (name, type, link) VALUES('{$name}s', 0, 'post-type/{$name}s')");
        }
        
        if($mysqli->query("SELECT COUNT(*) FROM `admin_sidebar` WHERE name = '{$name}s_categories'")->fetch_array()[0] == 0) {
            $mysqli->query("INSERT IGNORE INTO `admin_sidebar` (name, type, link) VALUES('{$name}s_categories', 0, 'categories/{$name}s')");
        }
        
        //Create Admin File
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/custom_' . $name . 's.php')) { 
            $adminFile = 
                '<?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/admin/templates/header.php\'); ?>

                <?php include_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/admin/templates/sidebar.php\'); ?>

                <div class="content">
                    <?php 
                        $posts = new postAdmin(\'' . $name . '\');
                        $posts->getPost();
                    ?>
                </div>

                <script src="/admin/settings/scripts/postPage.js"></script>

            <?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/admin/templates/footer.php\'); ?>';

            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/admin/custom_' . $name . 's.php', 'w');
            
            chmod($_SERVER['DOCUMENT_ROOT'] . '/admin/custom_' . $name . 's.php', 0775);
            
            fwrite($file, $adminFile);
            fclose($file);
        }
        
        //Create Admin Category File
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/custom_' . $name . 's_categories.php')) { 
            $adminFile = 
                '<?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/admin/templates/header.php\'); ?>
    
                <?php include_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/admin/templates/sidebar.php\'); ?>

                <div class="content" style="overflow-x: auto;">
                    <h1 id="postType"><?php adminTitle(); ?></h1>

                    <div class="formBlock">
                        <form id="catLayout" style="max-width: 100%;">
                            <?php new categoryTree(0, 0, 0, \'' . $name . 's\'); ?>
                        </form>
                    </div>
                </div>

                <script src="/admin/settings/scripts/updateCategories.js"></script>

            <?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/admin/templates/footer.php\'); ?>';

            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/admin/custom_' . $name . 's_categories.php', 'w');
            
            chmod($_SERVER['DOCUMENT_ROOT'] . '/admin/custom_' . $name . 's_categories.php', 0775);
            
            fwrite($file, $adminFile);
            fclose($file);
        }
        
        //Create Front End File
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/custom_' . $name . 's.php')) { 
            $frontFile = 
                '<?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/templates/header.php\'); ?>
                    
                    <?php 
                        $posts = new postUser(\'' . $name . '\');
                        $posts->getPost();
                    ?>

            <?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/templates/footer.php\'); ?>';

            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/custom_' . $name . 's.php', 'w');
            
            chmod($_SERVER['DOCUMENT_ROOT'] . '/custom_' . $name . 's.php', 0775);
            
            fwrite($file, $frontFile);
            fclose($file);
        }
        
        //Create Front End Category File
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/custom_' . $name . 's_categories.php')) { 
            $frontFile = 
                '<?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/templates/header.php\'); ?>
                    
                    <div class="mainInner">
                        <div class="content">
                            <h1>' . ucwords($name) .'s Categories</h1>

                            <?php
                                $categories = new categories();

                                if(isset($_GET[\'c\'])) {
                                    $categories->listCategories($_GET[\'c\'], \'' . $name . 's\');
                                }
                                else {
                                    $categories->listCategories(0, \'' . $name . 's\');
                                }

                            ?>
                        </div>
                    </div>

                <?php require_once($_SERVER[\'DOCUMENT_ROOT\'] . \'/templates/footer.php\'); ?>';

            $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/custom_' . $name . 's_categories.php', 'w');
            
            chmod($_SERVER['DOCUMENT_ROOT'] . '/custom_' . $name . 's_categories.php', 0775);
            
            fwrite($file, $frontFile);
            fclose($file);
        }
        
        //Echo Output
        echo json_encode($name . ' has been added.<br>');
    }
    
?>