<?php

class WAD_REST {

	private $storage;

	public function __construct( $storage_type = 'database' ) {
		if ( 'file' === $storage_type ) {
			if ( ! class_exists( 'WAD_Storage_File' ) ) {
				require_once WAD_PLUGIN_DIR . 'includes/class-wad-storage-file.php';
			}
			$this->storage = new WAD_Storage_File();
		} else {
			if ( ! class_exists( 'WAD_Storage_DB' ) ) {
				require_once WAD_PLUGIN_DIR . 'includes/class-wad-storage-db.php';
			}
			$this->storage = new WAD_Storage_DB();
		}
	}

	public function register_routes() {
		// Public tracking endpoint
		register_rest_route( 'wad/v1', '/track', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'track_view' ),
			'permission_callback' => '__return_true',
		) );

		// Admin stats endpoint
		register_rest_route( 'wad/v1', '/stats', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_stats' ),
			'permission_callback' => array( $this, 'check_admin_permissions' ),
		) );

		// Admin settings endpoint
		register_rest_route( 'wad/v1', '/settings', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'update_settings' ),
			'permission_callback' => array( $this, 'check_admin_permissions' ),
		) );
	}

	public function track_view( $request ) {
		$params = $request->get_json_params();

		// Basic Privacy-First Hashing
		$ip = $_SERVER['REMOTE_ADDR'];
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$salt = wp_salt( 'auth' ); 
		$hash_id = hash( 'sha256', $ip . $ua . date('Y-m-d') . $salt );

		$data = array(
			'path'     => sanitize_text_field( $params['path'] ?? '' ),
			'referrer' => sanitize_text_field( $params['referrer'] ?? '' ),
			'hash_id'  => $hash_id,
			'device'   => wp_is_mobile() ? 'mobile' : 'desktop',
			'browser'  => 'unknown', // Simplified
		);

		$this->storage->save( $data );

		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	public function get_stats( $request ) {
		$period = $request->get_param( 'period' ) ?? '7days';
		
		$to = current_time( 'Y-m-d' );
		$from = date( 'Y-m-d', strtotime( '-7 days' ) );

		if ( '30days' === $period ) {
			$from = date( 'Y-m-d', strtotime( '-30 days' ) );
		}

		$stats = $this->storage->get_stats( $from, $to );
		
		return new WP_REST_Response( $stats, 200 );
	}

	public function update_settings( $request ) {
		$params = $request->get_json_params();
		$storage = sanitize_text_field( $params['storage_type'] );

		if ( in_array( $storage, [ 'database', 'file' ] ) ) {
			update_option( 'wad_storage_type', $storage );
			return new WP_REST_Response( array( 'success' => true ), 200 );
		}

		return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid storage type' ), 400 );
	}

	public function check_admin_permissions() {
		return current_user_can( 'manage_options' );
	}
}
