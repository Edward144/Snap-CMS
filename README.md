# Snap-CMS-v2
A complete overhaul of Snap CMS

## SETUP

After copying the files to your required directory you will first need to update the .htaccess file and change RewriteBase to the relevant url. After this has been done go to the CMS in your browser and you should immediately be brought to a setup screen.

From here you will need to enter your database connection details, and a username and password to be able to login to the CMS. You will also need to set a root directory offset. If you are installing Snap CMS in the root directory leave this blank, otherwise enter the path to the subdirectory it is installed in including a leading and trailing slash.

Once setup is complete a message should appear linking you to the admin dashboard. Trying to access the setup from now on will redirect you to index. You can re-access setup by going into the database and setting 'setup complete' to 0 in the settings table.

If you are unable to access the admin dashboard and keep being redirected to the setup page even after successful install then it is most likely that includes/settings.php has not been rewritten correctly to link to the database. Check your permissions, and edit the file manually with your connection details if necessary.
