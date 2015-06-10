<?php

use \Comodojo\Zip\Zip;

class ZipTest extends \PHPUnit_Framework_TestCase {

    public function testCreate() {
        
        $zip = Zip::create(__DIR__.'/../tmp/test_1.zip');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->add(__DIR__.'/../resources/lorem.txt');

        $close = $zip->close();

        $this->assertTrue($close);

    }

    public function testCheck() {
        
        $check = Zip::check(__DIR__.'/../tmp/test_1.zip');

        $this->assertTrue($check);

    }

    public function testOpen() {
        
        $zip = Zip::open(__DIR__.'/../tmp/test_1.zip');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $close = $zip->close();

        $this->assertTrue($close);

    }

    public function testMultipleAdd() {

        $zip = Zip::create(__DIR__.'/../tmp/test_2.zip');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->setPath(__DIR__.'/../resources');

        $zip->add('lorem.txt');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->add('keepcalm.png');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $close = $zip->close();

        $this->assertTrue($close);

    }

    public function testListFiles() {

        $zipFileShouldContain = array('lorem.txt','keepcalm.png');

        $zip = Zip::open(__DIR__.'/../tmp/test_2.zip');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $list = $zip->listFiles();

        $this->assertEmpty(array_diff($list, $zipFileShouldContain));

        $close = $zip->close();

        $this->assertTrue($close);

    }

    public function testMask() {

        $zip = new Zip('fake.zip');

        $zip->setMask(777);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $mask = $zip->getMask();

        $this->assertSame(777, $mask);

    }

    public function testExtract() {

        $zip = Zip::open(__DIR__.'/../tmp/test_2.zip');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->setMask(777);

        $result = $zip->extract(__DIR__.'/../tmp/test_2_extract_1');

        $this->assertTrue($result);

    }

}
