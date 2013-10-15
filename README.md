wpjoyride
=========

A WordPress plugin that will create dashboard visual tours using the jquery joyride plugin so you can educate your users on new functionality.

To edit the dashboard links, see the function <a href="https://github.com/crypticsoft/wpjoyride/blob/master/wpjoyride.php"><em>tips_dashboard_widget_function()</em></a> and add titles and URLs to the dashboard in the array format ($tour_links).

To create a feature tour:
1. Once the plugin is activated you will see a tab on the right of the screen with a cog icon, click it to expand.
2. Enter in some basic information about your feature tour including a title and hashtag. The hashtag is what the javascript will look for when initializing the joyride plugin. Click "Add Tour" button to save the fields and begin adding tips.
3. To add tips to the tour, fill out the form below and click "save" when completed. There is a "Selector Gadget" button which will help you locate the element ID or classes which are necessary to create a tip and it's placement.
4. When you are finished creating Tips, simply append the hashtag (Tour slug) to the URL you want to link in the admin and reload the page.

TO-DO's:
1. Finish up settings page and define some options for how joyride is configured and styled.
2. Consider how tours can be also on the front end rather than strictly in the WordPress Admin.
3. Allow Tours / Tips to be edited after saving, as of now you can only create / delete.
4. Have an option to add a tour to the dashboard with a direct link to the feature tour.
5. Restrict the ability to create new tours to admin users, possibly add this into settings page.
6. Clean up wpjoyride.php by making a class based plugin. Evaluate backboneJS integration.