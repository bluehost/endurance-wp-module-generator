<?php

require __DIR__ . '/vendor/autoload.php';

// Ascertain the module slug and name
$module_slug = str_replace( 'endurance-wp-module-', '', basename( __DIR__ ) );
$module_name = ucwords( str_replace( '-', ' ', $module_slug ) );

// Prepare template string replacements
$replacements = [
	'moduleName'  => $module_name, // Format: Module Name
	'modulename'  => strtolower( $module_name ), // Format: module name
	'moduleSlug'  => $module_slug, // Format: module-name
	'module_slug' => str_replace( '-', '_', $module_slug ), // Format: module_name
];

// Write files
$mustache = new Mustache_Engine();

$templates = new RecursiveDirectoryIterator( __DIR__ . '/.templates', RecursiveDirectoryIterator::SKIP_DOTS );
foreach ( $templates as $file ) {
	$contents = $mustache->render( file_get_contents( $file->getRealPath() ), $replacements );
	file_put_contents( __DIR__ . '/' . str_replace( '.mustache', '', $file->getFilename() ), $contents );
}

file_put_contents( __DIR__ . '/' . $module_slug . '.php', '<?php ' . PHP_EOL );

// Clean up
unlink( __DIR__ . '/composer.lock' );
deleteDir( __DIR__ . '/.templates' );
deleteDir( __DIR__ . '/vendor' );

/**
 * Recursively delete everything in a directory.
 *
 * @param string $dir Directory to be deleted
 *
 * @return bool
 */
function deleteDir( $dir ) {
	$files = array_diff( scandir( $dir ), array( '.', '..' ) );
	foreach ( $files as $file ) {
		is_dir( "$dir/$file" ) ? deleteDir( "$dir/$file" ) : unlink( "$dir/$file" );
	}

	return rmdir( $dir );
}
