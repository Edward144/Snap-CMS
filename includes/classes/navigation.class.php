<?php

    class navbar {
        public $menuId;
        public $parentId;
        public $hasToggle = true;
        public $hasImages = false;
        private $output;
        
        public function __construct($menuId = 0, $parentId = 0) {
            if($menuId <= 0) {
                $this->menuId = 0;
            }
            else {
                $this->menuId = $menuId;
            }
            
            if($parentId <= 0) {
                $this->parentId = 0;
            }
            else {
                $this->parentId = $parentId;
            }
        }
        
        public function display() {
			$mysqli = $GLOBALS['mysqli'];
			$checkMenu = $mysqli->query("SELECT COUNT(*) FROM `navigation_structure` WHERE menu_id = {$this->menuId}")->fetch_array()[0];
			
			if($checkMenu <= 0) {
				return ;
			}
			else {
				$this->output =
					'<nav class="navbar navbar-expand-lg navbar-dark" id="menu' . $this->menuId . '">';

				if($this->hasToggle == true) {
					$this->output .= 
						'<a class="navbar-brand d-lg-none d-block">Navigation</a>
						<button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbar' . $this->menuId . '" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>';
				}

				$this->output .=
					'<div class="collapse navbar-collapse" id="navbar' . $this->menuId . '">';

				$this->output .=
					$this->createLevel($this->menuId, $this->parentId);

				$this->output .=
						'</div>
					</nav>';

				echo $this->output;
			}
        }
        
        private function createLevel($menuId = 0, $parentId = 0) {
            $mysqli = $GLOBALS['mysqli'];
			
            $items = $mysqli->query("SELECT * FROM `navigation_structure` WHERE menu_id = {$menuId} AND parent_id = {$parentId} ORDER BY position");
			
            if($items->num_rows > 0) :   
				$output .=
					'<ul class="navbar-nav mr-auto bg-dark">';
			
                while($item = $items->fetch_assoc()) :             
                    $checkChildren = $mysqli->query("SELECT id FROM `navigation_structure` WHERE menu_id = {$menuId} AND parent_id = {$item['id']}")->num_rows;
			
					$output .=
						'<li class="nav-item' . ($checkChildren > 0 ? ' hasChildren' : '') . '">
							<a class="nav-link text-light" href="' . $item['url'] . '">' . $item['name'] . ($checkChildren > 0 ? '<span class="ml-2 d-none d-lg-inline-block fa fa-caret-left"></span>' : '') . '</a>';

					if($checkChildren > 0) :
						$output .= $this->createLevel($this->menuId, $item['id']);
					endif;
					
					$output .=
						'</li>';
                endwhile;
			
				$this->output .=
					'</ul>';
            endif;
            
            return $output;
        }
    }

?>
