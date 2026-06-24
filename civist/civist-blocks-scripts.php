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

$civist_webpack_files = json_decode( '{"embed":{"size":223481,"entry":"civist-wordpress-blocks-embed.js","hash":"4812fc075c6374d63e01","css":["civist-wordpress-blocks-embed.css"]},"editor":{"size":2151833,"entry":"civist-wordpress-blocks-editor.js","hash":"e7e7437315282b38ded3","css":[]}}' );

