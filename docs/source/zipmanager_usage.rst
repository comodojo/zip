ZipManager usage
================

The ``\Comodojo\Zip\ZipManager`` class is designed to manage, extract and combine multiple zip files (``\Comodojo\Zip\Zip`` objects) .

Basic operations
----------------

Start the Manager and register Zips
...................................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\{
        Zip,
        ZipManager
    };

    // init manager
    $manager = new ZipManager();

    // register an existing zip file
    $manager->addZip(Zip::open('/path/to/my/file1.zip'));
    // register a new zip file
    $manager->addZip(Zip::create('/path/to/my/file3.zip'));

Zip management
..............

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\{
        Zip,
        ZipManager
    };

    // init manager
    $manager = new ZipManager();
    $zip_1_id = $manager->addZip(Zip::open('/path/to/my/file1.zip'));
    $zip_2_id = $manager->addZip(Zip::open('/path/to/my/file2.zip'));

    // get a list of registered zips as array
    $list = $manager->listZips();

    // remove a zip
    $manager->removeZipById($zip_1_id);

    // get a Zip
    $zip = $manager->getZip($zip_1_id);

Add files to all zips
.....................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\{
        Zip,
        ZipManager
    };

    // init manager
    $manager = new ZipManager();

    // register existing zips
    $manager->addZip(Zip::open('/path/to/my/file1.zip'));
    $manager->addZip(Zip::open('/path/to/my/file2.zip'));

    // add a file to all zips
    $manager->add('/path/to/my/file');

Extract zips
............

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\{
        Zip,
        ZipManager
    };

    // init manager
    $manager = new ZipManager();

    // register existing zips
    $manager->addZip(Zip::open('/path/to/my/file1.zip'));
    $manager->addZip(Zip::open('/path/to/my/file2.zip'));

    // separate content in folders
    $extract = $manager->extract('/path/to/uncompressed/files', true);

    // use a single folder
    $extract = $manager->extract('/path/to/uncompressed/files', false);

    // extract single file
    $extract = $manager->extract('/path/to/uncompressed/files', false, 'file');

    // extract multiple files
    $extract = $manager->extract('/path/to/uncompressed/files', false, ['file1','file2']);

Merge zips
..........

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\{
        Zip,
        ZipManager
    };

    // init manager
    $manager = new ZipManager();

    // register existing zips
    $manager->addZip(Zip::open('/path/to/my/file1.zip'));
    $manager->addZip(Zip::open('/path/to/my/file2.zip'));

    // separate content in folders
    $manager->merge('/path/to/output/file.zip', true);

    // flatten files
    $manager->merge('/path/to/output/file.zip', false);

Close zips
..........

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\{
        Zip,
        ZipManager
    };

    // init manager
    $manager = new ZipManager();

    // register existing zips
    $manager->addZip(Zip::open('/path/to/my/file1.zip'));
    $manager->addZip(Zip::open('/path/to/my/file2.zip'));

    $manager->close();

Additional methods
------------------

Change file mask (extract)
..........................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $manager = new ZipManager();

    // set the file mask (default 777)
    $manager->setMask(0644);

    // get mask
    $mask = $manager->getMask();

Change the base path
....................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $manager = new ZipManager();

    // set the base path
    $manager->setPath('/path/to/files');

    // get base path
    $path = $manager->getPath();
