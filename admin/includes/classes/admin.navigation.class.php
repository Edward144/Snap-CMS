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
                    '<ul class="navigationTree ' . ($parent == 0 ? 'p-0' : 'mt-2') .'" id="parent' . $parent . '" style="list-style: none;">';

                while($row = $menu->fetch_assoc()) {
                    $output .=
                        '<li id="navigation' . $row['id'] .'" class="mb-2">
                            <div class="navigationDetails bg-light py-2 px-3 d-flex align-items-center">
                                <div>' . $row['name'] . '</div>

                                <div class="actions ml-auto">
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

                                    <div class="form-group mb-0">
                                        <input type="button" class="btn btn-primary" id="navigation' . $row['id'] . '" name="edit" value="Edit">
                                        <input type="button" class="btn btn-danger" id="navigation' . $row['id'] . '" name="delete" value="Delete">
                                    </div>
                                </div>
                            </div>
                            ' . $this->createTree($row['id']) . '
                        </li>';
                }

                $output .=
                    '</ul>';
            }
            elseif($parent == 0 && $menu->num_rows == 0) {
                $output = '<h3 class="alert alert-info mt-3">There are no menu items</h3>';
            }
            else {
                $output .=
                    '<ul class="navigationTree p-0" id="parent' . $parent . '"></ul>';
            }

            return $output;
        }

        public function __destruct() {
            echo $this->createTree() . '<div class="form-group d-flex align-items-center"><input type="button" class="btn btn-primary" name="saveTree" value="Save Tree"></div>';
        }
    }

?>