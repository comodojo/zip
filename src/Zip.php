<?php namespace Comodojo\Zip;

/**
 * zip: poor man's php zip/unzip class
 * 
 * @package 	Comodojo zip (Spare Parts)
 * @author		Marco Giovinazzi <info@comodojo.org>
 * @license 	GPL-3.0+
 *
 * LICENSE:
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use \Comodojo\Exception\ZipException;

/**
 * Comodojo zip main class
 */
class Zip {
	
	/**
	 * If true, zip will skip hidden files
	 *
	 * @var	bool
	 */
	private $skip_hidden_files = true;

	/**
	 * If true, zip will skip comodojo filesystem hidden files
	 *
	 * @var	bool
	 */
	private $skip_comodojo_internal = true;

	/**
	 * Mask for the extraction folder (if it should be created)
	 *
	 * @var	int
	 */
	private $mask = 0644;

	/**
	 * Array of well known zip status codes
	 *
	 * @var	array
	 */
	private $zip_status_codes = Array(
		\ZipArchive::ER_OK           =>	'No error',
        \ZipArchive::ER_MULTIDISK    =>	'Multi-disk zip archives not supported',
        \ZipArchive::ER_RENAME       =>	'Renaming temporary file failed',
        \ZipArchive::ER_CLOSE        =>	'Closing zip archive failed',
        \ZipArchive::ER_SEEK         =>	'Seek error',
        \ZipArchive::ER_READ         =>	'Read error',
        \ZipArchive::ER_WRITE        =>	'Write error',
        \ZipArchive::ER_CRC          =>	'CRC error',
        \ZipArchive::ER_ZIPCLOSED    =>	'Containing zip archive was closed',
        \ZipArchive::ER_NOENT        =>	'No such file',
        \ZipArchive::ER_EXISTS       =>	'File already exists',
        \ZipArchive::ER_OPEN         =>	'Can\'t open file',
        \ZipArchive::ER_TMPOPEN      =>	'Failure to create temporary file',
        \ZipArchive::ER_ZLIB         =>	'Zlib error',
        \ZipArchive::ER_MEMORY       =>	'Malloc failure',
        \ZipArchive::ER_CHANGED      =>	'Entry has been changed',
        \ZipArchive::ER_COMPNOTSUPP  =>	'Compression method not supported',
        \ZipArchive::ER_EOF          =>	'Premature EOF',
        \ZipArchive::ER_INVAL        =>	'Invalid argument',
        \ZipArchive::ER_NOZIP        =>	'Not a zip archive',
        \ZipArchive::ER_INTERNAL     =>	'Internal error',
        \ZipArchive::ER_INCONS       =>	'Zip archive inconsistent',
        \ZipArchive::ER_REMOVE       =>	'Can\'t remove file',
        \ZipArchive::ER_DELETED      =>	'Entry has been deleted'
	);

	/**
	 * Internal pointer to zip archive
	 */
	private $zip_archive = null;

	/**
	 * Set files to skip
	 *
	 * @param	string	$mode	HIDDEN, COMODOJO, ALL, NONE
	 *
	 * @return 	Object	$this
	 */
	public final function setSkippedFiles($mode) {

		$mode = strtoupper($mode);

		switch ($mode) {

			case 'HIDDEN':
				$this->skip_hidden_files = true;
				break;
			
			case 'COMODOJO':
				$this->skip_comodojo_internal = true;
				break;
			
			case 'ALL':
				$this->skip_hidden_files = true;
				$this->skip_comodojo_internal = true;
				break;
			
			case 'NONE':
			default:
				$this->skip_hidden_files = false;
				$this->skip_comodojo_internal = false;
				break;
		}

		return $this;

	}

	/**
	 * Set extraction folder mask
	 *
	 * @param	int		$mask
	 *
	 * @return 	Object	$this
	 */
	public final function setMask($mask) {

		$mask = filter_var($mask, FILTER_VALIDATE_INT, array(
			"options" => array(
				"max_range" => 777,
				"default" => 644 )
			)
		);
		
		$this->mask = $mask;

		return $this;

	}

	/**
	 * Open a zip archive
	 *
	 * @param	string	$zip_file	ZIP archive
	 * @param	bool	$check		(optional) check for archive consistence
	 *
	 * @return 	Object	$this
	 */
	public final function open($zip_file, $check=false) {

		if ( empty($zip_file) ) throw new ZipException($this->getStatus(\ZipArchive::ER_NOENT));
		
		try {
			
			$this->zip_archive = $check ? $this->openZipFile($zip_file, \ZipArchive::CHECKCONS) : $this->openZipFile($zip_file, null);

		}
		catch (ZipException $ze) {

			throw $ze;

		}

		return $this;

	}

	/**
	 * Create a new zip archive
	 *
	 * @param	string	$zip_file	ZIP archive
	 *
	 * @return 	string
	 */
	public final function create($zip_file) {

		if ( empty($zip_file) ) throw new ZipException($this->getStatus(\ZipArchive::ER_NOENT));
		
		try {
			
			$this->zip_archive = $this->openZipFile($zip_file, \ZipArchive::CREATE);

		}
		catch (ZipException $ze) {

			throw $ze;

		}

		return $this;

	}

	/**
	 * Close the zip archive
	 *
	 * @return 	bool
	 */
	public final function close() {

		return $this->zip_archive->close();

	}

	/**
	 * Get a list of files in archive (array)
	 *
	 * @return 	array
	 */
	public final function listFiles() {

		$list = Array();

		for ( $i = 0; $i < $this->zip_archive->numFiles; $i++ ) {

			$name = $this->zip_archive->getNameIndex($i);

			if ( $name === false ) throw new ZipException($this->getStatus($this->zip_archive->status));

			array_push($list, $name);

		}

		return $list;

	}

	/**
	 * Extract files from zip archive
	 *
	 * @param	string	$destination	Destination path
	 * @param	mixed	$files			(optional) a filename or an array of filenames
	 *
	 * @return 	Object	$this
	 */
	public final function extract($destination, $files=null) {

		if ( empty($destination) ) throw new ZipException('Invalid destination path');

		if ( !file_exists($destination) ) {

			$action = mkdir($destination, $this->mask, true);

			if ( $action === false ) throw new ZipException("Error creating folder ".$destination);

		}

		if ( !is_writable($destination) ) throw new ZipException('Destination path not writable');

		$destination = substr($destination, -1) == '/' ? $destination : $destination.'/';

		if ( is_array($files) AND @sizeof($files) != 0 ) {

			$file_matrix = $files;

		}
		else $file_matrix = $this->getArchiveFiles();

		$extract = $this->zip_archive->extractTo($destination, $file_matrix);

		if ( $extract === false ) throw new ZipException($this->getStatus($this->zip_archive->status));

		return $this;

	}

	/**
	 * Add files to zip archive
	 *
	 * @param	mixed	$file_name_or_array		filename to add or an array of filenames
	 *
	 * @return 	Object	$this
	 */
	public final function add($file_name_or_array) {

		if ( empty($file_name_or_array) ) throw new ZipException($this->getStatus(\ZipArchive::ER_NOENT ));

		try {

			if ( is_array($file_name_or_array) ) {

				foreach ($file_name_or_array as $file_name) $this->add_item($file_name);

			}
			else $this->add_item($file_name_or_array);
			
		} catch (ZipException $ze) {
			
			throw $ze;

		}

		return $this;

	}

	/**
	 * Delete files from zip archive
	 *
	 * @param	mixed	$file_name_or_array		filename to delete or an array of filenames
	 *
	 * @return 	Object	$this
	 */
	public final function delete($file_name_or_array) {

		if ( empty($file_name_or_array) ) throw new ZipException($this->getStatus(\ZipArchive::ER_NOENT ));

		try {

			if ( is_array($file_name_or_array) ) {

				foreach ($file_name_or_array as $file_name) $this->delete_item($file_name);

			}
			else $this->delete_item($file_name_or_array);
			
		} catch (ZipException $ze) {
			
			throw $ze;

		}

		return $this;

	}

	/**
	 * Get status from zip status code
	 *
	 * @param	int	$code	ZIP status code
	 *
	 * @return 	string
	 */
	private function getStatus($code) {

		if ( array_key_exists($code, $this->zip_status_codes) ) return $this->zip_status_codes[$code];

		else return sprintf('Unknown status %s', $code);

	}

	/**
	 * Open a zip file
	 *
	 * @param	int	$code	ZIP status code
	 * @param	int	$code	ZIP status code
	 *
	 * @return 	Object	ZipArchive
	 */
	private function openZipFile($zip_file, $flags=null) {

		$zip = new \ZipArchive;

		$open = $zip->open($zip_file, $flags);

		if ($open !== true) throw new ZipException($this->getStatus($open));
		
		return $zip;

	}

	/**
	 * Get a list of file contained in zip archive before extraction
	 *
	 * @return 	Object	ZipArchive
	 */
	private function getArchiveFiles() {

		$list = Array();

		for ($i = 0; $i < $this->zip_archive->numFiles; $i++) {

			$file = $this->zip_archive->statIndex($i);

			if ( $file === false ) continue;

			$name = str_replace('\\', '/', $file['name']);

			if ( $name[0] == "." AND $this->skip_hidden_files ) continue;

			if ( $name[0] == "." AND @$name[1] == "_" AND $this->skip_comodojo_internal ) continue;			

			array_push($list, $name);

		}

		return $list;

	}

	/**
	 * Add item to zip archive
	 *
	 * @param	int	$file	File to add (realpath)
	 * @param	int	$base	(optional) Base to record in zip file
	 */
	private function add_item($file, $base=null) {

		$real_file = str_replace('\\', '/', realpath($file));

		$real_name = basename($real_file);

		if ( !is_null($base) ) {

			if ( $real_name[0] == "." AND $this->skip_hidden_files ) return;

			if ( $real_name[0] == "." AND @$real_name[1] == "_" AND $this->skip_comodojo_internal ) return;

		}

		if ( is_dir($real_file) ) {

			$folder_target = is_null($base) ? $real_name : $base.$real_name;

			$new_folder = $this->zip_archive->addEmptyDir($folder_target);

			if ( $new_folder === false ) throw new ZipException($this->getStatus($this->zip_archive->status));

			foreach(new \DirectoryIterator($real_file) as $path) {
	
				if ( $path->isDot() ) continue;

				$file_real = $path->getPathname();

				$base = $folder_target."/";

				try {
					
					$this->add_item($file_real, $base);

				} catch (ZipException $ze) {
					
					throw $ze;

				}

			}

		}
		else if ( is_file($real_file) ) {

			$file_target = is_null($base) ? $real_name : $base.$real_name;

			$add_file = $this->zip_archive->addFile($real_file, $file_target);

			if ( $add_file === false ) throw new ZipException($this->getStatus($this->zip_archive->status));

		}
		else {

			//...

		}

	}
	
	/**
	 * Delete item from zip archive
	 *
	 * @param	int	$file	File to delete (zippath)
	 */
	private function delete_item($file) {

		$deleted = $this->zip_archive->deleteName($file);

		if ( $deleted === false ) throw new ZipException($this->getStatus($this->zip_archive->status));

	}

}