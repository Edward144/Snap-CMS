<?php require_once('includes/header.php'); ?>

<?php 
    if(!isset($_GET['post-type'])) {
        http_response_code(404);
        header('Location: manage-content/' . $_GET['post-type']);
        exit();
    }
?>

<div class="container-fluid d-block d-xl-flex h-100">       
    <?php if(isset($_GET['id'])) : ?>
        <?php 
            //Check if post exists
            $post = $mysqli->query(
                "SELECT * FROM `posts` WHERE id = {$_GET['id']} LIMIT 1"
            );

            if($post->num_rows <= 0) {
                header('Location: ./' . $_GET['post-type']);
                exit();
            }

            $post = $post->fetch_assoc();
        ?>
        
        <form id="managePost" class="row flex-grow-1">
            <div class="col-xl-4 bg-light">
                <h2 class="py-2">Details</h2>
                
				<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
				<input type="hidden" name="postTypeId" value="<?php echo $post['post_type_id']; ?>">
				
                <div class="form-group">
                    <input type="button" class="btn btn-secondary" name="return" value="Return To Content List">
                </div>
                
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo $post['name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>URL Slug</label>
                    <input type="text" class="form-control" name="url" value="<?php echo $post['url']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Short Description</label>
                    <textarea type="text" class="form-control noTiny" name="shortDesc"><?php echo $post['short_description']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Date Posted</label>
                    <input type="datetime-local" class="form-control" name="datePosted" value="<?php echo date('Y-m-d\TH:i', strtotime($post['date_posted'])); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Author</label>
                    <input type="text" class="form-control" name="author" value="<?php echo $post['author']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Custom PHP File</label>
                    <input type="text" class="form-control" name="customContent" value="<?php echo $post['custom_content']; ?>">
                </div>
                
                <hr>
                
                <h3 class="py2">Meta Data</h3>
                
                <div class="form-group">
                    <label>Meta Title</label>
                    <input type="text" class="form-control" name="metaTitle" value="<?php echo $post['meta_title']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Meta Description</label>
                    <input type="text" class="form-control" name="metaDescription" value="<?php echo $post['meta_description']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input type="text" class="form-control" name="metaKeywords" value="<?php echo $post['meta_keywords']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Meta Author</label>
                    <input type="text" class="form-control" name="metaAuthor" value="<?php echo $post['meta_author']; ?>">
                </div>
                
                <div class="form-group">
                    <?php if($post['visible'] == 1) : ?>
                        <input type="button" class="btn btn-secondary mr-2" name="visibility" value="Visible" data-id="<?php echo $post['id']; ?>">
                    <?php else : ?>
                        <input type="button" class="btn btn-secondary mr-2" name="visibility" value="Hidden"  data-id="<?php echo $post['id']; ?>">
                    <?php endif; ?>

                    <input type="button" class="btn btn-danger" name="delete" value="Delete" data-id="<?php echo $post['id']; ?>">
                </div>
                
                <div class="form-group d-flex align-items-center">
                    <input type="submit" class="btn btn-primary" value="Save Content">
                </div>
            </div>

            <div class="col bg-white">
                <h2 class="py-2">Cover Image</h2>
                
                <div class="form-group">
					<small>Add your images below. Drag thumbnails to re-order images. <span class="text-danger">Adding, removing, or re-ordering images will cause any entered text to be lost from the carousel. Add and position all images before adding text.</span></small>
                </div>
                
                <div class="form-group d-flex align-items-center" id="carouselImages">
					<?php 
						if($post['gallery'] != null && $post['gallery'] != '') : 
							$gallery = json_decode($post['gallery'], true); 
					
							foreach($gallery as $index => $item) : ?>
								<div class='imageWrap mr-2'>
									<button class='btn btn-danger border-0 close' style='position: absolute; top: 0; right: 0; padding: 0 4px 4px 4px;'>&times;</button>
									<img src='<?php echo $item['imageUrl']; ?>'>
								</div>
							<?php endforeach; ?>
					<?php endif; ?>
					
                    <button class="btn btn-secondary" id="addImage" data-toggle="tooltip" data-placement="bottom" title="Add Image">&plus;</button>
                </div>
				
				<div class="carouselPreview">
					<?php 
						if($post['gallery'] != null && $post['gallery'] != '' && $post['gallery'] != 'null') : 
							$carousel = json_decode($post['gallery'], true); ?>
							
							<div id="carouselPrev" class="carousel slide d-flex align-items-center" data-ride="carousel">
								<div class="carousel-inner" style="height: 400px;">
									<?php foreach($carousel as $index => $item) : ?>
										<div class="carousel-item h-100 <?php echo ($index == 0 ? 'active' : ''); ?>" data-interval="10000" data-item="<?php echo $index; ?>">
											<img src="<?php echo $item['imageUrl']; ?>" class="h-100 w-100" style="object-fit: cover;" alt="slide <?php echo $index; ?>">

											<div class="carousel-caption d-none d-md-block" style="<?php echo ($item['captionPosition'] == 'top' ? 'top: 0;' : ($item['captionPosition'] == 'bottom' ? 'bottom: 0;' : 'top: 50%; transform: translateY(-50%);')); ?>">
												<div class="btn-group mb-2">
													<button type="button" class="btn btn-secondary" id="top" <?php echo ($item['captionPosition'] == 'top' ? 'data-active="true"' : ''); ?>><span class="fa fa-long-arrow-alt-up"></span></button>
													<button type="button" class="btn btn-secondary" id="center" <?php echo ($item['captionPosition'] == 'center' ? 'data-active="true"' : ''); ?>><span class="fa fa-arrows-alt-h"></span></button>
													<button type="button" class="btn btn-secondary" id="bottom" <?php echo ($item['captionPosition'] == 'bottom' ? 'data-active="true"' : ''); ?>><span class="fa fa-long-arrow-alt-down"></span></button>
												</div>

												<h5><input type="text" placeholder="Slide Title" name="slideTitle" value="<?php echo $item['title']; ?>"></h5>
												<p><input type="text" placeholder="Slide Small Text" name="slideSmall" value="<?php echo $item['small']; ?>"></p>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<a class="carousel-control-prev" href="#carouselPrev" role="button" data-slide="prev">
									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									<span class="sr-only">Previous</span>
								</a>

								<a class="carousel-control-next" href="#carouselPrev" role="button" data-slide="next">
									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only">Next</span>
								</a>
							</div>
						
					<?php endif; ?>
				</div>
                
                <h2 class="py-2">Content</h2>
                
                <div class="form-group">
                    <textarea name="content"><?php echo $post['content']; ?></textarea>
                </div>
            </div>
        </form>
    <?php else : ?>
        <?php         
            $postTypes = $mysqli->prepare("SELECT * FROM `post_types` WHERE name = ? LIMIT 1");
            $postTypes->bind_param('s', $_GET['post-type']);
            $postTypes->execute();
            $result = $postTypes->get_result();

            if($result->num_rows <= 0) {
                header('Location: pages');
                exit();
            }

            $postType = $result->fetch_assoc();
        ?>

        <div class="row flex-grow-1">
            <div class="col-xl-4 bg-light">
                <h2 class="py-2">Manage <?php echo ucwords(str_replace('-', ' ', $_GET['post-type'])); ?></h2>

                <form id="createContent" action="<?php echo ROOT_DIR; ?>admin/scripts/manageContent.php" method="post">
                    <input type="hidden" name="method" value="createContent">
                    <input type="hidden" name="postTypeId" value="<?php echo $postType['id']; ?>">
                    <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">

                    <div class="form-group d-flex align-items-center">
                        <input type="submit" class="btn btn-primary" value="Create New">
                    </div>

                    <?php if(isset($_SESSION['createmessage'])) : ?>
                        <div class="alert alert-<?php echo (isset($_SESSION['status']) && $_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                            <?php echo $_SESSION['createmessage']; ?>
                        </div>
                    <?php endif; ?>
                </form>
                <hr>

                <form id="searchContent">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" class="form-control" name="search" value="<?php echo $_GET['search']; ?>">
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary mr-2" value="Search">

                        <?php if(isset($_GET['search'])) : ?>
                            <input type="button" class="btn btn-secondary" name="clearSearch" value="Clear Search">
                        <?php endif; ?>
                    </div>
                </form>

                <?php if($_GET['post-type'] != 'pages') : ?>
                    <hr>
                    <h3 class="py-2">Landing Page</h3>

                    <form id="updateLanding" action="<?php echo ROOT_DIR; ?>admin/scripts/manageContent.php" method="post">
                        <input type="hidden" name="method" value="updateLanding">
                        <input type="hidden" name="postTypeId" value="<?php echo $postType['id']; ?>">
                        <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo $postType['title']; ?>">
                        </div>

                        <div class="form-group coverImage">
                            <label class="d-block">Cover Image</label>
                            <input type="hidden" name="coverImage" value="<?php echo $postType['image_url']; ?>">
                            <?php echo ($postType['image_url'] != null && $postType['image_url'] != '' ? '<img src="' . $postType['image_url'] . '">' : ''); ?>

                            <div class="clearfix mt-2"></div>
                            <input type="button" class="btn btn-info mr-2" name="selectImage" value="Choose Image">
                            <input type="button" class="btn btn-secondary mt-2 mt-sm-0" name="clearImage" value="Remove Image" style="<?php echo ($postType['image_url'] != null && $postType['image_url'] != '' ? '' : 'display: none;'); ?>">                        
                        </div>

                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="content"><?php echo $postType['content']; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" class="form-control" name="metaTitle" value="<?php echo $postType['meta_title']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Meta Description</label>
                            <input type="text" class="form-control" name="metaDescription" value="<?php echo $postType['meta_description']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <input type="text" class="form-control" name="metaKeywords" value="<?php echo $postType['meta_keywords']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Meta Author</label>
                            <input type="text" class="form-control" name="metaAuthor" value="<?php echo $postType['meta_author']; ?>">
                        </div>

                        <div class="form-group d-flex align-itesm-center">
                            <input type="submit" class="btn btn-primary" value="Update Landing Page">
                        </div>

                        <?php if(isset($_SESSION['landingmessage'])) : ?>
                            <div class="alert alert-<?php echo (isset($_SESSION['status']) && $_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                                <?php echo $_SESSION['landingmessage']; ?>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>

            <div class="col bg-white hasTable">
                <h2 class="py-2">Content List</h2>

                <?php 
                    $searchTerm = (isset($_GET['search']) ? $_GET['search'] : '');

                    $itemCount = $mysqli->query("SELECT * FROM `posts` WHERE post_type_id = {$postType['id']} AND (name LIKE '%{$searchTerm}%' OR url LIKE '%{$searchTerm}%' OR author LIKE '%{$searchTerm}%')")->num_rows;
                    $pagination = new pagination($itemCount);
                    $pagination->load();

                    $posts = $mysqli->query(
                        "SELECT 
                            posts.id, 
                            posts.name, 
                            posts.url,
                            categories.name AS category,
                            posts.author,
                            posts.date_posted,
                            posts.last_edited,
                            posts.visible FROM `posts` AS posts 
                                LEFT OUTER JOIN `categories` AS categories ON posts.category_id = categories.id AND posts.post_type_id = categories.post_type_id
                            WHERE posts.post_type_id = {$postType['id']} AND (posts.name LIKE '%{$searchTerm}%' OR posts.url LIKE '%{$searchTerm}%' OR posts.author LIKE '%{$searchTerm}%') 
                            ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}"
                    ); 
                ?>

                <?php if($posts->num_rows > 0) : ?>
                    <div class="table-responsive-lg overflow-auto">
                        <table class="table" id="contentList">
                            <thead class="thead-dark">
                                <th>ID</th>
                                <th>Details</th>
                                <th>Author</th>
                                <th>Actions</th>
                            </thead>

                            <tbody>
                                <?php while($row = $posts->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <span><?php echo $row['id']; ?></span>
                                        </td>

                                        <td class="w-100">
                                            <span><strong><?php echo $row['name']; ?></strong></span>
                                            <span><?php echo ($row['url'] != null && $row['url'] != '' ? '<br>URL: ' . $row['url'] : ''); ?></span>
                                            <span><?php echo ($row['category'] != null && $row['category'] != '' ? '<br>Category: ' . $row['category'] : ''); ?></span>
                                        </td>

                                        <td>
                                            <span><strong>Author: </strong><?php echo ($row['author'] != null && $row['author'] != '' ? $row['author'] : 'Unknown'); ?></span>
                                            <br>
                                            <span><strong>Date Posted: </strong><?php echo date('d/m/Y', strtotime($row['date_posted'])); ?></span>
                                            <br>
                                            <span><strong>Last Edited: </strong><?php echo date('d/m/Y', strtotime($row['last_edited'])); ?></span>
                                        </td>

                                        <td>
                                            <?php if($row['visible'] == 1) : ?>
                                                <input type="button" class="btn btn-secondary" name="visibility" value="Visible" data-id="<?php echo $row['id']; ?>">
                                            <?php else : ?>
                                                <input type="button" class="btn btn-secondary" name="visibility" value="Hidden"  data-id="<?php echo $row['id']; ?>">
                                            <?php endif; ?>

                                            <input type="button" class="btn btn-primary" name="edit" value="Edit" data-id="<?php echo $row['id']; ?>">
                                            <input type="button" class="btn btn-danger" name="delete" value="Delete" data-id="<?php echo $row['id']; ?>">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php echo $pagination->display(); ?>
                <?php else : ?>
                    <h3 class="alert alert-info my-3">No <?php echo str_replace('-', ' ', $_GET['post-type']); ?> have been created</h3>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?php echo ROOT_DIR; ?>admin/scripts/manageContent.js"></script>

<?php require_once('includes/footer.php'); ?>

<?php
    unset($_SESSION['status']);
    unset($_SESSION['createmessage']);
    unset($_SESSION['landingmessage']);
    unset($_SESSION['savemessage']);
?>