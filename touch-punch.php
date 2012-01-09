<?php
/*
Plugin Name: Touch Punch
Plugin URI: http://www.presscoders.com/plugins/touch-punch/
Description: Super lightweight Plugin that enables WordPress drag/drop functionality to work on iPad/iPhone.
Version: 0.1
Author: David Gwyer
Author URI: http://www.presscoders.com
*/

/*  Copyright 2009 David Gwyer (email : d.v.gwyer@presscoders.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// 'tp_' prefix is derived from [t]ouch [p]unch

// @todo
// - Admin widgets.php page is a little 'buggy' when dropping a new widget into a widget area. Dragging around existing widgets inside a widget area isn't a problem.

add_action( 'init', 'tp_plugin_init' );
add_action('admin_init', 'tp_init' );
add_action( 'admin_menu', 'tp_add_options_page' );
add_filter( 'plugin_action_links', 'tp_plugin_action_links', 10, 2 );
register_activation_hook( __FILE__, 'tp_add_defaults' );
register_uninstall_hook( __FILE__, 'tp_delete_plugin_options' );

// Plugin init
function tp_plugin_init() {
	$options = get_option('tp_options');

	if( isset($options['chk_enq_admin']) && $options['chk_enq_admin'] ) {
		// Sortable jQuery admin pages.
		add_action( 'load-post.php', 'enq_admin_widgets_page_sortable' ); // Enqueue on admin widgets.php
		add_action( 'load-post-new.php', 'enq_admin_widgets_page_sortable' ); // Enqueue on admin widgets.php
		add_action( 'load-widgets.php', 'enq_admin_widgets_page_sortable' ); // Enqueue on admin widgets.php
		add_action( 'load-index.php', 'enq_admin_widgets_page_sortable' ); // Enqueue on admin main index.php
		add_action( 'load-link-add.php', 'enq_admin_widgets_page_sortable' ); // Enqueue on admin main index.php
		add_action( 'load-nav-menus.php', 'enq_admin_widgets_page_sortable' ); // Enqueue on admin widgets.php
	}

	if( isset($options['chk_enq_front_end']) && $options['chk_enq_front_end'] )
		add_action( 'wp_enqueue_scripts', 'enqueue_touch_punch' ); // Enqueue on the front end
}

// Delete options table entries ONLY when plugin deactivated AND deleted
function tp_delete_plugin_options() {
	delete_option('tp_options');
}

// Define default option settings
function tp_add_defaults() {
	$tmp = get_option('tp_options');
    if( ( $tmp['chk_default_options_db'] ) || ( !is_array($tmp) ) ) {
		$arr = array(	"chk_enq_front_end" => "",
						"chk_enq_admin" => "1",
						"chk_default_options_db" => ""
		);
		update_option('tp_options', $arr);
	}
}

// Init plugin options to white list our options
function tp_init(){
	register_setting( 'tp_plugin_options', 'tp_options' );
}

// Add menu page
function tp_add_options_page() {
	add_options_page('Touch Punch Options Page', 'Touch Punch', 'manage_options', __FILE__, 'tp_render_form');
}

// Render the Plugin options form
function tp_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Touch Punch Options</h2>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('tp_plugin_options'); ?>
			<?php $options = get_option('tp_options'); ?>

			<table class="form-table">

				<!-- Checkbox Buttons -->
				<tr valign="top">
					<th scope="row">Where to add Touch Punch</th>
					<td>
						<!-- First checkbox button -->
						<label><input name="tp_options[chk_enq_front_end]" type="checkbox" value="1" <?php if (isset($options['chk_enq_front_end'])) { checked('1', $options['chk_enq_front_end']); } ?> /> Front end of your site</label><br />

						<!-- Third checkbox button -->
						<label><input name="tp_options[chk_enq_admin]" type="checkbox" value="1" <?php if (isset($options['chk_enq_admin'])) { checked('1', $options['chk_enq_admin']); } ?> /> WordPress admin area</label><br />
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="tp_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<p style="margin-top:15px;">
			<p style="font-weight: bold;color: #26779a;">If you have found the Touch Punch Plugin at all useful, please consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LQ5WQXGVLWWCE" target="_blank">donation</a>. Thanks.</p>
			<span><a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/touch-punch/images/facebook-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/touch-punch/images/twitter-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://www.presscoders.com" title="PressCoders.com" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/touch-punch/images/pc-icon.png" /></a></span>
		</p>

	</div>
	<?php	
}

// Display a Settings link on the main Plugins page
function tp_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$tp_links = '<a href="'.get_admin_url().'options-general.php?page=touch-punch/touch-punch.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $tp_links );
	}
	return $links;
}

// Enqueue Touch Punch on the front end
function enqueue_touch_punch(){
	wp_enqueue_script( 'touch-punch', plugins_url( 'jquery.ui.touch-punch.min.js' , __FILE__ ), array('jquery-ui-sortable') );
}

// Enqueue Touch Punch in the admin
function enq_admin_widgets_page_sortable(){
	wp_enqueue_script( 'touch-punch', plugins_url( 'jquery.ui.touch-punch.min.js' , __FILE__ ), array('jquery-ui-sortable') );
}