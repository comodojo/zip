<?php namespace Comodojo\Zip\Interfaces;

use \Comodojo\Exception\ZipException;
use \ZipArchive;

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

interface ZipInterface {

    public const SKIP_NONE = 'NONE';

    public const SKIP_HIDDEN = 'HIDDEN';

    public const SKIP_ALL = 'ALL';

    public const SKIP_COMODOJO = 'COMODOJO';

    public const CM_DEFAULT = ZipArchive::CM_DEFAULT;

    public const CM_STORE = ZipArchive::CM_STORE;

    public const CM_DEFLATE = ZipArchive::CM_DEFLATE;

    public const EM_NONE = ZipArchive::EM_NONE;

    public const EM_AES_128 = ZipArchive::EM_AES_128;

    public const EM_AES_192 = ZipArchive::EM_AES_192;

    public const EM_AES_256 = ZipArchive::EM_AES_256;

    /**
     * Open a zip archive (static constructor)
     *
     * @param string $zip_file File name
     *
     * @return Zip
     * @throws ZipException
     */
    public static function open(string $zip_file): ZipInterface;

    /**
     * Check a zip archive (static constructor)
     *
     * @param string $zip_file ZIP file name
     *
     * @return bool
     * @throws ZipException
     */
    public static function check(string $zip_file): bool;

    /**
     * Create a new zip archive (static constructor)
     *
     * @param string $zip_file ZIP file name
     * @param bool $overwrite Overwrite existing file (if any)
     *
     * @return Zip
     * @throws ZipException
     */
    public static function create(string $zip_file, bool $overwrite = false): ZipInterface;

    /**
     * Get the list of files in the archive as an array
     *
     * @return array
     * @throws ZipException
     */
    public function listFiles(): array;

    /**
     * Extract files from zip archive
     *
     * @param string $destination Destination path
     * @param mixed $files (optional) a filename or an array of filenames
     *
     * @return bool
     * @throws ZipException
     */
    public function extract(string $destination, $files = null): bool;

    /**
     * Add files to the ZipArchive
     *
     * @param mixed $file_name_or_array
     *  Filename to add or an array of filenames
     * @param bool $flatten_root_folder
     *  In case of directory, specify if root folder should be flatten or not
     * @param int $compression
     *  Compression algorithm to use (default CM_DEFAULT)
     *
     * @return Zip
     * @throws ZipException
     */
    public function add(
        $file_name_or_array,
        bool $flatten_root_folder = false,
        int $compression = self::CM_DEFAULT,
        int $encryption = self::EM_NONE
    ): ZipInterface;

    /**
     * Delete files from ZipArchive
     *
     * @param mixed $file_name_or_array
     *  Filename to delete or an array of filenames
     *
     * @return Zip
     * @throws ZipException
     */
    public function delete($file_name_or_array): ZipInterface;

    /**
     * Close the ZipArchive
     *
     * @return bool
     * @throws ZipException
     */
    public function close(): bool;

}
