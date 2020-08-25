<?php

	class shortcode {
		protected $shortoutput;
		
		protected function contactform($params) {
			$mysqli = $GLOBALS['mysqli'];
			
            if(isset($params['id'])) {			
				$id = $params['id'];
				
				$form = $mysqli->prepare("SELECT * FROM `contact_forms` WHERE id = ?");
				$form->bind_param('i', $id);
				$form->execute();
				$result = $form->get_result();

				if($result->num_rows > 0) {
					$form = $result->fetch_assoc();
					$json = json_decode($form['structure'], true);
					
					if(!empty($json['inputs'])) {
						$this->shortoutput = 
							'<form id="contactForm' . $id . '" action="' . ROOT_DIR . 'includes/actions/sendForm.php" method="post">
								<input type="hidden" name="formId" value="' . $id . '">
								<input type="hidden" name="returnurl" value="' . $_SERVER['REQUEST_URI'] . '">';
						
						foreach($json['inputs'] as $index => $input) {
							$this->shortoutput .= 
								'<div class="form-group">';

							if($input['type'] != 'checkbox' && $input['type'] != 'radio' && $input['hidelabel'] == false) {
								$this->shortoutput .= 
									($input['label'] != '' && $input['label'] != null ? '<label for="' . $index . '__' . $input['label'] . '">' . $input['label'] . ($input['required'] == true ? '<sup class="text-danger">*</sup>': '') . '</label>' : '');
							}

							switch($input['type']) {
								case 'general': 
									$this->shortoutput .=
										'<p>' . str_replace(PHP_EOL, '</p><p>', $input['value']) . '</p>';
									break;
								case 'select': 
									$this->shortoutput .= 
										'<select class="form-control" name="' . $index . '__' . $input['label'] . '"' . ($input['required'] == true ? ' required' : '') . '>';

									foreach($input['options'] as $option) {
										$this->shortoutput .= 
											'<option value="' . $option . '">' . $option . '</option>';
									}

									$this->shortoutput .= 
										'</select>';
									break;
								case 'radio':
									$i = 0;

									foreach($input['options'] as $ind => $option) {
										$this->shortoutput .=
											'<div class="form-check">
												<input type="radio" class="form-check-input' . ($input['label'] == '' || $input['label'] == null ? ' position-static' : '') .'" name="' . $index . '__' . $input['label'] . '" value="' . $option . '"' . ($input['required'] == true ? ' required' : '') . ($i == 0 ? ' checked' : '') . '>' .
												($option != '' && $option != null ? '<label class="form-check-label" for="' . $ind . '__' . $option . '">' . $option . ($input['required'] == true ? '<sup class="text-danger">*</sup>': '') . '</label>' : '') .
											'</div>';
										$i++;
									}

									break;
								case 'checkbox':
									$this->shortoutput .= 
										'<div class="form-check">
											<input type="checkbox" class="form-check-input' . ($input['label'] == '' || $input['label'] == null ? ' position-static' : '') . '" name="' . $index . '__' . $input['label'] . '"' . ($input['required'] == true ? ' required' : '') . '>' .
											($input['label'] != '' && $input['label'] != null && $input['hidelabel'] == false ? '<label class="form-check-label" for="' . $index . '__' . $input['label'] . '">' . $input['label'] . ($input['required'] == true ? '<sup class="text-danger">*</sup>': '') . '</label>' : '') .
										'</div>';
									break;
								case 'textarea': 
									$this->shortoutput .= 
										'<textarea class="form-control" name="' . $index . '__' . $input['label'] . '" placeholder="' . $input['placeholder'] . '"'. ($input['required'] == true ? ' required' : '') . '></textarea>';
									break;
								case 'file':
									$this->shortoutput .= 
										'<input type="file" class="form-control-file" name="' . $index . '__' . $input['label'] . '"' . ($input['multiple'] == true ? ' multiple' : '') . ($input['required'] == true ? ' required' : '') . '>';
									break;
								default:
									$numberAttrs = 
										(isset($input['min']) ? ' min="' . $input['min'] . '"' : '') .
										(isset($input['max']) ? ' max="' . $input['max'] . '"' : '') .
										(isset($input['step']) ? ' step="' . $input['step'] . '"' : '');

									$this->shortoutput .= 
										'<input type="' . $input['type'] . '" class="form-control" placeholder="' . $input['placeholder'] . '" name="' . $index . '__' . $input['label'] .  '"' . $numberAttrs . ($input['required'] == true ? ' required' : '') . '>';
									break;
							}

							$this->shortoutput .= 
								'</div>';
						}

						$this->shortoutput .= 
								'<div class="form-group d-flex align-items-center">
									<input type="submit" class="btn btn-primary" name="submitForm' . $id . '" value="Submit">
								</div>';
							
						if(isset($_SESSION['contactmessage']) && isset($_SESSION['contactstatus'])) {
							$this->shortoutput .=
								'<div class="alert alert-' . ($_SESSION['contactstatus'] == 1 ? 'success' : 'danger') . '">'
									. $_SESSION['contactmessage'] .
								'</div>';
							
							unset($_SESSION['contactmessage']);
							unset($_SESSION['contactstatus']);
						}
						
						if(!empty($form['sitekey']) && !empty($form['secretkey'])) {
							$this->shortoutput .=
								'<input type="hidden" id="g-recaptcha-response-' . $id . '" name="g-recaptcha-response">
								<input type="hidden" name="action" value="validate_captcha">
								<script src="https://www.google.com/recaptcha/api.js?render=' . $form['sitekey'] . '"></script>
								<script>
									grecaptcha.ready(function() {
										grecaptcha.execute(\'' . $form['sitekey'] . '\', {action:\'validate_captcha\'})
												  .then(function(token) {
											document.getElementById(\'g-recaptcha-response-' . $id . '\').value = token;
										});
									});
								</script>';
						}
						
						$this->shortoutput .=
								'<script>
									$("#contactForm' . $id . '").submit(function() {										
										var checkboxes = $(this).find("input[type=\'checkbox\']");
										$("#hiddenCheck").remove();
										
										checkboxes.each(function() {
											if(!$(this).is(":checked")) {
												$("<input id=\'hiddenCheck\' type=\'hidden\' name=\'" + $(this).attr("name") + "\' value=\'off\'>").insertAfter($(this));
											}
										});
										
										$(this).find(":submit").prop("disabled", true);
										$("<div class=\'spinner-border ml-1\'><span class=\'sr-only\'>Processing...</span></div>").insertAfter($(this).find(\':submit\'));
									});
								</script>
							</form>';

						return $this->shortoutput;
					}
				}
        	}
        }
		
		protected function customfile($params) {
			if(isset($params['path'])) {
				$path = $params['path'];
				$transit = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
				
				return file_get_contents($transit . '://' . $_SERVER['SERVER_NAME'] . ROOT_DIR . $path);
			}
		}
		
		protected function googlemap($params) {
			if(isset($params['api'])) {
				$api = $params['api'];
				$lat = (isset($params['lat']) && is_numeric($params['lat']) ? $params['lat'] : 0);
				$lng = (isset($params['lat']) && is_numeric($params['lng']) ? $params['lng'] : 0);
				$zoom = (isset($params['zoom']) && is_numeric($params['zoom']) && $params['zoom'] >= 0 ? $params['zoom'] : 12);
				$unique = rtrim(base64_encode(time()), '=');
			
				$height = (isset($params['h'])  ? $params['h'] : '200px');
				$width = (isset($params['w'])  ? $params['w'] : '100%');
				
				$this->shortoutput = 
					'<div class="googleMap" id="googleMap' . $unique . '" style="height: ' . $height . '; width: ' . $width . ';"></div>

					<script>
						function initMap() {
						var location = {lat: ' . $lat . ', lng: ' . $lng . '};
						var map = new google.maps.Map(
						  document.getElementById(\'googleMap' . $unique . '\'), {zoom: ' . $zoom . ', center: location});
						var marker = new google.maps.Marker({position: location, map: map});
						}
					</script>

					<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $api . '&callback=initMap"></script>';
				
				return $this->shortoutput;
			}
		}
	}

	class parseContent extends shortcode {
		private $content;
		private $output = '';
		private $components;
		private $method = [];
		
		public function __construct($content) {			
			$this->content = (isset($content) ? $this->content = $content : '');
			
			$this->components = preg_split('/[\[*\]]/', $this->content, -1, PREG_SPLIT_DELIM_CAPTURE);

			foreach($this->components as $index => $component) {
				if(strpos($component, 'insert=') === 0) {
					$shortcode = explode(',', $component);
					$params = [];
					$this->method = [];
					
                    foreach($shortcode as $parameter) {
                        $values = preg_split('/[(*)\=\"(*)\"]/', $parameter, -1, PREG_SPLIT_NO_EMPTY);
                        $this->method[$values[0]] = $values[1];
						
						$function = $this->method['insert'];
                    }
					
					for($i = 1; $i < count($this->method); $i++) {
						$param = array_slice($this->method, $i);
						$params[key($param)] = $param[key($param)];
					}
					
					if(method_exists($this, $function) === true) {
						$this->output .= $this->$function($params);
					}
				}
				else {
					$this->output .= $component;
				}
			}
		}
		
		public function __toString() {
			return $this->output;
		}
	}
		
?>