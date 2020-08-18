<?php

    class categoryEditor {
        public $postTypeId;
        private $output;

        public function __construct($postTypeId = 1, $parent = 0) {
            if(isset($postTypeId) && $postTypeId > 0) {
                $this->postTypeId = $postTypeId;
            }
            else {
                $this->postTypeId = 1;
            }
        }

        private function createTree($parent = 0) {
            $mysqli = $GLOBALS['mysqli'];
            $output = '';

            $categories = $mysqli->query("SELECT * FROM `categories` WHERE post_type_id = {$this->postTypeId} AND parent_id = {$parent} ORDER BY name ASC");

            if($categories->num_rows > 0) {
                $output .=
                    '<ul class="categoryTree" id="parent' . $parent . '">';

                while($row = $categories->fetch_assoc()) {
                    $catCount = $mysqli->query("SELECT id FROM  `posts` WHERE post_type_id = {$this->postTypeId} AND category_id = {$row['id']}")->num_rows;

                    $output .=
                        '<li id="category' . $row['id'] .'">
                            <div class="categoryDetails">
                                ' . $row['name'] . ' <i style="color: #aaa;">(' . $catCount . ')</i>

                                <div class="actions formBlock">
                                    <div id="edit' . $row['id'] . '">
                                        <input type="hidden" name="hId" value="' . $row['id'] . '">
                                        <input type="hidden" name="hName" value="' . $row['name'] . '">
                                        <input type="hidden" name="hImage" value="' . $row['image_url'] . '">
                                        <input type="hidden" name="hDesc" value="' . $row['description'] . '">
                                        <input type="hidden" name="hParent" value="' . $row['parent_id'] . '">
                                        <input type="hidden" name="hDelete" value="0">
                                        <input type="hidden" name="hLevel" value="' . $row['level'] . '">
                                    </div>

                                    <p>
                                        <input type="button" id="category' . $row['id'] . '" name="edit" value="Edit">
                                        <input type="button" id="category' . $row['id'] . '" name="delete" value="Delete" class="redButton">
                                    </p>
                                </div>
                            </div>
                            ' . $this->createTree($row['id']) . '
                        </li>';
                }

                $output .=
                    '</ul>';
            }
            elseif($parent == 0 && $categories->num_rows == 0) {
                $output = '<h3 class="noContent">There are no categories</h3>';
            }

            return $output;
        }

        public function __destruct() {
            echo $this->createTree() . '<input type="button" name="saveTree" value="Save Tree"><p id="message" class="treemessage"></p>';
        }
    }

?>