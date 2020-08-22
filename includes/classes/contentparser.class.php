<?php

	class shortcode {
		protected $shortoutput;
		
		protected function contactform($id) {
			$mysqli = $GLOBALS['mysqli'];			
			
            if(isset($id)) {			
				$form = $mysqli->prepare("SELECT * FROM `contact_forms` WHERE id = ?");
				$form->bind_param('i', $id);
				$form->execute();
				$result = $form->get_result();

				if($result->num_rows > 0) {
					$form = $result->fetch_assoc();
					$json = json_decode($form['structure'], true);
					
					if(!empty($json)) {
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
												<input type="radio" class="form-check-input' . ($input['label'] == '' || $input['label'] == null ? ' position-static' : '') .'" name="' . $index . '__' . $input['label'] . '"' . ($input['required'] == true ? ' required' : '') . ($i == 0 ? ' checked' : '') . '>' .
												($option['label'] != '' && $option['label'] != null ? '<label class="form-check-label" for="' . $ind . '__' . $option['label'] . '">' . $option['label'] . ($input['required'] == true ? '<sup class="text-danger">*</sup>': '') . '</label>' : '') .
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
									});
								</script>
							</form>';

						return $this->shortoutput;
					}
				}
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
			
			$this->components = preg_split('/[\[.*\]]/', $this->content, -1, PREG_SPLIT_DELIM_CAPTURE);

			foreach($this->components as $index => $component) {
				if(strpos($component, 'insert=') === 0) {
					$shortcode = explode(',', $component);
                    
                    foreach($shortcode as $parameter) {
                        $values = preg_split('/[(.*)\=\"(.*)\"]/', $parameter, -1, PREG_SPLIT_NO_EMPTY);
                        $this->method[$values[0]] = $values[1];
						
						$function = $this->method['insert'];
                    
						if(method_exists($this, $function) === true) {
							$this->output .= $this->$function($this->method['id']);
						}
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