<?php 
    require_once('includes/database.php');
    require_once('includes/functions.php'); 

	$classes = scandir($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'includes/classes');

    foreach($classes as $class) {
        if(strpos($class, '.class') !== false) {
            include_once($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'includes/classes/' . $class);
        }
    }

	$companyDetails = companyDetails();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        
        <link rel="stylesheet" href="<?php echo ROOT_DIR; ?>css/style.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css" integrity="sha384-xxzQGERXS00kBmZW/6qxqJPyxW3UR0BPsL4c8ILaIWXva5kFi7TxkIIaMiKtqV1Q" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="<?php echo ROOT_DIR; ?>js/bootstrap.min.js"></script>
        <script src="<?php echo ROOT_DIR; ?>js/docRoot.min.js"></script>
    </head>

    <body>
        <div class="wrapper">
			<header id="header" class="container-fluid bg-primary">
				<div class="headerInner container-xl">
					<div class="row py-3">
						<div class="col-sm-4">
							<?php if(!empty($companyDetails['logo'])) : ?>
								<img src="<?php echo $companyDetails['logo']; ?>" class="img-fluid siteLogo siteTitle" alt="<?php echo $companyDetails['name'] . ' logo';?>">
							<?php elseif(!empty($companyDetails['name'])) : ?>
								<h2 class="siteLogo siteTitle"><?php echo $companyDetails['name']; ?></h2>
							<?php endif; ?>
						</div>

						<div class="col">
							<?php if(!empty($companyDetails['phone']) || !empty($companyDetails['email'])) : ?>
								<div class="contact text-right">
									<?php 
										echo (!empty($companyDetails['phone']) ? '<span class="phone text-light"><span class="fa fa-phone mr-1"></span><a class="text-light" href="tel: ' . $companyDetails['phone'] . '">' . $companyDetails['phone'] . '</a></span>' : '');
										echo (!empty($companyDetails['email']) ? '<span class="email text-light"><span class="fa fa-envelope mr-1"></span><a class="text-light" href="mailto: ' . $companyDetails['email'] . '">' . $companyDetails['phone'] . '</a></span>' : ''); 
									?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				
				<div class="row bg-dark">
					<div class="container-xl">
						<div class="row">
							<div class="col">
								<?php $navbar = new navbar(); $navbar->display(); ?>
							</div>
						</div>
					</div>
				</div>
			</header>