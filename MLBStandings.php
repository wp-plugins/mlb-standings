<?php
/*
Plugin Name: MLB Standings
Plugin URI: http://nothing.golddave.com/plugins/mlb-standings/
Description: Displays the standings for a given division of MLB as either a sidebar widget or template tag.
Version: 1.0
Author: David Goldstein
Author URI: http://nothing.golddave.com
*/

/*
Change Log

1.0
  * First public release.
*/

function ShowMLBStandings() {
	$options = get_option('MLBStandings_options');
	$filename = dirname(__FILE__)."/standings.xml";
	
	$date = date('h', time());
	
	if (($date !== $options['hour']) || (!file_exists($filename))) {
		update_option($options['hour'], $date);
		
		$sourcefile = "http://erikberg.com/mlb/standings.xml";
		
		if (!download2($sourcefile, $filename)) {
			echo "failed to copy $sourcefile...\n";
		}
	}
	
	$xml = simplexml_load_file($filename);
	
	$type = $xml->xpath("//standing/standing-metadata/sports-content-codes/sports-content-code/@code-type");
	$key = $xml->xpath("//standing/standing-metadata/sports-content-codes/sports-content-code/@code-key");
	
	for ($i = 0; $i < 12; $i++) {
		if (($type[$i]=="division") && ($key[$i]==str_replace("-",".",$options['division']))){
			$x = (($i+1)/2)-1;
			$division = $xml->xpath("/sports-content/standing");
			?>
			<link rel="stylesheet" href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mlb-standings/standings.css" type="text/css" media="screen" />
			<div id="mlb_standings_body">
			<?php
			echo "<table><tr><th align='left'>Team</th><th align='right'>W</th><th align='right'>L</th><th align='right'>Pct.</th><th align='right'>GB</th></tr>";
			for ($j = 0; $j < count($division[$x]->team); $j++) {
				if ($division[$x]->team[$j]->{'team-metadata'}->name->attributes()->last == $options['team']) {
					echo "<tr class='team'><td align='left'>".$division[$x]->team[$j]->{'team-metadata'}->name->attributes()->last."</td><td align='right'>".$division[$x]->team[$j]->{'team-stats'}->{'outcome-totals'}->attributes()->wins."</td><td align='right'>".$division[$x]->team[$j]->{'team-stats'}->{'outcome-totals'}->attributes()->losses."</td><td align='right'>".$division[$x]->team[$j]->{'team-stats'}->{'outcome-totals'}->attributes()->{'winning-percentage'}."</td>";
				} else {
					echo "<tr><td align='left'>".$division[$x]->team[$j]->{'team-metadata'}->name->attributes()->last."</td><td align='right'>".$division[$x]->team[$j]->{'team-stats'}->{'outcome-totals'}->attributes()->wins."</td><td align='right'>".$division[$x]->team[$j]->{'team-stats'}->{'outcome-totals'}->attributes()->losses."</td><td align='right'>".$division[$x]->team[$j]->{'team-stats'}->{'outcome-totals'}->attributes()->{'winning-percentage'}."</td>";
				}
				if ($j=='0'){
					echo "<td align='center'> - </td>";
				} else {
					echo "<td align='right'>".$division[$x]->team[$j]->{'team-stats'}->attributes()->{'games-back'}."</td>";
				}
			}
			echo "</tr></table>";
			$timestamp = $xml->{'sports-metadata'}->attributes()->{'date-time'};
			putenv("TZ=US/Pacific");
			$time=date("g:i A T", mktime(substr($timestamp,11,2),substr($timestamp,14,2),substr($timestamp,17,2)));			
			//echo "<p class='date'>Last updated: ".substr($timestamp,5,2)."/".substr($timestamp,8,2)."/".substr($timestamp,0,4)." - ".$time."</p></div>";
			echo "<p class='date'>Last updated: ".substr($timestamp,5,2)."/".substr($timestamp,8,2)."/".substr($timestamp,0,4)."</p></div>";
		}
	}
}

register_activation_hook(__FILE__, 'MLBStandings_add_defaults');
add_action('admin_init', 'MLBStandings_init' );
add_action('admin_menu', 'MLBStandings_add_options_page');
add_filter('plugin_action_links', 'MLBStandings_plugin_action_links', 10, 2);


// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'MLBStandings_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function MLBStandings_add_defaults() {
	$tmp = get_option('MLBStandings_options');
    if(!is_array($tmp)) {
		//delete_option('MLBStandings_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"division" => "MLB-NL-E",
						"team" => "Mets",
						"hour" => date('h', time()));
		update_option('MLBStandings_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'MLBStandings_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function MLBStandings_init(){
	register_setting( 'MLBStandings_plugin_options', 'MLBStandings_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'MLBStandings_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function MLBStandings_add_options_page() {
	add_options_page('MLB Standings Options Page', 'MLB Standings', 'manage_options', __FILE__, 'MLBStandings_render_form');
	
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function MLBStandings_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>MLB Standings Options</h2>
		
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('MLBStandings_plugin_options'); ?>
			<?php $options = get_option('MLBStandings_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">
				<tr>
					<th scope="row">Division</th>
					<td>
						<select name='MLBStandings_options[division]' id='mydiv'>
							<option value='MLB-AL-E' <?php selected('MLB-AL-E', $options['division']); ?>>AL East</option>
							<option value='MLB-AL-C' <?php selected('MLB-AL-C', $options['division']); ?>>AL Central</option>
							<option value='MLB-AL-W' <?php selected('MLB-AL-W', $options['division']); ?>>AL West</option>
							<option value='MLB-NL-E' <?php selected('MLB-NL-E', $options['division']); ?>>NL East</option>
							<option value='MLB-NL-C' <?php selected('MLB-NL-C', $options['division']); ?>>NL Central</option>
							<option value='MLB-NL-W' <?php selected('MLB-NL-W', $options['division']); ?>>NL West</option>
						</select>
						<span style="color:#666666;margin-left:2px;">Select the division you'd like to display on your blog.</span>
					</td>
				</tr>

				<tr>
					<th scope="row">Team</th>
					<td>
						<select name='MLBStandings_options[team]' id="teams" class="teams">
						</select>
						<span style="color:#666666;margin-left:2px;">Select the team you'd like bolded in the standings.</span>
					</td>
				</tr>
			</table>
			<input type="hidden" name='MLBStandings_options[hour]' value=<?php echo date('h', time()); ?>>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	
	<script type='text/javascript'>
		function teamchanger() {
			jQuery("#teams").empty()
			switch(jQuery("#mydiv").val()) {
				case "MLB-AL-E":
					jQuery("#teams").append("<option value='Orioles' <?php selected('Orioles', $options['team']); ?>>Baltimore Orioles</option><option value='Red Sox' <?php selected('Red Sox', $options['team']); ?>>Boston Red Sox</option><option value='Yankees' <?php selected('Yankees', $options['team']); ?>>New York Yankees</option><option value='Rays' <?php selected('Rays', $options['team']); ?>>Tampa Bay Rays</option><option value='Blue Jays' <?php selected('Blue Jays', $options['team']); ?>>Toronto Blue Jays</option>");
					break;
				case "MLB-AL-C":
					jQuery("#teams").append("<option value ='White Sox' <?php selected('White Sox', $options['team']); ?>>Chicago White Sox</option><option value ='Indians' <?php selected('Indians', $options['team']); ?>>Cleveland Indians</option><option value ='Tigers' <?php selected('Tigers', $options['team']); ?>>Detroit Tigers</option><option value ='Royals' <?php selected('Royals', $options['team']); ?>>Kansas City Royals</option><option value ='Twins' <?php selected('Twins', $options['team']); ?>>Minnesota Twins</option>");
					break;
				case "MLB-AL-W":
					jQuery("#teams").append("<option value ='Angels' <?php selected('Angels', $options['team']); ?>>Los Angeles Angels</option><option value ='Athletics' <?php selected('Athletics', $options['team']); ?>>Oakland Athletics</option><option value ='Mariners' <?php selected('Mariners', $options['team']); ?>>Seattle Mariners</option><option value ='Rangers' <?php selected('Rangers', $options['team']); ?>>Texas Rangers</option>");
					break;
				case "MLB-NL-E":
					jQuery("#teams").append("<option value ='Braves' <?php selected('Braves', $options['team']); ?>>Atlanta Braves</option><option value ='Marlins' <?php selected('Marlins', $options['team']); ?>>Miami Marlins</option><option value ='Mets' <?php selected('Mets', $options['team']); ?>>New York Mets</option><option value ='Phillies' <?php selected('Phillies', $options['team']); ?>>Philadelphia Phillies</option><option value ='Nationals' <?php selected('Nationals', $options['team']); ?>>Washington Nationals</option>");
					break;
				case "MLB-NL-C":
					jQuery("#teams").append("<option value ='Cubs' <?php selected('Cubs', $options['team']); ?>>Chicago Cubs</option><option value ='Reds' <?php selected('Reds', $options['team']); ?>>Cincinnati Reds</option><option value ='Astros' <?php selected('Astros', $options['team']); ?>>Houston Astros</option><option value ='Brewers' <?php selected('Brewers', $options['team']); ?>>Milwaukee Brewers</option><option value ='Pirates' <?php selected('Pirates', $options['team']); ?>>Pittsburgh Pirates</option><option value ='Cardinals' <?php selected('Cardinals', $options['team']); ?>>St. Louis Cardinals</option>");
					break;
				case "MLB-NL-W":
					jQuery("#teams").append("<option value ='Diamondbacks' <?php selected('Diamondbacks', $options['team']); ?>>Arizona Diamondbacks</option><option value ='Rockies' <?php selected('Rockies', $options['team']); ?>>Colorado Rockies</option><option value ='Dodgers' <?php selected('Dodgers', $options['team']); ?>>Los Angeles Dodgers</option><option value ='Padres' <?php selected('Padres', $options['team']); ?>>San Diego Padres</option><option value ='Giants' <?php selected('Giants', $options['team']); ?>>San Francisco Giants</option>");
					break;
			}
		}
		
		jQuery(document).ready(function() {
			teamchanger()
			jQuery('#mydiv').change(function(){
				teamchanger() 
			});
		});
	</script>


	<?php	
}

// Display a Settings link on the main Plugins page
function MLBStandings_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$MLBStandings_links = '<a href="'.get_admin_url().'options-general.php?page=mlb-standings/MLBStandings.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $MLBStandings_links );
	}

	return $links;
}











function download2($sourcefile, $filename) {
	// create a new curl resource
	$ch = curl_init();
	
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $sourcefile);
	curl_setopt($ch, CURLOPT_USERAGENT, 'MLBStandings; (http://golddave.com/)');
	curl_setopt($ch, CURLOPT_REFERER, get_bloginfo('url'));
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	set_time_limit(300); # 5 minutes for PHP
	curl_setopt($ch, CURLOPT_TIMEOUT, 300); # and also for CURL
	
	$outfile = fopen($filename, 'wb');
	curl_setopt($ch, CURLOPT_FILE, $outfile);
	
	// grab file from URL
	curl_exec($ch);
	fclose($outfile);
	
	// close CURL resource, and free up system resources
	curl_close($ch);
	sleep(10);
	return true;
}

class MLBStandings_Widget extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'MLBStandings_widget', // Base ID
			'MLB Standings', // Name
			array( 'description' => __( 'A widget to display the standings for a division of MLB.', 'text_domain' ), ) // Args
		);
	}

 	public function form( $instance ) {
		// outputs the options form on admin
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		ShowMLBStandings();
		echo $after_widget;
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget( "MLBStandings_Widget" );' ) );
?>