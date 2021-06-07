<?php require_once('includes/header.php'); ?>

<div class="container-fluid d-block d-xl-flex h-100">                    
    <div class="row flex-grow-1">
        <div class="col-xl-4 bg-light">
            <h2 class="py-2">Create New User</h2>
            
            <form id="createUser" action="admin/scripts/manageUsers.php" method="post">
                <input type="hidden" name="method" value="createUser">
                <input type="hidden" name="returnUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" name="firstName" value="<?php echo (isset($_SESSION['firstName']) ? $_SESSION['firstName'] : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" name="lastName" value="<?php echo (isset($_SESSION['lastName']) ? $_SESSION['lastName'] : ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" name="email" value="<?php echo (isset($_SESSION['email']) ? $_SESSION['email'] : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" value="<?php echo (isset($_SESSION['username']) ? $_SESSION['username'] : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" value="<?php echo (isset($_SESSION['password']) ? $_SESSION['password'] : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="passwordConf">Confirm Password</label>
                    <input type="password" class="form-control" name="passwordConf" value="<?php echo (isset($_SESSION['password']) ? $_SESSION['password'] : ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <input type="button" class="btn btn-secondary" name="generatePassword" value="Generate Password">
                </div>
                
                <div class="form-group d-flex align-items-center">
                    
                    <input type="submit" class="btn btn-primary" value="Create User">
                </div>
            </form>
            
            <?php if(isset($_SESSION['message'])) : ?>
                <div class="alert <?php echo ($_SESSION['message'][0] == 0 ? 'alert-danger' : 'alert-success'); ?>"><?php echo $_SESSION['message'][1]; ?></div>
            <?php endif; ?>
        </div>

        <div class="col bg-white">
            <h2 class="py-2">Manage Existing Users</h2>
            
            <?php $users = $mysqli->query("SELECT * FROM `users` ORDER BY id = {$_SESSION['adminid']} DESC"); ?>
            
            <?php if($users->num_rows > 0) : ?>
            <div class="userList row">
                <?php while($user = $users->fetch_assoc()) : ?>
                    <div class="col-sm-6 col-lg-4" style="max-width: 500px;">
                        <div class="card mb-3" id="<?php echo $user['id']; ?>">
                            <h4 class="card-title bg-primary text-white p-3 m-0"><?php echo $user['username']; ?></h4>
                            
                            <div class="card-body">
                                <h5 class="card-title font-weight-normal"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h5>
                                <p class="card-text"><?php echo $user['email']; ?></p>

                                <button <?php echo ($_SESSION['adminid'] == 1 || $_SESSION['adminid'] == $user['id'] ? 'onclick="showEdit(' . $user['id'] . ')"' : 'disabled'); ?> class="btn btn-primary">Edit User</button>
                                
                                <?php if($_SESSION['adminid'] == 1 && $user['id'] != 1) : ?>
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>);" class="btn btn-danger">Delete User</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php else : ?>
                <h3 class="alert alert-info my-3">There are currently no users set up, so how are you here?</h3>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="admin/scripts/manageUsers.js"></script>

<?php require_once('includes/footer.php'); ?>

<?php
    unset($_SESSION['firstName']);
    unset($_SESSION['lastName']);
    unset($_SESSION['email']);
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['message']);
?>
