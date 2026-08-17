<?php
/**
 * Exposes $civist_webpack_files variable containing the chunks injected by htmlWebpackPlugin as a php data type
 *
 * This file is generated from a template that allows the resource paths to be injected at build time
 *
 * @package civist
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$civist_webpack_files = json_decode( '{"embed":{"size":223481,"entry":"civist-wordpress-blocks-embed.js","hash":"10cd49b70bef979b78aa","css":["civist-wordpress-blocks-embed.css"]},"editor":{"size":2151028,"entry":"civist-wordpress-blocks-editor.js","hash":"2be48431751710f73b42","css":[]}}' );

