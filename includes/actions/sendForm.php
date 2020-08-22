<?php

	require_once('../database.php');

	if(isset($_POST['formId'])) {
		$allowsend = false;
		
		$contactForm = $mysqli->prepare("SELECT * FROM `contact_forms` WHERE id = ?");
		$contactForm->bind_param('i', $_POST['formId']);
		$contactForm->execute();
		$result = $contactForm->get_result();
		
		if($result->num_rows > 0) {
			$contactForm = $result->fetch_assoc();
			
			if(isset($_POST['g-recaptcha-response'])) {
				$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $contactForm['secretkey'] . '&response=' . $_POST['g-recaptcha-response'] . '&remoteid=' . $_SERVER['REMOTE_ADDR']);
				$response = json_decode($response);
				
				if($response->success == true && $response->score >= 0.5) {
					$allowsend = true;
				}
			}
			else {
				$allowsend = true;
			}
			
			if($allowsend == true) {
				$json = json_decode($contactForm['structure'], true);
				$output = '';

				foreach($_POST as $index => $postItem) {
					if(strpos($index, '__') !== false) {
						$output .=
							'<span><strong>' . str_replace('_', ' ', explode('__', $index)[1]) . ': </strong>' . $postItem . '</span><br>';
					} 
				}

				$to = implode(',', $json['emails']);
				$subject = (!empty($contactForm['subject']) ? $contactForm['subject'] : 'Contact Form ' . $contactForm['id'] . ': New message from ' . $_SERVER['SERVER_NAME']);
				echo $message = 
					'<p>Hi, </p>
					<p>You have received a new message from ' . (!empty($contactForm['name']) ? $contactForm['name'] : 'contact form ' . $contactForm['id']) . '.</p>
					<hr>
					<div class="formContent">'
						. $output . 
					'</div>';
				$headers  = 'From: noreply@' . $_SERVER['SERVER_NAME'] . "\r\n";
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-Type: text/html; charset=UTF-8';

				if(!mail($to, $subject, $message, $headers, '-fnoreply@' . $_SERVER['SERVER_NAME'])) {
					$_SESSION['contactmessage'] =  'Your message could not be sent, please try again later.';
					$_SESSION['contactstatus'] = 0;
				}
				else {
					$_SESSION['contactmessage'] = 'Thank you, your message has been sent.';
					$_SESSION['contactstatus'] = 1;
				}
			}
			else {
				$_SESSION['contactmessage'] =  'Captcha failed, please try again.';
				$_SESSION['contactstatus'] = 0;
			}
		}
	}
						
	header('Location: ' . $_POST['returnurl']);
	exit();

?>