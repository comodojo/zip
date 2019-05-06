<?php namespace Comodojo\Zip;

use \Comodojo\Zip\Interfaces\ZipInterface;
use \Comodojo\Zip\Base\StatusCodes;
use \Comodojo\Zip\Traits\{
    SkipTrait,
    MaskTrait,
    PasswordTrait,
    PathTrait,
    ArchiveTrait,
    CommentTrait
};
use \Comodojo\Foundation\Validation\DataFilter;
use \Comodojo\Exception\ZipException;
use \ZipArchive;
use \DirectoryIterator;
use \Countable;

/**
 * comodojo/zip - ZipArchive toolbox
 *
 * @package     Comodojo Spare Parts
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @license     MIT
 *
 * LICENSE:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Zip implements ZipInterface, Countable {

    use SkipTrait;
    use MaskTrait;
    use PasswordTrait;
    use PathTrait;
    use ArchiveTrait;
    use CommentTrait;

    /**
     * zip file name
     *
     * @var string
     */
    private $zip_file;

    /**
     * Class constructor
     *
     * @param string $zip_file ZIP file name
     *
     * @throws ZipException
     */
    public function __construct(string $zip_file) {

        if ( empty($zip_file) ) {
            throw new ZipException(StatusCodes::get(ZipArchive::ER_NOENT));
        }

        $this->zip_file = $zip_file;

    }

    /**
     * {@inheritdoc}
     */
    public static function open(string $zip_file): ZipInterface {

        try {

            $zip = new Zip($zip_file);
            $zip->setArchive(self::openZipFile($zip_file));

        } catch (ZipException $ze) {
            throw $ze;
        }

        return $zip;

    }

    /**
     * {@inheritdoc}
     */
    public static function check(string $zip_file): bool {

        try {

            $zip = self::openZipFile($zip_file, ZipArchive::CHECKCONS);
            $zip->close();

        } catch (ZipException $ze) {
            throw $ze;
        }

        return true;

    }

    /**
     * {@inheritdoc}
     */
    public static function create(string $zip_file, bool $overwrite = false): ZipInterface {

        $overwrite = DataFilter::filterBoolean($overwrite);

        try {

            $zip = new Zip($zip_file);

            if ( $overwrite ) {
                $zip->setArchive(
                    self::openZipFile(
                        $zip_file,
                        ZipArchive::CREATE | ZipArchive::OVERWRITE
                    )
                );
            } else {
                $zip->setArchive(
                    self::openZipFile($zip_file, ZipArchive::CREATE)
                );
            }

        } catch (ZipException $ze) {
            throw $ze;
        }

        return $zip;

    }

    /**
     * Count the number of files in the archive
     *
     * @return int
     */
    public function count(): int {
        return count(/** @scrutinizer ignore-type */ $this->getArchive());
    }

    /**
     * Get current zip file
     *
     * @return string
     */
    public function getZipFile(): string {

        return $this->zip_file;

    }

    /**
     * {@inheritdoc}
     */
    public function listFiles(): array {

        $list = [];

        for ( $i = 0; $i < $this->getArchive()->numFiles; $i++ ) {

            $name = $this->getArchive()->getNameIndex($i);
            if ( $name === false ) {
                throw new ZipException(StatusCodes::get($this->getArchive()->status));
            }
            $list[] = $name;

        }

        return $list;

    }

    /**
     * {@inheritdoc}
     */
    public function extract(string $destination, $files = null): bool {

        if ( empty($destination) ) {
            throw new ZipException("Invalid destination path: $destination");
        }

        if ( !file_exists($destination) ) {

            $omask = umask(0);
            $action = mkdir($destination, $this->getMask(), true);
            umask($omask);

            if ( $action === false ) {
                throw new ZipException("Error creating folder: $destination");
            }

        }

        if ( !is_writable($destination) ) {
            throw new ZipException("Destination path $destination not writable");
        }

        if ( is_array($files) && @sizeof($files) != 0 ) {
            $file_matrix = $files;
        } else {
            $file_matrix = $this->getArchiveFiles();
        }

        if ( $this->getArchive()->extractTo($destination, $file_matrix) === false ) {
            throw new ZipException(StatusCodes::get($this->getArchive()->status));
        }

        return true;

    }

    /**
     * {@inheritdoc}
     */
    public function add(
        $file_name_or_array,
        bool $flatten_root_folder = false,
        int $compression = self::CM_DEFAULT,
        int $encryption = self::EM_NONE
    ): ZipInterface {

        if ( empty($file_name_or_array) ) {
            throw new ZipException(StatusCodes::get(ZipArchive::ER_NOENT));
        }

        if ( $encryption !== self::EM_NONE && $this->getPassword() === null ) {
            throw new ZipException("Cannot encrypt resource: no password set");
        }

        $flatten_root_folder = DataFilter::filterBoolean($flatten_root_folder);

        try {

            if ( is_array($file_name_or_array) ) {
                foreach ( $file_name_or_array as $file_name ) {
                    $this->addItem($file_name, $flatten_root_folder, $compression, $encryption);
                }
            } else {
                $this->addItem($file_name_or_array, $flatten_root_folder, $compression, $encryption);
            }

        } catch (ZipException $ze) {
            throw $ze;
        }

        return $this;

    }

    /**
     * {@inheritdoc}
     */
    public function delete($file_name_or_array): ZipInterface {

        if ( empty($file_name_or_array) ) {
            throw new ZipException(StatusCodes::get(ZipArchive::ER_NOENT));
        }

        try {

            if ( is_array($file_name_or_array) ) {
                foreach ( $file_name_or_array as $file_name ) {
                    $this->deleteItem($file_name);
                }
            } else {
                $this->deleteItem($file_name_or_array);
            }

        } catch (ZipException $ze) {
            throw $ze;
        }

        return $this;

    }

    /**
     * {@inheritdoc}
     */
    public function close(): bool {

        if ( $this->getArchive()->close() === false ) {
            throw new ZipException(StatusCodes::get($this->getArchive()->status));
        }

        return true;

    }

    /**
     * Get a list of file contained in zip archive before extraction
     *
     * @return array
     */
    private function getArchiveFiles(): array {

        $list = [];

        for ( $i = 0; $i < $this->getArchive()->numFiles; $i++ ) {

            $file = $this->getArchive()->statIndex($i);
            if ( $file === false ) {
                continue;
            }

            $name = str_replace('\\', '/', $file['name']);
            if (
                (
                    $name[0] == "." &&
                    in_array($this->getSkipMode(), ["HIDDEN", "ALL"])
                ) ||
                (
                    $name[0] == "." &&
                    @$name[1] == "_" &&
                    in_array($this->getSkipMode(), ["COMODOJO", "ALL"])
                )
            ) {
                continue;
            }

            $list[] = $name;

        }

        return $list;

    }

    /**
     * Add item to zip archive
     *
     * @param string $file File to add (realpath)
     * @param bool $flatroot (optional) If true, source directory will be not included
     * @param string $base (optional) Base to record in zip file
     * @return void
     * @throws ZipException
     */
    private function addItem(
        string $file,
        bool $flatroot = false,
        int $compression = self::CM_DEFAULT,
        int $encryption = self::EM_NONE,
        ?string $base = null
    ): void {

        $file = is_null($this->getPath()) ? $file : $this->getPath()."/$file";
        $real_file = str_replace('\\', '/', realpath($file));
        $real_name = basename($real_file);

        if (
            $base !== null &&
            (
                (
                    $real_name[0] == "." &&
                    in_array($this->getSkipMode(), ["HIDDEN", "ALL"])
                ) ||
                (
                    $real_name[0] == "." &&
                    @$real_name[1] == "_" &&
                    in_array($this->getSkipMode(), ["COMODOJO", "ALL"])
                )
            )
        ) {
            return;
        }

        if ( is_dir($real_file) ) {
            $this->addDirectoryItem($real_file, $real_name, $compression, $encryption, $base, $flatroot);
        } else {
            $this->addFileItem($real_file, $real_name, $compression, $encryption, $base);
        }

    }

    private function addFileItem(
        string $real_file,
        string $real_name,
        int $compression = self::CM_DEFAULT,
        int $encryption = self::EM_NONE,
        ?string $base = null
    ): void {

        $file_target = is_null($base) ? $real_name : $base.$real_name;
        if (
            $this->getArchive()->addFile($real_file, $file_target) === false ||
            $this->getArchive()->setCompressionName($file_target, $compression) === false ||
            $this->getArchive()->setEncryptionName($file_target, $encryption) === false
        ) {
            throw new ZipException(StatusCodes::get($this->getArchive()->status));
        }

    }

    private function addDirectoryItem(
        string $real_file,
        string $real_name,
        int $compression = self::CM_DEFAULT,
        int $encryption = self::EM_NONE,
        ?string $base = null,
        bool $flatroot = false
    ): void {

        if ( !$flatroot ) {
            $folder_target = $base.$real_name;
            $new_base = "$folder_target/";
            if ( $this->getArchive()->addEmptyDir($folder_target) === false ) {
                throw new ZipException(StatusCodes::get($this->getArchive()->status));
            }
        } else {
            $new_base = null;
        }

        foreach ( new DirectoryIterator($real_file) as $path ) {

            if ( $path->isDot() ) {
                continue;
            }

            try {
                $this->addItem(
                    $path->getPathname(),
                    false,
                    $compression,
                    $encryption,
                    $new_base
                );
            } catch (ZipException $ze) {
                throw $ze;
            }

        }

    }

    /**
     * Delete item from zip archive
     *
     * @param string $file File to delete (zippath)
     * @return void
     * @throws ZipException
     */
    private function deleteItem(string $file): void {

        if ( $this->getArchive()->deleteName($file) === false ) {
            throw new ZipException(StatusCodes::get($this->getArchive()->status));
        }

    }

    /**
     * Open a zip file
     *
     * @param string $zip_file ZIP file name
     * @param int $flags ZipArchive::open flags
     *
     * @return  ZipArchive
     * @throws  ZipException
     */
    private static function openZipFile(string $zip_file, int $flags = null): ZipArchive {

        $zip = new ZipArchive();

        $open = $zip->open($zip_file, $flags);
        if ( $open !== true ) {
            throw new ZipException(StatusCodes::get($open));
        }

        return $zip;

    }

}
