<?php

class ElasticSearchTest extends TestCase {

	/**
	 * Tests for interaction with ElasticSearch
	 *
	 * @return void
	 */
	public function testAlive()
	{
		$params = array('hosts'=> array('localhost:9200'));

		$es = new Elasticsearch\Client($params);

		$this->assertTrue($es->ping());
	}

}