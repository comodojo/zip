<?php declare(strict_types=1);

namespace Comodojo\Zip\Tests;

use \Comodojo\Zip\Zip;

class ZipTest extends AbstractTestCase {

    public function testConstruct() {

        $name = $this->tmp('fake.zip');

        $zip = new Zip($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

    }

    public function testCreate() {

        $name = $this->tmp('test_1.zip');

        $zip = Zip::create($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->add($this->resource('lorem.txt'));

        $this->assertTrue($zip->close());

    }

    public function testCheck() {

        $name = $this->tmp('test_1.zip');

        $check = Zip::check($name);

        $this->assertTrue($check);

    }

    public function testOpen() {

        $name = $this->tmp('test_1.zip');

        $zip = Zip::open($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $this->assertTrue($zip->close());

    }

    public function testMultipleAdd() {

        $name = $this->tmp('test_2.zip');

        $zip = Zip::create($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->setPath($this->resource(null));

        $zip->add('lorem.txt');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->add('keepcalm.png');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $this->assertTrue($zip->close());

    }

    public function testListFiles() {

        $name = $this->tmp('test_2.zip');

        $zipFileShouldContain = ['lorem.txt','keepcalm.png'];

        $zip = Zip::open($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $list = $zip->listFiles();

        $this->assertEmpty(array_diff($list, $zipFileShouldContain));

        $this->assertTrue($zip->close());

    }

    public function testMask() {

        $name = $this->tmp('fake.zip');
        $new_mask = 0764;

        $zip = new Zip($name);

        $zip->setMask($new_mask);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $mask = $zip->getMask();

        $this->assertSame($new_mask, $mask);

    }

    public function testSkipped() {

        $name = $this->tmp('fake.zip');

        $zip = new Zip($name);

        $zip->setSkipMode("HIDDEN");

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $skip_mode = $zip->getSkipMode();

        $this->assertSame("HIDDEN", $skip_mode);

    }

    public function testPassword() {

        $name = $this->resource('lorem.zip');
        $pass = "verycomplexpassword";
        $dest = $this->tmp('test_password_extract');

        $zip = Zip::open($name);

        $zip->setPassword($pass);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $result = $zip->extract($dest);

        $this->assertTrue($result);

    }

    public function testGetArchive() {

        $name = $this->tmp('test_2.zip');

        $zip = Zip::open($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $archive = $zip->getArchive();

        $this->assertInstanceOf('\ZipArchive', $archive);

    }

    public function testExtract() {

        $name = $this->tmp('test_2.zip');
        $dest = $this->tmp('test_2_extract_1');

        $zip = Zip::open($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $result = $zip->extract($dest);

        $this->assertTrue($result);

    }

    public function testRecursiveAdd() {

        $name = $this->tmp('test_3.zip');
        $path = $this->resource(null);

        $zip = Zip::create($name);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $zip->add($path, true);

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $this->assertTrue($zip->close());

    }

    public function testDelete() {

        $name = $this->tmp('test_3.zip');

        $zip = Zip::open($name);

        $zip->delete('keepcalm.png');

        $this->assertInstanceOf('\Comodojo\Zip\Zip', $zip);

        $this->assertTrue($zip->close());

    }

    public function testCount() {

        $name = $this->tmp('test_4.zip');

        $zip = Zip::create($name);

        $this->assertEquals(0, count($zip));

        $zip->add($this->resource('lorem.txt'));

        $this->assertEquals(1, count($zip));

        $this->assertTrue($zip->close());

    }

    public function testInvalidSkipMode() {

        $this->expectException("\Comodojo\Exception\ZipException");

        $name = $this->tmp('test_2.zip');

        $zip = new Zip($name);

        $zip->setSkipMode("FOO");

    }

    /**
     * @dataProvider compressionProvider
     */
    public function testFileCompression($idx, $comp) {

        $name = $this->tmp("test_$idx.zip");

        $zip = Zip::create($name);

        $zip->add($this->resource('lorem.txt'), false, $comp);

        $this->assertTrue($zip->close());

    }

    public function compressionProvider() {
        return [
            [10, Zip::CM_STORE],
            [11, Zip::CM_DEFLATE],
            [12, Zip::CM_DEFAULT]
        ];
    }

    public function testMixedFileCompression() {

        $name = $this->tmp("test_15.zip");

        $zip = Zip::create($name);

        $zip->add($this->resource('lorem.txt'), false, Zip::CM_STORE)
            ->add($this->resource('keepcalm.png'), false, Zip::CM_DEFAULT)
            ->add($this->resource('resource'), false, Zip::CM_DEFLATE);

        $this->assertTrue($zip->close());

    }

    public function testOpenCompressedFile() {

        $name = $this->tmp('test_15.zip');
        $zip = Zip::open($name);

        $zip->delete('keepcalm.png');
        $this->assertTrue($zip->close());

    }

    /**
     * @dataProvider encryptionProvider
     */
    public function testFileEncrypt($idx, $encryption) {

        $name = $this->tmp("test_$idx.zip");

        $zip = Zip::create($name);

        $zip->setPassword('FordPerfect')
            ->add(
                $this->resource('lorem.txt'),
                false,
                Zip::CM_DEFAULT,
                $encryption
            );

        $this->assertTrue($zip->close());

    }

    public function encryptionProvider() {
        return [
            [20, Zip::EM_AES_128],
            [21, Zip::EM_AES_192],
            [22, Zip::EM_AES_256]
        ];
    }

    public function testMixedFileEncryption() {

        $name = $this->tmp("test_25.zip");

        $zip = Zip::create($name);

        $zip->setPassword('FordPerfect')
            ->add(
                $this->resource('lorem.txt'),
                false,
                Zip::CM_DEFAULT,
                Zip::EM_AES_192
            )
            ->add(
                $this->resource('keepcalm.png'),
                false,
                Zip::CM_DEFAULT,
                Zip::EM_AES_256
            )
            ->add(
                $this->resource('resource'),
                false,
                Zip::CM_DEFAULT,
                Zip::EM_NONE
            );

        $this->assertTrue($zip->close());

    }

    public function testSetArchiveComment() {

        $name = $this->tmp("test_26.zip");
        $zip = Zip::create($name);
        $zip->add($this->resource('lorem.txt'));
        $comment = "This is a comment";

        $zip->setComment($comment);
        $this->assertEquals($comment, $zip->getComment());

    }

    public function testGetArchiveComment() {

        $name = $this->tmp("test_26.zip");
        $zip = Zip::open($name);
        $comment = "This is a comment";
        $this->assertEquals($comment, $zip->getComment());

    }

}
