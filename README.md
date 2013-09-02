wpjoyride
=========

A WordPress plugin that will create dashboard visual tours using jquery joyride so you can educate your users on new functionality.

To edit the dashboard links, see the function <a href="https://github.com/crypticsoft/wpjoyride/blob/master/wpjoyride.php"><em>tips_dashboard_widget_function()</em></a> and add titles and URLs to the dashboard in the array format ($tour_links).

1) Start by first creating a "Tour" post by using a basic title and note the slug which will be used as the hashtag. 
2) Create "Tips" to include all of the tips for the "Tour" you just created, be sure to select the tour in the meta box to give it a proper relationship. You can also set the order of the tips by using the meta "Tour Order". 
3) When you are finished creating Tips, simply append the hashtag (Tour slug) to the URL you want to link in the admin. An example link is set for you in the dashboard with the "tips_dashboard_widget_function()" : '/wp-admin/options-general.php#website-title'

TO-DO's:
1) Create a filter in the "Tips" admin listing so that you can filter by a specific "Tour" and only show those related posts.
2) Consider how this can be also used on the front end rather than strictly in the WordPress Admin.