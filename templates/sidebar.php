<div class="sidebar">
    <div class="sidebarInner">
        <?php 
            $uri = $_SERVER['REQUEST_URI'];
            $homepage = $mysqli->query("SELECT setting_value FROM `settings` WHERE setting_name = 'homepage'")->fetch_array()[0];
        
            if(strpos($uri, '?') !== false) {
                $uri = explode('?', $uri)[0];
            }
        
            if($uri == '/posts' || $homepage == '' || $homepage == null) {
                $categories = new categories();
                
                if(isset($_GET['category'])) {
                    $categories->sidebar($_GET['category']);
                }
                else {
                    $categories->sidebar();
                }
            }
        ?>
    </div>
</div>