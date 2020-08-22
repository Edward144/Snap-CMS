<?php require_once('includes/header.php'); ?>

<div class="container-xl">
	<div class="content 404 single row py-3">
		<div class="col">
			<h1>This page does not exist!</h1>
			<p>You can <a href="<?php echo ROOT_DIR; ?>">return to the homepage</a> or <a href="#" onClick="javascript:history.go(-1)">go back to the last page you were on</a></p>
			
			<h2 class="text-muted bg404">404!</h2>
		</div>
	</div>
</div>

<?php require_once('includes/footer.php'); ?>