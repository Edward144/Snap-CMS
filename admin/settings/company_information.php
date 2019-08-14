<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <h1><?php adminTitle(); ?></h1>
        
        <div class="formBlock" id="companyInfo">
            <?php
                $checkCompany = $mysqli->query("SELECT * FROM `company_info`");
            
                if($checkCompany->num_rows > 0) {
                    $company = $checkCompany->fetch_assoc();
                }
            ?>
            
            <form>
                <p>
                    <label>Company Name: </label>
                    <input type="text" name="companyName" value="<?php echo $company['company_name']; ?>">
                </p>
                
                <p>
                    <label>Address 1: </label>
                    <input type="text" name="address1" value="<?php echo $company['address_1']; ?>">
                </p>
                
                <p>
                    <label>Address 2: </label>
                    <input type="text" name="address2" value="<?php echo $company['address_2']; ?>">
                </p>
                
                <p>
                    <label>Address 3: </label>
                    <input type="text" name="address3" value="<?php echo $company['address_3']; ?>">
                </p>
                
                <p>
                    <label>Address 4: </label>
                    <input type="text" name="address4" value="<?php echo $company['address_4']; ?>">
                </p>
                
                <p>
                    <label>Postcode: </label>
                    <input type="text" name="postcode" value="<?php echo $company['postcode']; ?>">
                </p>
                
                <p>
                    <label>Country: </label>
                    <select name="country">
                        <option value="" selected disabled>--Select Country--</option>
                        
                        <?php $countries = $mysqli->query("SELECT * FROM `countries` ORDER BY iso_code ASC"); ?>
                        
                        <?php while($row = $countries->fetch_assoc()) : ?>
                            <option value="<?php echo $row['iso_code']; ?>" <?php echo ($row['iso_code'] == $company['country'] ? 'selected' : ''); ?>><?php echo $row['iso_code'] . ': ' . $row['country']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </p>
            </form>
            
            <form>
                <p>
                    <label>Phone: </label>
                    <input type="text" name="phone" value="<?php echo $company['phone']; ?>">
                </p>
                
                <p>
                    <label>Fax: </label>
                    <input type="text" name="fax" value="<?php echo $company['fax']; ?>">
                </p>
                
                <p>
                    <label>Email: </label>
                    <input type="text" name="email" value="<?php echo $company['email']; ?>">
                </p>
                
                <p>
                    <label>VAT Number: </label>
                    <input type="text" name="vat" value="<?php echo $company['vat_number']; ?>">
                </p>
                
                <p>
                    <label>Reg Number: </label>
                    <input type="text" name="reg" value="<?php echo $company['reg_number']; ?>">
                </p>
                
                <p>
                    <label>Logo: </label>
                    <input type="file" name="logo">
                </p>
                
                <p>
                    <input type="submit" value="Submit">
                    
                    <?php if($company['logo'] != null && $company['logo'] != '') : ?>
                        <input type="button" name="clearLogo" value="Remove Uploaded Logo" class="badButton">
                    <?php endif; ?>
                </p>
                
                <p class="message"></p>                
            </form>
        </div>
        
        <h2>Social Links</h2>
        
        <div class="formBlock">            
            <form id="socialLinks">
                <?php $socialLinks = $mysqli->prepare("SELECT link_value FROM `social_links` WHERE link_value IS NOT NULL AND link_value <> '' AND link_name = ?"); ?>
                <p>
                    <?php 
                        $socialName = 'Facebook'; 
                        $socialLinks->bind_param('s', $socialName);
                        $socialLinks->execute();
                        $result = $socialLinks->get_result();
                        $result = $result->fetch_assoc()['link_value'];
                    ?>
                    
                    <label>Facebook: </label>
                    <input type="text" name="facebook" value="<?php echo $result; ?>">
                </p>
                
                <p>
                    <?php 
                        $socialName = 'Twitter'; 
                        $socialLinks->bind_param('s', $socialName);
                        $socialLinks->execute();
                        $result = $socialLinks->get_result();
                        $result = $result->fetch_assoc()['link_value'];
                    ?>
                    
                    <label>Twitter: </label>
                    <input type="text" name="twitter" value="<?php echo $result; ?>">
                </p>
                
                <p>
                    <?php 
                        $socialName = 'Youtube'; 
                        $socialLinks->bind_param('s', $socialName);
                        $socialLinks->execute();
                        $result = $socialLinks->get_result();
                        $result = $result->fetch_assoc()['link_value'];
                    ?>
                    
                    <label>Youtube: </label>
                    <input type="text" name="youtube" value="<?php echo $result; ?>">
                </p>
            </form>
            
            <form id="socialLinks">
                <p>
                    <?php 
                        $socialName = 'Instagram'; 
                        $socialLinks->bind_param('s', $socialName);
                        $socialLinks->execute();
                        $result = $socialLinks->get_result();
                        $result = $result->fetch_assoc()['link_value'];
                    ?>
                    
                    <label>Instagram: </label>
                    <input type="text" name="instagram" value="<?php echo $result; ?>">
                </p>
                
                <p>
                    <?php 
                        $socialName = 'Linkedin'; 
                        $socialLinks->bind_param('s', $socialName);
                        $socialLinks->execute();
                        $result = $socialLinks->get_result();
                        $result = $result->fetch_assoc()['link_value'];
                    ?>
                    
                    <label>LinkedIn: </label>
                    <input type="text" name="linkedin" value="<?php echo $result; ?>">
                </p>
                
                <p>
                    <?php $socialLinks->close(); ?>
                    
                    <input type="submit" value="Update Links">
                </p>
                
                <p class="message"></p>
            </form>
        </div>
    </div>

    <script src="scripts/companyInfo.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>