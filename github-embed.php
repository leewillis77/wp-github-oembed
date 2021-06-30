<?php
/*
Plugin Name: Github Embed
Plugin URI: https://wordpress.org/plugins/github-embed/
Description: Paste the URL to a Github project into your posts or pages, and have the project information pulled in and displayed automatically
Version: 2.1.0
Author: Ademti Software Ltd.
Author URI: https://www.ademti-software.co.uk/
*/

/**
 * Copyright (c) 2013 Ademti Software. All rights reserved.
 *
 * Released under the GPL license v2
 * https://www.gnu.org/licenses/gpl-2.0.en.html
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/**
 * This class handles being the oEmbed provider in terms of registering the URLs that
 * we can embed, and handling the actual oEmbed calls. It relies on the github_api
 * class to retrieve the information from the GitHub API.
 * @uses class github_api
 */
class github_embed {

	private $api;

	/**
	 * Constructor. Registers hooks and filters
	 *
	 * @param class $api An instance of the github_api classs
	 */
	public function __construct( $api ) {
		$this->api = $api;
		add_action( 'init', array( $this, 'register_oembed_handler' ) );
		add_action( 'init', array( $this, 'maybe_handle_oembed' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_init', array( $this, 'schedule_expiry' ) );
		add_action( 'github_embed_cron', array( $this, 'cron' ) );
		// @TODO i18n
	}

	/**
	 * Make sure we have a scheduled event set to clear down the oEmbed cache until
	 * WordPress supports cache_age in oEmbed responses.
	 */
	public function schedule_expiry() {
		if ( ! wp_next_scheduled( 'github_embed_cron' ) ) {
			$frequency = apply_filters( 'github_embed_cache_frequency', 'daily' );
			wp_schedule_event( time(), $frequency, 'github_embed_cron' );
		}
	}

	/**
	 * Expire old oEmbeds.
	 * Note: This is a bit sledgehammer-to-crack-a-nut hence why I'm only running it
	 * daily. Ideally WP should honour cache_age in oEmbed responses properly
	 */
	public function cron() {
		global $wpdb, $table_prefix;
		$sql     = "DELETE
				  FROM {$table_prefix}postmeta
				 WHERE meta_key LIKE '_oembed_%'";
		$results = $wpdb->get_results( $sql );
	}

	/**
	 * Enqueue the frontend CSS
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( 'github-embed', plugins_url( basename( dirname( __FILE__ ) ) . '/css/github-embed.css' ) );
		wp_enqueue_style( 'github-embed' );
	}

	/**
	 * Register the oEmbed provider, and point it at a local endpoint since github
	 * doesn't directly support oEmbed yet. Our local endpoint will use the github
	 * API to fulfil the request.
	 *
	 * @param array $providers The current list of providers
	 *
	 * @return array            The list, with our new provider added
	 */
	public function register_oembed_handler() {
		$oembed_url = home_url();
		$key        = $this->get_key();
		$oembed_url = add_query_arg( array( 'github_oembed' => $key ), $oembed_url );
		wp_oembed_add_provider( '#https?://github.com/.*#i', $oembed_url, true );
	}

	/**
	 * Generate a unique key that can be used on our requests to stop others
	 * hijacking our internal oEmbed API
	 * @return string The site key
	 */
	private function get_key() {
		$key = get_option( 'github_oembed_key' );
		if ( ! $key ) {
			$key = md5( time() . rand( 0, 65535 ) );
			add_option( 'github_oembed_key', $key, '', 'yes' );
		}

		return $key;
	}

	/**
	 * Check whether this is an oembed request, handle if it is
	 * Ignore it if not.
	 * Insert rant here about WP's lack of a front-end AJAX handler.
	 */
	public function maybe_handle_oembed() {
		if ( isset( $_GET['github_oembed'] ) ) {
			return $this->handle_oembed();
		}
	}

	/**
	 * Handle an oembed request
	 */
	public function handle_oembed() {
		// Check this request is valid
		if ( $_GET['github_oembed'] !== $this->get_key() ) {
			header( 'HTTP/1.0 403 Forbidden' );
			die( 'Sad Octocat is sad.' );
		}

		// Check we have the required information
		$url    = isset( $_REQUEST['url'] ) ? $_REQUEST['url'] : null;
		$format = isset( $_REQUEST['format'] ) ? $_REQUEST['format'] : null;

		if ( ! empty( $format ) && 'json' !== $format ) {
			header( 'HTTP/1.0 501 Not implemented' );
			die( 'This octocat only does json' );
		}

		if ( ! $url ) {
			header( 'HTTP/1.0 404 Not Found' );
			die( 'Octocat is lost, and afraid' );
		}

		// Issues / Milestones
		if ( preg_match( '#https?://github.com/([^/]*)/([^/]*)/graphs/contributors/?$#i', $url, $matches ) && ! empty( $matches[2] ) ) {
			$this->oembed_github_repo_contributors( $matches[1], $matches[2] );
		} elseif ( preg_match( '#https?://github.com/([^/]*)/([^/]*)/issues.*$#i', $url, $matches ) && ! empty( $matches[2] ) ) {
			if ( preg_match( '#issues.?milestone=([0-9]*)#i', $url, $milestones ) ) {
				$milestone = $milestones[1];
			} else {
				$milestone = null;
			}
			if ( $milestone ) {
				$this->oembed_github_repo_milestone_summary( $matches[1], $matches[2], $milestone );
			}
		} elseif ( preg_match( '#https?://github.com/([^/]*)/([^/]*)/milestone/([0-9]*)$#i', $url, $matches ) ) {
			// New style milestone URL, e.g. https://github.com/example/example/milestone/1.
			$this->oembed_github_repo_milestone_summary( $matches[1], $matches[2], $matches[3] );
		} elseif ( preg_match( '#https?://github.com/([^/]*)/([^/]*)/?$#i', $url, $matches ) && ! empty( $matches[2] ) ) {
			// Repository.
			$this->oembed_github_repo( $matches[1], $matches[2] );
		} elseif ( preg_match( '#https?://github.com/([^/]*)/?$#i', $url, $matches ) ) {
			// User.
			$this->oembed_github_author( $matches[1] );
		}
	}

	/**
	 * Capture then return output of template, provided theme or fallback to plugin default.
	 *
	 * @param string $template The template name to process.
	 * @param string $data Array, object, or variable that the template needs.
	 *
	 * @return string
	 */
	private function process_template( $template, $data ) {
		ob_start();
		if ( ! locate_template( 'wp-github-oembed/' . $template, true ) ) {
			require_once 'templates/' . $template;
		}

		return ob_get_clean();
	}

	/**
	 * Retrieve a list of contributors for a project
	 *
	 * @param string $owner The owner of the repository
	 * @param string $repository The repository name
	 */
	private function oembed_github_repo_contributors( $owner, $repository ) {
		$data                     = [];
		$data['repo']             = $this->api->get_repo( $owner, $repository );
		$data['contributors']     = $this->api->get_repo_contributors( $owner, $repository );
		$data['gravatar_size']    = apply_filters( 'github_oembed_gravatar_size', 64 );
		$data['logo_class']       = apply_filters( 'wp_github_oembed_logo_class', 'github-logo-octocat' );
		$data['details_expanded'] = apply_filters( 'wp_github_oembed_contributor_details_expanded', true );

		$response          = new stdClass();
		$response->type    = 'rich';
		$response->width   = '10';
		$response->height  = '10';
		$response->version = '1.0';
		$response->title   = $data['repo']->description;
		$response->html    = $this->process_template(
			'repository_contributors.php', $data );

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}

	/**
	 * Retrieve the summary information for a repo's milestone, and
	 * output it as an oembed response
	 */
	private function oembed_github_repo_milestone_summary( $owner, $repository, $milestone ) {
		$data               = [];
		$data['repo']       = $this->api->get_repo( $owner, $repository );
		$data['summary']    = $this->api->get_repo_milestone_summary( $owner, $repository, $milestone );
		$data['logo_class'] = apply_filters( 'wp_github_oembed_logo_class', 'github-logo-octocat' );

		$response          = new stdClass();
		$response->type    = 'rich';
		$response->width   = '10';
		$response->height  = '10';
		$response->version = '1.0';
		$response->title   = $data['repo']->description;
		$response->html    = $this->process_template(
			'repository_milestone_summary.php', $data );

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();

	}

	/**
	 * Retrieve the information from github for a repo, and
	 * output it as an oembed response
	 */
	private function oembed_github_repo( $owner, $repository ) {
		$data                     = [
			'owner_slug' => $owner,
			'repo_slug'  => $repository,
		];
		$data['repo']             = $this->api->get_repo( $owner, $repository );
		$data['commits']          = $this->api->get_repo_commits( $owner, $repository );
		$data['logo_class']       = apply_filters( 'wp_github_oembed_logo_class', 'github-logo-mark' );
		$data['details_expanded'] = apply_filters( 'wp_github_oembed_repository_commit_details_expanded', true );

		$response          = new stdClass();
		$response->type    = 'rich';
		$response->width   = '10';
		$response->height  = '10';
		$response->version = '1.0';
		$response->title   = $data['repo']->description;
		$response->html    = $this->process_template(
			'repository.php', $data );


		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}

	/**
	 * Retrieve the information from github for an author, and output
	 * it as an oembed response
	 */
	private function oembed_github_author( $owner ) {
		$data               = [];
		$data["owner"]      = $owner;
		$data["owner_info"] = $this->api->get_user( $owner );
		$data["logo_class"] = apply_filters( 'wp_github_oembed_logo_class',
			'github-logo-octocat' );

		$response          = new stdClass();
		$response->type    = 'rich';
		$response->width   = '10';
		$response->height  = '10';
		$response->version = '1.0';
		$response->title   = $data['owner_info']->name;
		$response->html    = $this->process_template(
			'author.php', $data );

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}
}

require_once( 'github-api.php' );

$github_api   = new github_api();
$github_embed = new github_embed( $github_api );
