<?php

namespace Comodojo\Zip\Traits;

use \Comodojo\Zip\Interfaces\ZipInterface;

/**
 * File mask helper trait.
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

trait MaskTrait
{

    /**
     * Mask for the extraction folder (if it should be created)
     *
     * @var int
     */
    private int $mask = 0777;

    /**
     * Set the mask of the extraction folder
     *
     * @param int $mask Integer representation of the file mask
     *
     * @return ZipInterface
     */
    public function setMask(int $mask): ZipInterface
    {
        $mask = filter_var($mask, FILTER_VALIDATE_INT, [
            "options" => [
                "max_range" => 0777,
                "default" => 0777
            ],
            'flags' => FILTER_FLAG_ALLOW_OCTAL
        ]);
        $this->mask = $mask;

        return $this;
    }

    /**
     * Get current mask of the extraction folder
     *
     * @return int
     */
    public function getMask(): int
    {
        return $this->mask;
    }
}
