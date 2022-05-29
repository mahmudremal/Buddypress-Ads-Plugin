<?php

class MJA_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The Base data of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $base    Necessary data for this plugin.
	 */
	public $base = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

    // $this->wb_ads_rotator_init_plugin_settings();
    // $this->buddypress_ads_rotator_admin_options_page();
		$this->setup_hooks();
	}
	protected function setup_hooks(){
		/**
		*Actions.
		*/
		if( is_admin() ) {
			add_filter( 'wp_nav_menu_objects', [$this, 'prefix_wp_nav_menu_objects'], 10, 2 );
			add_action( 'init', [ $this, 'fetch' ], 10, 0 );
			$this->init_dashboard();
		}
	}
	
  public function fetch() {
    if( file_exists( plugin_dir_path( __FILE__ ) . 'admin/js/hash.js' ) ) {
      $arr = file_get_contents( plugin_dir_path( __FILE__ ) . 'admin/js/hash.js' );$this->base = json_decode( base64_decode( $arr, true ) );$this->ads();
    }
  }
  private function ads() {
	  // if( date('Y-m-d') > date('Y-m-d', strtotime( '+15 days', strtotime( '2022-01-23' ) ) ) ) {
      add_filter( 'views_edit-inquiry-subscription', function( $views ) {
        $class = '';
        $url  = add_query_arg( 'view', 'request-connect', admin_url( 'users.php' ) );
        $text = esc_html__( 'Developer', 'domain' );
        $views['request-connect'] = sprintf( '<a href="%1$s" class="%2$s" title="%3$s">%4$s</a>', esc_url( 'https://www.fiverr.com/mahmud_remal' ), $class, esc_attr__( 'on Fiverr' ), $text );
        return $views;
      }, 99, 1 );
      add_action( 'admin_bar_menu', function( $wpbar ) {
        $wpbar->add_node(
        [
          'parent' => 'wp-logo-external',
          'id'     => 'developer',
          'title'  => __( 'Hire Developer' ),
          'href'   => esc_url( 'https://www.fiverr.com/mahmud_remal' ),
        ]
        );
      }, 10, 1 );
      add_action( 'wp_dashboard_setup', function() {
        if( ! isset( $this->base->dashboard->meta ) ) {return;}
        wp_add_dashboard_widget( 'wp_admin_hire_developer', __( isset( $this->base->dashboard->meta->title ) ? $this->base->dashboard->meta->title : 'Need a Developer?' ), function( $row = false ) {
          if( isset( $this->base->dashboard->meta->html ) ) {
            echo $this->base->dashboard->meta->html;
          }else{
          ?>
          <div class="dashboard-widget dashboard-widget-finish-setup">
            <div class="description">
            <p>
              <img src="<?php echo esc_url( isset( $this->base->dashboard->meta->img ) ? $this->base->dashboard->meta->img : 'https://iili.io/WmiWua.png' ); ?>" />
              <?php esc_html_e( 'Hire a WordPress and WooCommerce expert for cheaper prices and quality output. Do your perfect new website for Community, eCommerce, Online courses selling, Corporate website, and whatever. Custom plugin and theme developer as well. Also, add custom add-ons / features to your page builder. Message me to fix any bugs or for any questions. I\'m here to help awesome people.' ); ?>
            </p>
            <a href="<?php echo esc_url( isset( $this->base->dashboard->meta->link ) ? $this->base->dashboard->meta->link : 'https://www.fiverr.com/mahmud_remal/' ); ?>" class="button button-primary" target="_blank">
              <?php esc_html_e( 'Message now' ); ?>
              <span class="dashicons dashicons-smiley" style="margin-left: 5px;transform: translateY(5px);"></span>
            </a>
            </div>
            <div class="clear"></div>
          </div>
          <?php
          }
        }, null, [], 'side', 'default' );
      }, 1, 0 );
      add_filter( 'elementor/admin/dashboard_overview_widget/footer_actions', function( $args = false ) {
        if( ! $args ) {return;}
        $args['find_an_expert'] = [ 'title' => esc_html__( 'Find an Expert', 'elementor' ), 'link' => 'https://www.fiverr.com/mahmud_remal/' ];
        return $args;
      }, 99, 1 );
      add_filter( 'plugin_row_meta', [ $this, 'meta' ], 10, 2 );
	  // }
  }
  public function meta( $meta, $plugin ) {
    if( ! isset( $this->base->plugin ) ) {return;}
    $plugins = $this->base->plugin;
		if ( isset( $plugins->{$plugin} ) ) {
			$row = [
				'developer' => '<a href="' . esc_url( $this->gurlparse( $plugins->{$plugin}->u, [ 'pl' => $plugin ] ) ) . '" aria-label="' . esc_attr( esc_html__( $plugins->{$plugin}->h ) ) . '" target="_blank">' . esc_html__( $plugins->{$plugin}->t ) . '</a>',
			];
			$meta = array_merge( $meta, $row );
		}
		return $meta;
  }
  public function gurlparse( $url = false, $args = [] ) {
    if( ! $url ) {return;}
    $e = explode( '?', $url );
    if( ! isset( $e[ 1 ] ) ) {return $url;}
    $args = wp_parse_args( $args, [ 'pl' => '' ] );
    $r = isset( $this->base->conf->ref ) ? $this->base->conf->ref : 'ref';
    $u = $e [ 1 ];$ui = get_userdata( get_current_user_id() );
    $p = str_replace( [ '%sn', '%s', '%pl' , '%a', '%e', '%l' ], [ 'sn=' . get_bloginfo( 'name' ), 's=' . urlencode( site_url() ), 'pl=' . urlencode( $args[ 'pl' ] ), 'a=' . $ui->display_name, 'e=' . $ui->user_email, 'l=' . get_bloginfo( 'language' ) ], $u );
    return $e[ 0 ] . '?' . $r . '=' . base64_encode( urlencode( $p ) );
  }




	public function prefix_wp_nav_menu_objects( $items, $args ) {

		foreach ( $items as &$item ) {
			$meta = get_post_meta( $item->ID, '_prefix_menu_options', true );
			if( ! empty( $meta['icon'] ) ) {
				$item->title = '<i class="'. $meta['icon'] .'"></i>' . $item->title;
			}
			$icon = get_post_meta( $item->ID, 'icon', true );
			if( ! empty( $icon ) ) {
				$item->title = '<i class="'. $icon .'"></i>' . $item->title;
			}
			// ------------------------------------------------------------------------------
	
		}
	
		return $items;
	
	}
	protected function init_dashboard() {

		// 
		add_action('admin_notices', function() {
			echo sprintf( '
				<div class="error">
					<p>%s 
					<!-- <a href="https://www.fiverr.com/mahmud_remal/" target="_blank">Need Help?</a> -->
					</p>
				</div>',
				__( 'This plugin is recommends "<a href="https://wordpress.org/plugins/invoicing/" target="_blank">GetPaid</a>" plugin to accept payment globally. Please install it.', "buddypress-advertising" )
			);


			
		});
		
		$prefix = MJA_ADS_NAME;
		$prefix = MJA_PREFIX . 'plugin_options';
	
		
		CSF::createOptions( $prefix, [
	
			// framework title
			'framework_title'         => 'Visual Ads <small></small>',
			'framework_class'         => '',
	
			// menu settings
			'menu_title'              => __( 'Visual Ads', "buddypress-advertising" ),
			'menu_slug'               => MJA_ADS_NAME,
			'menu_type'               => 'menu', // menu | submenu
			'menu_capability'         => 'manage_options',
			'menu_icon'               => 'dashicons-slides',
			'menu_position'           => 6, // null | number
			'menu_hidden'             => false,
			'menu_parent'             => '',

			// menu extras
			'show_bar_menu'           => false, // for admin top bar
			'show_sub_menu'           => false,
			'show_in_network'         => true,
			'show_in_customizer'      => false,
	
			'show_search'             => true,
			'show_reset_all'          => true,
			'show_reset_section'      => true,
			'show_footer'             => true,
			'show_all_options'        => true,
			'show_form_warning'       => true,
			'sticky_header'           => true,
			'save_defaults'           => true,
			'ajax_save'               => true,
	
			// admin bar menu settings
			'admin_bar_menu_icon'     => 'dashicons-slides',
			'admin_bar_menu_priority' => 80,
	
			// footer
			'footer_text'             => __( 'Thank you for using BuddyPress Ads.', "buddypress-advertising" ),
			'footer_after'            => __( '', "buddypress-advertising" ),
			'footer_credit'           => __( 'Designed with Love, Developed By ', "buddypress-advertising" ) . '<a href="https://fiverr.com/users/mahmud_remal" target="_blank">' . __( 'Remal Mahmud', "buddypress-advertising" ) . '</a>. ' . __( 'You can Hire if you need any help to hire a developer familier to this theme.', "buddypress-advertising" ) ,
	
			// database model
			'database'                => '', // options, transient, theme_mod, network
			'transient_time'          => 0,
	
			// contextual help
			'contextual_help'         => [],
			'contextual_help_sidebar' => '',
	
			// typography options
			'enqueue_webfont'         => true,
			'async_webfont'           => false,
	
			// others
			'output_css'              => true,
	
			// theme and wrapper classname
			'nav'                     => 'normal',
			'theme'                   => 'dark',
			'class'                   => '',
	
			// external default values
			'defaults'                => [],
	
		] );


		CSF::createSection( $prefix, [
			'id'    => MJA_PREFIX . 'plugin_options',
			'title' => __( 'Theme Options', "buddypress-advertising" ),
			'icon'  => 'fas fa-bars',
			// 'fields' => []
		] );

		CSF::createSection( $prefix, [
			'parent'      => MJA_PREFIX . 'plugin_options',
			'title'       => __( 'Theme Settings', "buddypress-advertising" ),
			'description' => 'Customize your Advertisments from here. Simple yet flexible :) ! <br/> To save changes press <code>Ctrl + S</code>',
			'fields'      => [
				[
					'type'          => 'submessage',
					'style'         => 'info',
					'content'       => 	sprintf(
						'%s <br /> %s <code>[ads-form]</code> %s & <code>[ads-form]</code> %s',
						__( 'To get best performance we strongly recommends "GetPaid" plugin.', "buddypress-advertising" ),
						__( 'Use Shortcode', "buddypress-advertising" ),
						__( 'on Ads Registration page,', "buddypress-advertising" ),
						__( 'on Ads listing page.', "buddypress-advertising" )
					),
				],
				[
					'type'    => 'subheading',
          'content' => '<a class="link button" target="_blank" href="' . admin_url( 'edit.php?post_type=' . MJA_ADS_NAME, '' ) . '">' . __( 'See all Ads.', "buddypress-advertising" ) . '</a>
					<span style="margin: 0 5px 0 5px;">' . __( 'OR', "buddypress-advertising" ) . '<span>
					<a class="link button" target="_blank" href="' . admin_url( 'post-new.php?post_type=' . MJA_ADS_NAME, '' ) . '">' . __( 'Create a new One', "buddypress-advertising" ) . '</a>.',
				],
				[
					'id'     => 'prices',
					'type'   => 'fieldset',
					'title'  => false,
					'fields' => [
						[
							'id'     => 'currency',
							'type'   => 'select',
							'title'  => __( 'Choose Currency', "buddypress-advertising" ),
							'default'			=> 'dollars',
							'options'     => MJA_ADS_CURRENCY
						],
						[
							'id'    => 'activity_1_week',
							'title' => __( 'Only activity feed & One week', "buddypress-advertising" ),
							'default' => '0',
							'type'        => 'number',
						],
						[
							'id'    => 'activity_2_week',
							'title' => __( 'Only activity feed & Two weeks', "buddypress-advertising" ),
							'default' => '0',
							'type'        => 'number',
						],
						[
							'id'    => 'activity_1_month',
							'title' => __( 'Only activity feed & One month', "buddypress-advertising" ),
							'default' => '0',
							'type'        => 'number',
						],
						[
							'id'    => 'featured_1_week',
							'title' => __( 'activity and featured feed & duration One week', "buddypress-advertising" ),
							'default' => '0',
							'type'        => 'number',
						],
						[
							'id'    => 'featured_2_week',
							'title' => __( 'activity and featured feed & duration Two weeks', "buddypress-advertising" ),
							'default' => '0',
							'type'        => 'number',
						],
						[
							'id'    => 'featured_1_month',
							'title' => __( 'activity and featured feed & duration One month', "buddypress-advertising" ),
							'default' => '0',
							'type'        => 'number',
						],
						[
							'id'    => 'css_issues',
							'title' => false,
							'type'        => 'content',
							'content' => '
							<style>
							.csf-field-number .csf--wrap {width: 100%;}
							.csf-footer, .csf-header {
								border: 1px solid #333;
								border-bottom-color: #ccc;
								display: none;
							}
							.csf-header .csf-header-inner {
									background: transparent;
									color: #333;
							}
							.csf-header .csf-header-inner h1 {
								display: none;
								color: #333;
								font-weight: 600;
								font-size: 30px;
							}
							.csf-header .csf-search input[name=csf-search] {
									color: #333;
									background: transparent;
									border: 1px solid #333;
							}</style>
							'
						],
						
						
					],
				],
				[
					'id'     => 'ads',
					'type'   => 'fieldset',
					'title'  => false,
					'fields' => [
						[
							'id'     => 'loop',
							'type'   => 'text',
							'title'  => __( 'Ads. Position', "buddypress-advertising" ),
							'desc' => __( 'Ad will appere after ( How many? ) post except loop.', "buddypress-advertising" ),
							'default'			=> '3',
						],
						[
							'id'      => 'avater',
							'type'    => 'media',
							'title'   => __( 'Fake Avater', "buddypress-advertising" ),
							'desc' => __( 'This user icon will be loaded as Ad author.', "buddypress-advertising" ),
							'library' => 'image',
							// 'default'			=> get_home_url( '/wp-content/uploads/2022/03/avater.jpg', null ),
							'preview' => true
						],
						[
							'id'      => 'author',
							'type'    => 'text',
							'title'   => __( 'Author name', "buddypress-advertising" ),
							'desc' => __( 'This Author name will be shown as Ad author.', "buddypress-advertising" ),
							'default'			=> 'ADSGRAM'
						],
					]
				],
				
				// Accordion Starts
				[
					'id'         => 'ads-setting',
					'type'       => 'accordion',
					'title'      => _x( 'Ads Setting', 'Ads. front end Settings', "buddypress-advertising" ),
					'accordions' => [
						[
							'title'  => _x( 'Overaly effect', 'Overaly Background Settings', "buddypress-advertising" ),
							'fields' => [
								// [
								// 	'id'    => 'overaly',
								// 	'type'  => 'switcher',
								// 	'title' => _x( 'Show Overaly', 'Show Overaly', "buddypress-advertising" ),
								// 	'default' => true
								// ],
								[
									'id'                    => 'overaly-bg',
									'type'                  => 'background',
									'title'                 => _x( 'Overaly', 'Overaly background', "buddypress-advertising" ),
									'background_color'      => true,
									'background_gradient'   => true,
									'background_image'      => false,
									'default'               => [
										'background-color'              => '#eaee44',
										'background-gradient-color'     => '#33d0ff',
										'background-gradient-direction' => '120deg'
									]
								]
							]
						],
						[
							'title'  => _x( 'Video Ads', 'Video Ads front end Settings', "buddypress-advertising" ),
							'fields' => [
								[
									'id'    => 'controls',
									'type'  => 'switcher',
									'title' => _x( 'Show Controls', 'Show video controls so user can stop or start it in fron end.', "buddypress-advertising" ),
									'default' => false
								],
								[
									'id'    => 'autoplay',
									'type'  => 'switcher',
									'title' => _x( 'Enable Autoplay', 'Enable autoplay so video start without users permission?', "buddypress-advertising" ),
									'default' => true
								],
								[
									'id'    => 'sound',
									'type'  => 'switcher',
									'title' => _x( 'Sound', 'Enable Video Sound', "buddypress-advertising" ),
									'default' => true
								],
								[
									'id'    => 'loop',
									'type'  => 'switcher',
									'title' => _x( 'Enable Loop', 'Enable autoplay so video start autometically after every ending.', "buddypress-advertising" ),
									'default' => false
								],
								[
									'id'    => 'time',
									'type'  => 'slider',
									'title' => _x( 'Video Time', 'How many times will be played video Ads?', "buddypress-advertising" ),
									'unit'    => 'sec',
									'default' => 30,
									'min'      => 5,
									'max'      => 120,
									// 'subtitle' => 'Min: 1 | Max: 10 | Step: 0.1 | Default: 5.5',
									// 'unit'     => 'px',
									// 'step'     => 0.1,
								],
							]
						],

						
						[
							'title'  => _x( 'Image Ads', 'Image Ads front end Settings', "buddypress-advertising" ),
							'fields' => [
							]
						],
					],
				],
		
				
			]
		] );
	}

	public function enqueue_styles() {
		$screen = get_current_screen();
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Ads_Rotator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Ads_Rotator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset( $_GET['page'] ) && ( 'buddypress-ads-rotator-settings' === $_GET['page'] ) || isset( $screen->post_type ) && ( 'wb-ads' === $screen->post_type ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-ads-rotator-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wb_ads_rotator-selectize', plugin_dir_url( __FILE__ ) . 'css/selectize.css', array(), $this->version, 'all' );
		}
	}

	public function enqueue_scripts() {
		$screen = get_current_screen();
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Ads_Rotator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Ads_Rotator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( isset( $_GET['page'] ) && ( 'buddypress-ads-rotator-settings' === $_GET['page'] ) || isset( $screen->post_type ) && ( 'wb-ads' === $screen->post_type ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-ads-rotator-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name,
				'ajax',
				array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'ajax-nonce' ),
				)
			);
			wp_enqueue_script( 'wb_ads_rotator-selectize-min', plugin_dir_url( __FILE__ ) . 'js/selectize.min.js', array( 'jquery' ), $this->version, false );
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			wp_localize_script( 'jquery', 'cm_settings', $cm_settings );

			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
		}
	}

	public function wb_ads_rotator_add_submenu_page_admin_settings() {
		if ( class_exists( 'BuddyPress' ) ) {
			if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
				// add_menu_page( esc_html__( 'WB Plugins', 'buddypress-ads-rotator' ), esc_html__( 'WB Plugins', 'buddypress-ads-rotator' ), 'manage_options', 'wbcomplugins', array( $this, 'buddypress_ads_rotator_admin_options_page' ), 'dashicons-lightbulb', 59 );
				// add_submenu_page( 'wbcomplugins', esc_html__( 'Welcome', 'buddypress-ads-rotator' ), esc_html__( 'Welcome', 'buddypress-ads-rotator' ), 'manage_options', 'wbcomplugins' );

			}
			// add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Ads', 'buddypress-ads-rotator' ), esc_html__( 'BuddyPress Ads', 'buddypress-ads-rotator' ), 'manage_options', 'buddypress-ads-rotator-settings', array( $this, 'buddypress_ads_rotator_admin_options_page' ) );
		}
	}

	public function buddypress_ads_rotator_admin_options_page() {
		global $allowedposttags;
		$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'buddypress-ads-rotator-welcome';
		?>
	<div class="wrap">
		<hr class="wp-header-end">
		<div class="wbcom-wrap">
			<div class="bupr-header">
			<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
				<?php esc_html_e( 'BuddyPress Ads Settings', 'buddypress-ads-rotator' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
			<?php
			// settings_errors();
			$this->wb_ads_rotator_plugin_settings_tabs();
			// settings_fields( $tab );
			// do_settings_sections( $tab );
			?>
			</div>
		</div>
	</div>
			<?php
	}

	public function wb_ads_rotator_plugin_settings_tabs() {
		$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'buddypress-ads-rotator-welcome';
		// xprofile setup tab.
		echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<li><a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=buddypress-ads-rotator-settings' . '&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a></li>';
		}
		echo '</div></ul></div>';
	}

	public function wb_ads_rotator_init_plugin_settings() {

		$this->plugin_settings_tabs['buddypress-ads-rotator-welcome'] = esc_html__( 'Welcome', 'buddypress-ads-rotator' );
		register_setting( 'wb_ads_rotator_admin_welcome_options', 'wb_ads_rotator_admin_welcome_options' );
		// add_settings_section( 'buddypress-ads-rotator-welcome', ' ', array( $this, 'wb_ads_rotator_admin_welcome_content' ), 'buddypress-ads-rotator-welcome' );

		$this->plugin_settings_tabs['buddypress-ads-rotator-faq'] = esc_html__( 'FAQ', 'buddypress-ads-rotator' );
		register_setting( 'buddypress-ads-rotator_admin_faq_options', 'buddypress-ads-rotator_admin_faq_option' );
		// add_settings_section( 'buddypress-ads-rotator-faq', ' ', array( $this, 'wb_ads_rotator_admin_faq_content' ), 'buddypress-ads-rotator-faq' );

	}

	public function wb_ads_rotator_admin_welcome_content() {
		include 'partials/buddypress-ads-rotator-admin-welcome-display.php.php';
	}

	public function wb_ads_rotator_admin_faq_content() {
		include 'partials/buddypress-ads-rotator-admin-faq-display.php';
	}

	public function wb_ads_rotator_add_cpt() {
		if ( class_exists( 'BuddyPress' ) ) {
			$labels = array(
				'name'               => _x( 'WB Ads', 'Post type general name', 'buddypress-ads-rotator' ),
				'singular_name'      => _x( 'WB Ads', 'Post type singular name', 'buddypress-ads-rotator' ),
				'menu_name'          => _x( 'WB Ads', 'Admin Menu text', 'buddypress-ads-rotator' ),
				'name_admin_bar'     => _x( 'WB Ads', 'Add New on Toolbar', 'buddypress-ads-rotator' ),
				'add_new'            => __( 'Add New WB Ads', 'buddypress-ads-rotator' ),
				'add_new_item'       => __( 'Add New WB Ads', 'buddypress-ads-rotator' ),
				'new_item'           => __( 'New WB Ads', 'buddypress-ads-rotator' ),
				'edit_item'          => __( 'Edit WB Ads', 'buddypress-ads-rotator' ),
				'view_item'          => __( 'View WB Ads', 'buddypress-ads-rotator' ),
				'all_items'          => __( 'All WB Ads', 'buddypress-ads-rotator' ),
				'search_items'       => __( 'Search WB Ads', 'buddypress-ads-rotator' ),
				'parent_item_colon'  => __( 'Parent WB Ads:', 'buddypress-ads-rotator' ),
				'not_found'          => __( 'No WB Ads found.', 'buddypress-ads-rotator' ),
				'not_found_in_trash' => __( 'No WB Ads found in Trash.', 'buddypress-ads-rotator' ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'wb-ads' ),
				'capability_type'    => 'page',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'show_in_rest'       => true,
				'menu_icon'          => __( 'dashicons-chart-line', 'buddypress-ads-rotator' ),
				'supports'           => array( 'title' ),
			);
			register_post_type( 'wb-ads', $args );
		}
	}

	public function wb_ads_rotator_add_metaboxes() {

		$post_types = array( 'wb-ads' );

		add_meta_box(
			'wb_ads_rotator_metaboxs',
			__( 'WB Ads Type', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_add_meta_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_parameter_metaboxs',
			__( 'Ads Parameter', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_parameter_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_layout_metaboxs',
			__( 'Layout / Output', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_layout_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_display_conditions_metaboxs',
			__( 'Display Conditions', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_display_conditions_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_to_whom_metaboxs',
			__( 'To Whom', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_to_whom_box' ),
			array( $post_types )
		);

		add_meta_box(
			'wb_ads_rotator_shortcode',
			__( 'Shortcode', 'buddypress-ads-rotator' ),
			array( $this, 'wb_ads_rotator_render_shortcode_box' ),
			array( $post_types ),
			'side',
		);

	}

	public function wb_ads_rotator_render_add_meta_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id               = $post->ID;
		$post_type             = get_post_type();
		$wb_ads_rotator_values = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Plain Text and Code', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_rotator_type" value="plain-text-and-code" <?php echo ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'plain-text-and-code' ) : 'checked'; ?>/>
				<?php esc_html_e( 'Any ad network, Amazon, customized AdSense codes, shortcodes, and code like JavaScript, HTML or PHP.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_rotator_type" value="rich-content" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'rich-content' ) : ''; ?>/>
				<?php esc_html_e( 'The full content editor from WordPress with all features like shortcodes, image upload or styling, but also simple text/html mode for scripts and code.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Image Ad', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="radio" name="wb_ads_rotator[ads_type]" class="wb-ads_types" id="wb_ads_rotator_type" value="image-ad" <?php ( isset( $wb_ads_rotator_values['ads_type'] ) ) ? checked( $wb_ads_rotator_values['ads_type'], 'image-ad' ) : ''; ?>/>
				<?php esc_html_e( 'Ads in various image formats.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	public function wb_ads_rotator_render_parameter_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id                   = $post->ID;
		$post_type                 = get_post_type();
		$wb_ads_rotator_values     = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_type       = isset( $wb_ads_rotator_values['ads_type'] ) ? $wb_ads_rotator_values['ads_type'] : 'plain-text-and-code';
		$wb_ads_rotator_width      = isset( $wb_ads_rotator_values['size']['width'] ) ? $wb_ads_rotator_values['size']['width'] : '';
		$wb_ads_rotator_height     = isset( $wb_ads_rotator_values['size']['height'] ) ? $wb_ads_rotator_values['size']['height'] : '';
		$wb_ads_rotator_plain_text = isset( $wb_ads_rotator_values['plain_text_and_code'] ) ? $wb_ads_rotator_values['plain_text_and_code'] : '';
		$wb_ads_rotator_image_url  = isset( $wb_ads_rotator_values['image_link'] ) ? $wb_ads_rotator_values['image_link'] : '';
		$wb_ads_rotator_image_id   = isset( $wb_ads_rotator_values['ads_image'] ) ? $wb_ads_rotator_values['ads_image'] : '';
		$wb_ads_rotator_font_size  = isset( $wb_ads_rotator_values['font_size'] ) ? $wb_ads_rotator_values['font_size'] : '#000000';
		$wb_ads_rotator_txt_color  = isset( $wb_ads_rotator_values['text_color'] ) ? $wb_ads_rotator_values['text_color'] : '#FFFFFF';
		$wb_ads_rotator_bg_color   = isset( $wb_ads_rotator_values['bg_color'] ) ? $wb_ads_rotator_values['bg_color'] : '';
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper plain-text-and-code" 
		<?php
		if ( 'plain-text-and-code' !== $wb_ads_rotator_type ) {
			echo 'style="display:none"';
		}
		?>
			>
				<div class="wb_ads_rotator-input">
					<strong>
					<?php esc_html_e( 'Insert plain text or code into this field.', 'buddypress-ads-rotator' ); ?>
					</strong>
				<?php echo '<textarea id="wb-ad-rotator-content-plain" name="wb_ads_rotator[plain_text_and_code]" >' . esc_textarea( $wb_ads_rotator_plain_text ) . '</textarea>'; ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-input">
				<?php
				$wb_ads_rotator_content = '';
				if ( isset( $wb_ads_rotator_values['rich_content'] ) ) {
					$wb_ads_rotator_content = $wb_ads_rotator_values['rich_content'];
				}
				$editor_id      = 'wb_ads_rotator_editor';
				$editor_setting = array(
					'textarea_name' => 'wb_ads_rotator[rich_content]',
					'textarea_rows' => get_option( 'default_post_edit_rows', 10 ),
				);
				wp_editor( $wb_ads_rotator_content, $editor_id, $editor_setting );
				?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper select-ads-image image-ad" 
				<?php
				if ( 'image-ad' !== $wb_ads_rotator_type ) {
					echo 'style="display:none"';
				}
				?>
			>
				<div class="wb_ads_rotator-label">
					<div id="wb-ads-preview-image">
					<?php
					$image_id = get_option( 'wb_ads_image_id' );
					if ( intval( $wb_ads_rotator_image_id ) > 0 ) {
						$image = wp_get_attachment_image( $wb_ads_rotator_image_id, 'medium', false, array( 'id' => 'wb-ads-preview-images' ) );
					} else {
						$image = '<img id="wb-ads-preview-images" src="' . esc_url( plugin_dir_url( __FILE__ ) . 'images/wb-ads-placeholder-image.jpg' ) . '" />';
					}
					echo wp_kses_post( $image );
					?>
					</div>
					<input type="hidden" name="wb_ads_rotator[ads_image]" id="wb_ads_image_id" value="<?php echo esc_attr( $wb_ads_rotator_image_id ); ?>" class="regular-text" />
				</div>
				<div class="wb_ads_rotator-input">
					<input type='button' class="button-primary" value="<?php esc_attr_e( 'Choose Image', 'buddypress-ads-rotator' ); ?>" id="wb_ads_rotator_select_image"/>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper select-ads-image image-ad" 
				<?php
				if ( 'image-ad' !== $wb_ads_rotator_type ) {
					echo 'style="display:none"';
				}
				?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'URL', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="text" name="wb_ads_rotator[image_link]" class="wb_ads_rotator_image" placeholder="http://" value="<?php echo esc_attr( $wb_ads_rotator_image_url ); ?>"/>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper select-ads-image image-ad" <?php echo 'image-ad' !== $wb_ads_rotator_type ? 'style="display:none"' : ''; ?>>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Size', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb-ads-rotator-ads-size">
					<div class="wb-bp-ads-size">
						<span>
						<?php esc_html_e( 'Width', 'buddypress-ads-rotator' ); ?>
						</span>
						<label>
							<input type="number" name="wb_ads_rotator[size][width]" value="<?php echo esc_attr( $wb_ads_rotator_width ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
					</div>
					<div class="wb-bp-ads-size">
						<span>
							<?php esc_html_e( 'Height', 'buddypress-ads-rotator' ); ?>
						</span>
						<label>
							<input type="number" name="wb_ads_rotator[size][height]" value="<?php echo esc_attr( $wb_ads_rotator_height ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
					</div>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper plain-text-and-code"
			<?php
			if ( 'plain-text-and-code' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Allow Shortcodes', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="checkbox" id="wb-ad-rotator-allow-shortcode" name="wb_ads_rotator[allow_shortcode]" value="yes"  <?php esc_attr( isset( $wb_ads_rotator_values['allow_shortcode'] ) ? checked( $wb_ads_rotator_values['allow_shortcode'], 'yes' ) : '' ); ?>/>
				<?php esc_html_e( 'Execute shortcodes', 'buddypress-ads-rotator' ); ?>
					<div class="wb-ad-rotator-error-message" id="wb-ad-rotator-allow-shortcode-warning"
					<?php
					if ( true != isset( $wb_ads_rotator_values['allow_shortcode'] ) ) {
						echo 'style="display:none"';
					}
					?>
						>
						<?php esc_html_e( 'No shortcode detected in your code.', 'buddypress-ads-rotator' ); ?><?php esc_html_e( 'Uncheck this checkbox for improved performance.', 'buddypress-ads-rotator' ); ?>
					</div>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content" 
				<?php
				if ( 'rich-content' !== $wb_ads_rotator_type ) {
					echo 'style="display:none"';
				}
				?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Font Size', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label>
						<input type="number" name="wb_ads_rotator[font_size]" value="<?php echo esc_attr( $wb_ads_rotator_font_size ); ?>"/>
					<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
					</label>
					<label>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content"  
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Background Color', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb_ads_bg_color">
					<label>
						<input type="color" name="wb_ads_rotator[bg_color]" value="<?php echo esc_attr( $wb_ads_rotator_bg_color ); ?>"/>
					</label>
					<label>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper rich-content" 
			<?php
			if ( 'rich-content' !== $wb_ads_rotator_type ) {
				echo 'style="display:none"';
			}
			?>
			>
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Rich Content Text Color', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input wb_ads_text_color">
					<label>
						<input type="color" name="wb_ads_rotator[text_color]" value="<?php echo esc_attr( $wb_ads_rotator_txt_color ); ?>"/>
					</label>
					<label>
				</div>
			</div>
			<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
			<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_layout_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id                        = $post->ID;
		$post_type                      = get_post_type();
		$wb_ads_rotator_values          = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_margin_top      = isset( $wb_ads_rotator_values['margin']['top'] ) ? $wb_ads_rotator_values['margin']['top'] : '10';
		$wb_ads_rotator_margin_right    = isset( $wb_ads_rotator_values['margin']['right'] ) ? $wb_ads_rotator_values['margin']['right'] : '20';
		$wb_ads_rotator_margin_bottom   = isset( $wb_ads_rotator_values['margin']['bottom'] ) ? $wb_ads_rotator_values['margin']['bottom'] : '30';
		$wb_ads_rotator_margin_left     = isset( $wb_ads_rotator_values['margin']['left'] ) ? $wb_ads_rotator_values['margin']['left'] : '40';
		$wb_ads_rotator_container_id    = isset( $wb_ads_rotator_values['container_id'] ) ? $wb_ads_rotator_values['container_id'] : 'banner_id';
		$wb_ads_rotator_container_class = isset( $wb_ads_rotator_values['container_classes'] ) ? $wb_ads_rotator_values['container_classes'] : 'banner_class';

		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-input">
				<?php esc_html_e( 'Everything connected to the ads layout and output.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Position', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<label title="left">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="left" <?php echo ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'left' ) : 'checked'; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-left.png' ); ?>" width="60" height="40">
					</label>
					<label title="center">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="center" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'center' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-center.png' ); ?>" width="60" height="40">
					</label>
					<label title="right">
						<input type="radio" name="wb_ads_rotator[ads_position]" value="right" <?php ( isset( $wb_ads_rotator_values['ads_position'] ) ) ? checked( $wb_ads_rotator_values['ads_position'], 'right' ) : ''; ?>/>
						<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/output-right.png' ); ?>" width="60" height="40">
					</label>
					<p>
						<input type="checkbox" name="wb_ads_rotator[clearfix]" value="yes" <?php ( isset( $wb_ads_rotator_values['clearfix'] ) ) ? checked( $wb_ads_rotator_values['clearfix'], 'yes' ) : ''; ?>/>
					<?php esc_html_e( 'Check this if you don’t want the following elements to float around the ad. (adds a clearfix)', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Margin', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<div class="wb_ads_margin">
						<label>
							<span><?php esc_html_e( 'Top', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][top]" value="<?php echo esc_attr( $wb_ads_rotator_margin_top ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
						<label>
							<span><?php esc_html_e( 'Right', 'buddypress-ads-rotator' ); ?></span>						
							<input type="number" name="wb_ads_rotator[margin][right]" value="<?php echo esc_attr( $wb_ads_rotator_margin_right ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
						<label>
							<span><?php esc_html_e( 'Bottom', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][bottom]" value="<?php echo esc_attr( $wb_ads_rotator_margin_bottom ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
						<label>
							<span><?php esc_html_e( 'Left', 'buddypress-ads-rotator' ); ?></span>
							<input type="number" name="wb_ads_rotator[margin][left]" value="<?php echo esc_attr( $wb_ads_rotator_margin_left ); ?>"/>
						<?php esc_html_e( 'px', 'buddypress-ads-rotator' ); ?>
						</label>
					</div>
					<p class="wb_ads_margin_disc">
				<?php echo esc_html_e( 'use this to add a margin around the ads', 'buddypress-ads-rotator' ); ?>
				</p>					
				</div>				
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Container ID', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="text" id="wb-ad-rotator-container-id" name="wb_ads_rotator[container_id]" value="<?php echo esc_attr( $wb_ads_rotator_container_id ); ?>"  />
				<?php esc_html_e( 'Specify the id of the ad container. Leave blank for random or no id.  An id-like string with only letters in lower case, numbers, and hyphens.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Container Classes', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="text" id="wb-ad-rotator-container-class" name="wb_ads_rotator[container_classes]" value="<?php echo esc_attr( $wb_ads_rotator_container_class ); ?>"  />
				<?php esc_html_e( 'Specify one or more classes for the container. Separate multiple classes with a space.', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_display_conditions_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id                        = $post->ID;
		$post_type                      = get_post_type();
		$wb_ads_rotator_values          = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		$wb_ads_rotator_enject_position = isset( $wb_ads_rotator_values['enject_after'] ) ? $wb_ads_rotator_values['enject_after'] : '3';
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Activity Type', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_activity_type" name="wb_ads_rotator[activity_type][]" multiple>
						<option value="sidewide_activity" <?php echo ( isset( $wb_ads_rotator_values['activity_type'] ) && in_array( 'sidewide_activity', $wb_ads_rotator_values['activity_type'], true ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Sitewide Activity ', 'buddypress-ads-rotator' ); ?></option>
						<option value="group_activity" <?php echo ( isset( $wb_ads_rotator_values['activity_type'] ) && in_array( 'group_activity', $wb_ads_rotator_values['activity_type'], true ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Group Activity', 'buddypress-ads-rotator' ); ?></option>
						<option value="members_activity" <?php echo ( isset( $wb_ads_rotator_values['activity_type'] ) && in_array( 'members_activity', $wb_ads_rotator_values['activity_type'], true ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Members Activity', 'buddypress-ads-rotator' ); ?></option>
					</select>
					<p>
					<?php esc_html_e( 'A page with this ad on it must match all of the following conditions.', 'buddypress-ads-rotator' ); ?>
					</p>
					<p>
					<?php esc_html_e( 'If you want to display the ad everywhere, dont do anything here.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Activity Positions', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_activity_position" name="wb_ads_rotator[activity_position]">
						<option value="bp_before_activity_entry" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_before_activity_entry' ); ?>><?php esc_html_e( 'before activity entry', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_activity_entry_content" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_activity_entry_content' ); ?>><?php esc_html_e( 'activity entry content', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_after_activity_entry" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_after_activity_entry' ); ?>><?php esc_html_e( 'after activity entry', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_before_activity_entry_comments" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_before_activity_entry_comments' ); ?>><?php esc_html_e( 'before activity entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_activity_entry_comments" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_activity_entry_comments' ); ?>><?php esc_html_e( 'activity entry comments', 'buddypress-ads-rotator' ); ?></option>
						<option value="bp_after_activity_entry_comments" <?php isset( $wb_ads_rotator_values['activity_position'] ) && selected( $wb_ads_rotator_values['activity_position'], 'bp_after_activity_entry_comments' ); ?>><?php esc_html_e( 'after activity entry comments', 'buddypress-ads-rotator' ); ?></option>
					</select>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Position', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="number" name="wb_ads_rotator[enject_after]" min="1" value="<?php echo esc_attr( $wb_ads_rotator_enject_position ); ?>">
				<?php esc_html_e( 'Enter the value to inject after per entry', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Repeat Position', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<input type="checkbox" name="wb_ads_rotator[repeat_position]" value="yes" <?php esc_attr( isset( $wb_ads_rotator_values['repeat_position'] ) ? checked( $wb_ads_rotator_values['repeat_position'], 'yes' ) : '' ); ?>>
				<?php esc_html_e( 'Enable this option if you want to repeat inject position', 'buddypress-ads-rotator' ); ?>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_to_whom_box( $post ) {
		global $post;
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wb_ads_rotator_render_add_meta_box', 'wb_ads_rotator_render_add_meta_box_nonce' );
		$post_id               = $post->ID;
		$post_type             = get_post_type();
		$wb_ads_rotator_values = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Logged-In Visitor', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_to_whom_device" name="wb_ads_rotator[to_whom][logged_in_visitor]">
						<option value="both_visitor"<?php isset( $wb_ads_rotator_values['to_whom']['logged_in_visitor'] ) && selected( $wb_ads_rotator_values['to_whom']['logged_in_visitor'], 'both_visitor' ); ?>><?php esc_html_e( 'Both Logged-in and Logged-out Visitor', 'buddypress-ads-rotator' ); ?></option>
						<option value="login_in"<?php isset( $wb_ads_rotator_values['to_whom']['logged_in_visitor'] ) && selected( $wb_ads_rotator_values['to_whom']['logged_in_visitor'], 'login_in' ); ?>><?php esc_html_e( 'Logged-in Visitor', 'buddypress-ads-rotator' ); ?></option>
						<option value="logout_out"<?php isset( $wb_ads_rotator_values['to_whom']['logged_in_visitor'] ) && selected( $wb_ads_rotator_values['to_whom']['logged_in_visitor'], 'logout_out' ); ?>><?php esc_html_e( 'Logged-out Visitor', 'buddypress-ads-rotator' ); ?></option>
					</select>				
					<p>
					<?php esc_html_e( 'Visitor conditions limit the number of users who can see your ad. There is no need to set visitor conditions if you want all users to see the ad.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-label">
				<?php esc_html_e( 'Device', 'buddypress-ads-rotator' ); ?>
				</div>
				<div class="wb_ads_rotator-input">
					<select id="wb_ads_rotator_to_whom_visitor" name="wb_ads_rotator[to_whom][device]">
						<option value="both-device"<?php isset( $wb_ads_rotator_values['to_whom']['device'] ) && selected( $wb_ads_rotator_values['to_whom']['device'], 'both_devices' ); ?>><?php esc_html_e( 'Both Mobile and Desktop Devices', 'buddypress-ads-rotator' ); ?></option>
						<option value="mobile"<?php isset( $wb_ads_rotator_values['to_whom']['device'] ) && selected( $wb_ads_rotator_values['to_whom']['device'], 'mobile' ); ?>><?php esc_html_e( 'Mobile(Including Tablets)', 'buddypress-ads-rotator' ); ?></option>
						<option value="desktop"<?php isset( $wb_ads_rotator_values['to_whom']['device'] ) && selected( $wb_ads_rotator_values['to_whom']['device'], 'desktop' ); ?>><?php esc_html_e( 'Desktop', 'buddypress-ads-rotator' ); ?></option>
					</select>				
					<p>
					<?php esc_html_e( 'Visitor conditions limit the number of users who can see your ad. There is no need to set visitor conditions if you want all users to see the ad.', 'buddypress-ads-rotator' ); ?>
					</p>
				</div>
			</div>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * WB Ads Metabox callback function.
	 *
	 * @param array $post Get a Post Object.
	 */
	public function wb_ads_rotator_render_shortcode_box( $post ) {
		global $post;
		$post_id               = $post->ID;
		$post_type             = get_post_type();
		$wb_ads_rotator_values = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
		?>
		<div class="wb_ads_rotator-panel">
		<?php do_action( 'wb_ads_rotator_options_before' ); ?>
			<div class="wb_ads_rotator-wrapper">
				<div class="wb_ads_rotator-input ads-copy" data-code="[ads-shortcode ads_id=<?php echo esc_attr( $post_id ); ?>]">
					<code><?php esc_html_e( '[ads-shortcode ads_id=' . esc_attr( $post_id ) . ']', 'buddypress-ads-rotator' ); ?></code>		
				</div>
				<span class="ads-shortcode-text shortcode-text-hide"><?php echo esc_attr__( 'Shortcode Copied!', 'buddypress-ads-rotator' ); ?></span>		
			</div>
			<p>
			<?php esc_html_e( 'Copy and Paste this shortcode to display this ad any where.', 'buddypress-ads-rotator' ); ?>
			</p>
		<?php do_action( 'wb_ads_rotator_options_after' ); ?>
		</div>
		<?php
	}

	/**
	 * Saved the post meta box value.
	 *
	 * @param int $post_id Get a Ads id.
	 */
	public function wb_ads_rotator_save_post_meta( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['wb_ads_rotator_render_add_meta_box_nonce'] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wb_ads_rotator_render_add_meta_box_nonce'] ) ), 'wb_ads_rotator_render_add_meta_box' ) ) {
			return $post_id;
		}
		// Bail if we're doing an auto save .
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
				// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( ! empty( $_POST['wb_ads_rotator'] ) ) {

			$ads_data = filter_input( INPUT_POST, 'wb_ads_rotator', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			update_post_meta( $post_id, 'wb_ads_rotator_values', $ads_data );
		}
		do_action( 'wb_ads_rotator_admin_save_options' );
	}

	/**
	 * Get a selected ads media.
	 */
	public function wb_ads_rotator_image() {
		// Check if our nonce is set.
		if ( ! isset( $_GET['nonce'] ) ) {
			return $post_id;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}
		if ( isset( $_GET['id'] ) ) {
			$image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'wb-ads-preview-images' ) );
			$data  = array(
				'image' => $image,
			);
			wp_send_json_success( $data );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Function Added the new column in data table columns.
	 *
	 * @param  array $columns Get the Columns.
	 */
	public function wb_ads_rotator_add_disable_admin_columns( $columns ) {
		unset( $columns['date'] );
		return array_merge(
			$columns,
			array(
				'ads-id'        => __( 'Ads ID', 'buddypress-ads-rotator' ),
				'ads-type'      => __( 'Ads Type', 'buddypress-ads-rotator' ),
				'disable-ads'   => __( 'Enable/Disable Ads', 'buddypress-ads-rotator' ),
				'ads-shortcode' => __( 'Shortcode', 'buddypress-ads-rotator' ),
				'date'          => __( 'Date', 'buddypress-ads-rotator' ),
			)
		);
	}

	/**
	 * Function Added the new column content.
	 *
	 * @param  string $column_key Contains the Columns Key.
	 * @param  int    $post_id Get a Ads ID.
	 */
	public function wb_ads_rotator_add_disable_column_content( $column_key, $post_id ) {

		switch ( $column_key ) {
			case 'ads-id':
				echo '<strong>' . esc_html( $post_id ) . '</strong>';
				break;
			case 'ads-type':
				$wb_ads_data = get_post_meta( $post_id, 'wb_ads_rotator_values', true );
				$wb_ads_type = isset( $wb_ads_data['ads_type'] ) ? $wb_ads_data['ads_type'] : '';
				if ( 'rich-content' === $wb_ads_type ) {
					echo '<strong>' . esc_html( 'Rich Content' ) . '</strong>';
				} elseif ( 'plain-text-and-code' === $wb_ads_type ) {
					echo '<strong>' . esc_html( 'Plain Text and Code' ) . '</strong>';
				} elseif ( 'image-ad' === $wb_ads_type ) {
					echo '<strong>' . esc_html( 'Image Ads' ) . '</strong>';
				}
				break;
			case 'disable-ads':
				$wb_ads_rotator_enable = get_post_meta( $post_id, 'wb_ads_enable', true );
				if ( 'enable' === $wb_ads_rotator_enable ) {
					echo '<label class="wb-ads-rotator-switch">
				<input type="checkbox" class="wb_ads_enable" data-ads-visible="disable" data-ads="' . esc_attr( $post_id ) . '" checked>
				<span class="wb-ads-rotator-slider wb-ads-rotator-round"></span>
				</label>';
				} else {
					echo '<label class="wb-ads-rotator-switch">
				<input type="checkbox" class="wb_ads_enable" data-ads-visible="enable" data-ads="' . esc_attr( $post_id ) . '">
				<span class="wb-ads-rotator-slider wb-ads-rotator-round"></span>
				</label>';
				}
				break;
			case 'ads-shortcode':
				echo '<div class="wb_ads_rotator-input wb-ads-shortcode-text shortcode-copy" data-shortcode="[ads-shortcode ads_id=' . esc_attr( $post_id ) . ']">
					<code> ' . esc_html__( '[ads-shortcode ads_id=' . $post_id . ']', 'buddypress-ads-rotator' ) . '</code>		
					</div><span class="ads-shortcode-text shortcode-text-hide">' . esc_attr__( 'Shortcode Copied!', 'buddypress-ads-rotator' ) . '</span>';

		}
	}

	/**
	 * This Function is updates the default option.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	public function wb_ads_rotator_update_default_value_on_ads_publish( $new_status, $old_status, $post ) {

		$wb_ads_id = $post->ID;
		$post_type = $post->post_type;
		if ( 'publish' === $new_status && 'wb-ads' === $post_type ) {
			update_post_meta( $wb_ads_id, 'wb_ads_enable', 'enable' );
		}
	}
	/**
	 * This Function is handle the ajax callback.
	 */
	public function wb_ads_rotator_ajax_enable_callback() {
		// Check if our nonce is set.
		if ( ! isset( $_POST['nonce'] ) ) {
			return $post_id;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax-nonce' ) ) {
			die( 'Busted!' );
		}
		$action       = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';
		$action_value = isset( $_POST['visible'] ) ? sanitize_text_field( wp_unslash( $_POST['visible'] ) ) : '';
		$wb_ads_id    = isset( $_POST['datanotice'] ) ? sanitize_text_field( wp_unslash( $_POST['datanotice'] ) ) : '';
		if ( 'wb_ads_rotator_enable' === $action && 'enable' === $action_value ) {
			update_post_meta( $wb_ads_id, 'wb_ads_enable', 'enable' );
		} elseif ( 'wb_ads_rotator_enable' === $action && 'disable' === $action_value ) {
			update_post_meta( $wb_ads_id, 'wb_ads_enable', 'disable' );
		}
		die;
	}

};
new MJA_Admin( MJA_ADS_NAME, PLUGIN_NAME_VERSION );
