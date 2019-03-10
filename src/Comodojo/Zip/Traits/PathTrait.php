<?php namespace Comodojo\Zip\Traits;

use \Comodojo\Zip\Interfaces\ZipInterface;
use \Comodojo\Exception\ZipException;

/**
 * Path helper trait.
 *
 * @package     Comodojo Zip
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

trait PathTrait {

    /**
     * Current base path
     *
     * @var string
     */
    private $path;

    /**
     * Set current base path (to add relative files to zip archive)
     *
     * @param string|null $path
     *
     * @return Zip
     * @throws ZipException
     */
    public function setPath(?string $path = null): ZipInterface {

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

}
