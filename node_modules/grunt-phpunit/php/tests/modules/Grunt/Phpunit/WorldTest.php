<?php
/**
 * Tests for World class
 *
 */

class WorldTest extends PHPUnit_Framework_TestCase {

    public function testCanConstruct() {

        $this->assertInstanceOf('\Grunt\PhpUnit\World', new Grunt\PhpUnit\World('Earth'));
    }

    public function testCanSetName() {

        $sut = new Grunt\PhpUnit\World('Earth');
        $sut->setName('Mars');
        $this->assertEquals('Mars', $sut->getName());
    }

    public function testCanGetName() {

        $sut = new Grunt\PhpUnit\World('Earth');
        $this->assertEquals('Earth', $sut->getName());
    }
}