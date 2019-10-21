<?php

    class navigation {
        public $menuId;
        public $parentId;
        public $hasToggle = true;
        private $output;
        
        public function __construct($menuId = 0, $parentId = 0) {
            if($menuId <= 0) {
                $this->menuId = 0;
            }
            else {
                $this->parentId = $parentId;
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
                $this->createLevel();
            
            $this->output .=
                '</nav>';
            
            echo $this->output;
        }
        
        private function createLevel($menuId = 0, $parentId = 0) {
            $mysqli = $GLOBALS['mysqli'];
            
            $items = $mysqli->query("SELECT * FROM `navigation_structure` WHERE menu_id = {$menuId} AND parent_id = {$parentId}");
            
            if($items->num_rows > 0) :
                $output .= 
                    '<ul>';
                
                while($item = $items->fetch_assoc()) : 
                    $checkChildren = $mysqli->query("SELECT id FROM `navigation_structure` WHERE menu_id = {$menuId} AND parent_id = {$item['id']}")->num_rows;
            
                    $output .=
                        '<li class="item' . $item['id'] . ' ' .($checkChildren > 0 ? 'hasChildren' : '') . '">
                            <a href="' . $item['url'] . '" id="' . ($_SERVER['REQUEST_URI'] == $item['url'] ? 'active' : '') . '">' . $item['name'] . '</a>';
                
                        if($checkChildren > 0) : 
                            $output .=
                                '<div class="itemInner">' .
                                    $this->createLevel($this->menuId, $item['id']) .
                                '</div>';
                        endif; 
                    
                    $output .=    
                        '</li>';
                endwhile;
            
                $output .= 
                    '</ul>';
            endif;
            
            return $output;
        }
    }

?>