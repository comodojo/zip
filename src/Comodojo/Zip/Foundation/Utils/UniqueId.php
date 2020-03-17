<?php namespace Comodojo\Zip\Foundation\Utils;

use \InvalidArgumentException;

/**
 * @package     Comodojo Foundation
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

class UniqueId {

    /**
     * Generate a custom uid starting from a prefix
     *
     * The result uid format will be $string-[random]
     *
     * @param string $prefix
     * @param int $length
     * @return string
     */
    public static function generateCustom($prefix, $length=128) {

        if ( $length <= (strlen($prefix)+1) ) {
            throw new InvalidArgumentException("Uid length cannot be smaller than prefix length +1");

        }

        return "$prefix-".self::generate($length-(strlen($prefix)+1));

    }

    /**
     * Generate an uid
     *
     * @param int $length
     * @return string
     */
    public static function generate($length=128) {

        if ($length < 32) {

            return substr(self::getUid(), 0, $length);

        } else if ($length == 32) {

            return self::getUid();

        }  else {

            $numString = (int)($length/32) + 1;
            $randNum = "";
            for ($i = 0; $i < $numString; $i++) $randNum .= self::getUid();

            return substr($randNum, 0, $length);

        }

    }

    /**
     * Generate an uid (128 bit / 32 char random)
     *
     * @param int $length
     * @return string
     */
    protected static function getUid() {

        return md5(uniqid(rand(), true), 0);

    }

}