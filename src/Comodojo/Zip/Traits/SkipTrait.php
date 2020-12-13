<?php namespace Comodojo\Zip\Traits;

use \Comodojo\Zip\Interfaces\ZipInterface;
use \Comodojo\Exception\ZipException;

/**
 * Skip mode helper trait.
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

trait SkipTrait {

    /**
     * Select files to skip
     *
     * @var string
     */
    private string $skip_mode = 'NONE';

    /**
     * Supported skip modes
     *
     * @var array
     */
    private array $supported_skip_modes = ['NONE', 'HIDDEN', 'ALL', 'COMODOJO'];

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
    public function setSkipMode(string $mode): ZipInterface {

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
    public function getSkipMode(): string {

        return $this->skip_mode;

    }

}
