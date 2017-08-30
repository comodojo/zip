<?php namespace Comodojo\Zip\Tests;

use \Comodojo\Zip\Zip;
use \Comodojo\Zip\ZipManager;

class ZipManagerTest extends AbstractTestCase {

    public function testConstruct() {

        $manager = new ZipManager();

        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);

    }

    public function testArchiveManagement() {

        $name_1 = $this->tmp('test_manager_1.zip');
        $name_2 = $this->tmp('test_manager_2.zip');

        $manager = new ZipManager();

        $zip_1 = Zip::create($name_1);
        $zip_2 = Zip::create($name_2);

        $addZip = $manager->addZip($zip_1)->addZip($zip_2);

        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);

        $list = $manager->listZips();

        $this->assertInternalType('array', $list);

        $this->assertCount(2, $list);

        $delZip = $manager->removeZip($zip_2);

        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);

        $list = $manager->listZips();

        $this->assertInternalType('array', $list);

        $this->assertCount(1, $list);

        $zip = $manager->getZip(0);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $close = $manager->close();

        $this->assertTrue($close);

    }

    public function testCreateArchives() {

        $name_3 = $this->tmp('test_manager_3.zip');
        $name_4 = $this->tmp('test_manager_4.zip');

        $lorem = $this->resource('lorem.txt');

        $manager = new ZipManager();

        $addZip = $manager
            ->addZip(Zip::create($name_3))
            ->addZip(Zip::create($name_4));

        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);

        $addFile = $manager->add($lorem);

        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $addFile);

        $close = $manager->close();

        $this->assertTrue($close);

    }

    public function testExtractArchives() {

        $name_3 = $this->tmp('test_manager_3.zip');
        $name_4 = $this->tmp('test_manager_4.zip');

        $dest = $this->tmp('test_manager_34_extract_1');

        $manager = new ZipManager();

        $addZip = $manager
            ->addZip(Zip::open($name_3))
            ->addZip(Zip::open($name_4));

        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);

        $extract = $manager->extract($dest);

        $this->assertTrue($extract);

        $close = $manager->close();

        $this->assertTrue($close);

    }

    public function testMergeZipArchives() {

        $name_5 = $this->tmp('test_manager_5.zip');
        $name_6 = $this->tmp('test_manager_6.zip');

        $mergefile = $this->tmp('test_manager_merge.zip');

        $lorem = $this->resource('lorem.txt');
        $kc = $this->resource('keepcalm.png');

        $zip_1 = Zip::create($name_5);

        $zip_1->add($lorem)->close();

        $zip_2 = Zip::create($name_6);

        $zip_2->add($kc)->close();

        $manager = new ZipManager();

        $addZip = $manager
            ->addZip(Zip::open($name_5))
            ->addZip(Zip::open($name_6));

        $merge = $manager->merge($mergefile);

        $this->assertTrue($merge);

        $manager->close();

    }

    /**
     * @expectedException        Comodojo\Exception\ZipException
     */
    public function testInvalidZipId() {

        $manager = new ZipManager();

        $manager->getZip(123456);

    }

}
