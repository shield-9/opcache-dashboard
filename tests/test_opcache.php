<?php

class WP_Test_OPcache_dashboard extends WP_UnitTestCase {
	public function test_init() {
		$this->assertInstanceOf( 'OPcache_dashboard', OPcache_dashboard::init() );
	}
}
