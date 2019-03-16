<?php namespace Comodojo\Zip\Traits;

use \Comodojo\Zip\Interfaces\ZipInterface;

/**
 * Set/get the archive comment.
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

trait CommentTrait {

    /**
     * Set the comment for the current archive
     *
     * @return ZipInterface
     */
    public function setComment(string $comment): ZipInterface {

        $this->getArchive()->setArchiveComment($comment);

        return $this;

    }

    /**
     * Get the current zip archive comment
     *
     * @return string
     */
    public function getComment(): ?string {

        return $this->getArchive()->getArchiveComment();

    }

}
