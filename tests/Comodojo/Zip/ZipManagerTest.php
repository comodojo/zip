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

        $this->assertEquals(0, count($manager));

        $zip_1 = Zip::create($name_1);
        $zip_2 = Zip::create($name_2);

        $zip_1_id = $manager->addZip($zip_1);
        $zip_2_id = $manager->addZip($zip_2);
        $this->assertIsString($zip_1_id);
        $this->assertIsString($zip_2_id);

        $this->assertEquals(2, count($manager));

        $list = $manager->listZips();

        $this->assertIsArray($list);
        $this->assertCount(2, $list);

        $this->assertTrue($manager->removeZip($zip_2));

        $list = $manager->listZips();
        $this->assertIsArray($list);
        $this->assertCount(1, $list);

        $zip = $manager->getZip($zip_1_id);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);
        $this->assertStringEndsWith('test_manager_1.zip', $zip->getZipFile());

        $this->assertTrue($manager->removeZipById($zip_1_id));
        $this->assertEquals(0, count($manager));

        $close = $manager->close();
        $this->assertTrue($close);

    }

    public function testCreateArchives() {

        $name_3 = $this->tmp('test_manager_3.zip');
        $name_4 = $this->tmp('test_manager_4.zip');

        $lorem = $this->resource('lorem.txt');

        $manager = new ZipManager();

        $this->assertIsString($manager->addZip(Zip::create($name_3)));
        $this->assertIsString($manager->addZip(Zip::create($name_4)));

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

        $this->assertIsString($manager->addZip(Zip::open($name_3)));
        $this->assertIsString($manager->addZip(Zip::open($name_4)));

        $extract = $manager->extract($dest);

        $this->assertTrue($extract);

        $close = $manager->close();

        $this->assertTrue($close);

    }

    public function testListFiles() {

        $name_3 = $this->tmp('test_manager_3.zip');
        $name_4 = $this->tmp('test_manager_4.zip');
        $manager = new ZipManager();

        $this->assertIsString($manager->addZip(Zip::open($name_3)));
        $this->assertIsString($manager->addZip(Zip::open($name_4)));

        $files = $manager->listFiles();
        $this->assertIsArray($files);
        foreach ($files as $key => $list) {
            $this->assertIsString($key);
            $this->assertIsArray($list);
            $this->assertCount(1, $list);
        }

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

        $this->assertIsString($manager->addZip(Zip::open($name_5)));
        $this->assertIsString($manager->addZip(Zip::open($name_6)));

        $merge = $manager->merge($mergefile);

        $this->assertTrue($merge);

        $manager->close();

    }

    public function testInvalidZipId() {

        $this->expectException("\Comodojo\Exception\ZipException");

        $manager = new ZipManager();

        $manager->getZip(123456);

    }

}
