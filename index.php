<?php 
	require_once('includes/header.php'); 
	checkContent($_GET['url']);
?>

<?php if(isset($_postUrl)) : ?>
	<?php 
        $post = $mysqli->query("
            SELECT posts.id, posts.post_type_id, posts.name, posts.content, posts.url, posts.gallery, posts.specifications, posts.author, posts.date_posted, categories.name AS category, posts.custom_content FROM `posts` AS posts 
            LEFT OUTER JOIN `categories` AS categories ON categories.id = posts.category_id
            WHERE url = '{$_postUrl}' AND visible = 1 AND posts.post_type_id = {$postDetails['id']}
        "); 

        if($post->num_rows <= 0) {
            http_response_code(404);
            include($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . '404.php');
            
            exit();
        }
        else {
            $post = $post->fetch_assoc();
            $slider = $mysqli->query("
                SELECT slider_items.id, slider_items.slider_id, sliders.post_id, slider_items.position, slider_items.image_url, slider_items.content, sliders.animation_in, sliders.animation_out, sliders.speed, sliders.visible FROM slider_items
                LEFT OUTER JOIN sliders ON sliders.id = slider_items.slider_id
                WHERE sliders.post_id = {$post['id']} AND visible = 1
            ");
        }

        if($post['id'] == $homepage && $_SERVER['REQUEST_URI'] != ROOT_DIR) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: ' . ROOT_DIR);
            
            exit();
        }
    ?>

	<?php if(!empty($post['gallery'])) : ?>
		<?php $carousel = json_decode($post['gallery'], true); ?>
		
		<div id="hero" class="carousel slide d-flex align-items-center mx-n3" data-ride="carousel">
			<div class="carousel-inner">
				<?php foreach($carousel as $index => $item) : ?>
					<div class="carousel-item h-100 <?php echo ($index == 0 ? 'active' : ''); ?>" data-interval="10000" data-item="<?php echo $index; ?>">
						<img src="<?php echo $item['imageUrl']; ?>" class="h-100 w-100" style="object-fit: cover;" alt="slide <?php echo $index; ?>">

						<div class="carousel-caption d-none d-md-block" style="<?php echo ($item['captionPosition'] == 'top' ? 'top: 0;' : ($item['captionPosition'] == 'bottom' ? 'bottom: 0;' : 'top: 50%; transform: translateY(-25%);')); ?>">
							<h5><?php echo $item['title']; ?></h5>
							<p class="mb-0"><?php echo $item['small']; ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			
			<?php if(count($carousel) > 1) : ?>
				<a class="carousel-control-prev" href="#hero" role="button" data-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>

				<a class="carousel-control-next" href="#hero" role="button" data-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="container-xl">
		<div class="content single row my-3">
			<?php if(!empty($post['content'])) : ?>
				<div class="col userContent">
					<?php echo new parseContent($post['content']); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php else : ?>
	no url
<?php endif; ?>

<?php require_once('includes/footer.php'); ?>