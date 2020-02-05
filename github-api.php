<?php

// 0 - none.
define( 'GEDEBUG_NONE', 0 );

// 1 - call logging only.
define( 'GEDEBUG_CALL', 1 );

// 2 - calls, and responses.
define( 'GEDEBUG_RESP', 2 );

// Selected debug level.
if ( ! defined( 'GITHUB_API_LEVEL' ) ) {
	define( 'GITHUB_API_LEVEL', GEDEBUG_NONE );
}


/**
 * This class contains all the functions that actually retrieve information from the GitHub API
 */
class github_api {


	private $client_id = null;
	private $client_secret = null;
	private $access_token = null;
	private $access_token_username = null;

	/**
	 * Allow the client ID / secret to be set, and used for subsequent calls
	 */
	function __construct() {
		add_action( 'plugins_loaded', array( $this, 'set_credentials' ) );
		add_filter( 'http_request_timeout', array( $this, 'http_request_timeout' ) );
	}

	/**
	 * Extend the timeout since API calls can easily exceed 5 seconds
	 *
	 * @param int $seconds The current timeout setting
	 *
	 * @return int          The revised timeout setting
	 */
	function http_request_timeout( $seconds ) {
		return $seconds < 25 ? 25 : $seconds;
	}

	/**
	 * If you find yourself hitting rate limits, then you can register an application
	 * with GitHub(http://developer.github.com/v3/oauth/) use the filters here to
	 * provide the credentials.
	 */
	public function set_credentials() {
		$this->client_id             = apply_filters( 'github-embed-client-id', $this->client_id );
		$this->client_secret         = apply_filters( 'github-embed-client-secret', $this->client_secret );
		$this->access_token          = apply_filters( 'github-embed-access-token', $this->access_token );
		$this->access_token_username = apply_filters( 'github-embed-access-token-username', $this->access_token_username );
	}

	private function call_api( $url ) {
		// Allow users to supply auth details to enable a higher rate limit [Deprecated]
		if ( ! empty( $this->client_id ) && ! empty( $this->client_secret ) ) {
			$url = add_query_arg(
				array(
					'client_id'     => $this->client_id,
					'client_secret' => $this->client_secret
				),
				$url
			);
		}

		$args = array(
			'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed'
		);
		if ( ! empty( $this->access_token_username ) && ! empty ( $this->access_token ) ) {
			$args['headers'] = [
				'Authorization' => 'Basic ' . base64_encode( $this->access_token_username . ':' . $this->access_token ),
			];
		}
		$this->log( __FUNCTION__ . " : $url", GEDEBUG_CALL );

		$results = wp_remote_get( $url, $args );

		if ( is_wp_error( $results ) ||
		     ! isset( $results['response']['code'] ) ||
		     $results['response']['code'] != '200' ) {
			header( 'HTTP/1.0 404 Not Found' );
			die( 'Octocat is lost, and afraid' );
		}

		return $results;

	}


	/**
	 * Get a repository from the GitHub API
	 *
	 * @param string $owner The repository's owner
	 * @param string $repository The respository name
	 *
	 * @return object             The response from the GitHub API
	 */
	public function get_repo( $owner, $repository ) {

		$this->log( "get_repo( $owner, $repository )", GEDEBUG_CALL );

		$results = $this->call_api( "https://api.github.com/repos/$owner/$repository" );

		return json_decode( $results['body'] );

	}


	/**
	 * Get commit information for a repository from the GitHub API
	 *
	 * @param string $owner The repository's owner
	 * @param string $repository The respository name
	 *
	 * @return object             The response from the GitHub API
	 */
	public function get_repo_commits( $owner, $repository ) {

		$this->log( "get_repo_commits( $owner, $repository )", GEDEBUG_CALL );

		$results = $this->call_api( "https://api.github.com/repos/$owner/$repository/commits" );

		return json_decode( $results['body'] );

	}


	/**
	 * Get a milestone summary from the GitHub API
	 *
	 * @param string $owner The repository's owner
	 * @param string $repository The respository name
	 * @param string $milestone The milestone ID
	 *
	 * @return object             The response from the GitHub API
	 */
	public function get_repo_milestone_summary( $owner, $repository, $milestone ) {

		$this->log( "get_repo_milestone_summary( $owner, $repository, $milestone )", GEDEBUG_CALL );

		$results = $this->call_api( "https://api.github.com/repos/$owner/$repository/milestones/$milestone" );

		return json_decode( $results['body'] );

	}


	public function get_repo_contributors( $owner, $repository ) {

		$this->log( "get_repo_contributors( $owner, $repository )", GEDEBUG_CALL );

		$results = $this->call_api( "https://api.github.com/repos/$owner/$repository/stats/contributors" );

		return json_decode( $results['body'] );

	}


	/**
	 * Get a user from the GitHub API
	 *
	 * @param string $user The username
	 *
	 * @return object             The response from the GitHub API
	 */
	public function get_user( $user ) {

		$this->log( "get_user( $user )", GEDEBUG_CALL );

		$results = $this->call_api( "https://api.github.com/users/$user" );

		return json_decode( $results['body'] );

	}


	private function log( $msg, $level ) {
		if ( GITHUB_API_LEVEL >= $level ) {
			error_log( "[GE$level]: " . $msg );
		}
	}


}
