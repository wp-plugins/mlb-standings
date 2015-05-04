=== MLB Standings ===
Contributors: golddave
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3396118
Tags: baseball, standings, sports, major league baseball, mlb
Requires at least: 3.0
Tested up to: 4.2.1
Stable tag: 2.0.3

Display the standings for a division of <a href="http://mlb.com" target="_blank">Major League Baseball (MLB)</a> on your blog.

== Description ==
MLB Standings adds the ability to display the standings for a division of <a href="http://mlb.com" target="_blank">Major League Baseball (MLB)</a> on your blog via a sidebar widget or template tag. This is ideal for team fan blogs who want to show how their team is doing in the standings. You can also highlight your team in the standings.

Standings are derived from an XML file published daily at erikberg.com. The XML is saved to your Wordpress settings and parsed from there to display on your blog.

Version 2.0 removes cURL and file system dependencies to make it more universally compatible with webhosts. 

Users upgrading from 1.0 to 2.x should visit the settings page to reset their settings in order to display the standings properly.

== Installation ==
1. Download the .zip archive (link below) and extract.
2. Upload the 'mlb-standings' folder to your plugin directory. You should now have a '/wp-content/plugins/mlb-standings/' directory holding the contents of the .zip file.
3. Activate the plugin through the 'Plugins' page in Wordpress.
4. Go to 'Settings->MLB Standings' in your admin interface to select the division you'd like to display and the team you'd like to highlight.

= Sidebar Widget =

To display via sidebar widget:

1. Go to 'Appearance->Widgets' in your admin interface.
2. Under 'Available Widgets' look for 'MLB Standings'.
3. Drag 'MLB Standings' to the sidebar.
4. Enter a title for the widget and click the 'Save' button on the bottom of the widget.

= Template Tag =

To display via template tag add the following line of code to the place you'd like the standings to be displayed:

`<?php if(function_exists(ShowMLBStandings)) : ShowMLBStandings(); endif; ?>`

== Changelog ==
= 2.0.3 =
* Compatibility changes for NBA Standings plugin.

= 2.0.2 =
* Added compression support for XML download.

= 2.0.1 =
* Minor CSS change.

= 2.0 =
* Added option to highlight team in standings.
* Added AJAX menu for team selection. Only teams from the selected division will be available in the team select box.
* Rewrote settings page to better conform to the Wordpress settings API.
* Refactored code to remove unnecessary settings and variables.
* Added link to settings page to the MLB Standings listing on the plugin page.
* Changed download function to use WP_Http API eliminating dependency on cURL.
* Now saving XML to the database instead of downloading eliminating dependency on file system.
* Now using WP Transients API calls to cache the standings XML data instead of using a custom function.

= 1.0 =
* Initial release.

== Frequently Asked Questions ==
= I upgraded from 1.0 to 2.0 and now I get an error when displaying the widget. What happened? =

The settings mechanism was totally rewritten for version 2.0. Go to 'Settings->MLB Standings' to set your division and team to display the standings properly again.

== Upgrade Notice ==
= 2.0.1 =
Minor CSS change.

= 2.0 =
Added setting to highlight a team, removed cURL dependency and more under the hood optimizations.

== Screenshots ==

1. The setting page.
2. The front end.
