<?php
// use WP_Query;
class ShortCodes {

	function __construct() {
		$this->setup_hooks();
	}

	protected function setup_hooks() {

		add_action( 'wp_ajax_nopriv_load_more', [ $this, 'ajax_script_post_ads' ] );
		add_action( 'wp_ajax_load_more', [ $this, 'ajax_script_post_ads' ] );
		add_shortcode( 'ads', [ $this, 'ads_script_load_more' ] );
	}
	public function ajax_script_post_ads( bool $initial_request = false ) {

		// if ( ! $initial_request && ! check_ajax_referer( 'loadmore_post_nonce', 'ajax_nonce', false ) ) {
		// 	wp_send_json_error( __( 'Invalid security token sent.', 'text-domain' ) );
		// 	wp_die( '0', 400 );
		// }

		// Check if it's an ajax call.
		$is_ajax_request = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
		                   strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest';
		/**
		 * Page number.
		 * If get_query_var( 'paged' ) is 2 or more, its a number pagination query.
		 * If $_POST['page'] has a value which means its a loadmore request, which will take precedence.
		 */
		$page_no = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
		$page_no = ! empty( $_POST['page'] ) ? filter_var( $_POST['page'], FILTER_VALIDATE_INT ) + 1 : $page_no;

		// Default Argument.
		$args = [
			'post_type'      => MJA_ADS_NAME,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'paged'          => $page_no,
		];

		$query = mja_get_wp_query( $args );

		if ( $query->have_posts() ):
			$data = '';
			// Loop Posts.
			while ( $query->have_posts() ): $query->the_post();
				$data .= get_post_meta( get_the_ID() , 'mjc_ads_inf_type' , true );

			endwhile;

			// Pagination for Google.
			if ( ! $is_ajax_request ) :
				$total_pages = $query->max_num_pages;
				// get_template_part( 'template_parts/common/pagination', null, [
				// 	'total_pages'  => $total_pages,
				// 	'current_page' => $page_no,
				// ] );
			endif;
		else:
			// Return response as zero, when no post found.
			wp_die( '0' );
		endif;

		wp_reset_postdata();

		/**
		 * Check if its an ajax call, and not initial request
		 *
		 * @see https://wordpress.stackexchange.com/questions/116759/why-does-wordpress-add-0-zero-to-an-ajax-response
		 */
		if ( $is_ajax_request && ! $initial_request ) {
			wp_die();
		}
		return $data;
	}

	public function ads_script_load_more( $atts = [] ) {
		$data = '';
    $args = shortcode_atts( [
      'type'              => 'plaintext',
			'id'                => false,
			'src'               => '',
			'width'             => '',
			'height'            => '',
			'scrolling'         => '',
			'frameborder'       => '',
			'allowtransparency' => '',
			'allow'             => '',
			'allowfullscreen'   => '',
    ], $atts );
		// return '<pre/>'.print_r(args);
		$query = mja_get_wp_query( [
			'post_type'      => MJA_ADS_NAME,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'paged'          => $page_no,
		] );
		if ( $query->have_posts() ):
			ob_start();
			while ( $query->have_posts() ): $query->the_post();
			$post_id = get_the_ID();
				// if( get_post_meta( get_the_ID() , 'mjc_ads_inf_status' , true ) != 'active' ){continue;}
				$type = get_post_meta( $post_id , 'mjc_ads_inf_type' , true );
				switch( $type ) {
					case 'plaintext':
						?>
						<div class="ad-plaintext">
							<p class="text-muted">
								<?php
								if( get_post_meta( $post_id , 'mjc_ads_param_type_plain_allowshortcode' , true ) ) {
									esc_html_e( get_post_meta( $post_id , 'mjc_ads_param_type_plain_text' , true ), "buddypress-advertising" );
								}else{
									do_shortcode( get_post_meta( $post_id , 'mjc_ads_param_type_plain_text' , true ), true );
								}
								?>
							</p>
						</div>
						<?php
						break;
					case 'richtext':
						?>
						<div class="ad-richtext" style="font-size:
						<?php esc_attr( get_post_meta( $post_id , 'mjc_ads_param_type_rich_fontsize' , true ) ); ?>;background-color:
						<?php esc_attr( get_post_meta( $post_id , 'mjc_ads_param_type_rich_bgcolor' , true ) ); ?>;background-color:
						<?php esc_attr( get_post_meta( $post_id , 'mjc_ads_param_type_rich_txtcolor' , true ) ); ?>;
						" >
								<?php
									esc_html_e( get_post_meta( $post_id , 'mjc_ads_param_type_rich_text' , true ), "buddypress-advertising" );
								?>
						</div>
						<?php
						break;
					case 'media':
						?>
						<div class="ad-media">
							<?php
							if( get_post_meta( $post_id , 'mjc_ads_param_type_media' , true ) == 'image' ) :
								$image = get_post_meta( $post_id , 'mjc_ads_param_type_media_image' , true );
								?>
								<img src="<?php print_r( $image ); ?>" alt="" class="img-responsive" />
								<?php
							else:
								?>
								<video src="<?php esc_attr( get_post_meta( $post_id , 'mjc_ads_param_type_media_video' , true ) ); ?>" alt="" class="video-responsive">
								<?php
							endif;
							?>
						</div>
						<?php
						break;
					default: break;
				};











			endwhile;
			$data .= ob_get_clean();
		else:
			wp_die( '0' );
		endif;
		wp_reset_postdata();
		return '
		<div class="mja_post_ads">
					' . $data . '
		</div>';
	}



}
new ShortCodes();
