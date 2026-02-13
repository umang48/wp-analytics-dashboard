<?php

class WAD_Core {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function run() {
		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
	}

	public function init_plugin() {
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_api_hooks();
	}

	private function load_dependencies() {
		require_once WAD_PLUGIN_DIR . 'includes/class-wad-storage.php';
		require_once WAD_PLUGIN_DIR . 'includes/class-wad-storage-db.php';
		require_once WAD_PLUGIN_DIR . 'includes/class-wad-storage-file.php';
		require_once WAD_PLUGIN_DIR . 'includes/class-wad-admin.php';
		require_once WAD_PLUGIN_DIR . 'includes/class-wad-rest.php';
	}

	private function define_admin_hooks() {
		$plugin_admin = new WAD_Admin();
		$plugin_admin->init();
	}

	private function define_public_hooks() {
		add_action( 'wp_footer', array( $this, 'inject_tracking_script' ) );
	}

	private function define_api_hooks() {
		$storage_type = get_option( 'wad_storage_type', 'database' );
		$api = new WAD_REST( $storage_type );
		add_action( 'rest_api_init', array( $api, 'register_routes' ) );
	}

	public function inject_tracking_script() {
		if ( current_user_can( 'manage_options' ) ) {
			// Optional: Don't track admins? Or make it a setting.
			// For now, let's track everyone for simplicity, or skip admins to avoid noise.
			// return;
		}
		
		// Don't track 404s if desired, or maybe we want to track them.
		// For now, track everything.

		?>
		<script>
		(function() {
			if (navigator.doNotTrack === '1') return;
			// Simple Beacon
			var data = {
				path: window.location.pathname,
				referrer: document.referrer
			};
			
			// Use sendBeacon if available for better reliability on unload, 
			// but fetch is fine for page load.
			fetch('<?php echo esc_url_raw( rest_url( 'wad/v1/track' ) ); ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(data),
				keepalive: true
			}).catch(function(e){});
		})();
		</script>
		<?php
	}

}
