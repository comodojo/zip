<?php declare(strict_types=1);

namespace Comodojo\Zip\Tests;

use \Comodojo\Zip\Base\StatusCodes;
use \PHPUnit\Framework\TestCase;
use \ZipArchive;

class StatusCodesTest extends TestCase {

    public function testKnownReturnCode() {

        $code = ZipArchive::ER_OK;

        $desc = StatusCodes::get($code);

        $this->assertEquals('No error', $desc);

    }

    public function testUnknownReturnCode() {

        $code = 42;

        $desc = StatusCodes::get($code);

        $this->assertStringStartsWith('Unknown status', $desc);

    }

}
