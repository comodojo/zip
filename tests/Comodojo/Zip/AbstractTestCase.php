<?php namespace Comodojo\Zip\Tests;

use \PHPUnit\Framework\TestCase;
use \Comodojo\Zip\ManagerTools;

class AbstractTestCase extends TestCase {

    protected $root;

    protected function setUp() {
        $this->root = realpath(dirname(__FILE__)."/../../root/");
    }

    public static function setUpBeforeClass() {
        self::cleanupTmp();
    }

    public static function tearDownAfterClass() {
        self::cleanupTmp();
    }

    protected function tmp(?string $file) {
        return $this->root."/tmp/".$file;
    }

    protected function root(?string $file) {
        return $this->root."/".$file;
    }

    protected function resource(?string $file) {
        return $this->root."/resources/".$file;
    }

    private static function cleanupTmp() {
        $tmp = realpath(dirname(__FILE__)."/../../root/tmp/");
        ManagerTools::recursiveUnlink($tmp, false);
        file_put_contents(
            "$tmp/.placeholder",
            "this file intentionally left blank\nyou can safely remove it"
        );    
    }

}
