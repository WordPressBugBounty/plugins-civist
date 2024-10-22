<?php
/**
 * Handle the generation and transmission of the jwt token
 *
 * @package civist
 */

/**
 * Imports
 */
if ( ! class_exists( '\\Firebase\\JWT\\JWT' ) ) {
	require_once 'JWT.php';
}
use Firebase\JWT\JWT;

/**
 * Handle the generation and transmission of the jwt token
 */
class Civist_Jwt {
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;
	/**
	 * The client form action name for asking for the JWT token
	 *
	 * @var string
	 */
	private $client_action_name;
	/**
	 * The plugin settings manager
	 *
	 * @var Civist_Settings_Manager
	 */
	private $settings_manager;
	/**
	 * The Civist_Jwt class constructor
	 *
	 * @param string                  $plugin_slug The slug of the plugin.
	 * @param Civist_Settings_Manager $settings_manager The plugin settings manager.
	 */
	public function __construct( $plugin_slug, Civist_Settings_Manager $settings_manager ) {
		$this->plugin_slug        = $plugin_slug;
		$this->settings_manager   = $settings_manager;
		$this->client_action_name = $plugin_slug . '_jwt_token';
	}

	/**
	 * Returns the action to be used by the client form
	 *
	 * @return string
	 */
	public function get_client_action_name() {
		return $this->client_action_name;
	}

	/**
	 * Converts the input string to valid UTF-8 encoding.
	 * Does nothing in case mbstring extension is not available.
	 *
	 * @param string $str The string to be converted to valid utf-8.
	 * @return string
	 */
	private function convert_to_utf8( $str ) {
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$oldsub = mb_substitute_character();
			mb_substitute_character( 0xFFFD );
			$str = mb_convert_encoding( $str, 'UTF-8' );
			mb_substitute_character( $oldsub );
		}
		return $str;
	}

	/**
	 * Generates the civist JWT token.
	 *
	 * @param string  $key_id The key ID of the token.
	 * @param string  $secret The secret for the token signature.
	 * @param integer $now The current timestamp.
	 * @param string  $sub The token subject.
	 * @param string  $email The email of the currently logged-in user.
	 * @param string  $name The name of the currently logged-in user.
	 * @param string  $api_url The URL for the civist API.
	 * @return string
	 */
	private function generate_jwt( $key_id, $secret, $now, $sub, $email, $name, $api_url ) {
		$token = array(
			'sub'   => $sub,
			'exp'   => $now + 60, // the server MAY overrule the maximum validity of this field.
			'iat'   => $now,
			'nbf'   => $now,
			'aud'   => $api_url,
			'email' => $email,
			'name'  => $name,
		);
		return JWT::encode( $token, $secret, 'HS256', $key_id );
	}

	/**
	 * Handle http get request and return a newly created JWT token.
	 */
	public function serve_jwt_token() {
		if ( ! $this->settings_manager->is_connected() ) {
			status_header( 403 );
			echo( 'Forbidden' );
			wp_die(); // this is required to terminate immediately and return a proper response.
		}
		$api_url          = $this->settings_manager->get_option_by_name( 'api_url' );
		$api_key          = $this->settings_manager->get_option_by_name( 'api_key' );
		$api_key_id       = $this->settings_manager->get_option_by_name( 'api_key_id' );
		$time_url         = $api_url . 'time/now';
		$response         = wp_remote_get( $time_url );
		$response_status  = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );
		if ( $response_status < 200 || $response_status >= 300 ) {
			status_header( $response_status );
			echo( $response_message ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			wp_die(); // this is required to terminate immediately and return a proper response.
		}
		$now          = (int) wp_remote_retrieve_body( $response );
		$secret       = $api_key;
		$key_id       = $api_key_id;
		$current_user = wp_get_current_user();
		$sub          = $current_user->user_login;
		$email        = $current_user->user_email;
		$name         = $current_user->display_name;
		try {
			$jwt = $this->generate_jwt( $key_id, $secret, $now, $sub, $email, $name, $api_url );
		} catch ( DomainException $e ) {
			$sub   = $this->convert_to_utf8( $current_user->user_login );
			$email = $this->convert_to_utf8( $current_user->user_email );
			$name  = $this->convert_to_utf8( $current_user->display_name );
			$jwt   = $this->generate_jwt( $key_id, $secret, $now, $sub, $email, $name, $api_url );
		}
		echo( $jwt ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die(); // this is required to terminate immediately and return a proper response.
	}
}
