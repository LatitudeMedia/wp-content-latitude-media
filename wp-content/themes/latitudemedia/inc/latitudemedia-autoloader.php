<?php

spl_autoload_register( 'post_types_autoloader' );

/**
 * An example of a project-specific implementation.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
function post_types_autoloader($class_name) {
    // Define the base directory for class files
    $base_dir = __DIR__ . '/';

    // Replace namespace separators with directory separators (if using namespaces)
    $file = $base_dir . str_replace('\\', '/', $class_name) . '.php';

    // Check if the file exists and require it
    if (file_exists($file)) {
        require_once $file;
    }
}



// Function to scan directory, detect namespaces, and instantiate classes
function instantiateAllClasses($directory, $namespacePrefix = '') {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory) );

    foreach ($files as $file) {
        // Only process PHP files
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            // Remove the base directory from the file path
            $relativePath = str_replace([$directory, '.php'], '', $file);

            // Replace directory separators with namespace separators
            $relativeClass = str_replace('/', '\\', $relativePath);
            $relativeClass = ltrim($relativeClass, '\\'); // Trim any leading backslashes

            // Prepend namespace prefix if provided (optional)
            $fullClassName = $namespacePrefix ? $namespacePrefix . '\\' . $relativeClass : $relativeClass;

            // Check if the class exists (the autoloader will attempt to load it)
            if (class_exists($fullClassName)) {
                // Instantiate the class dynamically
                new $fullClassName();
            } else {
                echo "Class $fullClassName does not exist.\n";
            }
        }
    }
}

instantiateAllClasses(__DIR__ . '/LatitudeMedia/', 'LatitudeMedia');
