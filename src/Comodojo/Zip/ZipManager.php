<?php namespace Comodojo\Zip;

use \Comodojo\Zip\Base\ManagerTools;
use \Comodojo\Foundation\Utils\UniqueId;
use \Comodojo\Exception\ZipException;
use \Countable;
use \Exception;

/**
 * Multiple Comodojo\Zip\Zip manager
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

class ZipManager implements Countable {

    /**
     * Array of managed zip files
     *
     * @var array
     */
    private array $zip_archives = [];

    /**
     * Count the number of Zip objects registered to the manager
     *
     * @return int
     */
    public function count(): int {
        return count($this->zip_archives);
    }

    /**
     * Add a Zip object to manager and return its id
     *
     * @param Zip $zip
     *
     * @return string
     */
    public function addZip(Zip $zip): string {

        $id = UniqueId::generate(32);
        $this->zip_archives[$id] = $zip;
        return $id;

    }

    /**
     * Remove a Zip object from manager
     *
     * @param Zip $zip
     *
     * @return bool
     * @throws ZipException
     */
    public function removeZip(Zip $zip): bool {

        $archive_key = array_search($zip, $this->zip_archives, true);
        if ( $archive_key === false ) {
            throw new ZipException("Archive not found");
        }

        unset($this->zip_archives[$archive_key]);
        return true;

    }

    /**
     * Remove a Zip object from manager by Zip id
     *
     * @param string $id
     *
     * @return bool
     * @throws ZipException
     */
    public function removeZipById(string $id): bool {

        if ( isset($this->zip_archives[$id]) === false ) {
            throw new ZipException("Archive: $id not found");
        }
        unset($this->zip_archives[$id]);
        return true;

    }

    /**
     * Get a list of all registered Zips filenames as an array
     *
     * @return array
     */
    public function listZips(): array {

        return array_column(
            array_map(function($key, $archive) {
                return ["key" => $key, "file" => $archive->getZipFile()];
            }, array_keys($this->zip_archives), $this->zip_archives),
        "file", "key");

    }

    /**
     * Get a Zip object by Id
     *
     * @param string $id The zip id
     *
     * @return Zip
     * @throws ZipException
     */
    public function getZip(string $id): Zip {

        if ( array_key_exists($id, $this->zip_archives) === false ) {
            throw new ZipException("Archive id $id not found");
        }
        return $this->zip_archives[$id];

    }

    /**
     * Set current base path (to add relative files to zip archive)
     * for all Zips
     *
     * @param string $path
     *
     * @return ZipManager
     * @throws ZipException
     */
    public function setPath(string $path): ZipManager {

        try {
            foreach ( $this->zip_archives as $archive ) {
                $archive->setPath($path);
            }
            return $this;
        } catch (ZipException $ze) {
            throw $ze;
        }

    }

    /**
     * Get a list of paths used by Zips
     *
     * @return  array
     */
    public function getPath(): array {

        return array_column(
            array_map(function($key, $archive) {
                return ["key" => $key, "path" => $archive->getPath()];
            }, array_keys($this->zip_archives), $this->zip_archives),
        "path", "key");

    }

    /**
     * Set default file mask for all Zips
     *
     * @param int $mask
     *
     * @return ZipManager
     * @throws ZipException
     */
    public function setMask(int $mask): ZipManager {

        try {
            foreach ( $this->zip_archives as $archive ) {
                $archive->setMask($mask);
            }
            return $this;
        } catch (ZipException $ze) {
            throw $ze;
        }

    }

    /**
     * Get a list of masks from Zips
     *
     * @return array
     */
    public function getMask(): array {

        return array_column(
            array_map(function($key, $archive) {
                return ["key" => $key, "mask" => $archive->getMask()];
            }, array_keys($this->zip_archives), $this->zip_archives),
        "mask", "key");

    }

    /**
     * Get a list of files in Zips
     *
     * @return array
     * @throws ZipException
     */
    public function listFiles(): array {

        try {
            return array_column(
                array_map(function($key, $archive) {
                    return ["key" => $key, "files" => $archive->listFiles()];
                }, array_keys($this->zip_archives), $this->zip_archives),
            "files", "key");
        } catch (ZipException $ze) {
            throw $ze;
        }

    }

    /**
     * Extract Zips to common destination
     *
     * @param string $destination Destination path
     * @param bool $separate (optional) If true (default), files will be placed in different directories
     * @param mixed $files (optional) a filename or an array of filenames
     *
     * @return bool
     * @throws ZipException
     */
    public function extract(
        string $destination,
        bool $separate = true,
        $files = null
    ): bool {

        try {
            foreach ( $this->zip_archives as $archive ) {

                $local_path = substr($destination, -1) == '/' ? $destination : $destination.'/';
                $local_file = pathinfo($archive->getZipFile());
                $local_destination = $separate ? ($local_path.$local_file['filename']) : $destination;

                $archive->extract($local_destination, $files);

            }
            return true;
        } catch (ZipException $ze) {
            throw $ze;
        }

    }

    /**
     * Merge multiple Zips into one
     *
     * @param string $output_zip_file
     *  Destination zip
     * @param bool $separate (optional)
     *  If true (default), files will be placed in different directories
     *
     * @return bool
     * @throws ZipException
     */
    public function merge(string $output_zip_file, bool $separate = true): bool {

        $pathinfo = pathinfo($output_zip_file);
        $temporary_folder = $pathinfo['dirname']."/".ManagerTools::getTemporaryFolder();

        try {

            $this->extract($temporary_folder, $separate);
            $zip = Zip::create($output_zip_file);
            $zip->add($temporary_folder, true)->close();
            ManagerTools::recursiveUnlink($temporary_folder);
            return true;

        } catch (ZipException $ze) {
            throw $ze;
        } catch (Exception $e) {
            throw $e;
        }

    }

    /**
     * Add a file to all registered Zips
     *
     * @param mixed $file_name_or_array
     *  The filename to add or an array of filenames
     * @param bool $flatten_root_folder
     *  (optional) If true, the Zip root folder will be flattened (default: false)
     *
     * @return ZipManager
     * @throws ZipException
     */
    public function add($file_name_or_array, bool $flatten_root_folder = false): ZipManager {

        try {

            foreach ( $this->zip_archives as $archive ) {
                $archive->add($file_name_or_array, $flatten_root_folder);
            }
            return $this;

        } catch (ZipException $ze) {
            throw $ze;
        }

    }

    /**
     * Delete a file from any registered Zip
     *
     * @param mixed $file_name_or_array
     *  The filename to add or an array of filenames
     *
     * @return ZipManager
     * @throws ZipException
     */
    public function delete($file_name_or_array): ZipManager {

        try {

            foreach ( $this->zip_archives as $archive ) {
                $archive->delete($file_name_or_array);
            }
            return $this;

        } catch (ZipException $ze) {
            throw $ze;
        }

    }

    /**
     * Close all Zips
     *
     * @return bool
     * @throws ZipException
     */
    public function close(): bool {

        try {
            foreach ( $this->zip_archives as $archive ) {
                $archive->close();
            }
            return true;
        } catch (ZipException $ze) {
            throw $ze;
        }

    }

}
