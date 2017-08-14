<?php
/**
 * This file contains the Agent_Directory class.
 */

/**
 * This class handles the creation of the "Agent" post type,
 * and creates a UI to display the Agent-specific data on
 * the admin screens.
 */
class Agent_Directory {

	var $settings_page = 'aep-settings';

	var $settings_field = 'aeprofiles_taxonomies';
	var $menu_page = 'register-aep-taxonomies';

	var $options;

	/**
	 * Agent details array.
	 */
	var $agent_details;

	/**
	 * Construct Method.
	 */
	function __construct() {

		$this->options = get_option('plugin_ae_profiles_settings');

		$this->agent_details = apply_filters( 'agent_directory_details', array(
			'col1' => array(
				__( 'Title:', 'aep' ) 			=> '_agent_title',
				__( 'License #:', 'aep' ) 		=> '_agent_license',
				__( 'Designations:', 'aep' ) 	=> '_agent_designations',
				__( 'Phone:', 'aep' ) 			=> '_agent_phone',
				__( 'Mobile:', 'aep' ) 			=> '_agent_mobile',
				__( 'Fax:', 'aep' ) 			=> '_agent_fax',
				__( 'Email:', 'aep' )			=> '_agent_email',
				__( 'Website (NO http://):', 'aep' )			=> '_agent_website',
				__( 'Address:', 'aep' ) 		=> '_agent_address',
				__( 'City:', 'aep' )			=> '_agent_city',
				__( 'State:', 'aep' )			=> '_agent_state',
				__( 'Zip:', 'aep' )				=> '_agent_zip'
			),
			'col2' => array(
				__( 'Facebook URL:', 'apl' ) 		=> '_agent_facebook',
				__( 'Twitter URL:', 'apl' )			=> '_agent_twitter',
				__( 'LinkedIn URL:', 'apl' )		=> '_agent_linkedin',
				__( 'Google+ URL:', 'apl' )			=> '_agent_googleplus',
				__( 'Pinterest URL:', 'apl' )		=> '_agent_pinterest',
				__( 'YouTube URL:', 'apl' )			=> '_agent_youtube',
				__( 'Instagram URL:', 'apl' )		=> '_agent_instagram'
			),
		) );

		add_action( 'init', array( $this, 'create_post_type' ) );

		add_filter( 'manage_edit-aeprofiles_columns', array( $this, 'columns_filter' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );

		add_action( 'admin_menu', array( $this, 'register_meta_boxes' ), 5 );
		add_action( 'save_post', array( $this, 'metabox_save' ), 1, 2 );

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_init', array( &$this, 'add_options' ) );
		add_action( 'admin_menu', array( &$this, 'settings_init' ), 15 );

	}

	function register_settings() {

		register_setting( 'aep_options', 'plugin_ae_profiles_settings' );

	}

	function add_options() {

		$new_options = array(
			'stylesheet_load' => 0,
			'slug' => 'our-agents'
		);

		if ( empty($this->options['stylesheet_load']) && empty($this->options['slug']) )  {
			add_option( 'plugin_ae_profiles_settings', $new_options );
		}
	}

	/**
	 * Adds settings page under agent post type in admin menu
	 */
	function settings_init() {
		add_submenu_page( 'edit.php?post_type=aeprofiles', __( 'Settings', 'aep' ), __( 'Settings', 'aep' ), 'manage_options', $this->settings_page, array( &$this, 'settings_page' ) );
	}

	/**
	 * Creates display of settings page along with form fields
	 */
	function settings_page() {
		include( dirname( __FILE__ ) . '/views/aep-settings.php' );
	}

	/**
	 * Creates our "Agent" post type.
	 */
	function create_post_type() {

		$args = apply_filters( 'agent_directory_post_type_args',
			array(
				'labels' => array(
					'name'					=> __( 'Agent Directory', 'aep' ),
					'singular_name'			=> __( 'Agent', 'aep' ),
					'add_new'				=> __( 'Add New', 'aep' ),
					'add_new_item'			=> __( 'Add New Agent', 'aep' ),
					'edit_item'				=> __( 'Edit Agent', 'aep' ),
					'new_item'				=> __( 'New Agent', 'aep' ),
					'view_item'				=> __( 'View Agent', 'aep' ),
					'search_items'			=> __( 'Search Agents', 'aep' ),
					'not_found'				=> __( 'No agents found', 'aep' ),
					'not_found_in_trash'	=> __( 'No agents found in Trash', 'aep' )
				),
				'public'		=> true,
				'query_var'		=> true,
				'menu_position'	=> 7,
				'has_archive'	=> true,
				'supports'		=> array( 'title', 'editor', 'comments', 'thumbnail', 'page-attributes', 'genesis-seo', 'genesis-layouts', 'genesis-simple-sidebars', 'genesis-cpt-archives-settings' ),
				'rewrite'		=> array( 'slug' => $this->options['slug'] ),
			)
		);

		register_post_type( 'aeprofiles', $args );

	}

	function register_meta_boxes() {

		add_meta_box( 'agent_details_metabox', __( 'Agent Details', 'aep' ), array( &$this, 'agent_details_metabox' ), 'aeprofiles', 'normal', 'high' );
		add_meta_box( 'agentevo_metabox', __( 'Themes by Agent Evolution', 'aep' ), array( &$this, 'agentevo_metabox' ), 'aep-options', 'side', 'core' );

	}

	function agent_details_metabox() {
		include( dirname( __FILE__ ) . '/views/agent-details-metabox.php' );
	}

	function agentevo_metabox() {
		include( dirname( __FILE__ ) . '/views/agentevo-metabox.php' );
	}


	function metabox_save( $post_id, $post ) {

		/** Run only on aeprofiles post type save */
		if ( 'aeprofiles' != $post->post_type )
			return;

		if ( !isset( $_POST['aep_metabox_nonce'] ) || !wp_verify_nonce( $_POST['aep_metabox_nonce'], 'aep_metabox_save' ) )
			return $post_id;

		/** Don't try to save the data under autosave, ajax, or future post */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;

		/** Check permissions */
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		$agent_details = $_POST['aep'];

		/** Store the property details custom fields */
		foreach ( (array) $agent_details as $key => $value ) {

			/** Save/Update/Delete */
			if ( $value ) {
				update_post_meta($post->ID, $key, $value);
			} else {
				delete_post_meta($post->ID, $key);
			}

		}


	}

	/**
	 * Filters the columns in the "Agent Directory" screen, define our own.
	 */
	function columns_filter ( $columns ) {

		$columns = array(
			'cb'					=> '<input type="checkbox" />',
			'aeprofiles_thumbnail'	=> __( 'Thumbnail', 'aep' ),
			'title'					=> __( 'Agent Name', 'aep' ),
			'agent_details'			=> __( 'Details', 'aep' ),
		);

		return $columns;

	}

	/**
	 * Filters the data that shows up in the columns in the "Agent Directory" screen, define our own.
	 */
	function columns_data( $column ) {

		global $post, $wp_taxonomies;

		switch( $column ) {
			case "aeprofiles_thumbnail":
				printf( '<p>%s</p>', genesis_get_image( array( 'size' => 'thumbnail' ) ) );
				break;
			case "agent_details":
				foreach ( (array) $this->agent_details['col1'] as $label => $key ) {
					printf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
				}
				break;
		}

	}

}