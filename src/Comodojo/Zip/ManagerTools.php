<?php namespace Comodojo\Zip;

use \Comodojo\Foundation\Utils\UniqueId;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use \FilesystemIterator;
use \Exception;

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

class ManagerTools {

    /**
     * Get a temporary folder name (random)
     *
     * @return string
     */
    public static function getTemporaryFolder(): string {

        return "zip-temp-folder-".UniqueId::generate();

    }

    /**
     * Unlink a folder recursively
     *
     * @param string $folder The folder to be removed
     * @param bool $remove_folder If true, the folder itself will be removed
     * @return bool
     * @throws Exception
     */
    public static function recursiveUnlink(string $folder, bool $remove_folder = true): bool {

        try {

            self::emptyFolder($folder);
            if ( $remove_folder && rmdir($folder) === false ) {
                throw new Exception("Error deleting folder: $folder");
            }
            return true;

        } catch (Exception $e) {
            throw $e;
        }

    }

    protected static function emptyFolder(string $folder): bool {

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ( $iterator as $path ) {

            $pathname = $path->getPathname();

            if ( $path->isDir() ) {
                $action = rmdir($pathname);
            } else {
                $action = unlink($pathname);
            }

            if ( $action === false ) {
                throw new Exception("Error deleting $pathname during recursive unlink of folder: $folder");
            }

        }

        return true;

    }

}
