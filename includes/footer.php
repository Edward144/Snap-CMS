        </div>

        <footer id="footer">
            <div class="mainFooter">
                <?php 
                    $companyInfo = $mysqli->query("SELECT * FROM `company_info` LIMIT 1"); 
                    $social = $mysqli->query("SELECT * FROM `social_links`"); 
                    
                    if($companyInfo->num_rows > 0) : 
                        $company = $companyInfo->fetch_assoc();
                ?>
                    <div>
                        <address class="companyAddress">
                        <?php 
                            echo ($company['name'] != null ? '<strong>' . $company['name'] . '</strong><br>' : '') .
                                 ($company['address_1'] != null ? '<span>' . $company['address_1'] . '</span><br>' : '') .
                                 ($company['address_2'] != null ? '<span>' . $company['address_2'] . '</span><br>' : '') .
                                 ($company['address_3'] != null ? '<span>' . $company['address_3'] . '</span><br>' : '') .
                                 ($company['address_4'] != null ? '<span>' . $company['address_4'] . '</span><br>' : '') . 
                                 ($company['county'] != null ? '<span>' . $company['county'] . '</span><br>' : '') . 
                                 ($company['postcode'] != null ? '<span>' . $company['postcode'] . '</span><br>' : '') . 
                                 ($company['country'] != null ? '<span>' . $company['country'] . '</span>' : '');
                        ?>
                        </address>
                    </div>

                    <div>
                        <p class="contactDetails">
                        <?php
                            echo ($company['phone'] != null ? '<strong>Phone: </strong><span><a href="Tel: ' . $company['phone'] . '" target="_blank">' . $company['phone'] . '</a></span><br>' : '') .
                                 ($company['fax'] != null ? '<strong>Fax: </strong><span><a href="Tel: ' . $company['fax'] . '" target="_blank">' . $company['fax'] . '</a></span><br>' : '') .
                                 ($company['email'] != null ? '<strong>Email: </strong><span><a href="MailTo: ' . $company['email'] . '" target="_blank">' . $company['email'] . '</a></span>' : '');
                        ?>
                        </p>
                        
                        <p class="companyDetails">
                            <?php
                                echo ($company['vat_number'] != null ? '<strong>VAT Number: </strong><span>' . $company['vat_number'] . '</span><br>' : '') . 
                                     ($company['registration_number'] != null ? '<strong>Reg Number: </strong><span>' . $company['registration_number'] . '</span>' : '');
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if($social->num_rows > 0) : ?>
                    <div>
                        <ul class="socialLinks">
                            <?php while($link = $social->fetch_assoc()) : ?>
                                <?php if($link['url'] != null && $link['url'] != '') : ?>
                                    <li><a href="<?php echo $link['url']; ?>" target="_blank"><img src="<?php echo ROOT_DIR; ?>admin/images/social/<?php echo strtolower($link['name']) . '.png'; ?>" alt="<?php echo $link['name']; ?>"></a></li>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="afterFooter">
                <div>
                    <address>&copy; <?php echo date('Y') . ' ' . ($companyInfo->num_rows > 0 && $company['name'] != null ? $company['name'] : 'Snap CMS'); ?></address>
                </div>
            </div>
        </footer>

        <script src="<?php echo ROOT_DIR; ?>admin/scripts/docRoot.js"></script>
        <script src="<?php echo ROOT_DIR; ?>scripts/default.js"></script>
        <script src="<?php echo ROOT_DIR; ?>scripts/retina.min.js.js"></script>
    </body>
</html>
