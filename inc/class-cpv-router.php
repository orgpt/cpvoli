<?php
/**
 * Rewrite and routing support.
 *
 * @package ConfrontaPrezziVoli
 */

class CPV_Router {
	public static function init(): void {
		add_filter( 'query_vars', array( __CLASS__, 'register_query_vars' ) );
		add_action( 'init', array( __CLASS__, 'add_rewrite_rules' ) );
	}

	public static function register_query_vars( array $vars ): array {
		$vars[] = 'cpv_airport';
		$vars[] = 'cpv_route';
		$vars[] = 'cpv_city';
		$vars[] = 'cpv_airline';
		$vars[] = 'cpv_compare';

		return $vars;
	}

	public static function add_rewrite_rules(): void {
		add_rewrite_rule( '^voli-da/([^/]+)/?$', 'index.php?cpv_airport=$matches[1]', 'top' );
		add_rewrite_rule( '^voli/([^/]+)-a-([^/]+)/?$', 'index.php?cpv_route=$matches[1]__to__$matches[2]', 'top' );
		add_rewrite_rule( '^voli-per/([^/]+)/?$', 'index.php?cpv_city=$matches[1]', 'top' );
		add_rewrite_rule( '^compagnie-aeree/([^/]+)/?$', 'index.php?cpv_airline=$matches[1]', 'top' );
		add_rewrite_rule( '^confronto/([^/]+)-vs-([^/]+)/?$', 'index.php?cpv_compare=$matches[1]__vs__$matches[2]', 'top' );
	}
}

CPV_Router::init();

