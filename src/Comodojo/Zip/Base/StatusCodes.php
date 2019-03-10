<?php namespace Comodojo\Zip\Base;

use \ZipArchive;

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

class StatusCodes {

    /**
     * Array of well known zip status codes
     *
     * @const array
     */
    const ZIP_STATUS_CODES = [
        ZipArchive::ER_OK           => 'No error',
        ZipArchive::ER_MULTIDISK    => 'Multi-disk zip archives not supported',
        ZipArchive::ER_RENAME       => 'Renaming temporary file failed',
        ZipArchive::ER_CLOSE        => 'Closing zip archive failed',
        ZipArchive::ER_SEEK         => 'Seek error',
        ZipArchive::ER_READ         => 'Read error',
        ZipArchive::ER_WRITE        => 'Write error',
        ZipArchive::ER_CRC          => 'CRC error',
        ZipArchive::ER_ZIPCLOSED    => 'Containing zip archive was closed',
        ZipArchive::ER_NOENT        => 'No such file',
        ZipArchive::ER_EXISTS       => 'File already exists',
        ZipArchive::ER_OPEN         => 'Can\'t open file',
        ZipArchive::ER_TMPOPEN      => 'Failure to create temporary file',
        ZipArchive::ER_ZLIB         => 'Zlib error',
        ZipArchive::ER_MEMORY       => 'Malloc failure',
        ZipArchive::ER_CHANGED      => 'Entry has been changed',
        ZipArchive::ER_COMPNOTSUPP  => 'Compression method not supported',
        ZipArchive::ER_EOF          => 'Premature EOF',
        ZipArchive::ER_INVAL        => 'Invalid argument',
        ZipArchive::ER_NOZIP        => 'Not a zip archive',
        ZipArchive::ER_INTERNAL     => 'Internal error',
        ZipArchive::ER_INCONS       => 'Zip archive inconsistent',
        ZipArchive::ER_REMOVE       => 'Can\'t remove file',
        ZipArchive::ER_DELETED      => 'Entry has been deleted'
    ];

    /**
     * Get status from zip status code
     *
     * @param   int $code   ZIP status code
     *
     * @return  string
     */
    public static function get(int $code): string {

        if ( array_key_exists($code, self::ZIP_STATUS_CODES) ) return self::ZIP_STATUS_CODES[$code];

        else return sprintf('Unknown status %s', $code);

    }

}
