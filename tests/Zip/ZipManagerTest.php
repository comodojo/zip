<?php

use \Comodojo\Zip\Zip;
use \Comodojo\Zip\ZipManager;

class ZipManagerTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        
        $manager = new ZipManager();
        
        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);

    }
    
    public function testArchiveManagement() {
        
        $manager = new ZipManager();
        
        $zip_1 = Zip::create(__DIR__.'/../tmp/test_manager_1.zip');
        
        $zip_2 = Zip::create(__DIR__.'/../tmp/test_manager_2.zip');
        
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
        
        $manager = new ZipManager();
        
        $addZip = $manager->addZip(Zip::create(__DIR__.'/../tmp/test_manager_3.zip'))->addZip(Zip::create(__DIR__.'/../tmp/test_manager_4.zip'));
        
        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);
        
        $addFile = $manager->add(__DIR__.'/../resources/lorem.txt');
        
        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $addFile);
        
        $close = $manager->close();
        
        $this->assertTrue($close);
        
    }
    
    public function testExtractArchives() {
        
        $manager = new ZipManager();
        
        $addZip = $manager->addZip(Zip::open(__DIR__.'/../tmp/test_manager_3.zip'))->addZip(Zip::open(__DIR__.'/../tmp/test_manager_4.zip'));
        
        $this->assertInstanceOf('\Comodojo\Zip\ZipManager', $manager);
        
        $extract = $manager->extract(__DIR__.'/../tmp/test_manager_34_extract_1');
        
        $this->assertTrue($extract);
        
        $close = $manager->close();
        
        $this->assertTrue($close);
        
    }
    
    public function testMergeZipArchives() {
        
        $zip_1 = Zip::create(__DIR__.'/../tmp/test_manager_5.zip');

        $zip_1->add(__DIR__.'/../resources/lorem.txt')->close();
        
        $zip_2 = Zip::create(__DIR__.'/../tmp/test_manager_6.zip');

        $zip_2->add(__DIR__.'/../resources/keepcalm.png')->close();
        
        $manager = new ZipManager();
        
        $addZip = $manager->addZip(Zip::open(__DIR__.'/../tmp/test_manager_5.zip'))->addZip(Zip::open(__DIR__.'/../tmp/test_manager_6.zip'));
        
        $merge = $manager->merge(__DIR__.'/../tmp/test_manager_merge.zip');
        
        $this->assertTrue($merge);
        
        $manager->close();
        
    }

}
