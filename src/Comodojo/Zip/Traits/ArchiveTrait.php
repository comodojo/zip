<?php namespace Comodojo\Zip\Traits;

use \Comodojo\Zip\Interfaces\ZipInterface;
use \ZipArchive;

/**
 * Archive helper trait.
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

trait ArchiveTrait {

    /**
     * ZipArchive internal pointer
     *
     * @var ZipArchive
     */
    private $zip_archive;

    /**
     * Set the current ZipArchive object
     *
     * @param ZipArchive $zip
     *
     * @return Zip
     */
    public function setArchive(ZipArchive $zip): ZipInterface {

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

}
