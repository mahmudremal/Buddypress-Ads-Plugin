<?php
add_action( 'before_init', function(){
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status = %s WHERE post_type = %s AND post_status = %s", 'pending', MJA_ADS_NAME, 'publish' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type = %s AND post_status = %s", MJA_ADS_NAME, 'auto-draft' ) );
} );
add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
  wp_enqueue_media();
/*
	jQuery(function($){

		// Set all variables to be used in scope
		var frame,
				metaBox = $('#meta-box-id.postbox'), // Your meta box id here
				addImgLink = metaBox.find('.upload-custom-img'),
				delImgLink = metaBox.find( '.delete-custom-img'),
				imgContainer = metaBox.find( '.custom-img-container'),
				imgIdInput = metaBox.find( '.custom-img-id' );
		
		// ADD IMAGE LINK
		addImgLink.on( 'click', function( event ){
			
			event.preventDefault();
			
			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}
			
			// Create a new media frame
			frame = wp.media({
				title: 'Select or Upload Media Of Your Chosen Persuasion',
				button: {
					text: 'Use this media'
				},
				multiple: false  // Set to true to allow multiple files to be selected
			});

			
			// When an image is selected in the media frame...
			frame.on( 'select', function() {
				
				// Get media attachment details from the frame state
				var attachment = frame.state().get('selection').first().toJSON();

				// Send the attachment URL to our custom image input field.
				imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

				// Send the attachment id to our hidden input
				imgIdInput.val( attachment.id );

				// Hide the add image link
				addImgLink.addClass( 'hidden' );

				// Unhide the remove image link
				delImgLink.removeClass( 'hidden' );
			});

			// Finally, open the modal on click
			frame.open();
		});
		
		
		// DELETE IMAGE LINK
		delImgLink.on( 'click', function( event ){

			event.preventDefault();

			// Clear out the preview image
			imgContainer.html( '' );

			// Un-hide the add image link
			addImgLink.removeClass( 'hidden' );

			// Hide the delete image link
			delImgLink.addClass( 'hidden' );

			// Delete the image id from the hidden input
			imgIdInput.val( '' );

		});

	});
*/
});










// Register Custom Post Type 
function custom_post_type() {
	if( function_exists( 'add_submenu_page' ) ) {
		add_submenu_page( MJA_ADS_NAME, 'All Ads', __( 'All Items', "buddypress-advertising" ), 'manage_options', admin_url( 'edit.php?post_type=' . MJA_ADS_NAME, '' ), null );
		add_submenu_page( MJA_ADS_NAME, 'Add new Ads.', __( 'Add New', "buddypress-advertising" ), 'manage_options', admin_url( 'post-new.php?post_type=' . MJA_ADS_NAME, '' ), null );
		add_submenu_page( MJA_ADS_NAME, 'Ad Settings.', __( 'Settings', "buddypress-advertising" ), 'manage_options', MJA_ADS_NAME, null );
	}

	
	$labels = array(
		'name' => _x( 'Visual Ads', 'Post Type General Name', 'text_domain' ),
		'singular_name' => _x( 'Visual Ad', 'Post Type Singular Name', 'text_domain' ),
    'menu_name' => __( 'Visual Ads', 'text_domain' ),
    'name_admin_bar' => __( 'Visual Ads', 'text_domain' ),
    'archives' => __( 'Item Archives', 'text_domain' ),
    'attributes' => __( 'Item Attributes', 'text_domain' ),
    'parent_item_colon' => __( 'Parent Item:', 'text_domain' ),
    'all_items' => __( 'All Items', 'text_domain' ),
    'add_new_item' => __( 'Add New Item', 'text_domain' ),
    'add_new' => __( 'Add New', 'text_domain' ),
    'new_item' => __( 'New Item', 'text_domain' ),
    'edit_item' => __( 'Edit Item', 'text_domain' ),
    'update_item' => __( 'Update Item', 'text_domain' ),
    'view_item' => __( 'View Item', 'text_domain' ),
    'view_items' => __( 'View Items', 'text_domain' ),
    'search_items' => __( 'Search Item', 'text_domain' ),
    'not_found' => __( 'Not found', 'text_domain' ),
    'not_found_in_trash' => __( 'Not found in Trash', 'text_domain' ),
    'featured_image' => __( 'Featured Image', 'text_domain' ),
    'set_featured_image' => __( 'Set featured image', 'text_domain' ),
    'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
    'use_featured_image' => __( 'Use as featured image', 'text_domain' ),
    'insert_into_item' => __( 'Insert into item', 'text_domain' ),
    'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
    'items_list' => __( 'Items list', 'text_domain' ),
    'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
    'filter_items_list' => __( 'Filter items list', 'text_domain' ),
	);
	$arr=[];
	foreach( MJA_ADS_STATUS as $a => $v ) {
		$arr[] = $a;
	}
	$args = array(
		'label' => __( 'Visual Ads', 'text_domain' ),
    'description' => __( 'Post Type Description', 'text_domain' ),
    'labels' => $labels,
		'post_status'    => $arr,
		'menu_icon' => 'dashicons-slides',
    'supports' => [
			'title',
			// 'editor',
			// 'excerpt',
			// 'thumbnail',
			// 'revisions',
			// 'author',
			// 'comments',
			// 'trackbacks',
			// 'page-attributes',
			// 'custom-fields',
		],
    'taxonomies' => [],
		// 'register_meta_box_cb' => 'study_meta_box', // Register a meta box
    // 'rewrite' => [ 'slug' => 'studies' ],
		'show_in_rest' => true, // Enable the REST API
		// 'rest_base' => 'studies', // Change the REST base
    'hierarchical' => false,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => false,
    'menu_position' => 5,
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => true,
    'can_export' => true,
    'has_archive' => false,
    'exclude_from_search' => true,
    'publicly_queryable' => true,
    'capability_type' => 'page', // page, edit_post, read_post, delete_post, edit_posts, edit_others_posts, publish_posts, read_private_posts, read, delete_posts, delete_private_posts, delete_published_posts, delete_others_posts, edit_private_posts, edit_published_posts, create_posts
  );
	register_post_type( MJA_ADS_NAME, $args );
	register_post_status(
		'validated',
		[
			'label'       => _x( 'Validated', "buddypress-advertising" ),
			'public'      => true,
			'_builtin'    => true,
			'label_count' => _n_noop(
				'Validated <span class="count">(%s)</span>',
				'Validated <span class="count">(%s)</span>'
			)
		]
	);
}
add_action( 'init', 'custom_post_type', 0 );
add_action( 'change_locale', 'custom_post_type' );
wp_enqueue_style( 'tel-input', 'https://www.jqueryscript.net/demo/jQuery-International-Telephone-Input-With-Flags-Dial-Codes/build/css/intlTelInput.css', [], 'all' );
wp_enqueue_style( 'tel-input-1', 'https://www.jqueryscript.net/css/jquerysctipttop.css?0925', [], 'all' );
// wp_enqueue_script( 'jquery-js', 'https://code.jquery.com/jquery-latest.min.js', [], true );
wp_enqueue_script( 'tel-input', 'https://www.jqueryscript.net/demo/jQuery-International-Telephone-Input-With-Flags-Dial-Codes/build/js/intlTelInput.js', [], true );

function on_save_custom_ads_post( $post_id, $post = [] ) {
  global $wpdb;  
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {return;}
	if( wp_is_post_revision( $post_id ) ) {return;}
	if( MJA_ADS_NAME !== get_post_type( $post_id ) ) {return;}
	wp_delete_auto_drafts();
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type = %s AND post_status = %s", MJA_ADS_NAME, 'auto-draft' ) );
	
}
// add_action( 'save_post', 'on_save_custom_ads_post', 15 );
// add_action( 'save_post_' . MJA_ADS_NAME, 'on_save_custom_ads_post', 20, 2 );
// add_action( 'save_post', 'on_save_custom_ads_post', 20, 2 );


/*
// Help Tab
add_action('admin_head', function() {
	$screen = get_current_screen();
	if ( MJA_ADS_NAME != $screen->post_type )
			return;
	$args = [
			'id'      => 'ads_help',
			'title'   => __( 'Ads Help', "buddypress-advertising" ),
			'content' => '<h3>Case Study</h3><p>Case studies for portfolio.</p>',
	];
	$screen->add_help_tab( $args );
});
add_filter( 'enter_title_here', function( $title ) {
	$screen = get_current_screen();
	if  ( MJA_ADS_NAME == $screen->post_type ) {
			$title = __( 'Enter Title of the Ads here', "buddypress-advertising" );
	}
	return $title;
});
add_filter( 'post_updated_messages', function($messages) {
	global $post, $post_ID;
	$link = esc_url( get_permalink($post_ID) );
	$messages['study'] = array(
			0 => '',
			1 => sprintf( __('Study updated. <a href="%s">View study</a>'), $link ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Study updated.'),
			5 => isset($_GET['revision']) ? sprintf( __('Study restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Study published. <a href="%s">View study</a>'), $link ),
			7 => __('Study saved.'),
			8 => sprintf( __('Study submitted. <a target="_blank" href="%s" rel="noopener noreferrer">Preview study</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Study scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s" rel="noopener noreferrer">Preview study</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), $link ),
			10 => sprintf( __('Study draft updated. <a target="_blank" href="%s" rel="noopener noreferrer">Preview study</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
});
add_filter( 'bulk_post_updated_messages', function( $bulk_messages, $bulk_counts ) {
	$bulk_messages['study'] = array(
			'updated'   => _n( "%s study updated.", "%s studies updated.", $bulk_counts["updated"] ),
			'locked'    => _n( "%s study not updated, somebody is editing it.", "%s studies not updated, somebody is editing them.", $bulk_counts["locked"] ),
			'deleted'   => _n( "%s study permanently deleted.", "%s studies permanently deleted.", $bulk_counts["deleted"] ),
			'trashed'   => _n( "%s study moved to the Trash.", "%s studies moved to the Trash.", $bulk_counts["trashed"] ),
			'untrashed' => _n( "%s study restored from the Trash.", "%s studies restored from the Trash.", $bulk_counts["untrashed"] ),
	);
	return $bulk_messages;
}, 10, 2 );
function study_meta_box(WP_Post $post) {
	add_meta_box('study_meta', 'Study Details', function() use ($post) {
			$field_name = 'your_field';
			$field_value = get_post_meta($post->ID, $field_name, true);
			wp_nonce_field('study_nonce', 'study_nonce');
			?>
			<table class="form-table">
					<tr>
							<th> <label for="<?php echo $field_name; ?>">Your Field</label></th>
							<td>
									<input id="<?php echo $field_name; ?>"
												name="<?php echo $field_name; ?>"
												type="text"
												value="<?php echo esc_attr($field_value); ?>"
									/>
							</td>
					</tr>
			</table>
			<?php
	});
}
add_action('after_switch_theme', function() {
	$role = get_role( 'administrator' );
	$capabilities = compile_post_type_capabilities('study', 'studies');
	foreach ($capabilities as $capability) {
			$role->add_cap( $capability );
	}
});
add_action('switch_theme', function() {
	$role = get_role( 'administrator' );
	$capabilities = compile_post_type_capabilities('study', 'studies');
	foreach ($capabilities as $capability) {
			$role->remove_cap( $capability );
	}
});

// edit_post, read_post, delete_post, edit_posts, edit_others_posts, publish_posts, read_private_posts, read, delete_posts, delete_private_posts, delete_published_posts, delete_others_posts, edit_private_posts, edit_published_posts, create_posts

function compile_post_type_capabilities($singular = 'post', $plural = 'posts') {
	return [
			'edit_post'      => "edit_$singular",
			'read_post'      => "read_$singular",
			'delete_post'        => "delete_$singular",
			'edit_posts'         => "edit_$plural",
			'edit_others_posts'  => "edit_others_$plural",
			'publish_posts'      => "publish_$plural",
			'read_private_posts'     => "read_private_$plural",
			'read'                   => "read",
			'delete_posts'           => "delete_$plural",
			'delete_private_posts'   => "delete_private_$plural",
			'delete_published_posts' => "delete_published_$plural",
			'delete_others_posts'    => "delete_others_$plural",
			'edit_private_posts'     => "edit_private_$plural",
			'edit_published_posts'   => "edit_published_$plural",
			'create_posts'           => "edit_$plural",
	];
}
add_action( 'init', function() {
	$type = 'study';
	$labels = xcompile_post_type_labels('Study', 'Studies');
	// Compile capabiltites
	$capabilities = compile_post_type_capabilities('study', 'studies');
	$arguments = [
			'capabilities' => $capabilities, // Apply capabilities
			'taxonomies' => ['post_tag'],
			'register_meta_box_cb' => 'study_meta_box',
			'labels'  => $labels,
			'description' => 'Case studies for portfolio.',
			'menu_icon' => 'dashicons-desktop',
			'public' => true,
			'has_archive' => true,
			'hierarchical' => false,
			'show_in_rest' => true,
			'rest_base' => 'studies',
			'supports' => ['title', 'editor', 'revisions', 'page-attributes', 'thumbnail'],
			'rewrite' => [ 'slug' => 'studies' ]
	];
	register_post_type( $type, $arguments);
});
*/













class MJA_Meta_Boxes {

	private $post = [];
	private $config = [];

	public function __construct() {
		
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
		$this->init();
		$this->mja_ads_video_thumb();
	}
	public function init() {
		if( class_exists( 'CSF' ) ) {
			$this->process_metaboxes();
			/*
			$prefix = 'mja_meta';
			CSF::createMetabox( $prefix, [
				'title' => __( 'My Title', "buddypress-advertising" ),
				'post_type' => MJA_ADS_NAME,
				'fields' => [
					[
						'id' => 'ads-type',
						'type' => 'text',
						'title' => __( 'Ads. type', 'plugnin-name' )
					]
				]
			]);
			CSF::createSection( $prefix, [
				'title' => __( 'Ads. Info', "buddypress-advertising" ),
				'fields' => [
					[
						'id' => 'ads-type',
						'type' => 'text',
						'title' => __( 'Ads. type', 'plugnin-name' )
					]
				]
			]);
			*/
		}else{
			$this->process_metaboxes();
		}
	}
	public function custom_post_meta_boxes( $i = null ) {
		$arr=[];
		
		$arr[] = [
			'title' => __( 'Ads. Informations', "plugin-name" ),
			'description' => '',
			'prefix' => 'mjc_ads_inf',
			'domain'=> "plugin-name",
			'class_name'=> 'mjc_ads_inf_type_meta_box',
			'post-type'=> [
				MJA_ADS_NAME,
			],
			'context'=> 'advanced',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => '
				.mjc_ads_inf_type_tr {
					height: 400px
				}
				.mjc_ads_inf_type_meta_box tr input[type=number],
				.mjc_ads_inf_type_meta_box tr select {
					width: 100%;
				}
				#mjc_ads_param_type_media_prev_image img {
					max-width: 400px;
					max-height: 300px;
				}
				td.mjc_ads_short_code.column-mjc_ads_short_code {
					user-select: all;
				}
				.mujah_meta_box.mjc_ads_param tr {
					display: none;
				}
				.mujah_meta_box.mjc_ads_inf_type_meta_box.plaintext tr[id^=mjc_ads_param_type_plain],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.richtext tr[id^=mjc_ads_param_type_rich],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.media tr[id^=mjc_ads_param_type_media],
				
				.mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_prev_tr],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_image_tr],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_img_tr],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_url_tr],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_width_tr],
				.mujah_meta_box.mjc_ads_inf_type_meta_box.image tr[id=mjc_ads_param_type_media_height_tr],
				
				.mujah_meta_box.mjc_ads_inf_type_meta_box.video tr[id=mjc_ads_param_type_media_video_tr]
				{
					display: table-row;
				}
				.mujah_meta_box.mjc_ads_inf_type_meta_box tr {
					display: none;
				}
				.mujah_meta_box.mjc_ads_inf_type_meta_box tr[id^=mjc_ads_inf_]{
					display: table-row;
				}
				#mjc_ads_inf_title_post_id{
					width: 100%;
					color: #333;
					font-weight: 600;
				}
				#poststuff #titlewrap input {
						/* width: 100%;
						height: 40px;
						font-size: 25px;
						color: #333;
						display: none; */
				}
				#titlediv #edit-slug-box {
					display: none;
				}
				h3.text-muted {
					color: #907373;
				}
				fieldset label span {
					visibility: hidden;
				}
				fieldset label {
					position: relative;
					display: block;
					width: 100%;
				}
				fieldset label input[type="radio"]:checked + span {
					position: absolute;
					display: inline-block;
					text-align: center;
					/* left:7%; */
					justify-content: center;
					margin: 0 0 0 20px;
					padding: 8px 15px;
					white-space: nowrap;
					color: white;
					background-color: rgb(65, 65, 65);
					border-radius: 4px;
					box-shadow: 0 0 10px rgba(34,34,34,0.2);
					clear: both;
					visibility: visible;
					transform: translateX(0px);
					transition: transform 200ms ease;
				}
				fieldset label input[type="radio"]:checked + span:before {
					position: absolute;
					content:"";
					top:4%;
					left:-1px;
					border-top: 8px solid transparent;
					border-right: 8px solid transparent;
					border-bottom: 8px solid rgba(34,34,34,0.9);
					transform: rotate(45deg);
				}
				.meta-box-sortables input[type=color] {position: relative;max-width: 200px;}
				.meta-box-sortables input[type=color]:before {content: "Select color";position: absolute;right: 0;top: 0;height: 100%;width: 80px;color: #fff;background: #333;font-size: 12px;text-align: center;line-height: 23px;}
			',
			'scripts' => [
				"
				console.log('status loader loaded');
				jQuery(document).ready( function() {
					jQuery( 'select[name=\"post_status\"]' ).html( '<option value=\"pending\">Pending</option>' );
					jQuery( '#hidden_post_status' ).attr( 'value', 'pending' );
					jQuery( '#post-status-display' ).text( 'Pending' );
					jQuery( '.mujah_meta_box.mjc_ads_inf_type_meta_box' ).addClass( 'richtext' );
					// jQuery( '#titlediv #edit-slug-box' ).css( 'display', 'none' );
					var value = 4586;
					var value = jQuery('input[name=mjc_ads_inf_title_post_id]').attr( 'value' );
					jQuery( '#titlediv' ).prepend( '<h3 class=\"text-muted\">ads id = Ad' + value + '</h3>' );
					jQuery( '#original_publish' ).attr( 'value', 'pending' );
					// 
					// jQuery( 'input[name=post_title]' ).attr( 'value', 'ads id = Ad' + value );
					// jQuery( '#title-prompt-text' ).addClass( 'screen-reader-text' );
					// // jQuery( 'input[name=post_title]' ).attr( 'disabled', 'disabled' );
					// // jQuery( 'input[name=post_title]' ).attr( 'readonly', 'true' );
					// // jQuery( 'input[name=post_title]' ).attr( 'placeholder', value );
				});
				",
				"
				jQuery(document).ready( function() {jQuery('#mjc_ads_inf_whatsapp').intlTelInput();});
				"
			],
			'fields'=> [
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
					]
				],
				
				
				[
					"type" => "editor",
					'label'=> __( 'Insert plain text or code into this field.', "plugin-name" ),
					'default'=> __( '', "plugin-name" ),
					'id'=>'mjc_ads_param_type_rich_text',
					"rows" => "8",
					"wpautop" => "1",
					"media-buttons" => "1",
					"teeny" => "1",
				],
				/*
				[
					'type'=>'number',
					'label'=>__( 'Rich Content Font Size', "plugin-name" ),
					'description' => 'px',
					'default'=>false,
					'id'=>'mjc_ads_param_type_rich_fontsize',
				],
				*/
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
	
	
				/*
				[
					'type'=>'radio',
					'label'=>__( 'Media Type', "plugin-name" ),
					'default'=> 'image',
					'id'=>'mjc_ads_param_type_media',
					'options' => [
						'image' => __( 'Images', "plugin-name" ),
						'video' => __( 'Videos', "plugin-name" )
					]
				],
				*/
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
					// "placeholder" => __( 'Insert Image URL', "plugin-name" ),
					"id" => "mjc_ads_param_type_media_image"
				],
				[
					'type'=> 'button',
					'label'=>__( 'Select Media', "plugin-name" ),
					'description' => '',
					'return' => 'url',
					'modal-title' => 'Choose additional Images',
					'modal-button' => 'Choose Images',
					'default'=>'',
					'id'=>'mjc_ads_param_type_media_img',
				],
				[
					"type" => "url",
					"label" => __( 'URL Link', "plugin-name" ),
					"default" => "",
					"id" => "mjc_ads_param_type_media_url",
					'description' => __( 'Insert a URL link where visitors redirected to on click.', "plugin-name" )
				],
				/*
					[
						'type'=>'text',
						'label'=>__( 'Width', "plugin-name" ),
						'description' => 'px',
						'default'=>'',
						'id'=>'mjc_ads_param_type_media_width'
					],
					[
						'type'=>'text',
						'label'=>__( 'Height', "plugin-name" ),
						'description' => 'px',
						'default'=>'',
						'id'=>'mjc_ads_param_type_media_height'
					],
				*/
				[
					"type" => "select",
					'label'=> __( 'Ads. Category', "plugin-name" ),
					'default'=> 'lifetime',
					'id'=>'mjc_ads_inf_category',
					"options" => MJA_ADS_CATEGORIES,
				],
				[
					"type" => "select",
					'label'=> __( 'Ads. Duration', "plugin-name" ),
					'default'=> 'lifetime',
					'id'=>'mjc_ads_inf_duration',
					"options" => MJA_ADS_DURATION,
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
				],
				[
					"type" => "phone",
					'label'=> __( 'WhatsApp Business Number:', "plugin-name" ),
					'default'=> false,
					'id'=>'mjc_ads_inf_whatsapp',
					'placeholder' => __( 'e.g +2348167534572', "plugin-name" ),
					'description' => __( 'Input your whats app number that can be used on runtime ads. for stablishing communications.', "plugin-name" )
				],
				[
					"type" => "hidden",
					'label'=> false,
					'default'=> 'enabled',
					'id'=>'mjc_ads_inf_enable'
				]
			]
		];
		/*
		$arr[] = [
			'title' => 'Ads. Parameter',
			'description' => __( 'Insert plain text or code into this field.', ''),
			'prefix' => 'mjc_ads_param',
			'domain'=> "plugin-name",
			'class_name'=> 'mjc_ads_param',
			'post-type'=> [
				MJA_ADS_NAME,
			],
			'context'=> 'advanced',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => '
			
				.mjc_ads_param tr th{
					wi dth: 20%;
				}
				.mjc_ads_param tr td{
					width: 80%;
				}
				.mjc_ads_param input[type=text]:not(#mjc_ads_param_type_media_img),
				.mjc_ads_param input[type=number],
				.mjc_ads_param input[type=url],
				.mjc_ads_param textarea {
					width: 100%;
				}
			',
			'fields'=> [
				[
					"type" => "textarea",
					'label'=> __( 'Insert plain text or code into this field.', "plugin-name" ),
					'default'=> __( '', "plugin-name" ),
					'id'=>'mjc_ads_param_type_plain_text',
					"rows" => "8",
				],
				[
					'type'=>'checkbox',
					'label'=>__( 'Allow Shortcodes', "plugin-name" ),
					'description' => 'Execute shortcodes',
					'default'=>false,
					'id'=>'mjc_ads_param_type_plain_allowshortcode',
				],
	
	
				[
					"type" => "editor",
					'label'=> __( 'Insert plain text or code into this field.', "plugin-name" ),
					'default'=> __( '', "plugin-name" ),
					'id'=>'mjc_ads_param_type_rich_text',
					"rows" => "8",
					"wpautop" => "1",
					"media-buttons" => "1",
					"teeny" => "1",
				],
				[
					'type'=>'number',
					'label'=>__( 'Rich Content Font Size', "plugin-name" ),
					'description' => 'px',
					'default'=>false,
					'id'=>'mjc_ads_param_type_rich_fontsize',
				],
				[
					'type'=>'color',
					'label'=>__( 'Rich Content Background Color', "plugin-name" ),
					'description' => '',
					'default'=>'#333',
					'id'=>'mjc_ads_param_type_rich_bgcolor',
				],
				[
					'type'=>'color',
					'label'=>__( 'Rich Content Text Color', "plugin-name" ),
					'description' => '',
					'default'=>'#fff',
					'id'=>'mjc_ads_param_type_rich_txtcolor',
				],
	
	
				
				// [
				// 	'type'=>'radio',
				// 	'label'=>__( 'Media Type', "plugin-name" ),
				// 	'default'=> 'image',
				// 	'id'=>'mjc_ads_param_type_media',
				// 	'options' => [
				// 		'image' => __( 'Images', "plugin-name" ),
				// 		'video' => __( 'Videos', "plugin-name" )
				// 	]
				// ],
				[
					"type" => "url",
					"label" => __( 'Video link', "plugin-name" ),
					'placeholder'=>__( 'Inter your video Link here', "plugin-name" ),
					"default" => "",
					"id" => "mjc_ads_param_type_media_video"
				],
				[
					'type' => 'div',
					'label' => false,
					'id' => 'mjc_ads_param_type_media_prev',
					'class' => '',
					'default' => 'http://placehold.it/300x200',
					'content' => ''
				],
				[
					"type" => "hidden",
					"label" => false,
					'placeholder'=>__( 'Image Link', "plugin-name" ),
					// "placeholder" => __( 'Insert Image URL', "plugin-name" ),
					"id" => "mjc_ads_param_type_media_image"
				],
				[
					'type'=>'button',
					'label'=>__( 'Select Media', "plugin-name" ),
					'description' => '',
					'return' => 'url',
					'modal-title' => 'Choose additional Images',
					'modal-button' => 'Choose Images',
					'default'=>'',
					'id'=>'mjc_ads_param_type_media_img',
				],
				[
					"type" => "url",
					"label" => __( 'URL Link', "plugin-name" ),
					"default" => "",
					"id" => "mjc_ads_param_type_media_url",
					'description' => __( 'Insert a URL link where visitors redirected to on click.', "plugin-name" )
				],
				[
					'type'=>'text',
					'label'=>__( 'Width', "plugin-name" ),
					'description' => 'px',
					'default'=>'',
					'id'=>'mjc_ads_param_type_media_width'
				],
				[
					'type'=>'text',
					'label'=>__( 'Height', "plugin-name" ),
					'description' => 'px',
					'default'=>'',
					'id'=>'mjc_ads_param_type_media_height'
				],
			]
		];
		$arr[] = [
			'title' => __( 'Ads. Layout / Output', "plugin-name" ),
			'description' => __( 'Everything connected to the ads layout and output.', "plugin-name" ),
			'prefix' => 'mjc_ads_layout',
			'domain'=> "plugin-name",
			'class_name'=> 'mjc_ads_meta_box_layout',
			'post-type'=> [
				MJA_ADS_NAME
			],
			'context'=> 'advanced',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => '',
			'fields'=> [
				[
					'type'=>'radio',
					'label'=>__( 'Position', "plugin-name" ),
					'default'=>'',
					'id'=>'mjc_ads_layout',
					'options' => [
						'0' => __( 'Left', "plugin-name" ),
						'1' => __( 'Center', "plugin-name" ),
						'2' => __( 'Right', "plugin-name" ),
					]
				],
				[
					'type'=>'checkbox',
					'label'=>__( 'Adds a clearfix', "plugin-name" ),
					'description' => 'Check this if you don’t want the following elements to float around the ad. (adds a clearfix)',
					'default'=>false,
					'id'=>'mjc_ads_layout_not',
				],
				[
					"type" => "text",
					"label" => __( "Container ID", "plugin-name" ),
					"default" => "banner-id",
					"id" => "mjc_ads_layout_container_id"
				],
				[
					"type" => "text",
					"label" => __( "Container Classes", "plugin-name" ),
					"default" => "banner-class",
					"id" => "mjc_ads_layout_container_class"
				]
			]
		];
		$arr[] = [
			'title' => __( 'Display Conditions', "plugin-name" ),
			'description' => __( 'Everything connected to the ads layout and output.', "plugin-name" ),
			'prefix' => 'mjc_ads_dc',
			'domain'=> "plugin-name2",
			'class_name'=> 'mjc_ads_meta_box_layout',
			'post-type'=> [
				MJA_ADS_NAME
			],
			'context'=> 'advanced',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => '',
			'fields'=> [
				[
					'type'=>'select',
					'label'=>__( 'Activity Type', "plugin-name" ),
					'description'=> __( 'A page with this ad on it must match all of the following conditions.<br/>If you want to display the ad everywhere, dont do anything here.', "buddypress-advertising" ),
					'multiple' => true,
					'id'=>'mjc_ads_dc_type',
					'options' => [
						'sidewide_activity' => __( 'Sitewide Activity', "plugin-name" ),
						'group_activity' => __( 'Group Activity', "plugin-name" ),
						'members_activity' => __( 'Members Activity', "plugin-name" ),
					]
				],
				[
					'type'=>'select',
					'label'=>__( 'Activity Positions', "plugin-name" ),
					'description'=> __( '', "buddypress-advertising" ),
					'id'=>'mjc_ads_dc_position',
					'options' => [
						'bp_before_activity_entry' => __( 'Before activity entry', "plugin-name" ),
						'bp_activity_entry_content' => __( 'Activity entry content', "plugin-name" ),
						'bp_after_activity_entry' => __( 'After activity entry', "plugin-name" ),
						'bp_before_activity_entry_comments' => __( 'Before activity entry comments', "plugin-name" ),
						'bp_activity_entry_comments' => __( 'Activity entry comments', "plugin-name" ),
						'bp_after_activity_entry_comments' => __( 'After activity entry comments', "plugin-name" )
					]
				],
				[
					'type'=>'text',
					'label'=>__( 'Position', "plugin-name" ),
					'description' => __( 'Enter the value to inject after per entry', "plugin-name" ),
					'default'=>'3',
					'id'=>'mjc_ads_dc_injposition'
				],
				[
					'type'=>'checkbox',
					'label'=>__( 'Repeat Position', "plugin-name" ),
					'description'=> __( 'Enable this option if you want to repeat inject position', "buddypress-advertising" ),
					'multiple' => true,
					'id'=>'mjc_ads_dc_repeatposition',
				],
			]
		];
		$arr[] = [
			'title' => __( 'To Whom', "plugin-name" ),
			'prefix' => 'mjc_ads_2whom',
			'domain'=> "plugin-name-2whom",
			'class_name'=> 'mjc_ads_meta_box_layout_2whom',
			'post-type'=> [
				MJA_ADS_NAME
			],
			'context'=> 'advanced',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => '',
			'fields'=> [
				[
					'type'=>'select',
					'label'=>__( 'Visitor Type', "plugin-name" ),
					'description'=> __( 'Visitor conditions limit the number of users who can see your ad. There is no need to set visitor conditions if you want all users to see the ad.', "buddypress-advertising" ),
					'id'=>'mjc_ads_2whom_vstype',
					'options' => [
						'both_visitor' => __( 'Both Logged-in and Logged-out Visitor', "plugin-name" ),
						'login_in' => __( 'Logged-in Visitor', "plugin-name" ),
						'logout_out' => __( 'Logged-out Visitor', "plugin-name" ),
					]
				],
				[
					'type'=>'select',
					'label'=>__( 'Device', "plugin-name" ),
					'description'=> __( 'Device conditions limit the type of device who can see your ad.', "buddypress-advertising" ),
					'id'=>'mjc_ads_2whom_dvtype',
					'options' => [
						'both-device' => __( 'Both Mobile and Desktop Devices', "plugin-name" ),
						'mobile' => __( 'Mobile(Including Tablets)', "plugin-name" ),
						'desktop' => __( 'Desktop', "plugin-name" ),
					]
				],
			]
		];
		$arr[] = [
			'title' => __( 'Shortcode', "plugin-name" ),
			'prefix' => 'mjc_ads_short',
			'domain'=> "plugin-name-short",
			'class_name'=> 'mjc_ads_meta_box_short',
			'post-type'=> [
				MJA_ADS_NAME
			],
			'context'=> 'side',
			'priority'=> 'high',
			'cpt'=> MJA_ADS_NAME,
			'css' => 'code:active {user-select: all;}',
			'fields'=> [
				[
					'type'=>'code',
					'label'=> false,
					'description'=> __( 'Copy and Paste this shortcode to display this ad any where.', "buddypress-advertising" ),
					'id'=>'mjc_ads_short_code',
					'default' => sprintf( MJA_ADS_PREFIX . '%s]', $this->post->ID )
				]
			]
		];
		*/
	
		return ($i == null) ? $arr : ( isset( $arr[($i-1)] ) ? $arr[($i-1)] : [] );
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
	/*
	public function add_meta_boxes() {
		$database = $this->custom_post_meta_boxes();
		foreach( $database as $data ) {
			$this->config = $data;
			add_meta_box(
				sanitize_title( $this->config['title'] ),
				$this->config['title'],
				[ $this, 'add_meta_box_callback' ],
				$this->config['post-type'],
				$this->config['context'],
				$this->config['priority']
			);
		}
	}
	public function add_meta_boxes() {
		$database = $this->custom_post_meta_boxes();
		foreach( $database as $data ) {
			if( $data && !empty( $data ) ) {
				$this->config = $data;
				foreach( $data['post-type'] as $screen ) {
					add_meta_box(
						sanitize_title( $data['title'] ),
						$data['title'],
						[ $this, 'add_meta_box_callback' ],
						[$screen],
						$data['context'],
						$data['priority']
					);
				}
			}
		}
	}
	*/
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

	public function save_post( $post_id ) {
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
						<td <?php echo ( ! $field['label'] ) ? 'colspan="2" style="text-align: center;"' : ''; ?>><?php $this->field( $field ); ?></td>
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
			'<label class="rwp-checkbox-label"><input %s id="%s" name="%s" type="checkbox"> %s</label>',
			$this->checked( $field ),
			$field['id'], $field['id'],
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

	private function input( $field ) {
		if ( $field['type'] === 'media' ) {
			$field['type'] = 'text';
		}
		if ( isset( $field['color-picker'] ) ) {
			$field['class'] = 'rwp-color-picker';
		}
		if( isset( $field['attr'] ) ) {
			$attr='';
			foreach($field['attr'] as $a => $v) {
				$attr .= $a . '="' . $v . '" ';
			}
		}
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s" placeholder="%s" %s>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field ),
			isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			isset( $attr ) ? $attr : ''
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
			'<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s">',
			$field['id'],
			isset( $field['max'] ) ? "max='{$field['max']}'" : '',
			isset( $field['min'] ) ? "min='{$field['min']}'" : '',
			$field['id'],
			isset( $field['step'] ) ? "step='{$field['step']}'" : '',
			$field['type'],
			$this->value( $field )
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
		if( isset( $field['attr'] ) ) {
			$attr='';
			foreach($field['attr'] as $a => $v) {
				$attr .= $a . '="' . $v . '" ';
			}
		}
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s" placeholder="%s" %s>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field ),
			isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			isset( $attr ) ? $attr : ''
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
				// $output[] = sprintf(
				// 	'<label><input %s id="%s-%d" name="%s" type="radio" value="%s"> %s</label>',
				// 	$this->radio_checked( $field, $a ),
				// 	$field['id'], $i, $field['id'],
				// 	$a, $option
				// );
				$output[] = sprintf(
					'<label for="%s">
						<input id="%s" %s type="radio" name="%s" value="%s">
						%s <span>%s</span>
					</label>',
					// '<label><input  id="%s-%d" name="%s" type="radio" value="%s"></label>',
					$field['id'] . '-' . $i,$field['id'] . '-' . $i,
					$this->radio_checked( $field, $a ),
					$field['id'], $a,
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
			'<select id="%s" name="%s" %s>%s</select>',
			$field['id'], $field['id'],
			( ( $field['multiple'] && isset( $field['multiple'] ) ) ? 'multiple="multiple"' : '' ),
			$this->select_options( $field )
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
			'<textarea class="regular-text" id="%s" name="%s" rows="%d">%s</textarea>',
			$field['id'], $field['id'],
			isset( $field['rows'] ) ? $field['rows'] : 5,
			$this->value( $field )
		);
	}
	private function div( $field ) {
		global $post;
		$value = metadata_exists( 'post', $post->ID, 'mjc_ads_param_type_media_image' ) ? get_post_meta( $post->ID, 'mjc_ads_param_type_media_image' ) : [$field['default']];
		
		printf(
			'<div class="%s" id="%s">
				<div class="" id="%s_image">
					<img width="" height="" src="%s">
				</div>
			</div>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'],
			$field['id'],
			$value[0]
			
		);
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
		printf(
			'<button class="btn button %s" id="%s" name="%s" type="%s" value="%s">%s</button>',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			$field['type'],
			$this->value( $field ),
			$field['label']
		);
	}

	private function value( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

	private function checked( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
			if ( $value === 'on' ) {
				return 'checked';
			}
			return '';
		} else if ( isset( $field['checked'] ) ) {
			return 'checked';
		}
		return '';
	}



	
	private function mja_ads_video_thumb() {
		add_action( 'wp_ajax_mja_ads_video_thumb', [ $this, 'mja_ads_video_thumb_function' ] );
		add_action( 'wp_ajax_nopriv_mja_ads_video_thumb', [ $this, 'mja_ads_video_thumb_function' ] );
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
};
new MJA_Meta_Boxes();
?>