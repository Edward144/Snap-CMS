<?php require_once('includes/header.php'); ?>


<?php if(isset($_GET['id']) && $_GET['id'] > 0) : ?>
    <div class="flexContainer" id="sliderManager">
        <div class="column column-70 formBlock sliderContent">
            <h2 class="greyHeader">Slides</h2>

            <div>

            </div>
        </div>

        <div class="column column-30 formBlock sliderDetails">
            <h2 class="greyHeader">Slider Details</h2>

            <div>

            </div>
        </div>
    </div>
<?php else : ?>
    <div class="flexContainer">
        <div class="column column-30 formBlock sliderControls">
            <h2 class="greyHeader">Controls</h2>
            
            <div>
                <form id="createSlider" method="POST" action="scripts/createSlider.php">
                    <input type="submit" value="Create Slider">
                </form>
                
                <hr>
                
                <form id="searchSliders">
                    <p>
                        <label>Search Term</label>
                        <input type="text" name="search">
                    </p>
                    
                    <input type="submit" value="search">
                </form>
            </div>
        </div>
        
        <div class="column column-70 formBlock sliderList">
            <h2 class="greyHeader">Sliders</h2>
            
            <div>
                <!-- list sliders -->
            </div>
            
            <!--do pagination class-->
            
            <div class="pagination">
            
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once('includes/footer.php'); ?>