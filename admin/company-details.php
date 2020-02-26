<?php require_once('includes/header.php'); ?>

<div class="flexContainer" id="companyDetails">
    <div class="column column-60 formBlock detailsEditor">
        <h2 class="greyHeader">Company Information</h2>
        
        <div>            
            <form id="editCompany" method="POST" action="scripts/editCompany.php">
                <?php
                    $company = $mysqli->query("SELECT * FROM `company_info` ORDER BY id DESC LIMIT 1");
                    
                    if($company->num_rows == 1) {
                        $row = $company->fetch_assoc();
                    }
                ?>
                
                <p>
                    <label>Company Name</label>
                    <input type="text" name="name" value="<?php echo $row['name']; ?>">
                </p>
                
                <p>
                    <label>Address Line 1</label>
                    <input type="text" name="add1" value="<?php echo $row['address_1']; ?>">
                </p>
                
                <p>
                    <label>Address Line 2</label>
                    <input type="text" name="add2" value="<?php echo $row['address_2']; ?>">
                </p>
                
                <p>
                    <label>Address Line 3</label>
                    <input type="text" name="add3" value="<?php echo $row['address_3']; ?>">
                </p>
                
                <p>
                    <label>Address Line 4</label>
                    <input type="text" name="add4" value="<?php echo $row['address_4']; ?>">
                </p>
                
                <p>
                    <label>Postcode</label>
                    <input type="text" name="postcode" value="<?php echo $row['postcode']; ?>">
                </p>
                
                <p>
                    <label>County</label>
                    <input type="text" name="county" value="<?php echo $row['county']; ?>">
                </p>
                
                <p>
                    <label>Country</label>
                    <input type="text" name="country" value="<?php echo $row['country']; ?>">
                </p>
                
                <p>
                    <label>Telephone</label>
                    <input type="text" name="phone" value="<?php echo $row['phone']; ?>">
                </p>
                
                <p>
                    <label>Fax</label>
                    <input type="text" name="fax" value="<?php echo $row['fax']; ?>">
                    
                </p>
                
                <p>
                    <label>Email</label>
                    <input type="text" name="email" value="<?php echo $row['email']; ?>">
                </p>
                
                <p>
                    <label>VAT Number</label>
                    <input type="text" name="vat" value="<?php echo $row['vat_number']; ?>">
                </p>
                
                <p>
                    <label>Registration Number</label>
                    <input type="text" name="reg" value="<?php echo $row['registration_number']; ?>">
                </p>
                
                <p>
                    <label>Logo</label>
                    <input type="text" name="logo" style="max-width: 200px;" value="<?php echo $row['logo']; ?>">
                    <input type="button" name="logoSelector" value="Choose File" style="padding: 0.5em;">
                </p>
                
                <input type="submit" value="Update">
                    
                <p id="message"><?php 
                        if(isset($_SESSION['compmessage'])) {
                            echo $_SESSION['compmessage'];
                            unset($_SESSION['compmessage']);
                        }
                    ?></p>
            </form>
            
            <script>
                //Format Postcode
                $("#editCompany input[name='postcode']").on("keyup", function() {
                    $(this).val(formatPostcode($(this).val()));
                });
                
                //Select Logo
                $("#editCompany input[name='logoSelector']").click(function() {
                    moxman.browse({
                        extensions: 'png, jpg, jpeg, gif, webp, svg',
                        skin: "snapcms",
                        oninsert: function(args) {
                            var image = args.files[0].url;
                            
                            $("#editCompany input[name='logo']").val(image);
                        }
                    });
                });
            </script>
        </div>
    </div>
    
    <div class="column column-40 formBlock socialEditor">
        <h2 class="greyHeader">Social Media</h2>
        
        <div>
            <form id="editSocial" method="POST" action="scripts/editSocial.php">
                <?php $socials = $mysqli->query("SELECT * FROM `social_links`"); ?>
                
                <?php if($socials->num_rows > 0) : 
                        while($row = $socials->fetch_assoc()) : ?>
                        <p>
                            <label>
                                <?php if(file_exists($_SERVER['DOCUMENT_ROOT'] . ROOT_DIR . 'admin/images/social/' . strtolower($row['name']) . '.png')) : ?>
                                    <img src="images/social/<?php echo strtolower($row['name']); ?>.png" class="socialIcon" id="<?php echo strtolower($row['name']); ?>">
                                <?php else : ?>
                                    <img src="images/social/unknown.png" class="socialIcon" id="<?php echo strtolower($row['name']); ?>">
                                <?php endif; ?>
                                
                                <span><?php echo ucwords(str_replace('-', ' ', $row['name'])); ?> </span>
                            </label>
                            
                            <input type="text" name="<?php echo strtolower($row['name']); ?>" value="<?php echo (isset($_SESSION[strtolower($row['name'])]) ? $_SESSION[strtolower($row['name'])] : $row['url']); ?>" placeholder="https://www.<?php echo strtolower($row['name']); ?>.com/">
                        </p>
                
                        <?php unset($_SESSION[strtolower($row['name'])]); ?>
                    <?php endwhile; ?>
                
                    <input type="submit" value="Update">
                
                    <p id="message"><?php 
                            if(isset($_SESSION['socialmessage'])) {
                                echo $_SESSION['socialmessage'];
                                unset($_SESSION['socialmessage']);
                            }
                        ?></p>
                <?php endif; ?>
            </form>
        </div>
        
        <h2 class="greyHeader" style="margin-top: 1em;">Create New Social Media</h2>
        
        <div>            
            <p>Icons for social media are stored in <code>/admin/images/social/</code>, if the icon for your social media site is missing then upload a png here with the same name, replacing spaces with hyphens.</p>
            
            <form id="addSocial"method="POST" action="scripts/addSocial.php">
                <p>
                    <label>Social Media Name</label>
                    <input type="text" name="socialName">
                </p>
                
                <p>
                    <label>URL</label>
                    <input type="text" name="socialUrl">
                </p>
                
                <input type="submit" value="Submit">
                
                <p id="message"><?php 
                        if(isset($_SESSION['addmessage'])) {
                            echo $_SESSION['addmessage'];
                            unset($_SESSION['addmessage']);
                        }
                    ?></p>
            </form>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>