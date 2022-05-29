<?php
defined( 'MJA_ADS_NAME' ) || die( 'Silence is golden' );











add_action( 'init', function() {
	foreach( MJA_ADS_STATUS as $i => $v ) {
		register_post_status(
			$i,
			[
				'label'       => _x( $v['title'], "buddypress-advertising" ),
				'public'      => true,
				'post_type' => ['visual-ads'],
				'_builtin'    => true,
				'label_count' => _n_noop(
					$v['title'] . ' <span class="count">(%s)</span>',
					$v['title'] . ' <span class="count">(%s)</span>'
				)
			]
		);
	}
});


add_filter( 'display_post_states', function( $statuses = [] ) {
	global $post;
	if( $post->post_type == MJA_ADS_NAME ){
		if( $post->post_status == 'publish' ){
			return ['pending'];
		}elseif( $post->post_status == 'trash' ){
			return ['rejected'];
		}elseif( $post->post_status == 'auto-draft' ){
			wp_delete_post( $post->ID, false );
			return ['rejected'];
		}else{
			return [ strtolower( $post->post_status ) ];
		}
	}
	// if( get_query_var( 'post_status' ) != 'publish' ){
	// 	if( $post->post_status == 'publish' ){
	// 		return ['Publish'];
	// 	}
	// }
	return $statuses;
} );







if( isset( $_GET['post_type'] ) && $_GET['post_type'] == MJA_ADS_NAME ) {
	function set_custom_edit_visual_ads_columns( $columns = [] ) {
		if( isset( $columns['categories'] ) ) {unset( $columns['categories'] );}
		if( isset( $columns['tags'] ) ) {unset( $columns['tags'] );}
		if( isset( $columns['date'] ) ) {unset( $columns['date'] );}
	
		return array_merge(
			$columns,
			[
				'mjc_ads_id' 	=> __( 'ad ID', "buddypress-advertising" ),
				'post_author'  => __( 'Post author', "buddypress-advertising" ),
				'title'    => __( 'ad Title', "buddypress-advertising" ),
				'mjc_ads_inf_type'    => __( 'ad Type', "buddypress-advertising" ),
				// 'mjc_ads_inf_status'  => __( 'Status', "buddypress-advertising" ),
				// 'mjc_ads_short_code' 	=> __( 'Shortcode', "buddypress-advertising" ),
				// 'mjc_ads_short_code_enable' 	=> __( 'Enable', "buddypress-advertising" ),
				// 'mjc_ads_short_code' 	=> __( 'Content', "buddypress-advertising" ),
				'mjc_ads_inf_category' 	=> __( 'Category', "buddypress-advertising" ),
				'mjc_ads_inf_duration'								=> __( 'ad Duration', "buddypress-advertising" ),
				'mjc_ads_inf_location'								=> __( 'ad Location', "buddypress-advertising" ),
				'date'								=> __( 'Date created', "buddypress-advertising" ),
				'mjc_ads_action'								=> __( 'Action ', "buddypress-advertising" ),
				// mjc_ads_inf_category
				// mjc_ads_inf_duration
				// mjc_ads_inf_whatsapp
				// mjc_ads_inf_currency
			]
		);
	}
	add_filter( 'manage_visual-ads_posts_columns', 'set_custom_edit_visual_ads_columns' );
	function visual_ads_custom_column_values( $column, $post_id ) {
		switch ( $column ) {
			case 'mjc_ads_id' :
				echo $post_id;
				break;
			case 'mjc_ads_title' :
				_e( get_the_title(  $post_id ), "buddypress-advertising" );
				break;
			case 'mjc_ads_inf_category' :
				echo isset( MJA_ADS_CATEGORIES[get_post_meta( $post_id , $column , true )] ) ? MJA_ADS_CATEGORIES[get_post_meta( $post_id , $column , true )] : '';
				break;
			case 'mjc_ads_inf_duration' :
				echo isset( MJA_ADS_DURATION[get_post_meta( $post_id , $column , true )] ) ? MJA_ADS_DURATION[get_post_meta( $post_id , $column , true )] : '';
				break;
			case 'mjc_ads_inf_location' :
				$loc = get_post_meta( $post_id , $column , true );
				if( $loc == 'activity' ) {
					_e( 'Activity Feed Only', "buddypress-advertising" );
				}else{
					_e( 'Activity Feed & Featured Ads Page', "buddypress-advertising" );
					echo '<br/>';
					get_post_meta( $post_id , 'mjc_ads_inf_whatsapp' , true );
				}
				break;
			case 'mjc_ads_inf_currency' :
				echo get_post_meta( $post_id , $column , true );
				break;
			case 'mjc_ads_inf_type' :
				$type = get_post_meta( $post_id , $column , true );
				if( $type == 'plaintext' ) {_e( 'Plain Text and Code', "buddypress-advertising" );}
				if( $type == 'richtext' ) {_e( 'Rich Content', "buddypress-advertising" );}
				if( $type == 'image' ) {_e( 'Image Ad', "buddypress-advertising" );}
				if( $type == 'video' ) {_e( 'Video Ad', "buddypress-advertising" );}
				break;
			case 'post_author' :
				echo get_userdata( get_post( $post_id )->post_author )->display_name;
					break;
			case 'mjc_ads_inf_status' :
				$status = get_post_meta( $post_id , $column , true );
				echo '
				<select class="form-select form-select-sm" aria-label=".form-select-sm">';
				foreach( MJA_ADS_STATUS as $i => $name ) {
					$selected = ( $i == $status ) ? 'selected=""' : '';
					echo '<option value="' . $i . '" ' . $selected . '>' . $name . '</option>';
				}
					echo '
				</select>';
				break;
			case 'mjc_ads_short_code' :
				// get_post_meta( $post_id , $column , true ) . 
				echo MJA_ADS_PREFIX . $post_id . ']';
				break;
			case 'mjc_ads_short_code_enable' :
				$status = metadata_exists( 'post', $post_id, $column ) ? get_post_meta( $post_id , $column , true ) : 'disabled';
					echo '
					<div class="mb-3 form-check">
						<input type="checkbox" class="form-check-input ad_status_toggle_checkbox" id="status_toggle_' . $post_id . '" data-post_id="' . esc_attr( $post_id ) . '">
						<!-- <label class="form-check-label" for="status_toggle_' . $post_id . '">Enable</label> -->
					</div>';
				break;
			case 'mjc_ads_action' :
				echo mja_on_post_status_action_buttons( [
					'post_status' => get_post_status( $post_id ),
					'post_id' => $post_id
				] );
				break;
			default :
				echo get_post_meta( $post_id , $column , true );
				break;
		}
	}
	add_action( 'manage_visual-ads_posts_custom_column' , 'visual_ads_custom_column_values', 10, 2 );
}

function mja_on_post_status_action_buttons( $args = false) {
	if( ! $args ) {return '';}
	$args = wp_parse_args( $args, [
		'post_status' => 'pending',
		'post_id' => $post_id
	] );
	// $retrieved_nonce = $_REQUEST['_wpnonce'];
	// if (!wp_verify_nonce($retrieved_nonce, 'ads_list_action_nonce' ) ) wp_die( 'Failed security check' );
	$btn = false;
	// wp_die( print_r( MJA_ADS_STATUS[$args['post_status']] ) );
	$output = ( $btn ) ? '
	<form action="" method="post">
		<input type="hidden" name="post_id" value="' . $args['post_id'] . '" />' : '';
		!( $btn ) || wp_nonce_field('ads_list_action_nonce');
		

	switch ( $args['post_status'] ) {
		case 'trash' :
		case 'drafts' :
			break;
		case 'publish' :
		default:
			if( ! isset( MJA_ADS_STATUS[$args['post_status']] ) ) {$args['post_status']='pending';}
			foreach( MJA_ADS_STATUS[$args['post_status']]['params'] as $i => $v ) {
				$output .= ( $btn ) ? sprintf(
					'<input type="submit" name="%s" value="%s" />',
					$i,
					_x( $v, $v, "buddypress-advertising" )
				) : sprintf(
					'<button class="button mja_ads_action_btn" data-ads="%s" type="button" data-action="%s" data-nonce="%s" onclick="">%s</button>',
					$args['post_id'],
					$i,
					'', // wp_nonce_field( 'ads_list_action_nonce', null, null, false ),
					_x( $v, $v, "buddypress-advertising" )
				);
			}
			break;
	};
	return ( $btn ) ? $output : $output . '
	</form>';
}
/*
function wpse454363_posts_filter( $query ){
	global $pagenow;
	$type = 'post';
	if (isset($_GET['post_type'])) {
		$type = $_GET['post_type'];
	}
	if ( MJA_ADS_NAME == $type && is_admin() && $pagenow=='edit.php') {
		$meta_query = array(); // Declare meta query to fill after
		if (isset($_GET['post_date']) && $_GET['post_date'] != '') {
			// first meta key/value
			$meta_query[] = array (
				'key'      => 'post_date',
				'value'    => $_GET['post_date']
			);
		}
		if (isset($_GET['order_status']) && $_GET['order_status'] != '') {
			// second meta key/value
			$meta_query[] = array (
				'key'      => 'order_status',
				'value'    => $_GET['order_status']
			);
		}
		$query->query_vars['meta_query'] = $meta_query; // add meta queries to $query
	}
}
add_filter( 'parse_query', 'wpse454363_posts_filter' );
*/
add_action('pre_get_posts', function($query) {
	global $pagenow;
	if (!is_admin() || $query->query['post_type'] != MJA_ADS_NAME || $pagenow != 'edit.php') { return; }
	if (!isset($_GET['post_status']) || empty($_GET['post_status']) || (isset($_GET['post_status']) && $_GET['post_status'] == 'all')) {
		$query->set( 'post_status', MJA_ADS_STATUS ); }
	}
);








if( ! function_exists( 'mujah_set_ads_status' ) ) {
	function mujah_set_ads_status( $force, $ads ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status='%s' WHERE ID = %s AND post_type = %s", $force, $ads, MJA_ADS_NAME ) );
		wp_send_json_success( __( 'SUCCESSFULLY ' . strtoupper( $force ), "buddypress-advertising" ) );wp_die();
	}
	function mujah_update_ads_seen( $post_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}mjaads_time SET seen = ( seen + 1 ) WHERE post_id = %s AND ended_to IS NULL;", $post_id ) );
		return true;
	}
}
function mja_ads_list_actions_function( bool $initial_request = false ) {
	global $wpdb;$posttype=false;
	if ( ! $initial_request && ! check_ajax_referer( 'ads_list_action_nonce', 'ajax_nonce', false ) ) {
		wp_send_json_error( __( 'Invalid security token sent.', "buddypress-advertising" ) );
		wp_die( '0', 400 );
	}
	$is_ajax_request = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest';
	
	$force =  isset( $_GET[ 'force' ] ) ? $_GET[ 'force' ] : ( isset( $_POST[ 'force' ] ) ? $_POST[ 'force' ] : '' );
	$ads = isset( $_GET[ 'ads' ] ) ? $_GET[ 'ads' ] : ( isset( $_POST[ 'ads' ] ) ? $_POST[ 'ads' ] : '' );
	switch( $force ) {
		case 'active' :
			$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}mjaads_time SET started_from = NOW(), post_id = %s", $ads ) );
			mujah_set_ads_status( $force, $ads );
			break;
		case 'pending' :
		case 'paused' :
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}mjaads_time SET ended_to = NOW() WHERE post_id = %s", $ads ) );
			mujah_set_ads_status( $force, $ads );
			break;
		case 'expired' :case 'rejected' :
			mujah_set_ads_status( $force, $ads );
			break;
		case 'delete' :
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %s AND post_type = %s", $ads, MJA_ADS_NAME ) );
			wp_send_json_success( __( 'Successfully Removed.', "buddypress-advertising" ) );wp_die();
			break;
		default :
			wp_send_json_error( __( ' Problem while fetching action. ', "buddypress-advertising" ) . $force );wp_die();
			break;
	};
	if ( $is_ajax_request && ! $initial_request ) {
		wp_die();
	}
}
add_action( 'wp_ajax_nopriv_mja_ads_list_actions', 'mja_ads_list_actions_function' );
add_action( 'wp_ajax_mja_ads_list_actions', 'mja_ads_list_actions_function' );
if( isset( $_GET['post_type'] ) && $_GET['post_type'] == MJA_ADS_NAME ) {
	add_action('admin_footer-edit.php', function() {
		$opt = $tabs = '';
		$count = ( array ) wp_count_posts( MJA_ADS_NAME, null );
		$tabs .= '<li class="mja-status all"><a href="' . admin_url( 'edit.php?post_status=all&amp;post_type=' . MJA_ADS_NAME ) . '">All <span class="count"></span></a></li>';
		// $tabs .= '<li class="mja-status publish"><a href="' . admin_url( 'edit.php?post_status=publish&amp;post_type=' . MJA_ADS_NAME ) . '">Publish <span class="count">( ' . $count['publish'] . ' )</span></a></li>';
		foreach( MJA_ADS_STATUS as $i => $v ) {
			$opt .= "<option value=\"$i\">" . $v['title'] . "</option>";
			$tabs .= '<li class="mja-status '.$i.'"><a href="' . admin_url( 'edit.php?post_status=' . $i . '&amp;post_type=' . MJA_ADS_NAME ) . '">' . $v['title'] . ' <span class="count">( ' . $count[$i] . ' )</span></a></li>';
		}
		echo "
		<script>
			console.log('status loader loaded');
			function confirm_ajax( that = false ) {
				if( that ) {
					jQuery(that).prop('disabled');
					jQuery(that).attr('onclick', 'return;');
					var ads		= jQuery(that).data('ads'),
						action	= jQuery(that).data('action'),
						nonce		= jQuery(that).data('nonce');
						// window.wp.apiRequest.transport = $.ajax;
						jQuery.ajax({
							type: 'post',
							url: siteConfig?.ajaxUrl ?? '',
							dataType: 'json',
							data: {
								action: 'mja_ads_list_actions',
								force: action,
								ajax_nonce: siteConfig?.ads_list_action_nonce ?? '',
								ads: ads
							},
							success: ( data ) => {
								console.log( 'success', data.data );
								if( ! data.success ) {alert( data.data );}
								else{location.reload();}
							},
							error: ( err ) => {
								console.log( 'fail', err );
							}
						});
				}else{
					// alert();
				}
			}
		</script>
		<script>
			jQuery(document).ready( function() { // append
				jQuery( 'select[name=\"_status\"]' ).html( ' $opt ' );
				jQuery( 'ul.subsubsub' ).html( ' $tabs ' );
				jQuery( '.mja_ads_action_btn' ).attr( 'onclick', 'confirm_ajax(this)' );
			});
		</script>";
	});
	/*
	function mja_custom_status_add_in_post_page() {
		$opt = '';
		foreach( MJA_ADS_STATUS as $i => $v ) {
			$opt .= "<option value=\"$i\">" . $v['title'] . "</option>";
		}
		$opt = "<option value=\"pending\">Pending</option>";
		echo "
		<script>
		</script>";
	}
	add_action('admin_footer-post.php', 'mja_custom_status_add_in_post_page');
	add_action('admin_footer-post-new.php', 'mja_custom_status_add_in_post_page');
	*/
}




/*
add_post_type_support( MJA_ADS_NAME, 'buddypress-activity' );
function customize_page_tracking_args() {
    // Check if the Activity component is active before using it.
    if ( ! bp_is_active( 'activity' ) ) {
        return;
    }
 
    bp_activity_set_post_type_tracking_args( MJA_ADS_NAME, array(
        'component_id'             => buddypress()->blogs->id,
        'action_id'                => 'new_blog_page',
        'bp_activity_admin_filter' => __( 'Published a new Ads', 'custom-domain' ),
        'bp_activity_front_filter' => __( 'Ads', 'custom-domain' ),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __( '%1$s posted a new <a href="%2$s">Ads</a>', 'custom-textdomain' ),
        'bp_activity_new_post_ms'  => __( '%1$s posted a new <a href="%2$s">Ads</a>, on the site %3$s', 'custom-textdomain' ),
        'position'                 => 10,
    ) );
}
add_action( 'bp_init', 'customize_page_tracking_args' );
*/

function mja_generate_video_poster( $src = false, $size = 'full' ) {
	if( ! $src ){return;}
	switch( $size ) {
		case 'full' :
			return 'https://img.youtube.com/vi/w_9SxNAqmF8/0.jpg';
			break;
		case 'poster' :
			break;
	};
}
function mja_fetch_video_url( $url = false ) {
	if( ! $url ) {return;}
	return $url;
}







function mja_parse_video_content( $ads = [], $settings = [] ) {
	$arr = [
		"autoplay" => !empty( $settings['ads-setting']['autoplay'] ) ? true : false,
		"end" => 20,
		'controls' => !empty( $settings['ads-setting']['controls'] ) ? true : false,
		"aspectRatio" => '16:9',
		'fluid' => true,
		'playbackRates' => [0.5, 1, 1.5, 2],
		'fullscreenToggle' => false,
		'mute' => !empty( $settings['ads-setting']['sound'] ) ? true : false,
		'loop' => !empty( $settings['ads-setting']['loop'] ) ? true : false,
		/*
			'techOrder' => [ 'html5' ],
			"sources" => [
				// mja_DetermineVideoUrlType( $ads['media']['video'] )
				[
					"type" => "video/youtube",
					"src" => 'https://www.youtube.com/watch?v=w_9SxNAqmF8'
				]
			],
			"youtube" => [
				"iv_load_policy" => 1,
				"ytControls" => $settings['ads-setting']['controls'],
				"customVars" => [
					"wmode" => "transparent"
				]
			]
		*/
	];
	$vid = mja_DetermineVideoUrlType( $ads['media']['video'] );
	// if( $vid['type'] == 'video/youtube' ) {
	// 	$arr['techOrder'] = [ 'youtube' ];
	// 	$arr['youtube'] = [
	// 		'iv_load_policy' => 1,
	// 		'ytControls' => ( empty($settings['ads-setting']['controls']) || $settings['ads-setting']['controls'] === 0 ) ? false : true,
	// 		'customVars' => [
	// 			'wmode' => 'transparent'
	// 		]
	// 	];
	// }
	// if( $vid['type'] == 'video/vimeo' ) {$arr['techOrder'] = [ 'vimeo' ];}
	

	$rand = rand( 0, 99999 );
	$vtype = mja_DetermineVideoUrlType( $ads['media']['video'] );
	switch( $vtype['type'] ) {
		case 'video/youtube' :
			?>
			<div class="bb-activity-video-wrap bb-video-length-1">
				<div class="bb-activity-video-elem 9 act-grid-1-1 bb-vertical-layout" data-id="ads-9">
					<div id="mja_ads_iframe_<?php echo $rand; ?>" class="ads-video ads-youtube-video ads_iframe_youtube"></div>
					<i class="bb-icon-pause ads-video-sound-switcher" data-toggled="off"></i>
					<i class="bb-icon-volume-mute ads-video-sound-switcher" data-toggled="on"></i>
					<a class="ads_link_button" href="javascript:void(0);" data-href="<?php echo esc_url( $ads['media']['video'] ); ?>" onclick="return mja_ads_what_happend(this);" data-duration="100"><?php _e( 'Click here', "buddypress-advertising" ); ?></a>
				</div>
			</div>
			<script>iframe_loaded( '<?php echo $vtype['id']; ?>', 'mja_ads_iframe_<?php echo $rand; ?>');</script>
			<?php
			break;
		case 'video/vimeo' :
			print_r( MJA_VideoEmbed::get_embed_html( 'https://www.youtube.com/watch?v=w_9SxNAqmF8', 
				[ // embed_url_params
					'autoplay' => 1,
					'controls' => 0,
					'disablekb' => 1,
					'enablejsapi' => 1,
					'end' => 30,
					'fs' => 0, // full screen
					// 'hl' => 'en_us', // internationalized
					'iv_load_policy' => 3, // 1 & 3
					'loop' => 0,
					'modestbranding' => 1,
					'origin' => urlencode( site_url( '/', '' ) ),
					'rel' => 0,
					// 'start' => 1,
					// 'widget_referrer' => ''
				], 
				[ // options
				], 
				[ // frame_attributes
					'border' => 0,
					'frameborder' => 0,
					'type' => 'text/html',
					'width' => 400,
					'height' => 350,
					'id' => 'mja_ads_iframe_' . $rand,
					// 'onload' => ''
				]
			) );
			break;
		default :
			echo sprintf( '
				<div class="bb-activity-video-wrap bb-video-length-1">

					<div class="bb-activity-video-elem 9 act-grid-1-1 bb-vertical-layout" data-id="ads-9">
						<video playsinline id="ads_video_%s" class="ads-video video-js single-activity-video vjs-default-skin" data-id="9" data-attachment-full="%s" controls="false" poster="%s" data-setup=\'%s\'>
							<source src="%s" type="%s" poster="%s"/>
						</video>
						<i class="bb-icon-volume-up ads-video-sound-switcher" data-toggled="off"></i>
						<i class="bb-icon-pause ads-video-sound-switcher" data-toggled="play"></i>
						<a class="ads_link_button" href="javascript:void(0);" data-href="%s" onclick="return mja_ads_what_happend(this);"  data-duration="100">%s</a>
					</div>
				</div><script>onAdsPlayerReady( "ads_video_%s" );</script>',
				$rand,
				$vtype['poster'],
				$vtype['poster'],
				json_encode( $arr, true ),
				$vtype['src'],
				$vtype['type'],
				$vtype['poster'],
				$ads['media']['video'],
				_x( 'Click here', 'Click here', 'pluign-name' ),
				$rand
			);
		break;
	};
}

function mja_DetermineVideoUrlType( $url =  ''  ) {
	$yt_matches = $vm_matches = $data = [];
	$yt_rx = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
	$has_match_youtube = preg_match($yt_rx, $url, $yt_matches);

	$vm_rx = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([‌​0-9]{6,11})[?]?.*/';
	$has_match_vimeo = preg_match($vm_rx, $url, $vm_matches);

	if($has_match_youtube) {
		$data['id'] = $yt_matches[5];
		$data['poster'] = 'https://img.youtube.com/vi/' . $data['id'] . '/0.jpg';
		$data['type'] = 'video/youtube';
	}
	elseif($has_match_vimeo) {
		$data['id'] = $vm_matches[5];
		$data['poster'] = unserialize( file_get_contents( "http://vimeo.com/api/v2/video/{$data['id']}.php" ) )[0]['thumbnail_medium'];
		$data['type'] = 'video/vimeo';
	}
	else {
		$data['id'] = 0;
		$type = 'none';
		$data['poster'] = '';
		$data['type'] = 'video/mp4';
	}
	$data['src'] = $url;
	
	return $data;
}
function mja_number_format_short( $n = false, $precision = 1 ) {
  if( ! $n ) {return;}
  if( is_string($n) ){$n = (int) $n;}
  if( ! is_int($n) || ! is_integer($n) ) {return;}
  if ($n < 900) {
    // 0 - 900
    $n_format = number_format($n, $precision);
    $suffix = '';
  } else if ($n < 900000) {
    // 0.9k-850k
    $n_format = number_format($n / 1000, $precision);
    $suffix = 'K';
  } else if ($n < 900000000) {
    // 0.9m-850m
    $n_format = number_format($n / 1000000, $precision);
    $suffix = 'M';
  } else if ($n < 900000000000) {
    // 0.9b-850b
    $n_format = number_format($n / 1000000000, $precision);
    $suffix = 'B';
  } else {
    // 0.9t+
    $n_format = number_format($n / 1000000000000, $precision);
    $suffix = 'T';
  }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
  if ( $precision > 0 ) {
    $dotzero = '.' . str_repeat( '0', $precision );
    $n_format = str_replace( $dotzero, '', $n_format );
  }
  return $n_format . $suffix;
}
?>