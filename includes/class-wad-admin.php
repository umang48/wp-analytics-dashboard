<?php

class WAD_Admin {

	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function add_menu_page() {
		add_menu_page(
			'Analytics',
			'Analytics',
			'manage_options',
			'wp-analytics-dashboard',
			array( $this, 'render_admin_page' ),
			'dashicons-chart-area',
			2
		);
	}

	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_wp-analytics-dashboard' !== $hook ) {
			return;
		}

		$asset_path = WAD_PLUGIN_DIR . 'build/index.asset.php';
		
		if ( ! file_exists( $asset_path ) ) {
			// If build files are missing, show a warning or fallback
			wp_die( 'Please run `npm run build` to generate the dashboard assets.' );
		}

		$asset_file = include( $asset_path );

		wp_enqueue_script(
			'wad-dashboard',
			WAD_PLUGIN_URL . 'build/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_localize_script( 'wad-dashboard', 'wadSettings', array(
			'apiUrl' => rest_url( 'wad/v1/' ),
			'nonce'  => wp_create_nonce( 'wp_rest' ),
			'currentStorageType' => get_option( 'wad_storage_type', 'database' ),
		) );

		wp_enqueue_style(
			'wad-dashboard-style',
			WAD_PLUGIN_URL . 'build/index.css',
			array(),
			$asset_file['version']
		);
	}

	public function render_admin_page() {
		echo '<div id="wad-dashboard-root"></div>';
	}
}
