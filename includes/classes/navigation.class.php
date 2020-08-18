<?php

    class navigation {
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
            if($this->hasToggle == true) {
                $this->output = '<div class="navToggle" id="hidden"></div>';
            }
            
            $this->output .=
                '<nav class="navigation" id="menu' . $this->menuId . '">';
            
            $this->output .=
                $this->createLevel($this->menuId, $this->parentId);
            
            $this->output .=
                '</nav>';
            
            echo $this->output;
        }
        
        private function createLevel($menuId = 0, $parentId = 0) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `navigation_structure` WHERE menu_id = {$menuId} AND parent_id = {$parentId} ORDER BY position");
            $itemUrl = explode('?page=', $_SERVER['REQUEST_URI'])[0];
            $itemUrl = explode('?category=', $itemUrl)[0];
            $count = 1;
			
            if($items->num_rows > 0) :
                $output .= 
                    '<ul class="flex-column nav border-top border-dark' . ($parentId == 0 ? '  border-bottom' : '') . '">';
                
                while($item = $items->fetch_assoc()) :             
                    $checkChildren = $mysqli->query("SELECT id FROM `navigation_structure` WHERE menu_id = {$menuId} AND parent_id = {$item['id']}")->num_rows;
            
                    $output .=
                        '<li class="nav-item' . $item['id'] . ' ' . ($count < $items->num_rows ? 'border-bottom border-dark' : '') . ' ' . ($checkChildren > 0 ? 'hasChildren' : '') . ' ' . ($this->hasImages == true ? 'hasImages' : '') . '">
							<a class="nav-link btn btn-light rounded-0 ' . ($checkChildren > 0 ? 'd-inline-block' : '') . '" href="' . (strpos($item['url'], 'http') === false && $item['url'] != '#' ? ROOT_DIR . $item['url'] : $item['url']) . '" id="' . ($itemUrl == ROOT_DIR . $item['url'] ? 'active' : '') . '"><span>' . $item['name'] . '</span>' . ($checkChildren > 0 ? '' : '<span class="fa fa-link" style="margin-left: 18px"></span>') . '</a>' . 
							($checkChildren > 0 ? '<span class="btn btn-light rounded-0 d-inline-block" id="dropdown"><span class="h-100 d-flex align-items-center justify-content-center"><span class="fas fa-caret-left "></span></span></span>' : '');
                
                        if($checkChildren > 0) : 
                            $output .=
                                '<div class="itemInner">
                                    <div>';
                            
                            if($this->hasImages == true) : 
                                $output .=
                                    '<div class="navImage">' .
                                        ($item['image_url'] != null ? '<img src="' . $item['image_url'] .'" alt="' . $item['name'] .'">' : '') 
                                    . '</div>';
                            endif;
            
                            $output .=
                                        $this->createLevel($this->menuId, $item['id']) .
                                    '</div>
                                </div>';
                        endif; 
                    
                    $output .=    
                        '</li>';
			
					$count++;
                endwhile;
            
                $output .= 
                    '</ul>';
            endif;
            
            return $output;
        }
    }

?>
