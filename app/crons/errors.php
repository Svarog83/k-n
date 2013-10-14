<?php
$file_path = '/var/tmp/php_errors.log';
$message = '';
$message = file_get_contents( $file_path );

if ( strlen( $message ) )
{
	mail ( 'svaroggg@gmail.com', 'PHP ERROR FATAL!', $message );
	$file = fopen ( $file_path, 'w' );
	fwrite( $file, '' );
	fclose( $file );
}