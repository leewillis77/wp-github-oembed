<?php 

/**
 * This class contains all the functions that actually retrieve information from the GitHub API
 */
class github_api {



	/**
	 * Get a repository from the GitHub API
	 * @param  string $owner      The repository's owner
	 * @param  string $repository The respository name
	 * @return object             The response from the GitHub API
	 */
	public function get_repo ( $owner, $repository ) {

		$owner = trim ( $owner, '/' );
		$repository = trim ( $repository, '/' );

		$results = wp_remote_get( "https://api.github.com/repos/$owner/$repository", $args = array (
		              'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		return json_decode ( $results['body'] );

	}



	/**
	 * Get commit information for a repository from the GitHub API
	 * @param  string $owner      The repository's owner
	 * @param  string $repository The respository name
	 * @return object             The response from the GitHub API
	 */
	public function get_repo_commits ( $owner, $repository ) {

		$owner = trim ( $owner, '/' );
		$repository = trim ( $repository, '/' );

		$results = wp_remote_get( "https://api.github.com/repos/$owner/$repository/commits", $args = array (
			'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		return json_decode ( $results['body'] );

	}



	/**
	 * Get a milestone summary from the GitHub API
	 * @param  string $owner      The repository's owner
	 * @param  string $repository The respository name
	 * @param  string $milestone  The milestone ID
	 * @return object             The response from the GitHub API
	 */
	public function get_repo_milestone_summary ( $owner, $repository, $milestone ) {

		$owner = trim ( $owner, '/' );
		$repo = trim ( $repo, '/' );

		$results = wp_remote_get( "https://api.github.com/repos/$owner/$repository/milestones/$milestone", $args = array (
			'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		return json_decode ( $results['body'] );

	}



	/**
	 * Get a user from the GitHub API
	 * @param  string $user       The username
	 * @return object             The response from the GitHub API
	 */
	public function get_user ( $user ) {

		$user = trim ( $user, '/' );
		$repository = trim ( $repository, '/' );

		$results = wp_remote_get( "https://api.github.com/users/$user", $args = array (
		              'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		return json_decode ( $results['body'] );

	}
	
}