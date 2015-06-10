<?php namespace Comodojo\Zip;

use \Comodojo\Zip\Zip;
use \Comodojo\Exception\ZipException;
use \Exception;

/**
 * Multiple zip archive mangager
 * 
 * @package     Comodojo Spare Parts
 * @author      Marco Giovinazzi <info@comodojo.org>
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
 
 class ZipManager {

    private $zip_archives = array();
    
    public function addZip(\Comodojo\Zip\Zip $zip) {
        
        $this->zip_archives[] = $zip;
        
        return $this;
        
    }
    
    public function removeZip(\Comodojo\Zip\Zip $zip) {
        
        $archive_key = array_search($zip, $this->zip_archives, true);
        
        if ( $archive_key === false ) throw new ZipException("Archive not found");
        
        unset($this->zip_archives[$archive_key]);
        
        return $this;
        
    }
    
    public function listZips() {
        
        $list = array();
        
        foreach ($this->zip_archives as $key=>$archive) $list[$key] = $archive->getZipFile();
        
        return $list;
        
    }
    
    public function getZip($zipId) {
        
        if ( array_key_exists($zipId, $this->zip_archives) === false) throw new ZipException("Archive not found");
        
        return $this->zip_archives[$zipId];
        
    }
    
    /**
     * Set current base path (just for add relative files to zip archive)
     * for all zip files
     *
     * @param   string  $path
     *
     * @return  \Comodojo\Zip\Zip
     * @throws  \Comodojo\Exception\ZipException
     */
    public function setPath($path) {
        
        try {
            
            foreach ($this->zip_archives as $archive) $archive->setPath($path);
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return $this;
        
    }
    
    public function getPath() {
        
        $paths = array();
        
        foreach ($this->zip_archives as $key=>$archive) $paths[$key] = $archive->getPath();
        
        return $paths;
        
    }
    
    public function setMask() {
        
        try {
            
            foreach ($this->zip_archives as $archive) $archive->setPath($path);
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return $this;
        
    }
    
    public function getMask() {
        
        $masks = array();
        
        foreach ($this->zip_archives as $key=>$archive) $masks[$key] = $archive->getMask();
        
        return $masks;
        
    }
    
    public function listFiles() {
        
        $files = array();
        
        try {
        
            foreach ($this->zip_archives as $key=>$archive) $files[$key] = $archive->listFiles();
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return $masks;
        
    }
    
    public function extract($destination, $separate=true, $files=null) {
        
        try {
        
            foreach ($this->zip_archives as $archive) {
            
                $local_path = substr($destination, -1) == '/' ? $destination : $destination.'/';
                
                $local_file = pathinfo($archive->getZipFile());
            
                $local_destination = $separate ? ( $local_path . $local_file['filename'] ) : $destination;
               
                $archive->extract($local_destination, $files=null);
                
            }
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return true;
        
    }
    
    public function merge($output_zip_file, $separate=true) {
        
        $pathinfo = pathinfo($output_zip_file);
        
        $temporary_folder = $pathinfo['dirname'] . "/" . self::getTemporaryFolder();
        
        try {
        
            $this->extract($temporary_folder, $separate, null);
            
            $zip = Zip::create($output_zip_file);

            $zip->add($temporary_folder, true)->close();
            
            self::recursiveUnlink($temporary_folder);
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        } catch (Exception $e) {
            
            throw $e;
            
        }
        
        return true;
        
    }
    
    public function add($file_name_or_array) {
        
        try {
        
            foreach ($this->zip_archives as $archive) $archive->add($file_name_or_array);
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return $this;
        
    }
    
    public function delete($file_name_or_array) {
        
        try {
        
            foreach ($this->zip_archives as $archive) $archive->delete($file_name_or_array);
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return $this;
        
    }
    
    public function close() {
        
        try {
        
            foreach ($this->zip_archives as $archive) $archive->close();
            
        } catch (ZipException $ze) {
            
            throw $ze;
            
        }
        
        return true;
        
    }
    
    static private function removeExtension($filename) {
        
        $file_info = pathinfo($filename);

        return $file_info['filename'];

    }
    
    static private function getTemporaryFolder() {
        
        return "zip-temp-folder-" . md5(uniqid(rand(), true), 0);
        
    }
    
    private static function recursiveUnlink($folder) {

        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            
            $pathname = $path->getPathname();

            if ( $path->isDir() ) {

                $action = rmdir($pathname);

            } 
            else {

                $action = unlink($pathname);

            }

            if ( $action === false ) throw new Exception("Error deleting ".$pathname." during recursive unlink of folder ".$folder);

        }

        $action = rmdir($folder);

        if ( $action === false ) throw new Exception("Error deleting folder ".$folder);

    }
    
 }