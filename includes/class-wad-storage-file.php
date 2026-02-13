<?php

class WAD_Storage_File implements WAD_Storage {

	private $log_dir;

	public function __construct() {
		$upload_dir = wp_upload_dir();
		$this->log_dir = $upload_dir['basedir'] . '/wad-analytics-logs';
		if ( ! file_exists( $this->log_dir ) ) {
			mkdir( $this->log_dir, 0755, true );
			// Protect logs from public access
			file_put_contents( $this->log_dir . '/.htaccess', 'Deny from all' );
			file_put_contents( $this->log_dir . '/index.php', '<?php // Silence is golden.' );
		}
	}

	public function save( $data ) {
		$file = $this->log_dir . '/' . current_time( 'Y-m-d' ) . '.log';
		$entry = json_encode( array_merge( [ 'timestamp' => current_time( 'mysql' ) ], $data ) ) . "\n";
		file_put_contents( $file, $entry, FILE_APPEND | LOCK_EX );
	}

	public function get_stats( $from, $to ) {
		// Naive implementation: iterate all files in range.
		// For production, this should use caching or daily summaries.
		$stats = [];
		$period = new DatePeriod(
			new DateTime( $from ),
			new DateInterval( 'P1D' ),
			new DateTime( $to . ' +1 day' )
		);

		foreach ( $period as $date ) {
			$date_str = $date->format( 'Y-m-d' );
			$file = $this->log_dir . '/' . $date_str . '.log';
			
			$visitors = [];
			$pageviews = 0;

			if ( file_exists( $file ) ) {
				$lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
				foreach ( $lines as $line ) {
					$entry = json_decode( $line, true );
					if ( $entry ) {
						$pageviews++;
						$visitors[] = $entry['hash_id'];
					}
				}
			}

			$stats[] = [
				'date'      => $date_str,
				'visitors'  => count( array_unique( $visitors ) ),
				'pageviews' => $pageviews,
			];
		}
		
		return $stats;
	}
}
