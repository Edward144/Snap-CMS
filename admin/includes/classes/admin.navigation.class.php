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
                    '<ul class="navigationTree ' . ($parent == 0 ? 'p-0' : 'mt-2') .'" id="parent' . $parent . '">';

                while($row = $menu->fetch_assoc()) {
                    $output .=
                        '<li id="navigation' . $row['id'] .'" class="mb-2">
                            <div class="navigationDetails bg-light py-2 px-3 d-flex align-items-center">
                                <div>' . $row['name'] . '</div>

                                <div class="actions ml-auto">
                                    <div id="edit" class="modal fade show" tab-index="-1" style="pointer-events: none;">
                                        <div class="modal-dialog modal-dialog-centered" style="pointer-events: none;">
                                            <div class="modal-content" style="pointer-events: auto;">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit ' . $row['name'] . '</h5>

                                                    <button type="button" class="close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                
                                                <div class="modal-body">
                                                    <form id="editItem">
                                                        <input type="hidden" name="hId" value="' . $row['id'] . '">
                                                        <input type="hidden" name="hParent" value="' . $row['parent_id'] . '">
                                                        <input type="hidden" name="hLevel" value="' . $row['level'] . '">
                                                        <input type="hidden" name="hPosition" value="' . $row['position'] . '">
                                                        <input type="hidden" name="hDelete" value="0">
                                                        
                                                        <div class="form-group">
                                                            <small class="text-muted">After making changes, close the menu. Your changes will be stored and save upon clicking the "Save Tree" button at the bottom of the navigation tree.</small>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label>Name</label>
                                                            <input type="text" class="form-control" name="hName" value="' . $row['name'] . '">
                                                        </div class="form-group">
                                                        
                                                        <div class="form-group">
                                                            <label>URL Slug</label>
                                                            <input type="text" class="form-control" name="hUrl" value="' . $row['url'] . '">
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label>Icon' . ($row['icon'] != null && $row['icon'] != '' ? ' <span class="iconExample ' . $row['icon'] . '"></span>' : '') . '</label>
                                                            <input type="text" class="form-control" name="hIcon" value="' . $row['icon'] . '">
                                                            <small class="text-muted">Enter the code for any free icon from <a href="https://fontawesome.com/icons?d=gallery&q=image&m=free" target="_blank">Font-Awesome</a> e.g. <code>fa fa-image</code></small>
                                                        </div>
                                                        
                                                        <div class="form-group imageUrl">
                                                            <label>Image</label>
                                                            <input type="hidden" name="hImage" value="' . $row['image_url'] . '">

                                                            <div class="clearfix mt-2"></div>
                                                            <input type="button" class="btn btn-info mr-2" name="selectImage" value="Choose Image">
                                                            <input type="button" class="btn btn-secondary mt-2 mt-sm-0" name="clearImage" value="Remove Image" style="display: none;">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <input type="button" class="btn btn-primary" name="edit" value="Edit">
                                        <input type="button" class="btn btn-danger" name="delete" value="Delete">
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
                    '<ul class="navigationTree mt-2" id="parent' . $parent . '"></ul>';
            }

            return $output;
        }

        public function __destruct() {
            echo $this->createTree() . '<div class="form-group d-flex align-items-center"><input type="button" class="btn btn-primary" name="saveTree" data-menu="' . $this->menuId . '" value="Save Tree"></div>';
        }
    }

?>