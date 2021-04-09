<?php require_once('includes/header.php'); ?>

<div class="container-fluid d-block d-xl-flex h-100">                    
    <div class="row flex-grow-1">
        <div class="col-xl-8 bg-light">
            <form id="updateDetails" action="admin/scripts/manageWebsite.php" method="post">
                <input type="hidden" name="method" value="updateDetails">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
                <?php 
                    $websiteDetails = $mysqli->query("SELECT * FROM `company_info` ORDER BY id DESC LIMIT 1"); 
                    $website = ($websiteDetails->num_rows > 0 ? $websiteDetails->fetch_assoc() : null);
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="py-2">Address</h2>
                        
                        <div class="form-group">
                            <label for="name">Company/Website Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $website['name']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="address1">Address Line 1</label>
                            <input type="text" class="form-control" name="address1" value="<?php echo $website['address_1']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="address2">Address Line 2</label>
                            <input type="text" class="form-control" name="address2" value="<?php echo $website['address_2']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="address3">Address Line 3</label>
                            <input type="text" class="form-control" name="address3" value="<?php echo $website['address_3']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="address4">Address Line 4</label>
                            <input type="text" class="form-control" name="address4" value="<?php echo $website['address_4']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="county">County</label>
                            <input type="text" class="form-control" name="county" value="<?php echo $website['county']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="postcode">Postcode</label>
                            <input type="text" class="form-control" name="postcode" value="<?php echo $website['postcode']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" name="country" value="<?php echo $website['country']; ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h2 class="py-2">Contact Details</h2>
                        
                        <div class="form-group">
                            <label for="phone">Telephone</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo $website['phone']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" name="email" value="<?php echo $website['email']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="fax">Fax/Alternate Telephone</label>
                            <input type="text" class="form-control" name="fax" value="<?php echo $website['fax']; ?>">
                        </div>

                        <h2 class="py-2">Company Numbers</h2>

                        <div class="form-group">
                            <label>Company Registration Number</label>
                            <input type="text" class="form-control" name="registrationNumber" value="<?php echo $website['registration_number']; ?>">
                        </div>

                        <div class="form-group">
                            <label>VAT Number</label>
                            <input type="text" class="form-control" name="vatNumber" value="<?php echo $website['vat_number']; ?>">
                        </div>
                        
                        <h2 class="py-2">Logo</h2>
                        
                        <div class="form-group logo" style="max-width: 300px;">
                            <input type="hidden" name="logo" value="<?php echo $website['logo']; ?>">
                            <?php echo ($website['logo'] != null && $website['logo'] != '' ? '<img src="' . $website['logo'] . '" class="img-fluid">' : ''); ?>
                            
                            <input type="button" class="btn btn-info mr-2" name="selectImage" value="Choose Image">
                            <input type="button" class="btn btn-secondary mt-2 mt-sm-0" name="clearImage" value="Remove Image" style="<?php echo ($website['logo'] != null && $website['logo'] != '' ? '' : 'display: none;'); ?>">
                        </div>

                        <div class="form-group d-flex align-items-center">
                            <input type="submit" class="btn btn-primary" value="Save Details">
                        </div>
                        
                        <?php if(isset($_SESSION['detailsmessage'])) : ?>
                            <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                                <?php echo $_SESSION['detailsmessage']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="col bg-white">
            <h2 class="py-2">Social Media</h2>
            
            <form id="updateSocial" action="scripts/manageWebsite.php" method="post">
                <input type="hidden" name="method" value="updateSocial">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
                <?php $socials = $mysqli->query("SELECT * FROM `social_links` ORDER BY id ASC"); ?>
                
                <?php if($socials->num_rows > 0) : ?>
                    <?php while($social = $socials->fetch_assoc()) : ?>
                        <div class="form-group">
                            <label for="<?php echo str_replace(' ', '-', $social['name']); ?>"><span class="fab fa-<?php echo strtolower(str_replace(' ', '-', $social['name'])); ?>"></span> <?php echo ucwords($social['name']); ?></label>
                            <input type="text" class="form-control" name="<?php echo str_replace(' ', '-', $social['name']); ?>" placeholder="https://<?php echo strtolower(str_replace(' ', '-', $social['name'])); ?>.com/your-profile" value="<?php echo $social['url']; ?>">
                        </div>
                    <?php endwhile; ?>
                <?php else : ?>
                    <h3 class="alert alert-info my-3">Could not load social media links</h3>
                <?php endif; ?>
                
                <div class="form-group d-flex align-items-center">
                    <input type="submit" class="btn btn-primary" value="Save Social Links">
                </div>
                
                <?php if(isset($_SESSION['socialmessage'])) : ?>
                    <div class="alert alert-<?php echo ($_SESSION['status'] == 0 ? 'danger' : 'success'); ?>">
                        <?php echo $_SESSION['socialmessage']; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script src="admin/scripts/manageWebsite.js"></script>

<?php require_once('includes/footer.php'); ?>

<?php 
    unset($_SESSION['socialmessage']); 
    unset($_SESSION['detailsmessage']); 
    unset($_SESSION['status']); 
?>