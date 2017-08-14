<?php
/*
	Plugin Name: Genesis Agent Profiles
	Plugin URI: http://www.agentevolution.com
	Description: Adds an agent directory with team and individual profile pages, plus a sidebar widget for a featured agent(s).
	Author: Agent Evolution
	Author URI: http://www.agentevolution.com

	Version: 1.2.1

	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
*/

register_activation_hook( __FILE__, 'ae_profiles_activation' );
/**
 * This function runs on plugin activation. It checks to make sure the Genesis
 * and AgentEvo frameworks are installed. If not, it deactivates itself.
 *
 * @since 0.1.0
 */
function ae_profiles_activation() {

		if ( 'genesis' != basename( get_template_directory() ) ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) ); /** Deactivate ourself */
			wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed <a href="%s">Genesis</a>', 'aep' ), 'http://studiopress.com/genesis' ) );
		}

		/** Flush rewrite rules */
		if ( ! post_type_exists( 'aeprofiles' ) ) {
			agent_profiles_init();
			global $_agent_directory;
			$_agent_directory->create_post_type();
		}
		flush_rewrite_rules();

}

add_action( 'after_setup_theme', 'agent_profiles_init' );
/**
 * Initialize Agent Directory.
 *
 * Include the libraries, define global variables, instantiate the classes.
 *
 * @since 0.1.0
 */
function agent_profiles_init() {

	/** Do nothing if a Genesis child theme isn't active */
	if ( ! function_exists( 'genesis_get_option' ) )
		return;

	require_once dirname( __FILE__ ) . '/includes/helpers.php';

	global $_agent_directory;

	define( 'AEP_URL', plugin_dir_url( __FILE__ ) );
	define( 'AEP_VERSION', '0.9' );

	/** Loads textdomain for translation */
	load_plugin_textdomain( 'aep', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	/** Includes */
	require_once( dirname( __FILE__ ) . '/includes/class-aeprofiles.php' );
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/shortcodes.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-taxonomies.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-agent-evolution-profiles-widget.php');

	/** Create new featured image size */
	add_image_size( 'agent-profile-photo', 150, 200, true );

	/** Enqueues style file if it exists */
	add_action('wp_enqueue_scripts', 'add_aep_css');
	function add_aep_css() {

		$options = get_option('plugin_ae_profiles_settings');

		if ( !isset($options['stylesheet_load']) ) {
			$options['stylesheet_load'] = 0;
		}

		if ( 1 == $options['stylesheet_load'] ) {
			return;
		}

        $aep_css_path = AEP_URL . 'aep.css';
        if ( file_exists(dirname( __FILE__ ) . '/aep.css') ) {
            wp_register_style('agent-profiles', $aep_css_path);
            wp_enqueue_style('agent-profiles');
        }
    }

    add_action( 'widgets_init', 'register_agent_directory_widget' );

    function register_agent_directory_widget() {
    	register_widget('AgentEvolution_Profiles_Widget');
    }

	/** Instantiate */
	$_agent_directory = new Agent_Directory;
	$_aeprofiles_taxonomies = new Genesis_Agent_Profiles_Taxonomies;

	/**
	 * @todo add localization, create localization file.
	 */

}