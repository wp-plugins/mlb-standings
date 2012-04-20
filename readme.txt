=== Plugin Name ===
Contributors: David Goldstein
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3396118
Tags: baseball, standings, sports, major league baseball
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 1.0

This plugin adds the ability to display the standings for a division of <a href="http://mlb.com" target="_blank">Major League Baseball (MLB)</a> via a sidebar widget or template tag.

== Description ==
This plugin adds the ability to display the standings for a division of <a href="http://mlb.com" target="_blank">Major League Baseball (MLB)</a> via a sidebar widget or template tag.

== Installation ==
1. Download the .zip archive (link below) and extract.
1. Upload the 'mlb-standings' folder to your plugin directory. You should now have a '/wp-content/plugins/mlb-standings/' directory holding the contents of the .zip file.
1. Activate the plugin through the 'Plugins' page in WordPress.
1. Go to 'Settings->MLB Standings' in your admin interface to select the division you'd like to display.

To display via sidebar widget:
1. Go to 'Appearance->MLB Standings' in your admin interface.</li>
1. Under 'Available Widgets' look for 'MLB Standings'.</li>
1. Drag 'MLB Standings' to the sidebar.</li>
1. Enter a title for the widget and click the 'Save' button on the bottom of the widget.</li>

To display vi template tag add the following line of code to the place you'd like the standings to be displayed:
`<?php if(function_exists(ShowMLBStandings2)) : ShowMLBStandings2(); endif; ?>`

== Changelog ==
= 1.0 =
* Initial release.

== Options ==
There is only one setting for this plugin: Division. This can be one of the following:
<ul>
	<li>AL East</li>
	<li>AL Central</li>
	<li>AL West</li>
	<li>NL East</li>
	<li>NL Central</li>
	<li>NL West</li>
</ul>