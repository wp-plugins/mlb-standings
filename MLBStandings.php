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
	$filename = dirname(__FILE__)."/standings.xml";
	
	$date = date('h', time());
	
	if (($date !== (get_option('hour'))) || (!file_exists($filename))) {
		update_option('hour', $date);
		
		$sourcefile = "http://erikberg.com/mlb/standings.xml";
		
		if (!download2($sourcefile, $filename)) {
			echo "failed to copy $sourcefile...\n";
		}
	}
	
	$xml = simplexml_load_file($filename);
	
	$type = $xml->xpath("//standing/standing-metadata/sports-content-codes/sports-content-code/@code-type");
	$key = $xml->xpath("//standing/standing-metadata/sports-content-codes/sports-content-code/@code-key");
	for ($i = 0; $i < 12; $i++) {
		if (($type[$i]=="division") && ($key[$i]==get_option('division'))){
			$x = (($i+1)/2)-1;
			$division = $xml->xpath("/sports-content/standing");
			?>
			<link rel="stylesheet" href="<?php bloginfo('wpurl') ?>/wp-content/plugins/mlb-standings/standings.css" type="text/css" media="screen" />
			<div id="mlb_standings_body">
			<?php
			echo "<table><tr><th align='left'>Team</th><th align='right'>W</th><th align='right'>L</th><th align='right'>Pct.</th><th align='right'>GB</th></tr>";
			for ($j = 0; $j < count($division[$x]->team); $j++) {
				//echo "<tr>";
				if ($division[$x]->team[$j]->{'team-metadata'}->name->attributes()->last == "Mets") {
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
			update_option('timestamp', $xml->{'sports-metadata'}->attributes()->{'date-time'});
			$time_hour=substr(get_option('timestamp'),11,2);
			$time_minute=substr(get_option('timestamp'),14,2);
			$time_seconds=substr(get_option('timestamp'),17,2);
			$temptime=$time_hour.":".$time_minute.":".$time_seconds;
			putenv("TZ=US/Pacific");
			$time=date("g:i A T", mktime($time_hour,$time_minute,$time_seconds));			
			//echo "<p style='font-size:70%;'>Last updated: ".substr(get_option('timestamp'),5,2)."/".substr(get_option('timestamp'),8,2)."/".substr(get_option('timestamp'),0,4)." - ".$time."</p></div>";
			echo "<p class='date'>Last updated: ".substr(get_option('timestamp'),5,2)."/".substr(get_option('timestamp'),8,2)."/".substr(get_option('timestamp'),0,4)."</p></div>";
		}
	}
}

add_action('admin_menu', 'MLBStandings_add_options_page');

function MLBStandings_add_options_page() {
	// Add a new menu under Options:
	add_options_page('MLB Standings', 'MLB Standings', 'manage_options', __FILE__, 'MLBStandings_options_page');
}

// Create the options page
function MLBStandings_options_page() {
	?>
	<div>
		<h2>MLB Standings Options</h2>
		
		<form action="options.php" method="post">
			<?php settings_fields('MLBStandings_options'); ?>
			<?php do_settings_sections('MLBStandings'); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
	</div>
	<?php
}

// add the admin settings and such
add_action('admin_init', 'MLBStandings_admin_init');

function MLBStandings_admin_init(){
	register_setting( 'MLBStandings_options', 'division' );
	//update_option('division', 'MLB.AL.E');
	register_setting( 'MLBStandings_options', 'timestamp' );
	update_option('timestamp', '2006-09-10T04:09:00-07:00');
	register_setting( 'MLBStandings_options', 'hour' );
	update_option('hour', (date('h', time())));
	add_settings_section('MLBStandings_main', 'Division', 'division_section_text', 'MLBStandings');
	add_settings_field('division_text_string', 'Select the division you\'d like to display on your blog:', 'MLBStandings_setting_string', 'MLBStandings', 'MLBStandings_main');
}

function division_section_text() {
}

function MLBStandings_setting_string() {
$options = get_option('MLBStandings_options');

?>
<td><select name="division">
	<?php $selection = get_option('division'); ?>
	<option value ="MLB.AL.E" <?php if ($selection=='MLB.AL.E') { echo 'selected'; } ?> >AL East</option>
	<option value ="MLB.AL.C" <?php if ($selection=='MLB.AL.C') { echo 'selected'; } ?> >AL Central</option>
	<option value ="MLB.AL.W" <?php if ($selection=='MLB.AL.W') { echo 'selected'; } ?> >AL West</option>
	<option value ="MLB.NL.E" <?php if ($selection=='MLB.NL.E') { echo 'selected'; } ?> >NL East</option>
	<option value ="MLB.NL.C" <?php if ($selection=='MLB.NL.C') { echo 'selected'; } ?> >NL Central</option>
	<option value ="MLB.NL.W" <?php if ($selection=='MLB.NL.W') { echo 'selected'; } ?> >NL West</option>
</select></td>
<?php
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