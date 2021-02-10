<?php
/**
 * HFE Settings Page.
 *
 * Add plugin setting page.
 *
 * @since x.x.x
 * @package hfe
 */

namespace HFE\Themes;

/**
 * Class Settings Page.
 *
 * @since x.x.x
 */
class HFE_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		$this->setup_fallback_support();
		add_action( 'admin_menu', [ $this, 'hfe_register_settings_page' ] );
		add_action( 'admin_init', [ $this, 'hfe_admin_init' ] );
		add_action( 'admin_head', [ $this, 'hfe_global_css' ] );
		add_filter( 'views_edit-elementor-hf', [ $this, 'hfe_settings' ], 10, 1 );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ] );
	}

	/**
	 * var for tabs
	 */
	public static $hfe_settings_tabs;

	/**
	 * Adds CSS to Hide the extra submenu added for the settings tab.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function hfe_global_css() {
		wp_enqueue_style( 'hfe-admin-style', HFE_URL . 'admin/assets/css/ehf-admin.css', [], HFE_VER );
	}

	/**
	 * Adds a tab in plugin submenu page.
	 *
	 * @since x.x.x
	 * @param string $views to add tab to current post type view.
	 *
	 * @return mixed
	 */
	public function hfe_settings( $views ) {
		$this->hfe_tabs();
		return $views;
	}

	/**
	 * Function for registering the settings api.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function hfe_admin_init() {
		register_setting( 'hfe-plugin-options', 'hfe_compatibility_option' );
		add_settings_section( 'hfe-options', __( 'Add Theme Support', 'header-footer-elementor' ), [ $this, 'hfe_compatibility_callback' ], 'Settings' );
		add_settings_field( 'hfe-way', 'Methods to Add Theme Support', [ $this, 'hfe_compatibility_option_callback' ], 'Settings', 'hfe-options' );
	}

	/**
	 * Call back function for the ssettings api function add_settings_section
	 *
	 * This function can be used to add description of the settings sections
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function hfe_compatibility_callback() {
		_e( 'The Elementor - Header, Footer & Blocks plugin need compatibility with your current theme to work smoothly.</br></br>Following are two methods that enable theme support for the plugin.</br></br>Method 1 is selected by default and that works fine almost will all themes. In case, you face any issue with the header or footer template, try choosing Method 2.', 'header-footer-elementor' );
	}

	/**
	 * Call back function for the ssettings api function add_settings_field
	 *
	 * This function will contain the markup for the input feilds that we can add.
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function hfe_compatibility_option_callback() {
		$hfe_radio_button = get_option( 'hfe_compatibility_option', '1' );
			wp_enqueue_style( 'hfe-admin-style', HFE_URL . 'admin/assets/css/ehf-admin.css', [], HFE_VER );
		?>

		<label>
			<input type="radio" name="hfe_compatibility_option" value= 1 <?php checked( $hfe_radio_button, 1 ); ?> > <div class="hfe_radio_options"><?php esc_html_e( ' Method 1 (Recommended)', 'header-footer-elementor' ); ?></div>
			<p class="description"><?php esc_html_e( 'This method replaces your theme\'s header (header.php) & footer (footer.php) template with plugin\'s custom templates.', 'header-footer-elementor' ); ?></p><br>
		</label>
		<label>
			<input type="radio" name="hfe_compatibility_option" value= 2 <?php checked( $hfe_radio_button, 2 ); ?> > <div class="hfe_radio_options"><?php esc_html_e( 'Method 2', 'header-footer-elementor' ); ?></div>
			<p class="description">
				<?php
				echo sprintf(
					esc_html( "This method hides your theme's header & footer template with CSS and displays custom templates from the plugin.", 'header-footer-elementor' ),
					'<br>'
				);
				?>
			</p><br>
		</label>
		<p class="description">
			<?php
			echo sprintf(
				_e( 'Sometimes above methods might not work well with your theme, in this case, contact your theme author and request them to add support for the <a href="https://github.com/Nikschavan/header-footer-elementor/wiki/Adding-Header-Footer-Elementor-support-for-your-theme">plugin.</>', 'header-footer-elementor' ),
				'<br>'
			);
			?>
		</p>

		<?php
	}

	/**
	 * Show a settings page incase of unsupported theme.
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function hfe_register_settings_page() {
		add_submenu_page(
			'themes.php',
			__( 'Settings', 'header-footer-elementor' ),
			__( 'Settings', 'header-footer-elementor' ),
			'manage_options',
			'hfe-settings',
			[ $this, 'hfe_settings_page' ]
		);

		add_submenu_page(
			'themes.php',
			__( 'Step-By-Step Guide', 'header-footer-elementor' ),
			__( 'Step-By-Step Guide', 'header-footer-elementor' ),
			'manage_options',
			'hfe-guide',
			[ $this, 'hfe_settings_page' ]
		);

		add_submenu_page(
			'themes.php',
			__( 'About Us', 'header-footer-elementor' ),
			__( 'About Us', 'header-footer-elementor' ),
			'manage_options',
			'hfe-about',
			[ $this, 'hfe_settings_page' ]
		);
	}

	/**
	 * Setup Theme Support.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function setup_fallback_support() {
		$hfe_compatibility_option = get_option( 'hfe_compatibility_option', '1' );

		if ( '1' === $hfe_compatibility_option ) {
			require HFE_DIR . 'themes/default/class-hfe-default-compat.php';
		} elseif ( '2' === $hfe_compatibility_option ) {
			require HFE_DIR . 'themes/default/class-global-theme-compatibility.php';
		}
	}

	/**
	 * Settings page.
	 *
	 * Call back function for add submenu page function.
	 *
	 * @since x.x.x
	 */
	public function hfe_settings_page() {
		echo '<h1 class="hfe-heading-inline">';
		esc_attr_e( 'Elementor - Header, Footer & Blocks ', 'header-footer-elementor' );
		echo '</h1>';
		$this->hfe_tabs();
		?>
		<br />
		<?php
		$hfe_radio_button = get_option( 'hfe_compatibility_option', '1' );
		?>
		<?php
		switch( $_GET['page'] ){
			case 'hfe-settings' :
				$this->get_themes_support();
				break;
			
			case 'hfe-guide' :
				$this->get_guide_html();
				break;

			case 'hfe-about' :
				$this->get_about_html();
				break;

			case 'default' :
				break;
		}
		
	}

	/**
	 * Function for adding tabs
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function hfe_tabs() {
		?>
		<h2 class="nav-tab-wrapper">
			<?php
			if ( ! isset( self::$hfe_settings_tabs ) ) {
				self::$hfe_settings_tabs       = [
					'hfe_templates' => [
						'name' => __( 'All templates', 'header-footer-elementor' ),
						'url'  => admin_url( 'edit.php?post_type=elementor-hf' ),
					],
					'hfe_settings'  => [
						'name' => __( 'Theme Support', 'header-footer-elementor' ),
						'url'  => admin_url( 'themes.php?page=hfe-settings' ),
					],
					'hfe_guide'  => [
						'name' => __( 'Step-By-Step Guide', 'header-footer-elementor' ),
						'url'  => admin_url( 'themes.php?page=hfe-guide' ),
					],
					'hfe_about'  => [
						'name' => __( 'About Us', 'header-footer-elementor' ),
						'url'  => admin_url( 'themes.php?page=hfe-about' ),
					],
				];
			}

			$tabs = self::$hfe_settings_tabs;
			
			foreach ( $tabs as $tab_id => $tab ) {

				$tab_slug = str_replace( '_', '-', $tab_id );

				$active_tab = ( isset( $_GET['page'] ) && $tab_slug == $_GET['page'] ) ? $tab_id : 'hfe_templates';

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab['url'] ) . '" class="nav-tab' . esc_attr( $active ) . '">';
				echo esc_html( $tab['name'] );
				echo '</a>';
			}

			?>
		</h2>
		<br />
		<?php
	}
	
	/**
	 * Admin footer text.
	 *
	 * Modifies the "Thank you" text displayed in the admin footer.
	 *
	 * Fired by `admin_footer_text` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $footer_text The content that will be printed.
	 *
	 * @return string The content that will be printed.
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		$is_elementor_screen = ( $current_screen && 'elementor-hf' === $current_screen->post_type );

		if ( $is_elementor_screen ) {
			$footer_text = sprintf(
				/* translators: 1: Elementor, 2: Link to plugin review */
				__( 'Please rate %1$s on %2$s %3$s to help us spread the word. We really appreciate your support!', 'header-footer-elementor' ),
				'<strong>' . __( 'Elementor - Header, Footer & Blocks', 'header-footer-elementor' ) . '</strong>', '&#9733;&#9733;&#9733;&#9733;&#9733;',
				'<a href="https://wordpress.org/support/theme/astra/reviews/#new-post" target="_blank">WordPress.org</a>'
			);
		}

		return $footer_text;
	}

	/**
	 * Function for theme suuport tab
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function get_themes_support() {
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'hfe-plugin-options' ); ?>
			<?php do_settings_sections( 'Settings' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Function for Step-By-Step guide
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function get_guide_html() {

		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'hfe-plugin-options' ); ?>
			<?php do_settings_sections( 'Settings' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Function for About us HTML
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function get_about_html() {

		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'hfe-plugin-options' ); ?>
			<?php do_settings_sections( 'Settings' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

}

new HFE_Settings_Page();
