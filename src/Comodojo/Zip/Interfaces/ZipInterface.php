<?php namespace Comodojo\Zip\Interfaces;

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

interface ZipInterface {

    public const SKIP_NONE = 'NONE';

    public const SKIP_HIDDEN = 'HIDDEN';

    public const SKIP_ALL = 'ALL';

    public const SKIP_COMODOJO = 'COMODOJO';

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

}
