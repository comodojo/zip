<?php namespace Comodojo\Zip;

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

class Zip implements Countable {

    public const SKIP_NONE = 'NONE';

    public const SKIP_HIDDEN = 'HIDDEN';

    public const SKIP_ALL = 'ALL';

    public const SKIP_COMODOJO = 'COMODOJO';

    protected const DEFAULT_MASK = 0777;

    /**
     * Select files to skip
     *
     * @var string
     */
    private $skip_mode = self::SKIP_NONE;

    /**
     * Supported skip modes
     *
     * @var bool
     */
    private $supported_skip_modes = ['NONE', 'HIDDEN', 'ALL', 'COMODOJO'];

    /**
     * Mask for the extraction folder (if it should be created)
     *
     * @var int
     */
    private $mask = self::DEFAULT_MASK;

    /**
     * ZipArchive internal pointer
     *
     * @var ZipArchive
     */
    private $zip_archive;

    /**
     * zip file name
     *
     * @var string
     */
    private $zip_file;

    /**
     * zip file password (only for extract)
     *
     * @var string
     */
    private $password;

    /**
     * Current base path
     *
     * @var string
     */
    private $path;

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
     * Open a zip archive (static constructor)
     *
     * @param string $zip_file File name
     *
     * @return Zip
     * @throws ZipException
     */
    public static function open(string $zip_file): Zip {

        try {

            $zip = new Zip($zip_file);
            $zip->setArchive(self::openZipFile($zip_file));

        } catch (ZipException $ze) {
            throw $ze;
        }

        return $zip;

    }

    /**
     * Check a zip archive (static constructor)
     *
     * @param string $zip_file ZIP file name
     *
     * @return bool
     * @throws ZipException
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
     * Create a new zip archive (static constructor)
     *
     * @param string $zip_file ZIP file name
     * @param bool $overwrite Overwrite existing file (if any)
     *
     * @return Zip
     * @throws ZipException
     */
    public static function create(string $zip_file, bool $overwrite = false): Zip {

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
        return count($this->zip_archive);
    }

    /**
     * Set files to skip
     *
     * Supported skip modes:
     *  Zip::SKIP_NONE - skip no files
     *  Zip::SKIP_HIDDEN - skip hidden files
     *  Zip::SKIP_ALL - skip HIDDEN + COMODOJO ghost files
     *  Zip::SKIP_COMODOJO - skip comodojo ghost files
     *
     * @param string $mode Skip file mode
     *
     * @return  Zip
     * @throws  ZipException
     */
    public function setSkipped(string $mode): Zip {

        $mode = strtoupper($mode);

        if ( !in_array($mode, $this->supported_skip_modes) ) {
            throw new ZipException("Unsupported skip mode: $mode");
        }

        $this->skip_mode = $mode;

        return $this;

    }

    /**
     * Get current skip mode
     *
     * @return string
     */
    public function getSkipped(): string {

        return $this->skip_mode;

    }

    /**
     * Set zip password
     *
     * @param string $password
     *
     * @return Zip
     */
    public function setPassword(string $password): Zip {

        $this->password = $password;

        return $this;

    }

    /**
     * Get current zip password
     *
     * @return string
     */
    public function getPassword(): ?string {

        return $this->password;

    }

    /**
     * Set current base path (to add relative files to zip archive)
     *
     * @param string|null $path
     *
     * @return Zip
     * @throws ZipException
     */
    public function setPath(?string $path = null): Zip {

        if ( $path === null ) {
            $this->path = null;
        } else if ( !file_exists($path) ) {
            throw new ZipException("Not existent path: $path");
        } else {
            $this->path = $path;
        }

        return $this;

    }

    /**
     * Get current base path
     *
     * @return string|null
     */
    public function getPath(): ?string {

        return $this->path;

    }

    /**
     * Set the mask of the extraction folder
     *
     * @param int $mask Integer representation of the file mask
     *
     * @return Zip
     */
    public function setMask(int $mask): Zip {

        $mask = filter_var($mask, FILTER_VALIDATE_INT, [
            "options" => [
                "max_range" => self::DEFAULT_MASK,
                "default" => self::DEFAULT_MASK
            ],
            'flags' => FILTER_FLAG_ALLOW_OCTAL
        ]);
        $this->mask = $mask;

        return $this;

    }

    /**
     * Get current mask of the extraction folder
     *
     * @return int
     */
    public function getMask(): int {

        return $this->mask;

    }

    /**
     * Set the current ZipArchive object
     *
     * @param ZipArchive $zip
     *
     * @return Zip
     */
    public function setArchive(ZipArchive $zip): Zip {

        $this->zip_archive = $zip;

        return $this;

    }

    /**
     * Get current ZipArchive object
     *
     * @return ZipArchive|null
     */
    public function getArchive(): ?ZipArchive {

        return $this->zip_archive;

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
     * Get the list of files in the archive as an array
     *
     * @return array
     * @throws ZipException
     */
    public function listFiles(): array {

        $list = [];

        for ( $i = 0; $i < $this->zip_archive->numFiles; $i++ ) {

            $name = $this->zip_archive->getNameIndex($i);
            if ( $name === false ) {
                throw new ZipException(StatusCodes::get($this->zip_archive->status));
            }
            $list[] = $name;

        }

        return $list;

    }

    /**
     * Extract files from zip archive
     *
     * @param string $destination Destination path
     * @param mixed $files (optional) a filename or an array of filenames
     *
     * @return bool
     * @throws ZipException
     */
    public function extract(string $destination, $files = null): bool {

        if ( empty($destination) ) {
            throw new ZipException("Invalid destination path: $destination");
        }

        if ( !file_exists($destination) ) {

            $omask = umask(0);
            $action = mkdir($destination, $this->mask, true);
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

        if ( !empty($this->password) ) {
            $this->zip_archive->setPassword($this->password);
        }

        $extract = $this->zip_archive->extractTo($destination, $file_matrix);

        if ( $extract === false ) {
            throw new ZipException(StatusCodes::get($this->zip_archive->status));
        }

        return true;

    }

    /**
     * Add files to zip archive
     *
     * @param mixed $file_name_or_array Filename to add or an array of filenames
     * @param bool $flatten_root_folder In case of directory, specify if root folder should be flatten or not
     *
     * @return Zip
     * @throws ZipException
     */
    public function add($file_name_or_array, bool $flatten_root_folder = false): Zip {

        if ( empty($file_name_or_array) ) {
            throw new ZipException(StatusCodes::get(ZipArchive::ER_NOENT));
        }

        $flatten_root_folder = DataFilter::filterBoolean($flatten_root_folder);

        try {

            if ( is_array($file_name_or_array) ) {
                foreach ( $file_name_or_array as $file_name ) {
                    $this->addItem($file_name, $flatten_root_folder);
                }
            } else {
                $this->addItem($file_name_or_array, $flatten_root_folder);
            }

        } catch (ZipException $ze) {
            throw $ze;
        }

        return $this;

    }

    /**
     * Delete files from zip archive
     *
     * @param mixed $file_name_or_array Filename to delete or an array of filenames
     *
     * @return Zip
     * @throws ZipException
     */
    public function delete($file_name_or_array): Zip {

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
     * Close the zip archive
     *
     * @return bool
     * @throws ZipException
     */
    public function close(): bool {

        if ( $this->zip_archive->close() === false ) {
            throw new ZipException(StatusCodes::get($this->zip_archive->status));
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

        for ( $i = 0; $i < $this->zip_archive->numFiles; $i++ ) {

            $file = $this->zip_archive->statIndex($i);
            if ( $file === false ) {
                continue;
            }

            $name = str_replace('\\', '/', $file['name']);
            if (
                $name[0] == "." &&
                in_array($this->skip_mode, ["HIDDEN", "ALL"])
            ) {
                continue;
            }

            if (
                $name[0] == "." &&
                @$name[1] == "_" &&
                in_array($this->skip_mode, ["COMODOJO", "ALL"])
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
        ?string $base = null
    ): void {

        $file = is_null($this->path) ? $file : $this->path."/$file";
        $real_file = str_replace('\\', '/', realpath($file));
        $real_name = basename($real_file);

        if ( !is_null($base) ) {

            if (
                $real_name[0] == "." &&
                in_array($this->skip_mode, ["HIDDEN", "ALL"])
            ) {
                return;
            }

            if (
                $real_name[0] == "." &&
                @$real_name[1] == "_" &&
                in_array($this->skip_mode, ["COMODOJO", "ALL"])
            ) {
                return;
            }

        }

        if ( is_dir($real_file) ) {

            if ( !$flatroot ) {

                $folder_target = is_null($base) ? $real_name : $base.$real_name;
                $new_folder = $this->zip_archive->addEmptyDir($folder_target);
                if ( $new_folder === false ) {
                    throw new ZipException(StatusCodes::get($this->zip_archive->status));
                }

            } else {
                $folder_target = null;
            }

            foreach ( new DirectoryIterator($real_file) as $path ) {

                if ( $path->isDot() ) {
                    continue;
                }

                $file_real = $path->getPathname();
                $base = is_null($folder_target) ? null : ($folder_target."/");

                try {
                    $this->addItem($file_real, false, $base);
                } catch (ZipException $ze) {
                    throw $ze;
                }

            }

        } else if ( is_file($real_file) ) {

            $file_target = is_null($base) ? $real_name : $base.$real_name;
            $add_file = $this->zip_archive->addFile($real_file, $file_target);
            if ( $add_file === false ) {
                throw new ZipException(StatusCodes::get($this->zip_archive->status));
            }

        } else {
            return;
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

        $deleted = $this->zip_archive->deleteName($file);
        if ( $deleted === false ) {
            throw new ZipException(StatusCodes::get($this->zip_archive->status));
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
