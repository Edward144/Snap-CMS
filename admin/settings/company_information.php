<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <h1><?php adminTitle(); ?></h1>
        
        <div class="formBlock" id="companyInfo">
            <?php
                $checkCompany = $mysqli->query("SELECT * FROM `company_info");
            
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
    </div>

    <script src="scripts/companyInfo.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>