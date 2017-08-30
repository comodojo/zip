<?php namespace Comodojo\Zip;

use \Comodojo\Foundation\Validation\DataFilter;
use \Comodojo\Exception\ZipException;
use \ZipArchive;

/**
 * comodojo/zip - ZipArchive toolbox
 *
 * This class provide methods to handle single zip archive
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

class Zip extends ZipBase {

    /**
     * Open a zip archive
     *
     * @param   string  $zip_file   ZIP file name
     *
     * @return  Zip
     * @throws  ZipException
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
     * Check a zip archive
     *
     * @param   string  $zip_file   ZIP file name
     *
     * @return  bool
     * @throws  ZipException
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
     * Create a new zip archive
     *
     * @param   string  $zip_file   ZIP file name
     * @param   bool    $overwrite  overwrite existing file (if any)
     *
     * @return  Zip
     * @throws  ZipException
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
     * Open a zip file
     *
     * @param   string $zip_file   ZIP status code
     * @param   int    $flags      ZIP status code
     *
     * @return  ZipArchive
     * @throws  ZipException
     */
    private static function openZipFile(string $zip_file, $flags = null): ZipArchive {

        $zip = new ZipArchive();

        $open = $zip->open($zip_file, $flags);

        if ( $open !== true ) throw new ZipException(StatusCodes::get($open));

        return $zip;

    }

}
