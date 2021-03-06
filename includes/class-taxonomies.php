<?php
/**
 * This file contains the Genesis_Agent_Profiles_Taxonomies class.
 */

/**
 * This class handles all the aspects of displaying, creating, and editing the
 * user-created taxonomies for the "aeprofiles" post-type.
 *
 */
class Genesis_Agent_Profiles_Taxonomies {

	var $settings_field = 'aeprofiles_taxonomies';
	var $menu_page = 'register-aep-taxonomies';

	/**
	 * Construct Method.
	 */
	function __construct() {

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'settings_init' ), 15 );
		add_action( 'admin_init', array( &$this, 'actions' ) );
		add_action( 'admin_notices', array( &$this, 'notices' ) );

		add_action( 'init', array( &$this, 'register_taxonomies' ), 15 );

	}

	function register_settings() {

		register_setting( $this->settings_field, $this->settings_field );
		add_option( $this->settings_field, __return_empty_array(), '', 'yes' );

	}

	function settings_init() {

		add_submenu_page( 'edit.php?post_type=aeprofiles', __( 'Register Taxonomies', 'aep' ), __( 'Register Taxonomies', 'aep' ), 'manage_options', $this->menu_page, array( &$this, 'admin' ) );

	}

	function actions() {

		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != $this->menu_page ) {
			return;
		}

		/** This section handles the data if a new taxonomy is created */
		if ( isset( $_REQUEST['action'] ) && 'create' == $_REQUEST['action'] ) {
			$this->create_taxonomy( $_POST['aeprofiles_taxonomy'] );
		}

		/** This section handles the data if a taxonomy is deleted */
		if ( isset( $_REQUEST['action'] ) && 'delete' == $_REQUEST['action'] ) {
			$this->delete_taxonomy( $_REQUEST['id'] );
		}

		/** This section handles the data if a taxonomy is being edited */
		if ( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] ) {
			$this->edit_taxonomy( $_POST['aeprofiles_taxonomy'] );
		}

	}

	function admin() {

		echo '<div class="wrap">';

			if ( isset( $_REQUEST['view'] ) && 'edit' == $_REQUEST['view'] ) {
				require( dirname( __FILE__ ) . '/views/edit-tax.php' );
			}
			else {
				require( dirname( __FILE__ ) . '/views/create-tax.php' );
			}

		echo '</div>';

	}

	function create_taxonomy( $args = array() ) {

		/**** VERIFY THE NONCE ****/

		/** No empty fields */
		if ( ! isset( $args['id'] ) || empty( $args['id'] ) )
			wp_die( __( 'Please complete all required fields.', 'aep' ) );
		if ( ! isset( $args['name'] ) || empty( $args['name'] ) )
			wp_die( __( 'Please complete all required fields.', 'aep' ) );
		if ( ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) )
			wp_die( __( 'Please complete all required fields.', 'aep' ) );

		extract( $args );

		$labels = array(
			'name'					=> strip_tags( $name ),
			'singular_name' 		=> strip_tags( $singular_name ),
			'menu_name'				=> strip_tags( $name ),

			'search_items'			=> sprintf( __( 'Search %s', 'aep' ), strip_tags( $name ) ),
			'popular_items'			=> sprintf( __( 'Popular %s', 'aep' ), strip_tags( $name ) ),
			'all_items'				=> sprintf( __( 'All %s', 'aep' ), strip_tags( $name ) ),
			'edit_item'				=> sprintf( __( 'Edit %s', 'aep' ), strip_tags( $singular_name ) ),
			'update_item'			=> sprintf( __( 'Update %s', 'aep' ), strip_tags( $singular_name ) ),
			'add_new_item'			=> sprintf( __( 'Add New %s', 'aep' ), strip_tags( $singular_name ) ),
			'new_item_name'			=> sprintf( __( 'New %s Name', 'aep' ), strip_tags( $singular_name ) ),
			'add_or_remove_items'	=> sprintf( __( 'Add or Remove %s', 'aep' ), strip_tags( $name ) ),
			'choose_from_most_used'	=> sprintf( __( 'Choose from the most used %s', 'aep' ), strip_tags( $name ) )
		);

		$args = array(
			'labels'		=> $labels,
			'hierarchical'	=> true,
			'rewrite'		=> array( 'slug' => $id ),
			'editable'		=> 1
		);

		$tax = array( $id => $args );

		$options = get_option( $this->settings_field );

		/** Update the options */
		update_option( $this->settings_field, wp_parse_args( $tax, $options ) );

		/** Flush rewrite rules */
		$this->register_taxonomies();
		flush_rewrite_rules();

		/** Redirect with notice */
		aep_admin_redirect( 'register-aep-taxonomies', array( 'created' => 'true' ) );
		exit;

	}

	function delete_taxonomy( $id = '' ) {

		/**** VERIFY THE NONCE ****/

		/** No empty ID */
		if ( ! isset( $id ) || empty( $id ) )
			wp_die( __( "Nice try, partner. But that taxonomy doesn't exist. Click back and try again.", 'aep' ) );

		$options = get_option( $this->settings_field );

		/** Look for the ID, delete if it exists */
		if ( array_key_exists( $id, (array) $options ) ) {
			unset( $options[$id] );
		} else {
			wp_die( __( "Nice try, partner. But that taxonomy doesn't exist. Click back and try again.", 'aep' ) );
		}

		/** Update the DB */
		update_option( $this->settings_field, $options );

		aep_admin_redirect( 'register-aep-taxonomies', array( 'deleted' => 'true' ) );
		exit;

	}

	function edit_taxonomy( $args = array() ) {

		/**** VERIFY THE NONCE ****/

		/** No empty fields */
		if ( ! isset( $args['id'] ) || empty( $args['id'] ) )
			wp_die( __( 'Please complete all required fields.', 'aep' ) );
		if ( ! isset( $args['name'] ) || empty( $args['name'] ) )
			wp_die( __( 'Please complete all required fields.', 'aep' ) );
		if ( ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) )
			wp_die( __( 'Please complete all required fields.', 'aep' ) );

		extract( $args );

		$labels = array(
			'name'					=> strip_tags( $name ),
			'singular_name' 		=> strip_tags( $singular_name ),
			'menu_name'				=> strip_tags( $name ),

			'search_items'			=> sprintf( __( 'Search %s', 'aep' ), strip_tags( $name ) ),
			'popular_items'			=> sprintf( __( 'Popular %s', 'aep' ), strip_tags( $name ) ),
			'all_items'				=> sprintf( __( 'All %s', 'aep' ), strip_tags( $name ) ),
			'edit_item'				=> sprintf( __( 'Edit %s', 'aep' ), strip_tags( $singular_name ) ),
			'update_item'			=> sprintf( __( 'Update %s', 'aep' ), strip_tags( $singular_name ) ),
			'add_new_item'			=> sprintf( __( 'Add New %s', 'aep' ), strip_tags( $singular_name ) ),
			'new_item_name'			=> sprintf( __( 'New %s Name', 'aep' ), strip_tags( $singular_name ) ),
			'add_or_remove_items'	=> sprintf( __( 'Add or Remove %s', 'aep' ), strip_tags( $name ) ),
			'choose_from_most_used'	=> sprintf( __( 'Choose from the most used %s', 'aep' ), strip_tags( $name ) )
		);

		$args = array(
			'labels'		=> $labels,
			'hierarchical'	=> true,
			'rewrite'		=> array( 'slug' => $id ),
			'editable'		=> 1
		);

		$tax = array( $id => $args );

		$options = get_option( $this->settings_field );

		update_option( $this->settings_field, wp_parse_args( $tax, $options ) );

		aep_admin_redirect( 'register-aep-taxonomies', array( 'edited' => 'true' ) );
		exit;

	}

	function notices() {

		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != $this->menu_page ) {
			return;
		}

		$format = '<div id="message" class="updated"><p><strong>%s</strong></p></div>';

		if ( isset( $_REQUEST['created'] ) && 'true' == $_REQUEST['created'] ) {
			printf( $format, __('New taxonomy successfully created!', 'aep') );
			return;
		}

		if ( isset( $_REQUEST['edited'] ) && 'true' == $_REQUEST['edited'] ) {
			printf( $format, __('Taxonomy successfully edited!', 'aep') );
			return;
		}

		if ( isset( $_REQUEST['deleted'] ) && 'true' == $_REQUEST['deleted'] ) {
			printf( $format, __('Taxonomy successfully deleted.', 'aep') );
			return;
		}

		return;

	}

	/**
	 * Create the taxonomies.
	 */
	function register_taxonomies() {

		foreach( (array) $this->get_taxonomies() as $id => $data ) {
			register_taxonomy( $id, array( 'aeprofiles' ), $data );
		}

	}

	/**
	 * Get the taxonomies.
	 */
	function get_taxonomies() {

		return array_merge( (array) get_option( $this->settings_field ) );

	}

}