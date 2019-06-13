<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/header.php'); ?>
    
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/sidebar.php'); ?>

    <div class="content">
        <h1><?php adminTitle(); ?></h1>
        
        <div class="formBlock">
            <form id="userManagement" style="max-width: 100%;">                
                <table>
                    <tr class="headers">
                        <td>Username</td>
                        <td>Email Address</td>
                        <td>First Name</td>
                        <td>Last Name</td>
                        <td>Access Level</td>
                        <td>Current Password</td>
                        <td>New Password</td>
                        <td>Actions</td>
                    </tr>
                    
                    <?php 
                        $users = $mysqli->query("SELECT * FROM `users` ORDER BY username ASC");
                        
                        while($row = $users->fetch_assoc()) :
                    ?>
                        <tr class="userRow">
                            <td>
                                <?php echo $row['username']; ?>
                            </td>
                            
                            <td>
                                <p style="margin: 0 auto;">
                                    <input type="text" name="email" value="<?php echo $row['email']; ?>">
                                </p>
                            </td>
                            
                            <td>
                                <p style="margin: 0 auto;">
                                    <input type="text" name="firstName" value="<?php echo $row['first_name']; ?>">
                                </p>
                            </td>
                            
                            <td>
                                <p style="margin: 0 auto;">
                                    <input type="text" name="lastName" value="<?php echo $row['last_name']; ?>">
                                </p>
                            </td>
                            
                            <td>
                                <p style="margin: 0 auto;">
                                    <?php if($row['username'] == 'admin') : ?>
                                        <input type="number" name="accessLevel" value="<?php echo $row['access_level']; ?>" step="1" disabled>
                                    <?php else : ?>
                                        <input type="number" name="accessLevel" value="<?php echo $row['access_level']; ?>" step="1">
                                    <?php endif; ?>
                                </p>
                            </td>
                            
                            <td>
                                <p style="margin: 0 auto;">
                                    <input type="password" name="cPassword" placeholder="Current Password">
                                </p>                            
                            </td>
                            
                            <td>
                                <p style="margin: 0 auto 0.5em;">
                                    <input type="password" name="nPassword" placeholder="New Password">
                                </p>
                                
                                <p style="margin: 0.5em auto 0;">
                                    <input type="password" name="nPasswordConf" placeholder="Confirm New Password">
                                </p>
                            </td>
                            
                            <td>
                                <?php if($row['username'] != 'admin') : ?>
                                <p style="margin: 0 auto 0.5em;">
                                    <input class="badButton" type="button" name="deleteUser" value="Delete User">
                                </p>
                                <?php endif; ?>
                                <p style="margin: 0 auto;">
                                    <input type="submit" name="updateUser" value="Apply Changes">
                                </p>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                
                <p class="message"></p>
            </form>
        </div>
        
        <h2>Create User</h2>
        
        <div class="formBlock" id="addUser">
            <form>                
                <p>
                    <label>Username: </label>
                    <input type="text" name="username">
                </p>
                
                <p>
                    <label>Email Address: </label>
                    <input type="text" name="email">
                </p>
                
                <p>
                    <label>First Name: </label>
                    <input type="text" name="firstName">
                </p>
                
                <p>
                    <label>Last Name: </label>
                    <input type="text" name="lastName">
                </p>
            </form>
            
            <form>
                <p>
                    <label>Access Level: </label>
                    <input type="number" name="access" step="1">
                </p>
                
                <p>
                    <label>Password: </label>
                    <input type="password" name="password">
                </p>
                
                <p>
                    <label>Confirm Password: </label>
                    <input type="password" name="passwordConf">
                </p>
                
                <p>
                    <input type="submit" value="Submit">
                </p>
                
                <p class="message"></p>
            </form>
        </div>
    </div>

    <script src="scripts/userManagement.js"></script>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/footer.php'); ?>