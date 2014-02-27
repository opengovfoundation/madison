<?php

class ElasticSearchTest extends TestCase {

	/**
	 * Tests for interaction with ElasticSearch
	 *
	 * @return void
	 */
	public function testAlive()
	{
		$params = array('hosts'=> Config::get('elasticsearch.hosts'));

		$es = new Elasticsearch\Client($params);

		$this->assertTrue($es->ping(), 'Elasticsearch not running.');
	}

}