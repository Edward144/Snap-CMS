<?php http_response_code(404); require_once('includes/header.php'); ?>

<div class="container-fluid d-flex flex-grow-1">                    
    <div class="row flex-grow-1">
        <div class="col py-3">
            <div class="jumbotron py-4 bg-light">
                <h1>This page does not exist</h1>
                <br>                
                <a href="admin">Return to the dashboard</a> or 
                <a href="javascript:history.back()">Go back to your previous page</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('includes/footer.php'); ?>