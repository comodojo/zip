<?php namespace Comodojo\Zip;

use \Comodojo\Exception\ZipException;
use \Exception;

/**
 * Multiple ZipArchive manager
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

class ZipManager {

    /**
     * Array of managed zip files
     *
     * @var array
     */
    private $zip_archives = [];

    /**
     * Add a Zip object to manager
     *
     * @param   Zip  $zip
     *
     * @return  ZipManager
     */
    public function addZip(Zip $zip): self {

        $this->zip_archives[] = $zip;

        return $this;

    }

    /**
     * Remove a Zip object from manager
     *
     * @param   Zip  $zip
     *
     * @return  ZipManager
     * @throws  ZipException
     */
    public function removeZip(Zip $zip): self {

        $archive_key = array_search($zip, $this->zip_archives, true);

        if ( $archive_key === false ) throw new ZipException("Archive not found");

        unset($this->zip_archives[$archive_key]);

        return $this;

    }

    /**
     * Get a list of managed Zips
     *
     * @return  array
     */
    public function listZips(): array {

        return array_map(function($archive) {
            return $archive->getZipFile();
        }, $this->zip_archives);

    }

    /**
     * Get a  a Zip object
     *
     * @param   int    $zipId    The zip id from self::listZips()
     *
     * @return  Zip
     * @throws  ZipException
     */
    public function getZip(int $zipId): Zip {

        if ( array_key_exists($zipId, $this->zip_archives) === false ) throw new ZipException("Archive id $zipId not found");

        return $this->zip_archives[$zipId];

    }

    /**
     * Set current base path (just to add relative files to zip archive)
     * for all zip files
     *
     * @param   string  $path
     *
     * @return  ZipManager
     * @throws  ZipException
     */
    public function setPath(string $path): self {

        try {

            foreach ( $this->zip_archives as $archive ) $archive->setPath($path);

        } catch (ZipException $ze) {

            throw $ze;

        }

        return $this;

    }

    /**
     * Get a list of paths used by Zips
     *
     * @return  array
     */
    public function getPath(): array {

        return array_map(function($archive) {
            return $archive->getPath();
        }, $this->zip_archives);

    }

    /**
     * Set default file mask for all Zips
     *
     * @param   int  $mask
     *
     * @return  ZipManager
     * @throws  ZipException
     */
    public function setMask(int $mask): self {

        try {

            foreach ( $this->zip_archives as $archive ) $archive->setMask($mask);

        } catch (ZipException $ze) {

            throw $ze;

        }

        return $this;

    }

    /**
     * Get a list of masks from Zips
     *
     * @return  array
     */
    public function getMask() {

        return array_map(function($archive) {
            return $archive->getMask();
        }, $this->zip_archives);

    }

    /**
     * Get a list of files in Zips
     *
     * @return  array
     * @throws  ZipException
     */
    public function listFiles(): array {

        $files = [];

        try {

            foreach ( $this->zip_archives as $key=>$archive ) $files[$key] = $archive->listFiles();

        } catch (ZipException $ze) {

            throw $ze;

        }

        return $files;

    }

    /**
     * Extract Zips to common destination
     *
     * @param   string  $destination    Destination path
     * @param   bool    $separate       (optional) Specify if files should be placed in different directories
     * @param   mixed   $files          (optional) a filename or an array of filenames
     *
     * @return  bool
     * @throws  ZipException
     */
    public function extract(string $destination, bool $separate = true, $files = null): bool {

        try {

            foreach ( $this->zip_archives as $archive ) {

                $local_path = substr($destination, -1) == '/' ? $destination : $destination.'/';

                $local_file = pathinfo($archive->getZipFile());

                $local_destination = $separate ? ($local_path.$local_file['filename']) : $destination;

                $archive->extract($local_destination, $files);

            }

        } catch (ZipException $ze) {

            throw $ze;

        }

        return true;

    }

    /**
     * Merge multiple Zips into one
     *
     * @param   string  $output_zip_file    Destination zip
     * @param   bool    $separate           Specify if files should be placed in different directories
     *
     * @return  bool
     * @throws  ZipException
     */
    public function merge(string $output_zip_file, bool $separate = true): bool {

        $pathinfo = pathinfo($output_zip_file);

        $temporary_folder = $pathinfo['dirname']."/".ManagerTools::getTemporaryFolder();

        try {

            $this->extract($temporary_folder, $separate);

            $zip = Zip::create($output_zip_file);

            $zip->add($temporary_folder, true)->close();

            ManagerTools::recursiveUnlink($temporary_folder);

        } catch (ZipException $ze) {

            throw $ze;

        } catch (Exception $e) {

            throw $e;

        }

        return true;

    }

    /**
     * Add a file to zip
     *
     * @param   mixed   $file_name_or_array     filename to add or an array of filenames
     * @param   bool    $flatten_root_folder    in case of directory, specify if root folder should be flatten or not
     *
     * @return  ZipManager
     * @throws  ZipException
     */
    public function add($file_name_or_array, bool $flatten_root_folder = false): self {

        try {

            foreach ( $this->zip_archives as $archive ) $archive->add($file_name_or_array, $flatten_root_folder);

        } catch (ZipException $ze) {

            throw $ze;

        }

        return $this;

    }

    /**
     * Delete a file from Zips
     *
     * @param   mixed   $file_name_or_array     filename to add or an array of filenames
     *
     * @return  ZipManager
     * @throws  ZipException
     */
    public function delete($file_name_or_array): self {

        try {

            foreach ( $this->zip_archives as $archive ) $archive->delete($file_name_or_array);

        } catch (ZipException $ze) {

            throw $ze;

        }

        return $this;

    }

    /**
     * Close Zips
     *
     * @return  bool
     * @throws  ZipException
     */
    public function close(): bool {

        try {

            foreach ( $this->zip_archives as $archive ) $archive->close();

        } catch (ZipException $ze) {

            throw $ze;

        }

        return true;

    }

}
