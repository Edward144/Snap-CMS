			</div>

			<footer id="footer" class="container-fluid bg-secondary text-light">
				<div class="footerInner container-xl">
					<div class="row py-3">
						<?php
							$address = (!empty($companyDetails['name']) ? '<strong>' . $companyDetails['name'] . '</strong><br>' : '') .
										(!empty($companyDetails['address_1']) ? $companyDetails['address_1'] . '<br>' : '') .
										(!empty($companyDetails['address_2']) ? $companyDetails['address_2'] . '<br>' : '') .
										(!empty($companyDetails['address_3']) ? $companyDetails['address_3'] . '<br>' : '') .
										(!empty($companyDetails['address_4']) ? $companyDetails['address_4'] . '<br>' : '') .
										(!empty($companyDetails['county']) ? $companyDetails['county'] . '<br>' : '') .
										(!empty($companyDetails['postcode']) ? $companyDetails['postcode'] : '');
						
							$contact = (!empty($companyDetails['phone']) ? '<span class="phone d-block"><span class="fa fa-phone mr-1"></span><a class="text-light" href="tel: ' . 				$companyDetails['phone'] . '">' . $companyDetails['phone'] . '</a></span>' : '') .
										(!empty($companyDetails['email']) ? '<span class="email d-block"><span class="fa fa-envelope mr-1"></span><a class="text-light" href="mailto: ' . $companyDetails['email'] . '">' . $companyDetails['email'] . '</a></span>' : '') .
										(!empty($companyDetails['fax']) ? '<span class="fax d-block"><span class="fa fa-fax mr-1"></span><a class="text-light" href="tel: ' . $companyDetails['fax'] . '">' . $companyDetails['fax'] . '</a></span>' : '');
						?>
						
						<?php if(!empty($address) || !empty($contact)) : ?>
							<div class="col-md">
								<address class="siteAddress"><?php echo $address; ?></address>
								
								<?php echo $contact; ?>
							</div>
						<?php endif; ?>
						
						<?php if(!empty($companyDetails['vat_number']) || !empty($companyDetails['registration_number'])) : ?>
							<div class="col-md">
								<?php 
									echo (!empty($companyDetails['vat_number']) ? '<span class="vatNum d-block">VAT Number: ' . $companyDetails['vat_number'] . '</span>' : '') .
										 (!empty($companyDetails['registration_number']) ? '<span class="regNum d-block">Registration Number: ' . $companyDetails['registration_number'] . '</span>' : '');
								?>
							</div>
						<?php endif; ?>
						
						<?php $socials = $mysqli->query("SELECT * FROM `social_links` WHERE url <> '' AND url IS NOT NULL"); ?>
						
						<?php if($socials->num_rows > 0) : ?>
							<div class="col-md">
								<ul class="socialLinks nav d-flex justify-content-end align-items-center">
									<?php while($social = $socials->fetch_assoc()) : ?>
										<li class="ml-2"><a class="text-light h2" href="<?php echo $social['url']; ?>" target="_blank"><span class="fab fa-<?php echo $social['name']; ?>"></span></a></li>
									<?php endwhile; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</footer>

			<div class="footerAfter container-fluid bg-dark text-white">
				<div class="container-xl">
					<div class="row py-">
						<div class="col">
							<span class="copyright small text-center d-block">&copy; <?php echo date('Y') . (!empty($companyDetails['name']) ? ' ' . $companyDetails['name'] : ''); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
    </body>
</html>