<?php require_once('includes/header.php'); ?>

<div id="companyDetails">
    <div class="column-60 formBlock detailsEditor">
        <h2 class="greyHeader">Company Information</h2>
        
        <div>
            <form id="editCompany" method="POST" action="scripts/editCompany.php">
                <p>
                    <label>Company Name</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Address Line 1</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Address Line 2</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Address Line 3</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Address Line 4</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Postcode</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>County</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Telephone</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Fax</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Email</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>VAT Number</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Registration Number</label>
                    <input type="text">
                </p>
                
                <p>
                    <label>Logo</label>
                    <input type="text">
                </p>
                
                <input type="submit" value="Update">
                    
                <p id="message"><?php 
                        if(isset($_SESSION['compmessage'])) {
                            echo $_SESSION['compmessage'];
                            unset($_SESSION['compmessage']);
                        }
                    ?></p>
            </form>
        </div>
    </div>
    
    <div class="column-40 formBlock socialEditor">
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
            <p>Icons for social media are stored in <code>/admin/images/social/</code>, if the icon for your social media site is missing then upload a png here with the same name, replaceing spaces with hyphens.</p>
            
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