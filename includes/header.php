<?php
    ob_start();

    require_once('database.php');
    require_once('functions.php');

    $classes = scandir($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'includes/classes');

    foreach($classes as $class) {
        if(strpos($class, '.class') !== false) {
            include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'includes/classes/' . $class);
        }
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <?php $meta = metaData($mysqli); ?>
        <title><?php echo $meta['title']; ?></title>        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="<?php echo $meta['author']; ?>">
        <meta name="description" content="<?php echo $meta['description']; ?>">
        
        <link href="<?php echo ROOT_DIR ?>includes/style.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300&display=swap" rel="stylesheet">
        <link href="<?php echo ROOT_DIR; ?>scripts/owlcarousel/owl.carousel.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>scripts/owlcarousel/owl.carousel.min.js"></script>
        
        <?php googleAnalytics(); ?>
    </head>
    
    <body>
        <header id="header">
            <div class="headerInner">
                <?php $logo = $mysqli->query("SELECT name, logo FROM `company_info` LIMIT 1"); ?>
                
                <?php if($logo->num_rows > 0) : ?>
                    <?php $logo = $logo->fetch_assoc(); ?>
                    <?php if($logo['logo'] != null && $logo['logo'] != '') : ?>
                        <div>
                            <a href="<?php echo ROOT_DIR; ?>"><img class="logo" src="<?php echo ucwords($logo['logo']); ?>"></a> 
                        </div>
                    <?php elseif($logo['name'] != null) : ?>
                        <div>
                            <h2 class="logo"><a href="<?php echo ROOT_DIR; ?>"><?php echo ucwords($logo['name']); ?></a></h2>     
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div>
                    <?php $contact = $mysqli->query("SELECT phone, email FROM `company_info` LIMIT 1"); ?>
                    <?php if($contact->num_rows > 0) : ?>
                        <?php $contact = $contact->fetch_assoc(); ?>
                        <div class="headerContact">
                            <p>
                                <?php if($contact['phone'] != null) : ?>
                                    <strong>Phone: </strong><span><a href="Tel: <?php echo $contact['phone']; ?>" target="_blank"><?php echo $contact['phone']; ?></a></span> 
                                <?php endif; ?>
                                <?php echo ($contact['phone'] != null && $contact['email'] != null ? ' | ' : ''); ?>
                                <?php if($contact['email'] != null) : ?>
                                <strong>Email: </strong><span><a href="MailTo: <?php echo $contact['email']; ?>" target="_blank"><?php echo $contact['email']; ?></a></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php include_once('includes/navigation.php'); ?>
        </header>
        
        <div class="main <?php echo ($_SERVER['REQUEST_URI'] == ROOT_DIR ? 'homepage' : ''); ?>">