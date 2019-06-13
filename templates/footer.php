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
            </div>
        </div>
        
        <div class="footerAfter">
            <div class="footerAfterInner">
                <address>&copy; Copyright <?php echo date('Y'); ?></address>
            </div>
        </div>
    </footer>

    <script src="/scripts/main.js"></script>
</html>