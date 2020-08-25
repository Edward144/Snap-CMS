# Snap-CMS-v3
A further redesign of Snap CMS using bootstrap, because I find it impossible to stay consistent.

# IMPORTANT!
I have been entirely rewriting every file since v2. Currently not everything is back in place, I will update this read me as I finish everything off. 

In Progress:
* Admin: Categories Management - I had removed categories as I personally never used them, I will look at recreating them in time.

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
    
    $somemenu = new navbar(menu_id);
    $somemenu->display();
	
This uses bootstrap's navbar class to create a horizonal navigation menu. I will be looking to add a separate class for vertical navigation menus at some point. 

# Shortcodes
I have created a shortcode like system to allow for easy insertion of php functions within TinyMCE. You can add your own shortcodes by editing includes/classes/contentparser.class.php

The content parser class looks for sets of square brackets within the string that is passed to it, then takes whatever parameters are inside and passes them to a function. 

The square brackets should always include an "insert" parameter, this is the name of the function that will be run. You must also use double quotes within the shortcode. e.g.

    $shortcode = '[insert="functionname",param1="x",param2="y"]';
	echo new parsecontent($shortcode);
	
To create your own function edit the shortcodes class within includes/classes/contentparser.class.php. Call the function whatever you like and pass a single variable to it. Every parameter within the shortcode square brackets, other then "insert", will be passed as an associative array. You can then do whatever you like with those variables. 

Current shortcodes included are:
* [insert="contactform",id="x"] - Inserts a contact form that has been created in the CMS onto the page. Including code to validate and send it.
* [insert="customfile",path="path/to/file"] - Used to include any custom php, html, js etc file you want. Should be an easy way to achieve whatever you want on a page. 
* [insert="googlemap",api="your-api-key",lat="",lng="",zoom="",h="",w=""] - Will insert a Google map using their javascript API. This should be easily modifiable to add as many parameters as needed, for more complex maps. 

# Bootstrap
I am using pre compiled Bootstrap 4.5 for this build. The bootstrap node_modules are now included so you can theme using SASS. 

# Other plugins
I am using the following third party plugins as well as what comes included with Bootstrap
* TinyMCE 4.9.11
* MoxieManager
* Retina.min.js
* Apex Charts
* [Gumlet/PHP-Image-Resize](https://github.com/gumlet/php-image-resize)
* Font-Awesome