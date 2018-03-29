<?php
/*
Plugin Name: Hello-World
Plugin URI: http://retrodevelopers.com/
Description: A simple hello world wordpress plugin
Version: 1.0
Author: Dishant
Author URI: http://retrodevelopers.com/
License: GPL
*/
/* This calls hello_world() function when wordpress initializes.*/
/* Note that the hello_world doesnt have brackets. */

add_action('init','hello_world');
function hello_world()
{
echo "Hello World";
}



/* Runs when plugin is activated */
register_activation_hook(__FILE__,'hello_world_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'hello_world_remove' );

function hello_world_install() {
/* Creates new database field */
add_option("hello_world_data", 'Default', '', 'yes');
}

function hello_world_remove() {
/* Deletes the database field */
delete_option('hello_world_data');
}

if ( is_admin() ){

/* Call the html code */
add_action('admin_menu', 'hello_world_admin_menu');

function hello_world_admin_menu() {
add_options_page('Hello World', 'Hello World', 'administrator',
'hello-world', 'hello_world_html_page');
}
}

function hello_world_html_page() {
?>
<div>
<h2>Hello World Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table width="510">
<tr valign="top">
<th width="92" scope="row">Enter Text</th>
<td width="406">
<input name="hello_world_data" type="text" id="hello_world_data"
value="<?php echo get_option('hello_world_data'); ?>" />
(ex. Hello World)</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="hello_world_data" />

<p>
<input type="submit" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
<?php
}

/* Create Table Hook with version wise */
register_activation_hook( __FILE__, 'my_plugin_create_db' );
function my_plugin_create_db() {


	global $wpdb;
  	$version = get_option( 'my_plugin_version', '1.0' );
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'my_analysis';


	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		views smallint(5) NOT NULL,
		clicks smallint(5) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";


	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	if ( version_compare( $version, '2.0' ) < 0 ) {
		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  views smallint(5) NOT NULL,
		  clicks smallint(5) NOT NULL,
		  blog_id smallint(5) NOT NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";
		dbDelta( $sql );
	
	  	update_option( 'my_plugin_version', '2.0' );
		
	}
	
}
/* Delete Table Hook */
register_deactivation_hook( __FILE__, 'my_plugin_remove_database' );
function my_plugin_remove_database() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'my_analysis';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
     delete_option("my_plugin_db_version");
}   

?>
