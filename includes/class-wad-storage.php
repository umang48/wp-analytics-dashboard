<?php

interface WAD_Storage {
	public function save( $data );
	public function get_stats( $from, $to );
}
