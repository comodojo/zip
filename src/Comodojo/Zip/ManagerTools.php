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

    public static function getTemporaryFolder(): string {

        // return UniqueId::generateCustom("zip-temp-folder");

        return "zip-temp-folder-".UniqueId::generate();

    }

    /**
     * @param string $folder
     */
    public static function recursiveUnlink(string $folder, bool $remove_folder = true): bool {

        try {

            self::emptyFolder($folder);

            if ( $remove_folder && rmdir($folder) === false ) {
                throw new Exception("Error deleting folder ".$folder);
            }

        } catch (Exception $e) {

            throw $e;

        }

        return true;

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

            if ( $action === false ) throw new Exception("Error deleting ".$pathname." during recursive unlink of folder ".$folder);

        }

        return true;

    }

}
