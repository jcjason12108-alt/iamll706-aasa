<?php
/**
 * Plugin Name: IAMLL706 AASA
 * Plugin URI: https://github.com/jcjason12108-alt/iamll706-aasa/
 * Description: Serves Apple's App Site Association (AASA) JSON at /.well-known/apple-app-site-association.
 * Version: 1.0.3
 * Requires at least: 6.0
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * Author: Jason Cox
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: iamll706-aasa
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Usage:
 * 1) Copy this folder to wp-content/plugins/iamll706-aasa/
 * 2) Activate "IAMLL706 AASA" in wp-admin.
 * 3) If your host delays rewrite propagation, go to Settings > Permalinks > Save.
 *
 * Testing:
 * - curl -I https://iamll706.org/.well-known/apple-app-site-association
 * - curl    https://iamll706.org/.well-known/apple-app-site-association
 *
 * Expected:
 * - HTTP/1.1 200 OK
 * - Content-Type: application/json
 * - JSON body only (no HTML, no redirects, no extra output)
 */

if (!defined('IAMLL706_AASA_QUERY_VAR')) {
	define('IAMLL706_AASA_QUERY_VAR', 'iamll706_aasa');
}

if (!defined('IAMLL706_AASA_ROUTE_REGEX')) {
	define('IAMLL706_AASA_ROUTE_REGEX', '^\.well-known/apple-app-site-association$');
}

require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

$iamll706_aasa_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/jcjason12108-alt/iamll706-aasa/',
	__FILE__,
	'iamll706-aasa'
);
$iamll706_aasa_update_checker->setBranch('main');

$iamll706_aasa_github_token = defined('IAMLL706_AASA_GITHUB_TOKEN')
	? IAMLL706_AASA_GITHUB_TOKEN
	: getenv('IAMLL706_AASA_GITHUB_TOKEN');

if (empty($iamll706_aasa_github_token)) {
	$iamll706_aasa_github_token = defined('PLUGIN_UPDATE_GITHUB_TOKEN')
		? PLUGIN_UPDATE_GITHUB_TOKEN
		: getenv('PLUGIN_UPDATE_GITHUB_TOKEN');
}

if (!empty($iamll706_aasa_github_token)) {
	$iamll706_aasa_update_checker->setAuthentication($iamll706_aasa_github_token);
}

add_filter(
	$iamll706_aasa_update_checker->getUniqueName('vcs_update_detection_strategies'),
	static function (array $strategies): array {
		return isset($strategies['branch']) ? ['branch' => $strategies['branch']] : $strategies;
	}
);

if (!function_exists('iamll706_aasa_add_rewrite_rule')) {
	function iamll706_aasa_add_rewrite_rule() {
		add_rewrite_rule(IAMLL706_AASA_ROUTE_REGEX, 'index.php?' . IAMLL706_AASA_QUERY_VAR . '=1', 'top');
	}
}

if (!function_exists('iamll706_aasa_add_query_var')) {
	function iamll706_aasa_add_query_var($vars) {
		$vars[] = IAMLL706_AASA_QUERY_VAR;
		return $vars;
	}
}

if (!function_exists('iamll706_aasa_disable_canonical_redirect')) {
	function iamll706_aasa_disable_canonical_redirect($redirect_url) {
		if (get_query_var(IAMLL706_AASA_QUERY_VAR)) {
			return false;
		}

		return $redirect_url;
	}
}

if (!function_exists('iamll706_aasa_serve_json')) {
	function iamll706_aasa_serve_json() {
		if (!get_query_var(IAMLL706_AASA_QUERY_VAR)) {
			return;
		}

		$payload = [
			'webcredentials' => [
				'apps' => ['8VLBYL3SV8.org.iamll706.AskBruno'],
			],
		];

		status_header(200);
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: public, max-age=3600');
		echo wp_json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		exit;
	}
}

if (!function_exists('iamll706_aasa_activate')) {
	function iamll706_aasa_activate() {
		iamll706_aasa_add_rewrite_rule();
		flush_rewrite_rules();
	}
}

if (!function_exists('iamll706_aasa_deactivate')) {
	function iamll706_aasa_deactivate() {
		flush_rewrite_rules();
	}
}

add_action('init', 'iamll706_aasa_add_rewrite_rule');
add_filter('query_vars', 'iamll706_aasa_add_query_var');
add_filter('redirect_canonical', 'iamll706_aasa_disable_canonical_redirect');
add_action('template_redirect', 'iamll706_aasa_serve_json', 0);

register_activation_hook(__FILE__, 'iamll706_aasa_activate');
register_deactivation_hook(__FILE__, 'iamll706_aasa_deactivate');
