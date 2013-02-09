<?php

/*
Plugin Name: Github Embed
Plugin URI: http://www.leewillis.co.uk/wordpress-plugins
Description: Paste the URL to a Github project into your posts or pages, and have the project information pulled in and displayed automatically
Version: 1.0
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

class github_embed {



	/**
	 * Constructor. Registers hooks and filters
	 */
	public function __construct() {

		add_action ( 'init', array ( $this, 'register_oembed_handler' ) );
		add_action ( 'init', array ( $this, 'maybe_handle_oembed' ) );
		add_action ( 'wp_enqueue_scripts', array ( $this, 'enqueue_styles' ) );
		
		// @TODO i18n

	}



	/**
	 * Enqueue the frontend CSS
	 * @return void
	 */
	function enqueue_styles() {

		wp_register_style ( 'github-embed', plugins_url(basename(dirname(__FILE__)).'/css/github-embed.css' ) );
        wp_enqueue_style ( 'github-embed' );
	
	}



	/**
	 * Register the oEmbed provider, and point it at a local endpoint since github
	 * doesn't directly support oEmbed yet. Our local endpoint will use the github
	 * API to fulfil the request.
	 * @param  array $providers The current list of providers
	 * @return array            The list, with our new provider added
	 */
	public function register_oembed_handler() {

		$oembed_url = home_url ();
		$key = $this->get_key();
		$oembed_url = add_query_arg ( array ( 'github_oembed' => $key ), $oembed_url);
		wp_oembed_add_provider ( '#https?://github.com/.*#i', $oembed_url, true );

	}



	/**
	 * Generate a unique key that can be used on our requests to stop others
	 * hijacking our internal oEmbed API
	 * @return string The site key
	 */
	private function get_key() {

		$key = get_option ( 'github_oembed_key' );
		
		if ( ! $key ) {
			$key = md5 ( time() . rand ( 0,65535 ) );
			add_option ( 'github_oembed_key', $key, '', 'yes' );
		}

		return $key;

	}



	/**
	 * Check whether this is an oembed request, handle if it is
	 * Ignore it if not.
	 * Insert rant here about WP's lack of a front-end AJAX handler.
	 */
	public function maybe_handle_oembed() {

		if ( isset ( $_GET['github_oembed'] ) ) {
			return $this->handle_oembed();
		}

	}



	/**
	 * Handle an oembed request
	 */
	public function handle_oembed() {

		// Check this request is valid
		if ( $_GET['github_oembed'] != $this->get_key() ) {
            header ( 'HTTP/1.0 403 Forbidden' );
			die ( 'Sad Octocat is sad.' );
		}

		// Check we have the required information
		$url = isset ( $_REQUEST['url'] ) ? $_REQUEST['url'] : null;
		$format = isset ( $_REQUEST['format'] ) ? $_REQUEST['format'] : null;

		if ( ! empty ( $format ) && $format != 'json' ) {
			header ( 'HTTP/1.0 501 Not implemented' );
			die ( 'This octocat only does json' );
		}

		if ( ! $url ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		if ( preg_match ( '#https?://github.com/([^/]*)/([^/]*)/?$#i', $url, $matches ) && ! empty ( $matches[2] ) ) {

			$this->oembed_github_repo ( $matches[1], $matches[2] );

		} elseif ( preg_match ( '#https?://github.com/([^/]*)/?$#i', $url, $matches ) ) {

			$this->oembed_github_author ( $matches[1] );

		}

	}



	/**
	 * Retrieve the information from github for a repo, and
	 * output it as an oembed response
	 */
	private function oembed_github_repo ( $owner, $repository ) {

		$repository = trim ( $repository, '/' );

		$results = wp_remote_get( "https://api.github.com/repos/$owner/$repository", $args = array (
		              'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		$repo = json_decode ( $results['body'] );

		$results = wp_remote_get( "https://api.github.com/repos/$owner/$repository/commits", $args = array (
		              'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		$commits = json_decode ( $results['body'] );

		$response = new stdClass();
		$response->type = 'rich';
		$response->width = '10';
		$response->height = '10';
		$response->version = '1.0';
		$response->title = $repo->description;

		// @TODO This should all be templated
		$response->html = '<div class="github-embed github-embed-repository">';
		$response->html .= '<p><a href="'.esc_attr($repo->html_url).'" target="_blank"><strong>'.esc_html($repo->description)."</strong></a><br/>";
		$response->html .= '<a href="'.esc_attr($repo->html_url).'" target="_blank">'.esc_html($repo->html_url)."</a><br/>";
		$response->html .= esc_html($repo->forks_count)." forks.<br/>";
		$response->html .= esc_html($repo->open_issues_count)." open issues.<br/>";

		if ( count ( $commits ) ) {

			$cnt = 0;
			$response->html .= 'Recent commits:';
			$response->html .= '<ul class="github_commits">';

			foreach ( $commits as $commit ) {

				if ($cnt > 4)
					break;

				$response->html .= '<li class="github_commit">';
				$response->html .= '<a href="https://github.com/'.$owner.'/'.$repository.'/commit/'.esc_attr($commit->sha).'" target="_blank">'.esc_html($commit->commit->message)."</a>, ";
				$response->html .= esc_html($commit->commit->committer->name);
				$response->html .= '</li>';
				
				$cnt++;

			}

			$response->html .= '</ul>';

		}
		$response->html .= '</p>';
		$response->html .= '</div>';

		header ( 'Content-Type: application/json' );
		echo json_encode ( $response );
		die();

	}



	/**
	 * Retrieve the information from github for an author, and output
	 * it as an oembed response
	 */
	private function oembed_github_author ( $owner ) {

		$owner = trim ( $owner, '/' );

		$results = wp_remote_get( "https://api.github.com/users/$owner", $args = array (
		              'user-agent' => 'WordPress Github oEmbed plugin - https://github.com/leewillis77/wp-github-oembed' ) );

		if ( is_wp_error( $results ) ||
		    ! isset ( $results['response']['code'] ) ||
		    $results['response']['code'] != '200' ) {
			header ( 'HTTP/1.0 404 Not Found' );
			die ( 'Octocat is lost, and afraid' );
		}

		$owner_info = json_decode ( $results['body'] );

		$response = new stdClass();
		$response->type = 'rich';
		$response->width = '10';
		$response->height = '10';
		$response->version = '1.0';
		$response->title = $owner_info->name;

		// @TODO This should all be templated
		$response->html = '<div class="github-embed github-embed-user">';
		$response->html .= '<p><a href="https://github.com/'.esc_attr($owner).'" target="_blank"><strong>'.esc_html($owner)."</strong></a><br/>";
		$response->html .= esc_html($owner_info->public_repos).' repositories, ';
		$response->html .= esc_html($owner_info->followers).' followers.</p>';
		$response->html .= '</div>';

		header ( 'Content-Type: application/json' );
		echo json_encode ( $response );
		die();

	}

}

$github_embed = new github_embed();