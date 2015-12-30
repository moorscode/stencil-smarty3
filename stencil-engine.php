<?php
/**
 * Engine code
 *
 * @package Stencil
 * @subpackage Smarty3
 */

/**
 * Double check to make sure Stencil is loaded
 */
if ( class_exists( 'Stencil_Implementation' ) ) :

	/**
	 * Create new instance on init
	 */
	add_action( 'init', create_function( '', 'new Stencil_Smarty_3();' ) );

	/**
	 * Class stencil_smarty3
	 *
	 * Implementation of the "Smarty 3.x" templating engine
	 */
	class Stencil_Smarty_3 extends Stencil_Implementation {

		/**
		 * Initialize Smarty3 and set defaults
		 */
		public function __construct() {
			parent::__construct();

			require_once( 'lib/Smarty/Smarty.class.php' );

			$this->engine = new Smarty();
			$this->engine->setCacheDir( $this->cache_path );
			$this->engine->setCompileDir( $this->compile_path );
			$this->engine->setTemplateDir( $this->template_path );

			/**
			 * For config see:
			 * http://www.smarty.net/docs/en/config.files.tpl
			 */

			/*
			 * $this->engine->setConfigDir( $template_dir . 'configs/');
			 */

			// Add custom plugins to smarty (per template).
			$plugin_dir = apply_filters( 'smarty3-template-plugin-dir', 'smarty-plugins' );

			/**
			 * Add theme plugins & child-theme plugins
			 */
			if ( ! empty( $plugin_dir ) ) {
				$template_root = get_template_directory();
				$plugin_bases  = array( $template_root );

				$child_root = get_stylesheet_directory();
				if ( $child_root !== $template_root ) {
					$plugin_bases[] = $child_root;
				}

				foreach ( $plugin_bases as $plugin_base ) {
					$plugin_dir = implode( DIRECTORY_SEPARATOR, array( $plugin_base, $plugin_dir, '' ) );
					if ( is_dir( $plugin_dir ) ) {
						$this->engine->addPluginsDir( $plugin_dir );
					}
				}
			}

			/**
			 * Caching - when and how?
			 * http://www.smarty.net/docsv2/en/caching.tpl
			 */
			$this->engine->caching = 0;

			$this->ready();
		}

		/**
		 * Sets the variable to value
		 *
		 * @param string $variable Variable to set.
		 * @param mixed  $value Value to apply.
		 *
		 * @return mixed|void
		 */
		public function set( $variable, $value ) {
			if ( is_object( $value ) ) {
				$this->engine->assignByRef( $variable, $value );
			} else {
				$this->engine->assign( $variable, $value );
			}

			return $this->get( $variable );
		}

		/**
		 * Gets the value of variable
		 *
		 * @param string $variable Variable to read.
		 *
		 * @return mixed|string
		 */
		public function get( $variable ) {
			return $this->engine->getTemplateVars( $variable );
		}

		/**
		 * Fetches the Smarty compiled template
		 *
		 * @param string $template Template file to get.
		 *
		 * @return string
		 */
		public function fetch( $template ) {
			return $this->engine->fetch( $template );
		}
	}

endif;
