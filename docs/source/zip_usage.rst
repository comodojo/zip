Zip usage
=========

The ``\Comodojo\Zip\Zip`` class is designed to streamline the management of a single Zip file.

Basic operations
----------------

Open a zip file
...............

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');

Create zip file
...............

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::create('file.zip');

Check zip file
..............

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $is_valid = Zip::check('file.zip'); // true in case of success

Zip file operations
-------------------

Extract
.......

To extract the whole content of the zip file:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');

    // extract whole archive
    $zip->extract('/path/to/uncompressed/files');

To select one or multiple files to extract from the archive:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');

    // extract one file
    $zip->extract('/path/to/uncompressed/files', 'file');

    // extract multiple files
    $zip->extract('/path/to/uncompressed/files', ['file1','file2']);

Add a file or a directory
.........................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::create('file.zip');

    $zip->add('/path/to/my/file');

    $zip->add('/path/to/my/directory');

To add only the directory content (i.e. flattening files):

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::create('file.zip');

    $zip->add('/path/to/my/directory', true);

To set a default path and add files with relative location (i.e. change the root folder):

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::create('file.zip');

    // move the path
    $zip->setPath('/path/to/my');

    // add relative files or directories
    $zip->add('file')
        ->add('directory');

Change the compression method
.............................

.. note:: This feature is available since comodojo/zip 3.0

To change the compression method while adding a file or a directory:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::create('file.zip');

    // add a file specifying the compression method to use
    // available methods:
    //  Zip::CM_DEFAULT
    //  Zip::CM_STORE
    //  Zip::CM_DEFLATE
    $zip->add('/path/to/my/file', false, Zip::CM_DEFLATE);

Different files can have different compression methods, for example:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::create('file.zip');

    $zip->add('/path/to/my/file_1', false, Zip::CM_DEFLATE)
        ->add('/path/to/my/file_2', false, Zip::CM_STORE);

Add multiple files/directories
..............................

The ``Zip::add()`` method accepts an array in input to add more resources at once:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $zip = Zip::create('file.zip');

    use \Comodojo\Zip\Zip;

    $zip->add([
        '/path/to/my/file1',
        '/path/to/my/file2'
    ]);

    // the Zip::add() method can be chained too:
    $zip->add('/path/to/my/file1')
        ->add('/path/to/my/file2');

Delete a file or a directory
............................

To delete a file or a directory from a Zip file:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');

    $zip->delete('file');

Also the ``Zip::delete()`` method accepts an array in input to delete multiple files at once:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');

    $zip->delete([
        'file1',
        'file2'
    ]);

    // the Zip::delete() method can be chained too:
    $zip->delete('file1')
        ->delete('file2');

List content of the file
........................

The ``Zip::listFiles()`` method can be used to get the list of files in the zip archive as an array:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');
    $zip->listFiles();

Count the number of elements
............................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');
    $elements = count($zip);

Close
.....

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    use \Comodojo\Zip\Zip;

    $zip = Zip::open('file.zip');

    // ...

    $zip->close();

Additional methods
------------------

Set SkipMode
............

The ``Zip::setSkipMode()`` method can force the Zip class to skip hidden files while adding directories:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $zip = Zip::open('file.zip');

    // set the skip mode
    // available modes:
    //  Zip::SKIP_NONE (default)
    //  Zip::SKIP_HIDDEN
    //  Zip::SKIP_ALL
    //  Zip::SKIP_COMODOJO
    $zip->setSkipped(Zip::SKIP_HIDDEN);

    // get skip mode
    $mode = $zip->getSkipped();

Change file mask (extract)
..........................

To change the file mask:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $zip = Zip::open('file.zip');

    // set the file mask (default 777)
    $zip->setMask(0644);

    // get mask
    $mask = $zip->getMask();

Password protected zip files
----------------------------

The ``Zip::setPassword()`` method can be used to set a password for the current zip.

Extract a password protected zip
................................

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $zip = Zip::open('file.zip');

    // set the zip password
    $zip->setPassword('FordPerfect')
        ->extract('/destination/folder');

Create a password protected zip file
....................................

.. note:: This feature is available since comodojo/zip 3.0

To create a password protected Zip file, once a password is set for the archive, each file should be flagged as encrypted, using one of the available encryption method.

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $zip = Zip::create('file.zip');

    // set the zip password
    $zip->setPassword('FordPerfect')
        ->add('file', false, Zip::CM_DEFAULT, Zip::EM_AES_128);

Different files can have different encryption methods, for example:

.. code-block:: php
   :linenos:

    <?php namespace My\Namespace;

    $zip = Zip::create('file.zip');

    // set the zip password and the encryption method
    // available methods:
    //  Zip::EM_NONE (default)
    //  Zip::EM_AES_128
    //  Zip::EM_AES_192
    //  Zip::EM_AES_256
    $zip->setPassword('FordPerfect')
        ->add('file_1', false, Zip::CM_DEFAULT, Zip::EM_AES_128)
        ->add('file_2', false, Zip::CM_DEFAULT, Zip::EM_AES_256);
