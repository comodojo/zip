CHANGELOG
=========

2.0.3
-----

* fix ZipManager::extract error when providing a list of files to extract

2.0.2
-----

* removed setPassword/getPassword methods (#3), will be reintroduced in 2.1.

2.0.1
-----

* added comodojo/exceptions as a dependency

2.0.0
-----

* New ZipManager class to handle multiple Zip files
* Merge of zip files via ZipManager
* Zip `Zip::open()` and `Zip::create()` methods are now static
* Zip `Zip::open()` cannot check consitence of Zip, use `Zip::check()` instead
* Support for password protected Zip files
* Directories can now be flattened when added
* PHPUnit tests
* license changed from GPL to MIT

1.0.0
-----
> released 23 Jul 2014

* Initial release
