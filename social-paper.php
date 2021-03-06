<?php
/*
Plugin Name: Social Paper
Description: Create a paper allowing colleagues to comment on each paragraph. Inspired by Medium.com.
Author: CUNY Academic Commons Team
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', array( 'Social_Paper', 'init' ), 20 );

/**
 * Social Paper core class.
 *
 * @package Social_Paper
 * @subpackage Core
 */
class Social_Paper {

	/**
	 * Absolute path to our this directory.
	 *
	 * @var string
	 */
	public static $PATH = '';

	/**
	 * URL to this directory.
	 *
	 * @var string
	 */
	public static $URL = '';

	/**
	 * Absolute path to our bundled template directory.
	 *
	 * @var string
	 */
	public static $TEMPLATEPATH = '';

	/**
	 * Marker to determine if we're on a social paper page.
	 *
	 * @var bool
	 */
	public static $is_page = false;

	/**
	 * Static initializer.
	 */
	public static function init() {
		return new self();
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// bail if a commenting plugin is not enabled
		if ( false === $this->is_commenting_plugin_enabled() ) {
			$notice = __( 'Social Paper requires a paragraph commenting plugin enabled.', 'social-paper' );

			if ( count( array_keys( $this->get_supported_commenting_plugins() ) ) > 1 ) {
				$notice .= ' ' . __( 'Please install one of the following:', 'social-paper' );
			} else {
				$notice .= ' ' . __( 'Please install:', 'social-paper' );
			}

			$plugins = '';
			foreach( (array) $this->get_supported_commenting_plugins() as $plugin ) {
				$plugins .= "&middot; <a href=\"{$plugin['link']}\">{$plugin['name']}</a><br />";
			}

			// show admin notice
			add_action( 'admin_notices', create_function( '', "
				echo '<div class=\"error\"><p>" . $notice . "</p><p>" . $plugins . "</p></div>';
			" ) );
			return;
		}

		$this->properties();
		$this->includes();
	}

	/**
	 * Properties.
	 */
	protected function properties() {
		self::$PATH         = dirname( __FILE__ );
		self::$URL          = plugins_url( basename( self::$PATH ) );
		self::$TEMPLATEPATH = self::$PATH . '/templates';
	}

	/**
	 * Includes.
	 */
	protected function includes() {
		require dirname( __FILE__ ) . '/includes/functions.php';
		require dirname( __FILE__ ) . '/includes/class-cacsp-paper.php';
		require dirname( __FILE__ ) . '/includes/hooks-template.php';

		/**
		 * Should we register our custom post type?
		 *
		 * Handy to disable if developers already have their own post types in mind.
		 *
		 * @param type bool
		 */
		$register_cpt = (bool) apply_filters( 'sp_register_cpt', true );
		if ( true === $register_cpt ) {
			require dirname( __FILE__ ) . '/includes/schema.php';
		}

		// BuddyPress integration
		if ( function_exists( 'buddypress' ) ) {
			//require dirname( __FILE__ ) . '/includes/hooks-buddypress-activity.php';
			//require dirname( __FILE__ ) . '/includes/hooks-buddypress-group.php';
			//require dirname( __FILE__ ) . '/includes/hooks-buddypress-directory.php';
		}
	}

	/**
	 * Get supported commenting plugins.
	 *
	 * Social Paper heavily relies on a 3rd-party commenting plugin to handle
	 * paragraph commenting.
	 *
	 * Currently, we only support UBC CTLT's WP Side Comments plugin:
	 * https://github.com/richardtape/wp-side-comments
	 *
	 * @return array
	 */
	protected function get_supported_commenting_plugins() {
		$plugins = array();

		$plugins['wp-side-comments'] = array();
		$plugins['wp-side-comments']['name']   = 'WP Side Comments';
		$plugins['wp-side-comments']['exists'] = class_exists( 'CTLT_WP_Side_Comments' );
		$plugins['wp-side-comments']['link']   = 'https://github.com/richardtape/wp-side-comments';

		// @todo investigate Inline Comments
		// https://github.com/kevinweber/inline-comments

		/**
		 * Allow plugins to filter if a commenting plugin is enabled.
		 *
		 * Handy for developers to build support for other commenting plugins.
		 *
		 * @param type $retval bool
		 */
		return apply_filters( 'sp_get_supported_commenting_plugins', $plugins );
	}

	/**
	 * See if a commenting plugin is enabled.
	 *
	 * @return bool
	 */
	protected function is_commenting_plugin_enabled() {
		$enabled = false;

		foreach ( (array) $this->get_supported_commenting_plugins() as $plugin => $prop ) {
			if ( true === $prop['exists'] ) {
				$enabled = true;
				break;
			}
		}

		return $enabled;
	}
}