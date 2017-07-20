<?php

/*
Plugin Name: Github Embed
Plugin URI: http://www.leewillis.co.uk/wordpress-plugins
Description: Paste the URL to a Github project into your posts or pages, and have the project information pulled in and displayed automatically
Version: 1.6
Author: Lee Willis
Author URI: http://www.leewillis.co.uk/
*/

/**
 * Copyright (c) 2013 Lee Willis. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
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
	function schedule_expiry() {
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
	function cron() {
		global $wpdb, $table_prefix;
		$sql = "DELETE
				  FROM {$table_prefix}postmeta
				 WHERE meta_key LIKE '_oembed_%'";
		$results = $wpdb->get_results( $sql );
	}

	/**
	 * Enqueue the frontend CSS
	 * @return void
	 */
	function enqueue_styles() {
		wp_register_style( 'github-embed', plugins_url( basename( dirname( __FILE__ ) ) . '/css/github-embed.css' ) );
		wp_enqueue_style( 'github-embed' );
	}

	/**
	 * Register the oEmbed provider, and point it at a local endpoint since github
	 * doesn't directly support oEmbed yet. Our local endpoint will use the github
	 * API to fulfil the request.
	 * @param  array $providers The current list of providers
	 * @return array            The list, with our new provider added
	 */
	public function register_oembed_handler() {
		$oembed_url = home_url();
		$key = $this->get_key();
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
		$url = isset( $_REQUEST['url'] ) ? $_REQUEST['url'] : null;
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
	 * Retrieve a list of contributors for a project
	 * @param  string $owner      The owner of the repository
	 * @param  string $repository The repository name
	 */
	private function oembed_github_repo_contributors( $owner, $repository ) {
		$repo = $this->api->get_repo( $owner, $repository );
		$contributors = $this->api->get_repo_contributors( $owner, $repository );

		$response = new stdClass();
		$response->type = 'rich';
		$response->width = '10';
		$response->height = '10';
		$response->version = '1.0';
		$response->title = $repo->description;

		$gravatar_size = apply_filters( 'github_oembed_gravatar_size', 64 );

		// @TODO This should all be templated
		$response->html = '<div class="github-embed github-embed-repo-contributors">';
		$response->html .= '<p><a href="' . esc_attr( $repo->html_url ) . '" target="_blank">';
		$response->html .= '<strong>' . esc_html( $repo->description ) . '</strong></a><br/>';
		$response->html .= '<span class="github-heading">Contributors: </span>';
		$response->html .= '<ul class="github-repo-contributors">';
		foreach ( $contributors as $contributor ) {
			$response->html .= '<li class="github-repo-contributor">';
			$response->html .= '<img class="github-repo-contributor-avatar" src="';
			$response->html .= esc_url( add_query_arg( array( 's' => $gravatar_size ), $contributor->author->avatar_url ) );
			$response->html .= '" alt="Picture of ' . esc_attr( $contributor->author->login ) . '">';
			$response->html .= '<span class="github-repo-contributor-login">';
			$response->html .= '<a href="https://github.com/' . esc_attr( $contributor->author->login ) . '">' . esc_attr( $contributor->author->login ) . '</a></span>';
		}
		$response->html .= '</ul>';
		$response->html .= '<div style="clear: both;"></div>';
		$response->html .= '</div>';
		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}

	/**
	 * Retrieve the summary information for a repo's milestone, and
	 * output it as an oembed response
	 */
	private function oembed_github_repo_milestone_summary( $owner, $repository, $milestone ) {
		$repo = $this->api->get_repo( $owner, $repository );
		$summary = $this->api->get_repo_milestone_summary( $owner, $repository, $milestone );

		$response = new stdClass();
		$response->type = 'rich';
		$response->width = '10';
		$response->height = '10';
		$response->version = '1.0';
		$response->title = $repo->description;

		// @TODO This should all be templated
		$response->html = '<div class="github-embed github-embed-milestone-summary">';
		$response->html .= '<p><a href="' . esc_attr( $repo->html_url ) . '" target="_blank"><strong>' . esc_html( $repo->description ) . '</strong></a><br/>';

		$response->html .= '<span class="github-heading">Milestone: </span>';
		$response->html .= '<span class="github-milestone-title">' . esc_html( $summary->title ) . '</span><br>';

		$response->html .= '<span class="github-heading">Issues: </span>';
		$response->html .= '<span class="github-milestone-issues">';
		$response->html .= esc_html( number_format_i18n( $summary->open_issues ) ) . ' open, ';
		$response->html .= esc_html( number_format_i18n( $summary->closed_issues ) ) . ' closed.</span><br>';

		if ( ! empty( $summary->due_on ) ) {
			$response->html .= '<span class="github-heading">Due: </span>';
			$due_date = date_format( date_create( $summary->due_on ), 'jS F Y' );
			$response->html .= '<span class="github-milestone-due-date">' . esc_html( $due_date ) . '</span><br>';
		}

		$response->html .= '<p class="github-milestone-description">' . nl2br( esc_html( $summary->description ) ) . '</p><br>';
		$response->html .= '</div>';

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();

	}

	/**
	 * Retrieve the information from github for a repo, and
	 * output it as an oembed response
	 */
	private function oembed_github_repo ( $owner, $repository ) {
		$repo = $this->api->get_repo( $owner, $repository );
		$commits =$this->api->get_repo_commits( $owner, $repository );

		$response = new stdClass();
		$response->type = 'rich';
		$response->width = '10';
		$response->height = '10';
		$response->version = '1.0';
		$response->title = $repo->description;

		// @TODO This should all be templated
		$response->html = '<div class="github-embed github-embed-repository">';
		$response->html .= '<p><a href="' . esc_attr( $repo->html_url ) . '" target="_blank"><strong>' . esc_html( $repo->description ) . '</strong></a><br/>';
		$response->html .= '<a href="' . esc_attr( $repo->html_url ) . '" target="_blank">' . esc_html( $repo->html_url ) . '</a><br/>';
		$response->html .= '<a href="' . esc_attr( $repo->html_url . '/network' ) . '" target="_blank">' . esc_html( number_format_i18n( $repo->forks_count ) ) . '</a> forks.<br/>';
		$response->html .= '<a href="' . esc_attr( $repo->html_url . '/stargazers' ) . '" target="_blank">' . esc_html( number_format_i18n( $repo->stargazers_count ) ) . '</a> stars.<br/>';
		$response->html .= '<a href="' . esc_attr( $repo->html_url . '/issues' ) . '" target="_blank">' . esc_html( number_format_i18n( $repo->open_issues_count ) ) . '</a> open issues.<br/>';

		if ( count( $commits ) ) {
			$cnt = 0;
			$response->html .= 'Recent commits:';
			$response->html .= '<ul class="github_commits">';
			foreach ( $commits as $commit ) {
				if ( $cnt > 4 ) {
					break;
				}
				$response->html .= '<li class="github_commit">';
				$response->html .= '<a href="https://github.com/' . $owner . '/' . $repository . '/commit/' . esc_attr( $commit->sha ) . '" target="_blank">' . esc_html( $commit->commit->message ) . '</a>, ';
				$response->html .= esc_html( $commit->commit->committer->name );
				$response->html .= '</li>';
				$cnt++;
			}
			$response->html .= '</ul>';
		}
		$response->html .= '</p>';
		$response->html .= '</div>';
		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}

	/**
	 * Retrieve the information from github for an author, and output
	 * it as an oembed response
	 */
	private function oembed_github_author ( $owner ) {

		$owner_info = $this->api->get_user( $owner );

		$response = new stdClass();
		$response->type = 'rich';
		$response->width = '10';
		$response->height = '10';
		$response->version = '1.0';
		$response->title = $owner_info->name;

		// @TODO This should all be templated
		$response->html = '<div class="github-embed github-embed-user">';
		$response->html .= '<p><a href="https://github.com/' . esc_attr( $owner ) . '" target="_blank"><strong>' . esc_html( $owner ) . '</strong></a><br/>';
		$response->html .= esc_html( number_format_i18n( $owner_info->public_repos ) ) . ' repositories, ';
		$response->html .= esc_html( number_format_i18n( $owner_info->followers ) ) . ' followers.</p>';
		$response->html .= '</div>';
		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die();
	}
}

require_once( 'github-api.php' );

$github_api = new github_api();
$github_embed = new github_embed( $github_api );
