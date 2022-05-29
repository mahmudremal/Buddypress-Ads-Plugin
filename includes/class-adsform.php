<?php
class MJA_ADS_FORM {

	private $post = [];
	private $config = [];
	private $reactions;

	public function __construct() {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		$this->reactions   = new MJA_Reactions();

		// add_action( 'save_post', [ $this, 'save_post' ] );
		add_action( 'admin_post_mja_ads_form', [ $this, 'save_post' ] );
		add_action( 'admin_post_nopriv_mja_ads_form', [ $this, 'save_post' ] );
		// add_action( 'wp_ajax_mja_ads_video_thumb', [ $this, 'mja_ads_video_thumb_function' ] );
		// add_action( 'wp_ajax_nopriv_mja_ads_video_thumb', [ $this, 'mja_ads_video_thumb_function' ] );
		add_shortcode( 'ads-form', [ $this, 'ads_form_generator' ] );
		add_shortcode( 'ads-list', [ $this, 'ads_list_template' ] );
		add_filter( 'woocommerce_stripe_request_body', [ $this, 'add_application_fee' ], 20, 2 );
	}
	
	public function save_post() {
		if( ! isset( $_POST['mja_ads_form_nonce'] ) || ! wp_verify_nonce( $_POST['mja_ads_form_nonce'], 'mja_ads_form' ) ){wp_die( __( 'Invalid arguments supplied!', "buddypress-advertising" ) );}
		$post_id = ( isset( $_POST['is_edit'] ) && !empty( $_POST['ad_id'] ) ) ? $_POST['ad_id'] : wp_insert_post(
			[
				'post_title'  => isset( $_POST[ 'mjc_ads_inf_post_title' ] ) ? $_POST[ 'mjc_ads_inf_post_title' ] : '',
				'post_type'   => MJA_ADS_NAME,
				'post_status' => 'pending',
				'post_author' => wp_get_current_user()->ID
			],
			false,
			false
		);
		// $this->post = get_post( $post_id );
		$database = $this->custom_post_meta_boxes();
		foreach( $database as $data ) {
			$this->config = $data;
			foreach ( $this->config['fields'] as $field ) {
				switch ( $field['type'] ) {
					case 'checkbox':
						update_post_meta( $post_id, $field['id'], isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : '' );
						break;
					case 'editor':
						if ( isset( $_POST[ $field['id'] ] ) ) {
							$sanitized = wp_filter_post_kses( $_POST[ $field['id'] ] );
							update_post_meta( $post_id, $field['id'], $sanitized );
						}
						break;
					case 'email':
						if ( isset( $_POST[ $field['id'] ] ) ) {
							$sanitized = sanitize_email( $_POST[ $field['id'] ] );
							update_post_meta( $post_id, $field['id'], $sanitized );
						}
						break;
					case 'url':
						if ( isset( $_POST[ $field['id'] ] ) ) {
							$sanitized = esc_url_raw( $_POST[ $field['id'] ] );
							update_post_meta( $post_id, $field['id'], $sanitized );
						}
						break;
					default:
						if ( isset( $_POST[ $field['id'] ] ) ) {
							$sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
							update_post_meta( $post_id, $field['id'], $sanitized );
						}
				}
			}
		}
		if ( wp_get_referer() ){
			wp_safe_redirect( add_query_arg( [
				'ad_id' => $post_id,
				'created' => 1
			], wp_get_referer() ) );
		}else{
			wp_safe_redirect( get_home_url() );
		}
	}
	public function mja_ads_video_thumb_function( bool $initial_request = false ) {
		global $wpdb;$posttype=false;
		if ( ! $initial_request && ! check_ajax_referer( 'mja_ads_video_thumb_nonce', 'ajax_nonce', false ) ) {
			wp_send_json_error( __( 'Invalid security token sent.', "buddypress-advertising" ) );wp_die( '0', 400 );
		}
		$is_ajax_request = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest';
		$url =  isset( $_GET[ 'url' ] ) ? $_GET[ 'url' ] : ( isset( $_POST[ 'url' ] ) ? $_POST[ 'url' ] : '' );
		if( empty( $url ) ) {wp_send_json_error( __( 'Invalid request sent.', "buddypress-advertising" ) );wp_die( '0', 400 );}
		wp_send_json_success( mja_DetermineVideoUrlType( $url ) );
		if ( $is_ajax_request && ! $initial_request ) {
			wp_die();
		}
	}
	public function ads_form_generator( $atts = [] ) {
		wp_enqueue_script( 'ads-form-work', plugin_dir_url( __DIR__ ) . 'public/js/ads-form.js', ['jquery'], filemtime( plugin_dir_path( __DIR__ ) . 'public/js/ads-form.js' ), true );
		wp_localize_script( 'ads-form-work', 'adsInformation', [
			'adsCosts' => MJA_OPTIONS['prices']
		] );

		$is_edit='';
		if( isset( $_GET['created'] ) && !empty( $_GET['created'] ) ) {
			// Payment function
			ob_start();
			?>
			<p>Ads registered successfully</p>
			<h1>Here will go Payment field</h1>
			<?php
			return ob_get_clean();
		}
		if( isset( $_GET['ad_id'] ) && !empty( $_GET['ad_id'] ) ) {
			$this->post = get_post( $_GET['ad_id'] );
			$is_edit = sprintf( '<input type="hidden" name="is_edit" value="%s">', $this->post->ID );
		}
		// global $wp;$url = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
		$data = '';
		$args = shortcode_atts( [
			'type'	=> 'plaintext',
			'id'		=> false,
		], $atts );
		ob_start();
		?>
		<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
			<input type="hidden" name="action" value="mja_ads_form">
			<?php wp_nonce_field( 'mja_ads_form', 'mja_ads_form_nonce' );echo $is_edit; ?>
			<?php
			$fields = $this->custom_post_meta_boxes();
			foreach( $fields as $i => $field ) {
				// foreach( $field['fields'] as $j => $row ) {
				// 	if( $row['type'] == 'editor' ){unset( $row['media-buttons'] );}
				// 	$field['fields'][$j]=$row;
				// }
				
				$this->config = $field;
				echo '<div class="rwp-description">' . $this->config['description'] . '</div>';
				$this->fields_table();
			}
			?>
		</form>
		<style>
			
		</style>
		<?php
		return ob_get_clean();
	}
	public function custom_post_meta_boxes( $i = null ) {
		$arr=[];
		

		$MJA_ADS_CATEGORIES = MJA_ADS_CATEGORIES;
		$MJA_ADS_CATEGORIES = [ '" selected disabled="disabled' => __( 'Choose category', "buddypress-advertising" ) ] + $MJA_ADS_CATEGORIES;
		$MJA_ADS_DURATION = MJA_ADS_DURATION;
		unset( $MJA_ADS_DURATION['lifetime'] );
		$MJA_ADS_DURATION = [ '" selected disabled="disabled' => __( 'Choose duration', "buddypress-advertising" ) ] + $MJA_ADS_DURATION;
		$arr[] = [
			'title' => __( 'Ads. Informations', "plugin-name" ),
			'description' => '',
			'prefix' => 'mjc_ads_inf',
			'domain'=> "plugin-name",
			'class_name'=> 'mjc_ads_inf_type_meta_box',
			'post-type'=> [ MJA_ADS_NAME ],
			'context'=> 'advanced',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => '
				tr td:last-child {width: 80%;}tr th:first-child {vertical-align: middle;}.mjc_ads_inf_type_tr {height: 400px }.mjc_ads_inf_type_meta_box tr input[type=number], .mjc_ads_inf_type_meta_box tr select {width: 100%;}#mjc_ads_param_type_media_prev_image img {max-width: 400px;max-height: 300px;}td.mjc_ads_short_code.column-mjc_ads_short_code {user-select: all;}.mujah_meta_box.mjc_ads_param tr {display: none;}.mujah_meta_box.mjc_ads_inf_type_meta_box.plaintext tr[id^=mjc_ads_param_type_plain], .mujah_meta_box.mjc_ads_inf_type_meta_box.richtext tr[id^=mjc_ads_param_type_rich], .mujah_meta_box.mjc_ads_inf_type_meta_box.media tr[id^=mjc_ads_param_type_media], .mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_prev_tr], .mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_image_tr], .mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_img_tr], .mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_url_tr], .mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_width_tr], .mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_height_tr], .mujah_meta_box.mjc_ads_inf_type_meta_box.video tr[id=mjc_ads_param_type_media_video_tr] {display: table-row;}.mujah_meta_box.mjc_ads_inf_type_meta_box tr {display: none;}.mujah_meta_box.mjc_ads_inf_type_meta_box tr[id^=mjc_ads_inf_]{display: table-row;}#mjc_ads_inf_title_post_id{width: 100%;color: #333;font-weight: 600;}#poststuff #titlewrap input {}#titlediv #edit-slug-box {display: none;}h3.text-muted {color: #907373;}fieldset label span {visibility: hidden;}fieldset label {position: relative;display: block;width: 100%;}fieldset label input[type="radio"]:checked + span {position: absolute;display: inline-block;text-align: center;justify-content: center;margin: 0 0 0 20px;padding: 8px 15px;white-space: normal;color: white;background-color: rgb(65, 65, 65);border-radius: 4px;box-shadow: 0 0 10px rgba(34,34,34,0.2);clear: both;visibility: visible;transform: translateX(0px);transition: transform 200ms ease;}fieldset label input[type="radio"]:checked + span:before {position: absolute;content:"";top:4%;left:-1px;border-top: 8px solid transparent;border-right: 8px solid transparent;border-bottom: 8px solid rgba(34,34,34,0.9);transform: rotate(45deg);}input[type=color] {width: 100%;max-width: 400px;}.intl-tel-input input, input[type=url] {width: 100%;}.mujah_meta_box.mjc_ads_inf_type_meta_box #mjc_ads_submit_tr, .mujah_meta_box.mjc_ads_inf_type_meta_box #mjc_ads_price_tr {display: block;}@media only screen and (max-width: 600px) {fieldset label input[type="radio"]:checked + span{position: relative;background: none;color: #333;box-shadow: none;border: 0.5px solid #333;margin-top: 5px;}fieldset label input[type="radio"]:checked + span:before {top: 0;left: 10%;transform: translateY(-40%) rotate(135deg);}}
				.notice.is-dismissible {position: relative;background: #fff;border: 1px solid #c3c4c7;border-left-width: 4px;box-shadow: 0 1px 1px rgba(0,0,0,.04);margin: 5px 15px 2px;padding: 1px 12px;padding-right: 38px;border-left-color: #00a32a;}.notice.updated > p {margin: .5em 0;padding: 2px;}.notice .notice-dismiss {position: absolute;top: 0;right: 1px;border: none;margin: 0;padding: 9px;background: 0 0;color: #787c82;cursor: pointer;}
				td input[type=color] {position: relative;max-width: 200px;}
				td input[type=color]:before {content: "Select color";position: absolute;right: 0;top: 0;height: 100%;width: 80px;color: #fff;background: #333;font-size: 12px;line-height: 43px;text-align: center;border-radius: 0 5px 5px 0;transform: translateX(90%);}div#mjc_ads_param_type_media_prev_image {text-align: center;}input#mjc_ads_inf_post_title {width: 100%;}
			',
			'scripts' => [
				"
				jQuery(document).ready( function() {jQuery('#mjc_ads_inf_whatsapp').intlTelInput();});
				"
			],
			'fields'=> [
				[
					"type" => "div",
					'label'=> false,
					'default' => '',
					'id'=>'mjc_ads_inf_title_post_title',
					'content' => sprintf(
						'
						<!-- <h1 class="">%s</h1> -->
						%s',
						__( 'Create a new Ad.', "buddypress-advertising" ),
						isset( $_GET['created'] ) && !empty( $_GET['created'] ) ? sprintf( '
							<div class="updated notice is-dismissible">
								<p>
									%s
								</p>
								<button type="button" class="notice-dismiss" onclick="jQuery(this).parent().remove();">
									<i class="bb-icon-close-circle"></i>
								</button>
							</div>',
							__( 'Ads Updated.', "buddypress-advertising" )
						) : ''
					)
				],
				[
					"type" => "hidden",
					'label'=> false, // __( 'Ads. title', "buddypress-advertising" ),
					'default' => sprintf( '%s', $this->post->ID ),
					'id'=>'mjc_ads_inf_title_post_id',
					'attr' => [
						'readonly' => 'readonly',
						'disabled' => 'disabled'
					]
				],
				[
					"type" => "text",
					'label'=> __( 'Ads. title', "buddypress-advertising" ),
					'default' => '',
					'placeholder' => __( 'Enter ad title here', "buddypress-advertising" ),
					'id'=>'mjc_ads_inf_post_title',
					'attr' => [
						'required' => 'required'
					]
				],
				[
					"type" => "radio",
					'label'=> __( 'Ads. Type', "plugin-name" ),
					'default'=> 'richtext',
					'id'=>'mjc_ads_inf_type',
					"options" => [
						// 'plaintext' => __( '<b>Plain Text and Code: </b>Any ad network, Amazon, customized AdSense codes, shortcodes, and code like JavaScript, HTML or PHP.', "plugin-name" ),
						'richtext' => [
							'title' => __( 'Rich Content', "plugin-name" ),
							'desc' => __( 'The full content editor from WordPress with all features like shortcodes, <br/>image upload or styling, but also simple text/html mode for scripts and code.', "plugin-name" )
						],
						'image' => [
							'title' => __( 'Image Ad', "plugin-name" ),
							'desc' => __( 'Ads in various image formats.', "plugin-name" )
						],
						'video' => [
							'title' => __( 'Video Ad', "plugin-name" ),
							'desc' => __( 'Ads in various video formats.', "plugin-name" )
						],
					],
					'attr' => ['required' => 'required']
				],
				
				
				[
					"type" => "editor",
					'label'=> __( 'Insert plain text or code into this field.', "plugin-name" ),
					'default'=> __( '', "plugin-name" ),
					'id'=>'mjc_ads_param_type_rich_text',
					"rows" => "8",
					"wpautop" => "1",
					"teeny" => "1",
				],
				[
					'type'=>'color',
					'label'=>__( 'Rich Content Background Color', "plugin-name" ),
					'description' => '',
					'default'=> '#0000ff',
					'id'=>'mjc_ads_param_type_rich_bgcolor',
					'color-picker' => ''
				],
				[
					'type'=>'color',
					'label'=>__( 'Rich Content Text Color', "plugin-name" ),
					'description' => '',
					'default'=>'#ffffff',
					'id'=>'mjc_ads_param_type_rich_txtcolor',
					'color-picker' => ''
				],
				[
					"type" => "url",
					"label" => __( 'Video link', "plugin-name" ),
					'placeholder'=> __( 'Inter your video Link here', "plugin-name" ),
					"default" => "",
					"id" => "mjc_ads_param_type_media_video",
					"prev" => "video"
				],
				[
					'type' => 'div',
					'label' => false,
					'id' => 'mjc_ads_param_type_media_prev',
					'class' => '',
					'default' => 'https://place-hold.it/400x350',
					'content' => ''
				],
				[
					"type" => "hidden",
					"label" => false,
					'placeholder'=>__( 'Image Link', "plugin-name" ),
					"id" => "mjc_ads_param_type_media_image"
				],
				[
					'type'=> 'button',
					'label'=>__( 'Select Media', "plugin-name" ),
					'description' => '',
					'return' => 'url',
					'btn-title' => 'Choose Images',
					'default'=>'',
					'id'=>'mjc_ads_param_type_media_img',
					'attr' => ['accept' => 'image/*']
				],
				[
					"type" => "url",
					"label" => __( 'URL Link', "plugin-name" ),
					"default" => "",
					"id" => "mjc_ads_param_type_media_url",
					'description' => __( 'Insert a URL link where visitors redirected to on click.', "plugin-name" )
				],
				[
					"type" => "select",
					'label'=> __( 'Ads. Category', "plugin-name" ),
					'default'=> 'lifetime',
					'id'=>'mjc_ads_inf_category',
					"options" => $MJA_ADS_CATEGORIES,
					'attr' => ['required' => 'required']
				],
				[
					"type" => "select",
					'label'=> __( 'Ads. Duration', "plugin-name" ),
					'default'=> 'lifetime',
					'id'=>'mjc_ads_inf_duration',
					"options" => $MJA_ADS_DURATION,
					'attr' => ['required' => 'required']
				],
				[
					"type" => "radio",
					'label'=> __( 'Display Location', "plugin-name" ),
					'default'=> 'featured',
					'id'=>'mjc_ads_inf_location',
					"options" => [
						'activity' => __( 'Activity Feed Only', "plugin-name" ),
						'featured' => __( 'Activity Feed & Featured Ads Page', "plugin-name" ),
					],
					'attr' => ['required' => 'required']
				],
				[
					"type" => "phone",
					'label'=> __( 'WhatsApp Business Number:', "plugin-name" ),
					'default'=> false,
					'id'=>'mjc_ads_inf_whatsapp',
					'placeholder' => __( 'e.g +2348167534572', "plugin-name" ),
					'description' => __( 'Input your whats app number that can be used on runtime ads. for stablishing communications.', "plugin-name" ),
					'attr' => ['required' => 'required']
				],
				[
					"type" => "hidden",
					'label'=> false,
					'default'=> 'enabled',
					'id'=>'mjc_ads_inf_enable'
				],
				[
					"type" => "div",
					'label'=> __( 'Estimated Price', "buddypress-advertising" ),
					'default'=> 0.00,
					'id'=> 'mjc_ads_price',
					'content' => '
					<div class="price_table">
						<span class="prefix"></span>
						<span class="price">0.00</span>
						<span class="suffix"></span>
					</div>'
				],
				[
					"type" => "div",
					'label'=> false,
					'default'=> 'submit',
					'id'=>'mjc_ads_submit',
					'content' => '
					<div class="align-right">
						<input class="button" type="submit" value="submit" />
					</div>'
				]
			]
		];
	
		return ($i == null) ? $arr : ( isset( $arr[($i-1)] ) ? $arr[($i-1)] : [] );
	}


	public function ads_list_template( $atts = [] ) {
		$argv = [
      'position' => 'before_activity_entry',
      'is_fullwidth' => false,
      'total_active_ads' => 20,
      'activity_per_page' => 20
    ];
		$post = []; $settings = MJA_OPTIONS;
		$is_edit='';
		$data = '';
		$ad_posts = get_posts( [
			'post_type'  => MJA_ADS_NAME,
			'post_status' => isset( $_GET['status'] ) && in_array( $_GET['status'], [ 'active', 'publish', 'pending', 'trash', 'rejected', 'paused' ] ) ? [$_GET['status']] : ['active'],
			'numberposts' => -1,
			'paged' => 1,
			'orderby'          => 'ID',
			'order'            => 'DESC',
		] );
		ob_start();
		?>
		<div id="buddypress" class="buddypress-wrap bp-dir-hori-nav">
		<div class="screen-content">
		<div  class="acti vity" data-bp-list="ac tivity" style="">
		<ul class="activity-list item-list bp-list">
			<?php
			foreach( $ad_posts as $post ) :
				$ads = [
					'type' => get_post_meta( $post->ID, 'mjc_ads_inf_type', true ),
					'href' => get_post_meta( $post->ID, 'mjc_ads_param_type_media_url', true ),
					'rich' => [
						'bg' => get_post_meta( $post->ID, 'mjc_ads_param_type_rich_bgcolor', true ),
						'color' => get_post_meta( $post->ID, 'mjc_ads_param_type_rich_txtcolor', true ),
						'text' => get_post_meta( $post->ID, 'mjc_ads_param_type_rich_text', true )
					],
					'media' => [
						'image' => get_post_meta( $post->ID, 'mjc_ads_param_type_media_image', true ),
						'video' => get_post_meta( $post->ID, 'mjc_ads_param_type_media_video', true )
					]
				];
				$is_fullwidth = ( isset( $argv['is_fullwidth'] ) && $argv['is_fullwidth'] ) ? true : false;
				$config = isset( $settings['ads'] ) ? $settings['ads'] : $settings;
				$config = wp_parse_args( $config, [
					'avater' => [
						'url' => plugin_dir_url( 'public/img/avater.png' ),
						'thumbnail' => plugin_dir_url( 'public/img/avater.png' ),
						'width' => 238,
						'height' => 250,
						'alt' => '',
						'title' => 'avater'
					]
				] );
				$is_liked = $this->reactions->is_liked( $post->ID );
				if( mujah_update_ads_seen( $post->ID ) ) {}// Ads Updated a count
				$post_author = get_user_by( 'id', $post->post_author );
				?>
				<li class="activity activity-item">
					<div class="bp-activity-head">
						<div class="activity-avatar item-avatar">
							<img
								src="<?php
								// https://developer.wordpress.org/reference/functions/get_avatar_url/
								echo esc_url( get_avatar_url( wp_get_current_user()->ID ), [
									'size' => '40',
									'default' => $config['avater']['url']
								] ); ?>"
								class="avatar user-1-avatar avatar-300 photo"
								width="300"
								height="300"
								alt="profile" />
						</div>

						<div class="activity-header">
							<p>
								<b style="color: #333;">
									<?php
					_e( 'AD BY ', "buddypress-advertising" );
					echo strtoupper( $settings['ads']['author'] );
					?>
								</b>
							</p>
							<p>
							<?php _e( strtoupper('Sponsored'), "buddypress-advertising" ); ?>
							</p>
						</div>
					</div>
					<div class="activity-content <?php echo $ads['type']; ?>-activity-wrap">
						<div class="activity-inner media media-<?php echo $ads['type']; ?>" style="<?php
							//  echo ( $ads['type'] == 'richtext' ) ? 'background: '. $ads['rich']['bg'] . ';color: '. $ads['rich']['color'] . ';' : '';
						?>">
							
							<?php
							switch( $ads['type'] ) {
								case 'plaintext' :
									if( get_post_meta( $post->ID, 'mjc_ads_param_type_plain_allowshortcode', true ) ) {
										do_shortcode( get_post_meta( $post->ID, 'mjc_ads_param_type_plain_allowshortcode', true ), true );
									}else {
										echo sprintf(
											'<div class="mja-ads mja-plain-text" style="">
												%s
											</div>',
											get_post_meta( $post->ID, 'mjc_ads_param_type_plain_text', true )
										);
									}
									break;
								case 'richtext' :
									echo sprintf(
										'<div class="mja-ads mja-rich-content" style="background-color: %s !important;color: %s !important;">
											%s
										</div>',
										$ads['rich']['bg'],
										$ads['rich']['color'],
										$ads['rich']['text']
									);
									break;
								case 'image' :
									echo sprintf(
										'<div class="mja_ads_image_ovaraly">
											<img src="%s" alt="%s" class="mja_ads_image" />
											%s
										</div>',
										$ads['media']['image'],
										_x( 'Ad banner', 'Ad banner', "buddypress-advertising" ),
										( ! empty( $ads['href'] ) ? sprintf(
											'<a href="javascript:void(0);" data-href="%s" class="" title="%s" onclick="return mja_ads_what_happend(this);" data-duration="120">%s</a>',
											$ads['href'],
										_x( 'Follow link in new tab', 'Follow link in new tab', "buddypress-advertising" ),
										_x( 'Click here', 'Click here', "buddypress-advertising" )
										) : '' ),
									);
									break;
								case 'video' :
									mja_parse_video_content( $ads, $settings );
									break;
								default :
									_e( 'Ads. can not loaded properly', "buddypress-advertising" );
									break;
							};
							?>
						<div class="activity-state <?php echo ( $this->reactions->has_like( $post->ID ) ) ? 'has-likes' : ''; ?>">
							<a href="javascript:void(0);" class="activity-state-likes">
								<span class="like-text hint--bottom hint--medium hint--multiline activity-like-<?php echo $post->ID; ?>" data-hint="" ><?php echo $this->reactions->get_liked( $post->ID ); ?></span>
							</a>
						</div>
					</div>

					<div class="bp-generic-meta activity-meta action">
						<?php if( is_user_logged_in() ){ ?>
						<div class="generic-button">
							<a
								href="javascript:void(0);"
								class="button <?php echo ( ! $is_liked ) ? 'fav' : 'unfav'; ?> bp-secon dary-action"
								aria-pressed="false" onclick="likeAdsPost( this, <?php echo $post->ID; ?>)"
								><span class="bp-screen-reader-text"><?php echo ( ! $is_liked ) ? _x( 'Like', 'Like', "buddypress-advertising" ) : _x( 'Unlike', 'Unlike', "buddypress-advertising" ); ?></span>
								<span class="like-count"><?php echo ( ! $is_liked ) ? _x( 'Like', 'Like', "buddypress-advertising" ) : _x( 'Unlike', 'Unlike', "buddypress-advertising" ); ?></span></a
							>
						</div>
						<?php } ?>
						<?php if( is_user_logged_in() && get_post_meta( $post->ID , 'mjc_ads_inf_location' , true ) == 'featured' ){
						// https://wa.me/8801814118328/?text=urlencodedtext
						// https://web.whatsapp.com/send?phone=8801814118328&text=urlencodedtext&app_absent=0
						// https://api.whatsapp.com/send/?phone=8801814118328&text=urlencodedtext&app_absent=0
						?>
						<div class="generic-button">
							<a class="button acomm ent-reply bp-whatsapp bp-primary-action" aria-expanded="false" href="<?php echo wp_is_mobile() ? 'https://wa.me/' . preg_replace('/[^\dxX]/', '', get_post_meta( $post->ID , 'mjc_ads_inf_whatsapp' , true )) . '?text=' : 'https://web.whatsapp.com/send?phone=' . preg_replace('/[^\dxX]/', '', get_post_meta( $post->ID , 'mjc_ads_inf_whatsapp' , true )) . '&text='; echo urlencode( sprintf( 'Hi %s I saw your ad on %s and I will like to know more about this.', $post_author->user_login, site_url() ) ); ?>" target="_blank" role="button">
							<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 48 48" width="22px" height="22px" fill-rule="evenodd" clip-rule="evenodd" style="position: relative;top: 6px;"><path fill="#fff" d="M4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5 c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98 c-0.001,0,0,0,0,0h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303z"/><path fill="#fff" d="M4.868,43.803c-0.132,0-0.26-0.052-0.355-0.148c-0.125-0.127-0.174-0.312-0.127-0.483l2.639-9.636 C5.389,30.63,4.526,27.33,4.528,23.98C4.532,13.238,13.273,4.5,24.014,4.5c5.21,0.002,10.105,2.031,13.784,5.713 c3.679,3.683,5.704,8.577,5.702,13.781c-0.004,10.741-8.746,19.48-19.486,19.48c-3.189-0.001-6.344-0.788-9.144-2.277l-9.875,2.589 C4.953,43.798,4.911,43.803,4.868,43.803z"/><path fill="#c1f5ea" d="M24.014,42.974L24.014,42.974L24.014,42.974 M24.014,42.974L24.014,42.974L24.014,42.974 M24.014,4 L24.014,4C12.998,4,4.032,12.962,4.027,23.979c-0.001,3.367,0.849,6.685,2.461,9.622L3.903,43.04 c-0.094,0.345,0.002,0.713,0.254,0.967c0.19,0.192,0.447,0.297,0.711,0.297c0.085,0,0.17-0.011,0.254-0.033l9.687-2.54 c2.828,1.468,5.998,2.243,9.197,2.244c11.024,0,19.99-8.963,19.995-19.98c0.002-5.339-2.075-10.359-5.848-14.135 C34.378,6.083,29.357,4.002,24.014,4L24.014,4z"/><path fill="#00b569" d="M35.176,12.832c-2.98-2.982-6.941-4.625-11.157-4.626c-8.704,0-15.783,7.076-15.787,15.774 c-0.001,2.981,0.833,5.883,2.413,8.396l0.376,0.597l-1.595,5.821l5.973-1.566l0.577,0.342c2.422,1.438,5.2,2.198,8.032,2.199h0.006 c8.698,0,15.777-7.077,15.78-15.776C39.795,19.778,38.156,15.814,35.176,12.832z"/><path fill="#c1f5ea" fill-rule="evenodd" d="M19.268,16.045c-0.355-0.79-0.729-0.806-1.068-0.82 c-0.277-0.012-0.593-0.011-0.909-0.011s-0.83,0.119-1.265,0.594c-0.435,0.475-1.661,1.622-1.661,3.956s1.7,4.59,1.937,4.906 s3.282,5.259,8.104,7.161c4.007,1.58,4.823,1.266,5.693,1.187c0.87-0.079,2.807-1.147,3.202-2.255 c0.395-1.108,0.395-2.057,0.277-2.255c-0.119-0.198-0.435-0.316-0.909-0.554s-2.807-1.385-3.242-1.543 c-0.435-0.158-0.751-0.237-1.068,0.238c-0.316,0.474-1.225,1.543-1.502,1.859c-0.277,0.317-0.554,0.357-1.028,0.119 c-0.474-0.238-2.002-0.738-3.815-2.354c-1.41-1.257-2.362-2.81-2.639-3.285c-0.277-0.474-0.03-0.731,0.208-0.968 c0.213-0.213,0.474-0.554,0.712-0.831c0.237-0.277,0.316-0.475,0.474-0.791c0.158-0.317,0.079-0.594-0.04-0.831 C20.612,19.329,19.69,16.983,19.268,16.045z" clip-rule="evenodd"/></svg>  
							<span class="bp-screen-reader-text"><?php _e( 'WhatsApp', "buddypress-advertising" ); ?></span>
								<span class="comment-count"><?php _e( 'WhatsApp', "buddypress-advertising" ); ?></span>
							</a>
						</div>
						<div class="generic-button">
							<a class="button ads_send-message bp-message bp-primary-action" aria-expanded="false" href="<?php echo esc_url( home_url( sprintf( 'members/%s/messages/compose/?r=%s&_wpnonce=%s', $post_author->user_login , $post_author->user_login, wp_create_nonce('_wpnonce') ), false ) );  ?>" data-target="_blank" role="button">
							<span class="bp-screen-reader-text"><?php _e( 'Message', "buddypress-advertising" ); ?></span>
								<span class="comment-count"><?php _e( 'Message', "buddypress-advertising" ); ?></span>
							</a>
						</div>
						<?php } ?>
					</div>

					<div class="activity-comments">
						
					</div>
				</li>
				<?php
			endforeach;
			?>
			</ul>
			</div>
			</div>
			</div>
			<?php
		return ob_get_clean();
	}
	protected function list_table( $ad_posts ) {
		?>
		<div class="table-responsive data-table table-striped">
			<table class="datatable">
				<tr>
					<th>Ad Title</th>
					<th>Ad ID</th>
					<th>Ad Type</th>
					<th>Category</th>
					<th>Duration</th>
					<th>Location</th>
					<th>Date</th>
				</tr>
					<?php
					foreach( $ad_posts as $i => $row ){
						?>
				<tr>
					<td><?php echo esc_html( $row->post_title ); ?></td>
					<td><?php echo esc_html( $row->ID ); ?></td>
					<td><?php echo esc_html( get_post_meta( $row->ID, 'mjc_ads_inf_type', true ) ); ?></td>
					<td><?php echo esc_html( MJA_ADS_CATEGORIES[get_post_meta( $row->ID, 'mjc_ads_inf_category', true )] ); ?></td>
					<td><?php echo esc_html( MJA_ADS_DURATION[get_post_meta( $row->ID, 'mjc_ads_inf_duration', true )] ); ?></td>
					<td><?php echo esc_html( get_post_meta( $row->ID, 'mjc_ads_inf_location', true ) ); ?></td>
					<td><?php echo esc_html( date( 'Y-M, d', strtotime( $row->post_date )) ); ?></td>
				</tr>
					<?php } ?>
			</table>
		</div>
		<?php
	}
	public function add_application_fee( $request, $api ) {
		
		//	Try to retrieve the order ID from the metadata
		
		$orderID = $request['metadata']['order_id'];
		$order = wc_get_order( $orderID );
		
		//	This filter is hit multiple times, so the order ID might not be available. If not, just return the request.
		
		if ( !$order ) {
			return $request;
		}
		
		// This is a custom filter that returns a fee based on the order total. 
		
		$applicationFee = 125;	//Change value with your fees, will charge $1.25 as application fee.
		
		if ( $applicationFee > 0 ) {
			$request['application_fee_amount'] = $applicationFee;
		}
		
		return $request;
	}
	public function create_memberplan_after_update_order($order){
    $order = wc_get_order($order);
    if ($order->data['status'] == 'processing') {
        //update user when status is changed to processing
    }
	}
	public function add_meta_boxes() {
		add_meta_box(
			sanitize_title( $this->config['title'] ),
			$this->config['title'],
			[ $this, 'add_meta_box_callback' ],
			$this->config['post-type'],
			$this->config['context'],
			$this->config['priority']
		);
	}
	public function process_cpts() {
		if ( !empty( $this->config['cpt'] ) ) {
			if ( empty( $this->config['post-type'] ) ) {
				$this->config['post-type'] = [];
			}
			$parts = explode( ',', $this->config['cpt'] );
			$parts = array_map( 'trim', $parts );
			$this->config['post-type'] = array_merge( $this->config['post-type'], $parts );
		}
	}
	public function admin_enqueue_scripts() {
		global $typenow;
		if ( in_array( $typenow, ( is_array($this->config['post-type'])?$this->config['post-type']:[]) ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}
	public function admin_head() {
		global $typenow;
		if ( in_array( $typenow, ( is_array($this->config['post-type'])?$this->config['post-type']:[]) ) ) {
			?>
			<script>
				jQuery.noConflict();
				(function($) {
					$(function() {
						$('body').on('click', '.rwp-media-toggle', function(e) {
							e.preventDefault();
							let button = $(this);
							let rwpMediaUploader = null;
							rwpMediaUploader = wp.media({
								title: button.data('modal-title'),
								button: {
									text: button.data('modal-button')
								},
								multiple: true
							}).on('select', function() {
								let attachment = rwpMediaUploader.state().get('selection').first().toJSON();
								button.prev().val(attachment[button.data('return')]);
							}).open();
						});
						$('.rwp-color-picker').wpColorPicker();
					});
				})(jQuery);
			</script>
			<?php
		}
	}


	public function add_meta_box_callback() {
		$this->process_cpts();
		echo '<div class="rwp-description">' . $this->config['description'] . '</div>';
		$this->fields_table();
	}
	public function process_metaboxes() {
		for( $i = 1; $i <= count( $this->custom_post_meta_boxes() ); $i++ ){
			add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes'.$i ] );
		}
	}
	public function add_meta_box( $args = false ) {
		if( $args ) {
			$arr = $this->custom_post_meta_boxes($args);
			add_meta_box(
				sanitize_title( $arr['title'] ),
				$arr['title'],
				[ $this, 'add_meta_box_callback' . $args ],
				$arr['post-type'],
				$arr['context'],
				$arr['priority']
			);
		}
	}
	public function add_meta_boxes1() {
		$this->add_meta_box(1);
	}
	public function add_meta_boxes2() {
		$this->add_meta_box(2);
	}
	public function add_meta_boxes3() {
		$this->add_meta_box(3);
	}
	public function add_meta_boxes4() {
		$this->add_meta_box(4);
	}
	public function add_meta_boxes5() {
		$this->add_meta_box(5);
	}
	public function add_meta_boxes6() {
		$this->add_meta_box(6);
	}
	public function add_meta_boxes7() {
		$this->add_meta_box(7);
	}
	public function add_meta_boxes8() {
		$this->add_meta_box(8);
	}
	public function add_meta_box_callback1( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(1);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback2( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(2);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback3( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(3);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback4( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(4);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback5( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(5);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback6( $post = [] ) {$this->post = $post;
		// $post_type             = get_post_type();
		// $post_value 					 = get_post_meta( $post_id, 'mjc_ads_short_code', true );
		$data = $this->custom_post_meta_boxes(6);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback7( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(7);$this->config = $data;$this->add_meta_box_callback();
	}
	public function add_meta_box_callback8( $post = [] ) {$this->post = $post;
		$data = $this->custom_post_meta_boxes(8);$this->config = $data;$this->add_meta_box_callback();
	}
	

	private function fields_table() {
		?><table class="form-table mujah_meta_box <?php echo $this->config['class_name']; ?>" role="presentation">
			<tbody><?php
				foreach ( $this->config['fields'] as $field ) {
					?><tr id="<?php echo $field['id'] . '_tr'; ?>">
						<?php if( $field['label'] ) { ?>
						<th scope="row"><?php $this->label( $field ); ?></th>
						<?php } ?>
						<td <?php echo ( ! $field['label'] ) ? 'colspan="2"' : ''; ?>><?php $this->field( $field ); ?></td>
					</tr><?php
				}
			?></tbody>
		</table><?php
		if( isset( $this->config['css'] ) && !empty( $this->config['css'] ) ) {
			?><style><?php echo $this->config['css']; ?></style>
			<?php
		}
		if( isset( $this->config['scripts'] ) && !empty( $this->config['scripts'] ) ) {
			foreach( $this->config['scripts'] as $script ) {
			?><script><?php echo $script; ?></script>
			<?php
			}
		}
	}
	private function label( $field ) {
		switch ( $field['type'] ) {
			case 'editor':
			case 'radio':
				echo '<div class="">' . $field['label'] . '</div>';
				break;
			case 'media':
				printf(
					'<label class="" for="%s_button">%s</label>',
					$field['id'], $field['label']
				);
				break;
			default:
				printf(
					'<label class="" for="%s">%s</label>',
					$field['id'], $field['label']
				);
		}
	}
	private function field( $field ) {
		switch ( $field['type'] ) {
			case 'checkbox':
				$this->checkbox( $field );
				break;
			case 'date':
			case 'month':
			case 'number':
			case 'range':
			case 'time':
			case 'week':
			case 'hidden':
				$this->input_minmax( $field );
				break;
			case 'phone':
				$this->phone( $field );
				break;
			case 'editor':
				$this->editor( $field );
				break;
			case 'media':
				$this->input( $field );
				$this->media_button( $field );
				break;
			case 'radio':
				$this->radio( $field );
				break;
			case 'select':
				$this->select( $field );
				break;
			case 'textarea':
				$this->textarea( $field );
				break;
			case 'div':
				$this->div( $field );
				break;
			case 'code':
				$this->code( $field );
				break;
			case 'button':
				$this->button( $field );
				break;
			default:
				$this->input( $field );
		}
	}
	private function checkbox( $field ) {
		printf(
			'<label class="rwp-checkbox-label"><input %s id="%s" name="%s" type="checkbox" %s> %s</label>',
			$this->checked( $field ),
			$field['id'], $field['id'],
			$this->attr( $field ),
			isset( $field['description'] ) ? $field['description'] : ''
		);
	}
	private function editor( $field ) {
		wp_editor( $this->value( $field ), $field['id'], [
			'wpautop' => isset( $field['wpautop'] ) ? true : false,
			'media_buttons' => isset( $field['media-buttons'] ) ? true : false,
			'textarea_name' => $field['id'],
			'textarea_rows' => isset( $field['rows'] ) ? isset( $field['rows'] ) : 20,
			'teeny' => ( isset( $field['teeny'] ) && $field['teeny'] ) ? $field['teeny'] : false
		] );
	}
	private function attr( $field ) {
		$attr='';
		if( isset( $field['attr'] ) ) {
			foreach($field['attr'] as $a => $v) {
				$attr .= $a . '="' . $v . '" ';
			}
		}
		return $attr;
	}
	private function input( $field ) {
		if ( $field['type'] === 'media' ) {
			$field['type'] = 'text';
		}
		if ( isset( $field['color-picker'] ) ) {
			$field['class'] = 'rwp-color-picker';
		}
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s" placeholder="%s" %s>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field ),
			isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			$this->attr( $field )
		);
		if( isset( $field['prev'] ) ) {
			switch( $field['prev'] ) {
				case 'video':
					$type = mja_DetermineVideoUrlType( $this->value( $field ) );
					printf(
						'<img class="video-preview_image" width="%s" height="%s" src="%s" alt="" data-loader="%s" />',
						'','',
						( ! empty( $type['poster'] ) ) ? $type['poster'] : '',
						plugin_dir_url( __DIR__ ) . 'public/img/loading.gif'
					);
					break;
				default: break;
			};
		}
	}
	private function input_minmax( $field ) {
		printf(
			'<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s" %s>',
			$field['id'],
			isset( $field['max'] ) ? "max='{$field['max']}'" : '',
			isset( $field['min'] ) ? "min='{$field['min']}'" : '',
			$field['id'],
			isset( $field['step'] ) ? "step='{$field['step']}'" : '',
			$field['type'],
			$this->value( $field ),
			$this->attr( $field )
		);
	}
	private function media_button( $field ) {
		printf(
			' <button class="button rwp-media-toggle" data-modal-button="%s" data-modal-title="%s" data-return="%s" id="%s_button" name="%s_button" type="button">%s</button>',
			isset( $field['modal-button'] ) ? $field['modal-button'] : __( 'Select this file', '"plugin-name"' ),
			isset( $field['modal-title'] ) ? $field['modal-title'] : __( 'Choose a file', '"plugin-name"' ),
			$field['return'],
			$field['id'], $field['id'],
			isset( $field['button-text'] ) ? $field['button-text'] : __( 'Upload', '"plugin-name"' )
		);
	}
	private function phone( $field ) {
		if ( $field['type'] === 'media' ) {
			$field['type'] = 'text';
		}
		if ( isset( $field['color-picker'] ) ) {
			$field['class'] = 'rwp-color-picker';
		}
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s" placeholder="%s" %s>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field ),
			isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			$this->attr( $field )
		);
	
	}
	private function radio( $field ) {
		printf(
			'<fieldset>
			<legend class="screen-reader-text">%s</legend>%s
			</fieldset>',
			$field['label'],
			$this->radio_options( $field )
		);
	}
	private function radio_checked( $field, $current ) {
		$value = $this->value( $field );
		if ( $value === $current ) {
			return 'checked';
		}
		return '';
	}
	private function radio_options( $field ) {
		$output = [];
		$options = is_array( $field['options'] ) ? $field['options'] : explode( "\r\n", $field['options'] );
		$i = 0;
		if( is_array( $field['options'] ) ) {
			foreach ( $options as $a => $option ) {
				$output[] = sprintf(
					'<label for="%s">
						<input id="%s" %s type="radio" name="%s" value="%s" %s>
						%s <span>%s</span>
					</label>',
					$field['id'] . '-' . $i,$field['id'] . '-' . $i,
					$this->radio_checked( $field, $a ),
					$field['id'], $a,
					$this->attr( $field ),
					is_array($option)?$option['title']:$a,
					is_array($option)?$option['desc']:$option
				);
				$i++;
			}
			return implode( '<br>', $output );
		}else{
			foreach ( $options as $option ) {
				$pair = explode( ':', $option );
				$pair = array_map( 'trim', $pair );
				$output[] = sprintf(
					'<label><input %s id="%s-%d" name="%s" type="radio" value="%s"> %s</label>',
					$this->radio_checked( $field, $pair[0] ),
					$field['id'], $i, $field['id'],
					$pair[0], $pair[1]
				);
				$i++;
			}
			return implode( '<br>', $output );
		}
	}
	private function select( $field ) {
		printf(
			'<select id="%s" name="%s" %s %s>%s</select>',
			$field['id'], $field['id'],
			isset( $field['multiple'] ) && ( $field['multiple'] ) ? 'multiple="multiple"' : '' ,
			$this->attr( $field ),
			$this->select_options( $field ),
		);
	}
	private function select_selected( $field, $current ) {
		$value = $this->value( $field );
		if ( $value === $current ) {
			return 'selected';
		}
		return '';
	}
	private function select_options( $field ) {
		$output = [];
		$options = is_array( $field['options'] ) ? $field['options'] : explode( "\r\n", $field['options'] );
		$i = 0;
		if( is_array( $field['options'] ) ) {
			foreach ( $options as $a => $option ) {
				$output[] = sprintf(
					'<option %s value="%s"> %s</option>',
					$this->select_selected( $field, $a ),
					$a, $option
				);
				$i++;
			}
			return implode( '<br>', $output );
		}else{
			foreach ( $options as $option ) {
				$pair = explode( ':', $option );
				$pair = array_map( 'trim', $pair );
				$output[] = sprintf(
					'<option %s value="%s"> %s</option>',
					$this->select_selected( $field, $pair[0] ),
					$pair[0], $pair[1]
				);
				$i++;
			}
			return implode( '<br>', $output );
		}
	}
	private function textarea( $field ) {
		printf(
			'<textarea class="regular-text" id="%s" name="%s" rows="%s" %s>%s</textarea>',
			$field['id'], $field['id'],
			isset( $field['rows'] ) ? $field['rows'] : 5,
			$this->attr( $field ),
			$this->value( $field )
		);
	}
	private function div( $field ) {
		if( $field['id'] == 'mjc_ads_param_type_media_prev' ) {
			printf(
				'<div id="%s_image" class="" style="">
					<img width="" height="" title="" alt="" src="%s" />
				</div>',
				$field['id'],
				$this->value( $field )
			);
		}else{
			printf(
				'%s',
				isset( $field['content'] ) ? $field['content'] : ''
			);
		}
	}
	private function code( $field ) {
		printf(
			'<code class="%s" id="%s" name="%s" data-code="%s">%s</code><p>%s</p>',

			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'],$field['id'],
			$this->value( $field ),
			$this->value( $field ),
			$field['description']
		);
	}
	private function button( $field ) {
		if( 1 == 1 ){
			printf(
				'<button class="btn button %s" id="%s" name="%s" type="%s" value="%s">%s</button>',
				isset( $field['class'] ) ? $field['class'] : '',
				$field['id'], $field['id'],
				$field['type'],
				$this->value( $field ),
				$field['label']
			);
		}else{
			printf(
				'<button class="btn button %s" id="%s" name="%s" type="%s" value="%s">%s</button>',
				isset( $field['class'] ) ? $field['class'] : '',
				$field['id'], $field['id'],
				$field['type'],
				$this->value( $field ),
				$field['label']
			);
		}
	}

	private function value( $field ) {
		if ( metadata_exists( 'post', $this->post->ID, $field['id'] ) ) {
			$value = get_post_meta( $this->post->ID, $field['id'], true );
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

	private function checked( $field ) {
		if ( metadata_exists( 'post', $this->post->ID, $field['id'] ) ) {
			$value = get_post_meta( $this->post->ID, $field['id'], true );
			if ( $value === 'on' ) {
				return 'checked';
			}
			return '';
		} else if ( isset( $field['checked'] ) ) {
			return 'checked';
		}
		return '';
	}


};
new MJA_ADS_FORM();
?>