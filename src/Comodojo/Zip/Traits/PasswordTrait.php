<?php

namespace Comodojo\Zip\Traits;

use \Comodojo\Zip\Interfaces\ZipInterface;
use \ZipArchive;

/**
 * Password helper trait.
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

trait PasswordTrait
{

    abstract public function getArchive(): ?ZipArchive;

    /**
     * zip file password
     *
     * @var string
     */
    private ?string $password = null;

    /**
     * Set zip password
     *
     * @param string $password
     *
     * @return ZipInterface
     */
    public function setPassword(string $password): ZipInterface
    {
        $this->password = $password;
        $this->getArchive()->setPassword($password);

        return $this;
    }

    /**
     * Get current zip password
     *
     * @return string
     */
    protected function getPassword(): ?string
    {
        return $this->password;
    }
}
