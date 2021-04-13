<?php require_once('includes/header.php'); ?>

<div class="container-fluid d-block d-xl-flex h-100">
	<div class="row flex-grow-1">
		<?php if(isset($_GET['id'])) : ?>
			<?php 
				$contact = $mysqli->query(
					"SELECT * FROM `contact_forms` WHERE id = {$_GET['id']} LIMIT 1"
				);

				if($contact->num_rows <= 0) {
					header('Location: ./contact-forms');
					exit();
				}

				$contact = $contact->fetch_assoc();
			?>
		
			<div class="col-xl-4 bg-light">
				<h2 class="py-2">Contact Form <?php echo $contact['id']; ?></h2>
				
				<form id="updateContact" action="admin/scripts/manageContact.php" method="post">
					<input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
					<input type="hidden" name="returnurl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<input type="hidden" name="method" value="updateContact">
					<input type="hidden" name="structure">
					
					<div class="form-group">
						<input type="button" class="btn btn-secondary" name="return" value="Return To Contact Form List">
					</div>
					
					<div class="form-group">
						<label>Form Name</label>
						<input type="text" class="form-control" name="name" value="<?php echo $contact['name']; ?>" required>
						<small class="text-muted">This will appear on form submissions for your reference. It will not be visible to users.</small>
					</div>
					
					<div class="form-group">
						<label>Subject</label>
						<input type="text" class="form-control" name="subject" value="<?php echo $contact['subject']; ?>">
						<small class="text-muted">If a subject is not provided then one will be automatically generated.</small>
					</div>
					
					<div class="form-group">
						<label>Email Addresses</label>
						<textarea class="form-control noTiny" name="emails" required><?php echo implode(',', json_decode($contact['structure'], true)['emails']); ?></textarea>
						<small class="text-muted">Enter the email addresses that will receive communications from this form. Separated by a comma.</small>
					</div>
					
					<h5 class="py-2"><span class="fab fa-google mr-2"></span>reCaptcha <small>(supports v3)</small></h5>
					
					<div class="form-group">
						<label>reCaptcha Sitekey</label>
						<input type="text" class="form-control" name="sitekey" value="<?php echo $contact['sitekey']; ?>">
					</div>
					
					<div class="form-group">
						<label>reCAPTCHA Secretkey</label>
						<input type="text" class="form-control" name="secretkey" value="<?php echo $contact['secretkey']; ?>">
					</div>
					
					<div class="form-group d-flex align-items-center">
						<input type="submit" class="btn btn-primary" value="Save Form">
					</div>
					
					<?php if(isset($_SESSION['updatemessage'])) : ?>
                        <div class="alert alert-<?php echo (isset($_SESSION['status']) && $_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                            <?php echo $_SESSION['updatemessage']; ?>
                        </div>
                    <?php endif; ?>
				</form>
			</div>

			<div class="col bg-white">
				<h2 class="py-2">Form Structure</h2>
				
				<ul class="list-group formInputs" style="max-width: 992px;">
					<?php $inputs = json_decode($contact['structure'], true)['inputs']; ?>
					
					<?php foreach($inputs as $iindex => $input) : ?>
						<?php
							$default = 
								'<div class="input-group form-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Input Type</span>
									</div>
									<input type="text" class="form-control" name="type" value="' . $input['type'] . '" disabled>
									<div class="input-group-append">
										<div class="input-group-text">
											<input type="checkbox" name="required" ' . ($input['required'] == true ? 'checked' : '') . '>
										</div>
										<span class="input-group-text">Required?</span>
									</div>
									<div class="input-group-append">
										<input type="button" class="btn btn-danger" value="Delete Input" name="deleteInput">
									</div>
								</div>
								<div class="input-group form-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Label</span>
									</div>
									<input type="text" class="form-control" name="label" value="' . $input['label'] . '">
									<div class="input-group-append">
										<div class="input-group-text">
											<input type="checkbox" name="hidelabel" ' . ($input['hidelabel'] == true ? 'checked' : '') . '>
										</div>
										<span class="input-group-text">Hide Label?</span>
									</div>
								</div>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">Placeholder</span>
									</div>
									<input type="text" class="form-control" name="placeholder" value="' . $input['placeholder'] . '">
								</div>';
						?>
					
						<li class="list-group-item">
							<?php 
								switch($input['type']) {
									case 'general': 
										echo '<div class="input-group form-group">
											<div class="input-group-prepend">
												<span class="input-group-text">Input Type</span>
											</div>
											<input type="text" class="form-control" name="type" value="general" disabled>
											<div class="input-group-append">
												<input type="button" class="btn btn-danger" name="deleteInput" value="Delete Input">
											</div>
										</div>
										<div class="form-group mb-0">
											<textarea class="form-control noTiny" name="value" placeholder="Enter some to be displayed to the user...">' . $input['value'] . '</textarea>
										</div>';
										break;
									case 'number': 
										echo $default . 
											'<div class="input-group form-group mt-3">
												<div class="input-group-prepend">
													<span class="input-group-text">Min Value</span>
												</div>
												<input type="text" class="form-control" name="min" value="' . $input['min'] . '">
											</div>
											<div class="input-group form-group">
												<div class="input-group-prepend">
													<span class="input-group-text">Max Value</span>
												</div>
												<input type="text" class="form-control" name="max" value="' . $input['max'] . '">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">Step (Decimal Places)</span>
												</div>
												<input type="text" class="form-control" name="step" vaue="' . $input['step'] . '">
											</div>';
										break;
									case 'select': 
										echo $default . 
											'<div class="input-group mt-3">
												<div class="input-group-prepend">
													<span class="input-group-text">Options</span>
												</div>
												<textarea class="form-control noTiny" name="options" placeholder="Option 1, Option 2, Option 3, etc...">' . implode(',', $input['options']) . '</textarea>
											</div>';
										break;
									case 'radio': 
										echo $default . 
											'<div class="input-group mt-3">
												<div class="input-group-prepend">
													<span class="input-group-text">Options</span>
												</div>
												<textarea class="form-control noTiny" name="options" placeholder="Option 1, Option 2, Option 3, etc...">' . implode(',', $input['options']) . '</textarea>
											</div>';
										break;
									case 'file': 
										echo $default .
											'<div class="input-group mt-3">
												<div class="input-group-prepend">
													<span class="input-group-text">Allow Multiple Files</span>
												</div>
												<div class="input-group-append">
													<div class="input-group-text">
														<input type="checkbox" name="multiple" ' . ($input['multiple'] == true ? 'checked' : '') . '>
													</div>
												</div>
											</div>';
										break;
									default: 
										echo $default;
										break;
								} 
							?>
						</li>
					<?php endforeach; ?>
					
					<li class="list-group-item" id="actions">
						<div class="input-group">
							<div class="input-group-prepend">
								<input type="button" class="btn btn-primary" name="addInput" value="Add Input">
							</div>
							
							<select name="inputTypes" class="form-control">
								<option value="general">General Text</option>
								<option value="text">Textbox</option>
								<option value="textarea">Textarea</option>
								<option value="email">Email</option>
								<option value="number">Number</option>
								<option value="date">Date</option>
								<option value="time">Time</option>
								<option value="datetime-local">Date &amp; time</option>
								<option value="checkbox">Checkbox</option>
								<option value="radio">Radio Buttons</option>
								<option value="select">Multi Select</option>
								<option value="file">File Upload</option>
								<option value="hidden">Hidden</option>
							</select>
						</div>
					</li>
				</ul>
			</div>
		<?php else : ?>
			<div class="col-xl-4 bg-light">
				<h2 class="py-2">Manage Contact Forms</h2>
				
				<form id="createContact" action="admin/scripts/manageContact.php" method="post">
                    <input type="hidden" name="method" value="createContact">
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
			</div>

			<div class="col bg-white overflow-hidden">
				<h2 class="py-2">Contact Forms List</h2>
				
				<?php
					$itemCount = $mysqli->query("SELECT * FROM `contact_forms`")->num_rows;
					$pagination = new pagination($itemCount);
					$pagination->load();
				
					$contacts = $mysqli->query("SELECT * FROM `contact_forms` ORDER BY id ASC LIMIT {$pagination->itemLimit} OFFSET {$pagination->offset}");
				?>
				
				<?php if($contacts->num_rows > 0) : ?>
					<div class="table-responsive-lg overflow-auto">
						<table class="table" id="contactList">
							<thead class="thead-dark">
								<th>ID</th>
								<th>Details</th>
								<th>Actions</th>
							</thead>
							
							<tbody>
								<?php while($contact = $contacts->fetch_assoc()) : ?>
									<tr>
										<td>
											<span><?php echo $contact['id']; ?></span>
										</td>
										
										<td class="w-100">
											<span><strong><?php echo $contact['name']; ?></strong></span>
											<?php echo (!empty($contact['subject']) ? '<span class="text-muted">' . $contact['subject'] . '</span>' : ''); ?>
											<?php echo (!empty($contact['sitekey']) && !empty($contact['secretkey']) ? '<br><span class="alert-success">Protected by reCAPTCHA</span>' : ''); ?>
											<br><span><code class="text-muted bg-light">Shortcode: [insert="contactform",id="<?php echo $contact['id']; ?>"]</code></span>
										</td>
										
										<td>
											<input type="button" class="btn btn-primary" name="edit" value="Edit" data-id="<?php echo $contact['id']; ?>">
                                            <input type="button" class="btn btn-danger" name="delete" value="Delete" data-id="<?php echo $contact['id']; ?>">
										</td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				
					<?php echo $pagination->display(); ?>
				<?php else : ?>
				
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<script src="admin/scripts/manageContact.js"></script>

<?php require_once('includes/footer.php'); ?>

<?php
	unset($_SESSION['status']);
	unset($_SESSION['createmessage']);
	unset($_SESSION['updatemessage']);
?>