<?php

class WAD_Storage_DB implements WAD_Storage {

	private $table_name;

	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'wad_analytics_data';
	}

	public function save( $data ) {
		global $wpdb;
		return $wpdb->insert(
			$this->table_name,
			array(
				'date'       => current_time( 'Y-m-d' ),
				'path'       => $data['path'], // sanitized
				'hash_id'    => $data['hash_id'],
				'device'     => $data['device'],
				'browser'    => $data['browser'],
				'referrer'   => $data['referrer'],
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	public function get_stats( $from, $to ) {
		global $wpdb;

		// Get raw data
		$query = $wpdb->prepare(
			"SELECT date, COUNT(DISTINCT hash_id) as visitors, COUNT(*) as pageviews 
			 FROM {$this->table_name} 
			 WHERE date BETWEEN %s AND %s 
			 GROUP BY date 
			 ORDER BY date ASC",
			$from,
			$to
		);
		$results = $wpdb->get_results( $query, ARRAY_A );

		// Index results by date
		$data_by_date = [];
		foreach ( $results as $row ) {
			$data_by_date[ $row['date'] ] = $row;
		}

		// Iterate full date range to fill gaps
		$stats = [];
		$period = new DatePeriod(
			new DateTime( $from ),
			new DateInterval( 'P1D' ),
			new DateTime( $to . ' +1 day' )
		);

		foreach ( $period as $date ) {
			$date_str = $date->format( 'Y-m-d' );
			if ( isset( $data_by_date[ $date_str ] ) ) {
				$stats[] = array(
					'date'      => $date_str,
					'visitors'  => (int) $data_by_date[ $date_str ]['visitors'],
					'pageviews' => (int) $data_by_date[ $date_str ]['pageviews'],
				);
			} else {
				$stats[] = array(
					'date'      => $date_str,
					'visitors'  => 0,
					'pageviews' => 0,
				);
			}
		}

		return $stats;
	}
}
