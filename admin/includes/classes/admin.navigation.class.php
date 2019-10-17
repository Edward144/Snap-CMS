<?php

    class navigationEditor {
        public $menuId;
        private $output;

        public function __construct($menuId = 0, $parent = 0) {
            if(isset($menuId) && $menuId >= 0) {
                $this->menuId = $menuId;
            }
            else {
                $this->menuId = 1;
            }
        }

        private function createTree($parent = 0) {
            $mysqli = $GLOBALS['mysqli'];
            $output = '';

            $menu = $mysqli->query("SELECT * FROM `navigation_structure` WHERE menu_id = {$this->menuId} AND parent_id = {$parent} ORDER BY position, name ASC");

            if($menu->num_rows > 0) {
                $output .=
                    '<ul class="navigationTree" id="parent' . $parent . '">';

                while($row = $menu->fetch_assoc()) {
                    $output .=
                        '<li id="navigation' . $row['id'] .'">
                            <div class="navigationDetails">
                                ' . $row['name'] . '

                                <div class="actions formBlock">
                                    <div id="edit' . $row['id'] . '">
                                        <input type="hidden" name="hId" value="' . $row['id'] . '">
                                        <input type="hidden" name="hName" value="' . $row['name'] . '">
                                        <input type="hidden" name="hImage" value="' . $row['image_url'] . '">
                                        <input type="hidden" name="hParent" value="' . $row['parent_id'] . '">
                                        <input type="hidden" name="hSlug" value="' . $row['url'] . '">
                                        <input type="hidden" name="hLevel" value="' . $row['level'] . '">
                                        <input type="hidden" name="hPosition" value="' . $row['position'] . '">
                                        <input type="hidden" name="hDelete" value="0">
                                    </div>

                                    <p>
                                        <input type="button" id="navigation' . $row['id'] . '" name="edit" value="Edit">
                                        <input type="button" id="navigation' . $row['id'] . '" name="delete" value="Delete" class="redButton">
                                    </p>
                                </div>
                            </div>
                            ' . $this->createTree($row['id']) . '
                        </li>';
                }

                $output .=
                    '</ul>';
            }
            elseif($parent == 0 && $menu->num_rows == 0) {
                $output = '<h3 class="noContent">There are no menu items</h3>';
            }

            return $output;
        }

        public function __destruct() {
            echo $this->createTree() . '<input type="button" name="saveTree" value="Save Tree"><p id="message" class="treemessage"></p>';
        }
    }

?>