# CHANGELOG

## 3.1.1
> released 13 Feb 2021

* fix invalid types #11
* add strict_types=1 to all UTs
* update devcontainer configuration
* update docker img

## 3.1.0
> released 23 Jan 2021

* Support PHP >= 7.4|8
* Add test-in-docker and vscode .devcontainer support
* Update docs
* Move from travis to github action
> So long, travis.ci, and thanks for all the fish! 

## 3.0.0
> released 06 May 2019

This is the 3rd major release of comodojo/zip.
It works with PHP >= 7.2 (however 7.3 is strongly suggested).

### New Features

* Support for zip file compression
* Create encrypted (password protected) zip files

### Notable changes

* [API Change] `ZipManager::addZip()` now returns Zip id instead of self
* [API Change] `ZipManager::removeZip()` now returns bool instead of self
* [API Change] `ZipManager::getPassword()` is now protected
* [New API] ZipManager class now implements the Countable interface
* [New API] Zip class now implements the Countable interface
* [New API] `ZipManager::removeZipById()` to remove a Zip object using its id
* [New API] `Zip::getSkipMode()` and `Zip::setSkipMode()`
* [New API] `Zip::getComment()` and `Zip::setComment()`
* [Deprecated API] `Zip::getSkipped()` and `Zip::setSkipped()` are deprecated

### 3.0.0-beta2
> released 17 Mar 2019

* min php version to 7.2
* code refactoring
* [API Change] `ZipManager::addZip()` now returns Zip id instead of self
* [API Change] `ZipManager::removeZip()` now returns bool instead of self
* [API Change] `ZipManager::getPassword()` is now protected
* [New API] ZipManager class now implements the Countable interface
* [New API] Zip class now implements the Countable interface
* [New API] `ZipManager::removeZipById()` to remove a Zip object using its id
* [New API] `Zip::getSkipMode()` and `Zip::setSkipMode()`
* [New API] `Zip::getComment()` and `Zip::setComment()`
* [Deprecated API] `Zip::getSkipped()` and `Zip::setSkipped()` are deprecated
* [New Feature] Support for zip file compression
* [New Feature] Create encrypted (password protected) zip files

### 3.0.0-beta
> released 31 Aug 2017

* min php version to 7.1
* code refactoring

### 2.1.1
> released 31 Aug 2017

* [FIX] ZipManager::extract error when providing a list of files to extract

### 2.1.0
> released 22 Dec 2015

This release offers the same functionalities of 2.0.* plus support for password-protected zips (see #3); it require PHP 5.6.

* setPassword/getPassword methods are back and working
* min PHP version >= 5.6.0

### 2.0.3
> released 31 Aug 2017

* [FIX] ZipManager::extract error when providing a list of files to extract

### 2.0.2
> released 22 Dec 2015

* removed setPassword/getPassword methods (#3), will be reintroduced in 2.1.

### 2.0.1
> released 17 Oct 2015

* added comodojo/exceptions as a dependency

### 2.0.0
> released 12 Jun 2015

* New ZipManager class to handle multiple Zip files
* Merge of zip files via ZipManager
* Zip `Zip::open()` and `Zip::create()` methods are now static
* Zip `Zip::open()` cannot check consitence of Zip, use `Zip::check()` instead
* Support for password protected Zip files
* Directories can now be flattened when added
* PHPUnit tests
* license changed from GPL to MIT

### 1.0.0
> released 5 Aug 2014

* Initial release
