    </div>

    <footer>
        <div class="footerInner">
            <div class="left">
                <?php $company = $mysqli->query("SELECT * FROM `company_info`"); ?>
                <?php $details = $company->fetch_assoc(); ?>
                
                <address>
                    <strong>
                        <?php echo ($details['company_name'] ? $details['company_name'] . '<br>' : ''); ?>
                    </strong>
                    
                    <?php 
                        echo ($details['address_1'] ? $details['address_1'] . '<br>' : '');
                        echo ($details['address_2'] ? $details['address_2'] . '<br>' : '');
                        echo ($details['address_3'] ? $details['address_3'] . '<br>' : ''); 
                        echo ($details['address_4'] ? $details['address_4'] . '<br>' : ''); 
                        echo ($details['postcode'] ? $details['postcode'] . '<br>' : '');
                        echo ($details['country'] ? $details['country'] . '<br>' : '');
                    ?>
                </address>
            </div>
            
            <div class="middle">
                <div class="contact">
                    <?php 
                        echo ($details['company_name'] ? '<span class="phone"><strong>Phone: </strong>' . $details['phone'] . '</span><br>' : ''); 
                        echo ($details['company_name'] ? '<span class="fax"><strong>Fax: </strong>' . $details['fax'] . '</span><br>' : '');
                        echo ($details['company_name'] ? '<span class="email"><strong>Email: </strong>' . $details['email'] . '</span><br>' : ''); 
                    ?>
                </div>
            </div>
            
            <div class="right">
                <div class="companyNums">
                    <?php 
                        echo ($details['company_name'] ? '<span class="vat"><strong>VAT Number: </strong>' . $details['vat_number'] . '</span><br>' : ''); 
                        echo ($details['company_name'] ? '<span class="reg"><strong>Reg Number: </strong>' . $details['reg_number'] . '</span><br>' : '');
                    ?>
                </div>
                
                <?php 
                    $socialCheck = $mysqli->query("SELECT COUNT(*) FROM `social_links` WHERE link_value IS NOT NULL AND link_value <> ''")->fetch_array()[0]; 
                    $socialLinks = $mysqli->query("SELECT * FROM `social_links` WHERE link_value IS NOT NULL AND link_value <> ''");
                ?>
                
                <?php if($socialCheck > 0) : ?>
                    <div class="socialLinks">
                        <?php while($link = $socialLinks->fetch_assoc()) : ?>
                            <?php if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/images/social/' . strtolower($link['link_name']) . '.png')) : ?>
                                <a href="<?php echo $link['link_value']; ?>" target="_blank">
                                    <img src="/admin/images/social/<?php echo strtolower($link['link_name']); ?>.png">
                                </a>
                            <?php else : ?>
                                <a href="<?php echo $link['link_value']; ?>" target="_blank">
                                    <span><?php echo $link['link_name']; ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footerAfter">
            <div class="footerAfterInner">
                <address>&copy; Copyright <?php echo date('Y'); ?></address>
            </div>
        </div>
    </footer>

    <script src="/scripts/functions.js"></script>
</html>