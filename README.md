# Snap-CMS-v3
A further redesign of Snap CMS using bootstrap, because I find it impossible to stay consistent.

# IMPORTANT!
I have been entirely rewriting every file since v2. Currently not everything is back in place, I will update this read me as I finish everything off. 

In Progress:
* Admin: Navigation Management - You can add items to navigation menus, but cannot edit or re-order existing items. 
* Admin: Categories Management - I had removed categories as I personally never used them, I will look at recreating them in time.
* Frontend: Index Template - index.php is not yet set up to display content that has been created. 
* Frontend: Posts Template - Alongside index.php should be a posts.php file which is essentially identical to index, with minor changes for a more relevant layout for blog/news posts.

# Installation
Simply extract everything into the required directory on your webserver. You might need to edit the .htaccess file in the root folder depending on whether you are installing to a sub-directory or not. Ensure that the rewrite base is correctly set to your sub-directory.

# Setup
Start by creating a MySQL database to be used and assign a user. You do not need to create any tables.

On your first vist to the site url you should be automatically redirected to the setup form. From here you will be asked to enter the connection details for your MySQL database.

You will also be asked to set the root directory for the site. If you are already installing on the root directory of your webserver leave this as "/". If you are installing into a sub-directory, then enter the path to that sub-directory including a leading and trailing slash. e.g. "/path/to/subdirectory/".

Finally you will need to enter the email and password which you will be using to sign into the primary admin account for the CMS. You will be sent an email confirming your details and a link to sign into the admin.

The main problem that can occur during setup is permissions preventing the writing of the "includes/settings.php" file which stores the MySQL connection details as well as the ROOT_DIR constant used throughout the CMS. Permissions will vary depending on your circumstances but usually 775 for directories and 664 for files is what works.

I would also recommend having SPF and DKIM set up for mail so that the various emails that are sent by the system do not get lost in transit. Currently emails are sent to new users when they are created, and for sending password reset links. 

# Navigation Menus
You can create as many navigation menus as you want within the CMS but need to manually add them within the code to display that menu. You just need to add the following code:
...
$somemenu = new navigation(menu_id);
$somemenu->display();
...

# Bootstrap
I am using pre compiled Bootstrap 4.5 for this build, there are no SASS files being used. 

# Other plugins
I am using the following third party plugins as well as what comes included with Bootstrap
* TinyMCE 4.9.4 (This should really be updated, especially since receiving a warning from Github that versions before 4.9.11 have a security vulnerabillity. However this version works with the version of MoxieManager that I am using. TinyMCE 5 doesn't seem to. You have been warned!)
* MoxieManager - Used for simple file management and adding images within TinyMCE
* Retina.min.js - Automatically checks every image on a page for an @2x version and attempts to replace them, if a retina display is detecteed.
* Apex Charts - Used within the admin to create various data charts.
