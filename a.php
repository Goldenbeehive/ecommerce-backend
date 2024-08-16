<?php
// Ensure PHAR can be created
ini_set('phar.readonly', 0);

$pharFile = 'project.phar';

// Delete the existing phar file if it exists
if (file_exists($pharFile)) {
    unlink($pharFile);
}

// Create a new PHAR file
$phar = new Phar($pharFile);

// Start buffering
$phar->startBuffering();

// Function to recursively add files from a directory
function addFilesRecursively($phar, $dir, $baseDir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($iterator as $file) {
        // Add files only (skip directories)
        if ($file->isFile()) {
            $path = $file->getPathname();
            // Adjust the internal path within the phar
            $localPath = substr($path, strlen($baseDir) + 1);
            $phar->addFile($path, $localPath);
        }
    }
}

// Base directory is the root of your project
$baseDir = __DIR__;

// Add files and directories to the PHAR recursively
addFilesRecursively($phar, __DIR__ . '/public', $baseDir);
addFilesRecursively($phar, __DIR__ . '/src', $baseDir);
addFilesRecursively($phar, __DIR__ . '/vendor', $baseDir);

// Set the custom stub
$stub = <<<EOF
<?php
Phar::mapPhar();
require 'phar://' . __FILE__ . '/public/index.php';
__HALT_COMPILER();
EOF;

$phar->setStub($stub);

// Stop buffering and save
$phar->stopBuffering();

echo "PHAR file created successfully: $pharFile\n";

// Prevent PHAR execution after creation
ini_set('phar.readonly', 1);