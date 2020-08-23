<?php 
	require_once('includes/database.php'); 
	require_once('includes/functions.php'); 
	checkContent($_GET['url']);
?>

<?php if(isset($_postUrl)) : ?>
	<?php 
        $post = $mysqli->query("
            SELECT posts.id, posts.post_type_id, posts.name, posts.content, posts.url, posts.gallery, posts.specifications, posts.author, posts.date_posted, categories.name AS category, posts.custom_content, posts.meta_title, posts.meta_description, posts.meta_keywords, posts.meta_author FROM `posts` AS posts 
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
            /*$slider = $mysqli->query("
                SELECT slider_items.id, slider_items.slider_id, sliders.post_id, slider_items.position, slider_items.image_url, slider_items.content, sliders.animation_in, sliders.animation_out, sliders.speed, sliders.visible FROM slider_items
                LEFT OUTER JOIN sliders ON sliders.id = slider_items.slider_id
                WHERE sliders.post_id = {$post['id']} AND visible = 1
            ");*/
        }

        if($post['id'] == $homepage && $_SERVER['REQUEST_URI'] != ROOT_DIR) {
            header("HTTP/1.1 301 Moved Permanently");
            header('Location: ' . ROOT_DIR);
            
            exit();
        }
    ?>

	<?php 
		$metaTitle = (!empty($post['meta_title']) ? $post['meta_title'] : $post['name']);
		$metaDesc = (!empty($post['meta_description']) ? $post['meta_description'] : '');
		$metaKeywords = (!empty($post['meta_keywords']) ? $post['meta_keywords'] : '');
		$metaAuthor = (!empty($post['meta_author']) ? $post['meta_author'] : $post['author']);

		require_once('includes/header.php'); 
	?>

	<?php if(!empty($post['gallery'])) : ?>
		<?php $carousel = json_decode($post['gallery'], true); ?>
		
		<?php if(!empty($carousel)) : ?>
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
	<?php endif; ?>

	<div class="container-xl<?php echo ($post['id'] == $homepage ? ' homepage' : ''); ?>">
		<div class="content single row my-3">			
			<div class="col">
				<?php echo (!empty($post['name']) ? '<h1 class="pageTitle">' . $post['name'] . '</h1>' : ''); ?>

				<?php if(!empty($post['content'])) : ?>
					<div class="userContent">
						<?php echo new parseContent($post['content']); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php else : ?>
	<?php 
        $postCount = $mysqli->query("
            SELECT posts.id, post_types.name AS post_type, post_types.meta_title, post_types.meta_description, post_types.meta_keywords, post_types.meta_keywords FROM `posts` AS posts 
                LEFT OUTER JOIN `post_types` AS post_types ON post_types.id = posts.post_type_id
            WHERE post_types.name = '{$_postType}' AND visible = 1
        ")->num_rows;
        $pagination = new pagination($postCount); 
        $pagination->load();

        if(isset($_GET['category']) && is_numeric($_GET['category'])) {
            $getCat = (isset($_GET['category']) ? 'AND category_id = ' . $_GET['category'] : ''); 
        }
        else {
            $getCat = (isset($_GET['category']) ? 'AND categories.name = "' . urldecode($_GET['category']) . '"' : ''); 
        }
        
        $posts = $mysqli->query("
            SELECT posts.id, posts.name, posts.content, posts.url, posts.gallery, posts.author, posts.date_posted, posts.short_description, posts.category_id, categories.name AS category, post_types.name AS post_type, posts.custom_content FROM `posts` AS posts 
                LEFT OUTER JOIN `categories` AS categories ON categories.id = posts.category_id
                LEFT OUTER JOIN `post_types` AS post_types ON post_types.id = posts.post_type_id
            WHERE visible = 1 AND post_types.name = '{$_postType}' {$getCat} ORDER BY date_posted DESC
            LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}
        "); 
    ?>

	<?php 
		$metaTitle = (!empty($postDetails['meta_title']) ? $postDetails['meta_title'] : ucwords(str_replace(['-', '_'], ' ', $postDetails['name'])));
		$metaDesc = (!empty($postDetails['meta_description']) ? $postDetails['meta_description'] : '');
		$metaKeywords = (!empty($postDetails['meta_keywords']) ? $postDetails['meta_keywords'] : '');
		$metaAuthor = (!empty($postDetails['meta_author']) ? $postDetails['meta_author'] : '');

		require_once('includes/header.php'); 
	?>

	<?php if(!empty($postDetails['image_url'])) : ?>
		<div id="hero" class="carousel mx-n3">
			<div class="carousel-inner">
				<img src="<?php echo $postDetails['image_url']; ?>" class="h-100 w-100" style="object-fit: cover;" alt="<?php echo (!empty($postDetails['title']) ? $postDetails['title'] : $postDetails['name']); ?> Hero">
			</div>
		</div>
	<?php endif; ?>

	<div class="container-xl">
		<div class="content list row my-3">
			<div class="col">				
				<?php echo (!empty($postDetails['title']) ? '<h1>' . $postDetails['title'] . '</h1>' : (!empty($postDetails['name']) ? '<h1>' . ucwords(str_replace('-', ' ', $postDetails['name'])) . '</h1>' : '')); ?>
				
				<?php if(!empty($postDetails['content'])) : ?>
					<div class="userContent">
						<?php echo new parseContent($postDetails['content']); ?>
					</div>
				<?php endif; ?>
				
				<?php if($posts->num_rows > 0) : ?>
					<div class="<?php echo $postDetails['name'] . 'List'; ?>">
						<?php while($post = $posts->fetch_assoc()) : ?>
							<div class="jumbotron py-3 <?php echo $postDetails['name'] . 'Item'; ?>">
								<h2 class="title"><a href="<?php echo ROOT_DIR . $postDetails['name'] . '/' . $post['url']; ?>"><?php echo $post['name']; ?></a></h2>
								
								<?php 
									echo (!empty($post['author']) ? '<h6 class="author d-inline-block mr-1"><strong>Author: </strong>' . $post['author'] . '</h6>' : ''); 
									echo (!empty($post['date_posted']) ? '<h6 class="posted d-inline-block mr-1"><strong>Posted: </strong>' . date('d/m/Y H:i', strtotime($post['date_posted'])) . '</h6>' : ''); 
								
									echo (!empty($post['short_description']) ? '<p>' . $post['short_description'] . ' <a href="' . ROOT_DIR . $postDetails['name'] . '/' . $post['url'] . '">Read More</a></p>' : ''); 
								?>
							</div>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
				
				<?php echo $pagination->display(); ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php require_once('includes/footer.php'); ?>