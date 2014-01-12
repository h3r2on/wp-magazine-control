<?php
/*
Plugin Name: Magazine Issue Control
Plugin URI: http://h3r2on.com/plugins
Description: Gives a magazine style blog control over the home page. Allow admin to only show posts that are in one issue while allowing for overrides
Version: 1.2
Author: Joel Herron
Author URI: http://h3r2on.com
License: CCSA3.0
*/


/* Runs when plugin is activated */
register_activation_hook(__FILE__,'magazine_issue_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'magazine_issue_remove' );

function magazine_issue_install() {
	/* Creates new database field */
	add_option("mic_current_issue", '1', '', 'yes');
}

function magazine_issue_remove() {
	/* Deletes the database field */
	delete_option('mic_current_issue');
}

/* 
now that we're installed
first check for a get var via init
if none defer to pre_get_post action
*/

function magazine_issue_check_override(){
	if (isset($_GET['beta'])) {
		$beta_mode = $_GET['beta'];
		if($beta_mode == 'active') {
			define("MIC_BETA", $beta_mode);
		}
	}
	// need to setup a way to setup a better way to list valid versions and not setup the constant unless it's good
	if (isset($_GET['issue']) && is_numeric($_GET['issue'])) {
		$override = $_GET['issue'];
		define("MIC_ISSUE", $override);               
	}
}
add_action('init', 'magazine_issue_check_override');

function magazine_issue_show_posts( $query ) {
	$currrent_issue = get_option('mic_current_issue');
	if($query->is_home()) {
		if(defined('MIC_BETA')) {
			$query->set('post_status', 'beta');
		} else {
			if (defined('MIC_ISSUE')) {
				$metaq = array(
					array(
						'key' => 'mic_issue',
						'value' => MIC_ISSUE,
						'compare' => '='
					)
				);
			} else {
				$metaq = array(
					array(
						'key' => 'mic_issue',
						'value' => $current_issue,
						'compare' => '='
					)
				);
			}
			$query->set('meta_query', $metaq);
		}
	}
}
add_action('pre_get_posts','magazine_issue_show_posts');


/*
 * Admin Menu section
 */

if ( is_admin() ){
	/* Call the html code */
	add_action('admin_menu', 'magazine_issue_admin_menu');
}

function magazine_issue_admin_menu() {
	add_options_page('Magazine Issue Control', 'Magazine Issue Control', 'administrator',
		'magazine-issue', 'magazine_issue_html_page');
}

function magazine_issue_html_page() { ?>
	<div>
		<h2>Magazine Issue Control Options</h2>

		<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>

		<table width="90%">
			<tr valign="top">
				<th width="25%" scope="row" style="text-align:left;">Set the Current Issue</th>
				<td width="50%">
					<input name="mic_current_issue" type="text" id="mic_current_issue" value="<?php echo get_option('mic_current_issue'); ?>" />
					(ex. 1)</td>
			</tr>
		</table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="mic_current_issue" />
		<p>
			<input type="submit" value="<?php _e('Save Changes') ?>" />
		</p>
		</form>
	</div>
<?php } ?>