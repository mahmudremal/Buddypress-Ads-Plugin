<?php
/**
 * This is ad showing classes for viewing front end ads.
 * 
 * @package visual-ads
 * @developer Remal mahmud
 */
class Mja_Show_Ads {
  private $plugin_name;
  private $version;
  private $reactions;
  public function __construct( $plugin_name = MJA_ADS_NAME, $version = '1.0.1' ) {
    $this->plugin_name = $plugin_name;
    $this->version     = $version;
    $this->reactions   = new MJA_Reactions();
    // wp_enqueue_script( 'mja_ads-video-js', 'https://vjs.zencdn.net/7.10.2/video.min.js', [], true );
    // wp_enqueue_script( 'mja_ads-youtube-video', plugin_dir_url( __DIR__ ) . 'public/js/youtube.min.js', [], true );
    // wp_dequeue_script( 'bp-nouveau-video' );
    // wp_dequeue_script( 'bp-media-videojs' );
    // wp_enqueue_style( 'video-js-css', 'https://vjs.zencdn.net/7.10.2/video-js.min.css', [], 'all' );

    add_action( 'wp_footer', function() {
      $cnf = MJA_OPTIONS;
      ?>
    <script>
      const MJA_ADS_TIMING = <?php echo empty($cnf['ads-setting']['time'])||!is_int($cnf['ads-setting']['time']) ? 30.00 : (int) $cnf['ads-setting']['time']; ?>;
      const MJA_ADS_AUTOPLAY = <?php echo empty( $cnf['ads-setting']['autoplay'] ) ? 'false' : 'true'; ?>;
      window.MJA_ADS_SOUND = <?php echo isset( $cnf['ads-setting']['sound'] ) ? $cnf['ads-setting']['sound'] : 100; ?>;
      var tag = document.createElement('script');
      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      window.AdsIframeIds = [];
    </script>

    <style>
      body{
        <?php
        $bg = $cnf['ads-setting']['overaly-bg'];
        echo sprintf('--mja-ads-background-gradient: linear-gradient(%s, %s, %s);',
        empty($bg['background-gradient-direction'])?'120deg':$bg['background-gradient-direction'],
        $bg['background-color'],
        $bg['background-gradient-color'] );
        ?>
      }
    </style>
      <?php
    } );
    /*
      $action = $shortcode = [];
      $action[] = [ 'wp_enqueue_scripts', 'enqueue_styles' ];
      $action[] = [ 'wp_enqueue_scripts', 'enqueue_scripts' ];

      $action[] = [ 'bp_before_activity_entry', 'mja_ads_display_before_activity_entry' ];
      $action[] = [ 'bp_activity_entry_content', 'mja_ads_display_activity_entry_content' ];
      $action[] = [ 'bp_after_activity_entry', 'mja_ads_display_after_activity_entry' ];
      $action[] = [ 'bp_before_activity_entry_comments', 'mja_ads_display_before_activity_entry_comments' ];
      $action[] = [ 'bp_activity_entry_comments', 'mja_ads_display_activity_entry_comments' ];
      $action[] = [ 'bp_after_activity_entry_comments', 'mja_ads_display_after_activity_entry_comments' ];

      $shortcode[] = [ 'ads-shortcode', 'mja_ads_shortcode_ads' ];
      $action[] = [ 'bp_before_directory_activity_content', 'mja_ads_check_ads' ];
      $action[] = [ 'bp_after_member_activity_post_form', 'mja_ads_check_ads' ];
      $action[] = [ 'bp_after_group_activity_post_form', 'mja_ads_check_ads' ];
      foreach( $action as $arr ) {
        add_action( $arr[0], [ $this, $arr[1] ] );
      }
      foreach( $shortcode as $arr ) {
        add_shortcode( $arr[0], [ $this, $arr[1] ] );
      }
    */
    $position = [
      'bp_before_activity_entry', 'bp_activity_entry_content', 'bp_after_activity_entry',
      'bp_before_activity_entry_comments', 'bp_activity_entry_comments', 'bp_after_activity_entry_comments'
    ];
    add_action( 'did_it_or_not', function( $args = [] ) {} );
    add_action( 'hook_it_or_not', function( $args = [] ) {} );
    // add_action( 'bp_activity_entry_meta', function() {
    //   echo 'Hellow there';
    // });
    add_action( 'bp_before_activity_entry', function() {
      $this->setup_hook( [
        'position' => 'before_activity_entry',
        'is_fullwidth' => true,
        'total_active_ads' => wp_count_posts( MJA_ADS_NAME, '' )->active,
        'activity_per_page' => 20
      ] );
    } );
    add_action( 'wp_ajax_nopriv_mja_ads_like', [ $this, 'mja_ads_like' ] );
    add_action( 'wp_ajax_mja_ads_like', [ $this, 'mja_ads_like' ] );

    
    add_action( 'wp_ajax_nopriv_mja_ads_click', [ $this, 'mja_ads_click' ] );
    add_action( 'wp_ajax_mja_ads_click', [ $this, 'mja_ads_click' ] );
  }
  public function mja_ads_like( bool $initial_request = false ) {
    $posttype=false;
    if ( ! $initial_request && ! check_ajax_referer( 'mja_ads_like_nonce', 'ajax_nonce', false ) ) {
      wp_send_json_error( __( 'Invalid security token sent.', "buddypress-advertising" ) );wp_die( '0', 400 );
    }
    $is_ajax_request = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest';
    $ad_id = isset( $_GET[ 'ad_id' ] ) ? $_GET[ 'ad_id' ] : ( isset( $_POST[ 'ad_id' ] ) ? $_POST[ 'ad_id' ] : '' );
    $toggle_like = $this->reactions->toggle_like( $ad_id );
    if( $toggle_like ) {
      wp_send_json_success( [
        'message' => __( 'Succesfully placed a like ', "buddypress-advertising" ),
        'action' => ( $this->reactions->is_liked( $ad_id ) ) ? 'liked' : 'unliked',
        'totals' =>  $this->reactions->get_liked( $ad_id )
      ] );
      wp_die();
    }
    
    
    if ( $is_ajax_request && ! $initial_request ) {
      wp_die();
    }
  }
  public function mja_ads_click( bool $initial_request = false ) {
    $posttype=false;
    if ( ! $initial_request && ! check_ajax_referer( 'mja_ads_click_nonce', 'ajax_nonce', false ) ) {
      wp_send_json_error( __( 'Invalid security token sent.', "buddypress-advertising" ) );wp_die( '0', 400 );
    }
    $is_ajax_request = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest';
    $ad_id = isset( $_GET[ 'ad_id' ] ) ? $_GET[ 'ad_id' ] : ( isset( $_POST[ 'ad_id' ] ) ? $_POST[ 'ad_id' ] : '' );
    $toggle_like = $this->reactions->toggle_like( $ad_id );
    if( $toggle_like ) {
      wp_send_json_success( [
        'message' => __( 'Succesfully placed a like ', "buddypress-advertising" ),
        'action' => ( $this->reactions->is_liked( $ad_id ) ) ? 'liked' : 'unliked',
        'totals' =>  $this->reactions->get_liked( $ad_id )
      ] );
      wp_die();
    }
    if ( $is_ajax_request && ! $initial_request ) {
      wp_die();
    }
  }
  private function setup_hook( $argv = [] ) {
    $argv = wp_parse_args( $argv, [
      'position' => 'before_activity_entry',
      'is_fullwidth' => false,
      'total_active_ads' => 20,
      'activity_per_page' => 20
    ] );
    
    $settings = MJA_OPTIONS;
    // print_r($settings);
    
    $did_it = did_action( 'did_it_or_not' );
    if( 0 === ( $did_it % $settings['ads']['loop'] ) ) {
      do_action( 'did_it_or_not', [] );
      do_action( 'hook_it_or_not', [] );
      global $activities_template;
      if( $activities_template->activity_count < $settings['ads']['loop'] ) {return;}
      $argv['activities_template'] = $activities_template;
      // print_r( $settings['ads']['loop'] % $activities_template->current_activity );


      $page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;
      // total posts load on each ajax is 20
      $page = number_format( ( ( ( ( $argv['activity_per_page'] * $page ) - $argv['activity_per_page'] ) / $settings['ads']['loop'] ) + did_action( 'hook_it_or_not' ) ), 0, '.', '');
      $args = [
        'post_type'  => MJA_ADS_NAME,
        'post_status' => 'active',
        'numberposts' => 1,
        'paged' => ( ( $page % $argv['total_active_ads'] ) + 1 ),
        'orderby'          => 'ID',
        'order'            => 'DESC',
        /*
          'fields'     => 'ids',
          'numberposts'      => 5,
          'category'         => 0,
          'include'          => [],
          'exclude'          => [],
          'meta_key'         => '',
          'meta_value'       => '',
          'post_type'        => 'post',
          'suppress_filters' => true,
          'query' => [
            [
              'key' => 'post_status',
              'value' => 'active',
              'compare' => 'LIKE'
            ]
          ]
        */
      ];
      $post = get_posts( $args );
      if( count( $post ) == 0 ){}
      $post = isset( $post[0] ) ? $post[0] : $post;
      if( !isset( $post->ID ) ) {return;}
      switch( get_post_meta( $post->ID, 'mjc_ads_inf_location', true ) ) {
        case 'activity' :
          $this->echo( $argv, $post, $settings );
          break;
        case 'featured' :
          $this->echo( $argv, $post, $settings );
          break;
        default :break;
      };
    }else{
      do_action( 'did_it_or_not', [] );
    }
  }
  public function echo( $argv = [], $post = false, $settings = [] ) {
    if( ! $post ){return;}
    // $post = get_post( $args[0] );
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
    
    <?php if( $is_fullwidth ) {} ?>
      <li class="activity activity-item">
        <?php echo 'Ad Id = '. $post->ID;
        echo '<br/>Activity = '. ($argv['activities_template']->current_activity + 1); ?>
        <div class="bp-activity-head">
          <div class="activity-avatar item-avatar">
            <img
              src="<?php echo esc_url( isset( $config['avater']['url'] ) ? $config['avater']['url'] : plugin_dir_url( 'public/img/avater.png' ) ); ?>"
              class="avatar user-1-avatar avatar-300 photo"
              width="300"
              height="300"
              alt="Profile photo" />
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
  }









  public function enqueue_styles() {
    // wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'public/css/plugin-name-public.css', array(), filemtime( plugin_dir_path( __FILE__ ) . 'public/css/plugin-name-public.css' ), 'all' );
  }
  public function enqueue_scripts() {
    global $post;
    // wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'public/js/plugin-name-public.js', [ 'jquery' ], filemtime( plugin_dir_path( __FILE__ ) . 'public/js/plugin-name-public.js' ), true );
    wp_localize_script(
      $this->plugin_name,
      MJA_ADS_NAME . '_check',
      array(
        'is_shortcode_page' => has_shortcode( $post->post_content, 'ads' ) ? true : false,
      )
    );
  }

  public function mja_ads_display_before_activity_entry() {
    $this->mja_ads_display_ads( 'bp_before_activity_entry' );
  }
  public function mja_ads_display_activity_entry_content() {
    $this->mja_ads_display_ads( 'bp_activity_entry_content' );
  }
  public function mja_ads_display_after_activity_entry() {
    $this->mja_ads_display_ads( 'bp_after_activity_entry' );
  }
  public function mja_ads_display_before_activity_entry_comments() {
    $this->mja_ads_display_ads( 'bp_before_activity_entry_comments' );
  }
  public function mja_ads_display_activity_entry_comments() {
    $this->mja_ads_display_ads( 'bp_activity_entry_comments' );
  }
  public function mja_ads_display_after_activity_entry_comments() {
    $this->mja_ads_display_ads( 'bp_after_activity_entry_comments' );
  }
  public function mja_ads_display_ads( $position ) {
    global $activities_template;
    $args = [
      'post_type'  => MJA_ADS_NAME,
      'fields'     => 'ids',
      'query' => [
        [
          'key' => 'post_status',
          'value' => 'active',
          'compare' => 'LIKE'
        ]
      ],
      // 'meta_query' => [
      //   [
      //     'key'     => 'mja_ads_values',
      //     'value'   => $position,
      //     'compare' => 'LIKE',
      //   ]
      // ]
    ];
    $ads = get_posts( $args );
    $mja_ads_get_ads = ( count( $ads ) > 0 ) ? $ads : [];
    foreach( $mja_ads_get_ads as $mja_ads_get_ad ) {
      $mja_ads_enable = get_post_meta( $mja_ads_get_ad, 'mjc_ads_inf_enable', true );
      if ( 'disable' === $mja_ads_enable ) {
        continue;
      }
      $mja_ads_type              = get_post_meta( $mja_ads_get_ad, 'mjc_ads_inf_type', true );
      $mja_ads_enject_position   = get_post_meta( $mja_ads_get_ad, 'mjc_ads_inf_location', true );
      $mja_ads_enject_repeat     = isset( $mja_ads_type['repeat_position'] ) ? $mja_ads_type['repeat_position'] : '';
      $mja_ads_activity_type     = isset( $mja_ads_type['activity_type'] ) ? $mja_ads_type['activity_type'] : array();
      $mja_ads_activity_position = isset( $mja_ads_type['activity_position'] ) ? $mja_ads_type['activity_position'] : '';
      
        
      

      if ( $position !== $mja_ads_activity_position ) {
        return;
      }
      if ( in_array( 'sidewide_activity', $mja_ads_activity_type, true ) ) {
        if ( ! function_exists( 'bp_is_activity_directory' ) || bp_is_activity_directory() ) {
          if ( 'yes' !== $mja_ads_enject_repeat ) {
            if ( $activities_template->current_activity == $mja_ads_enject_position ) {
              switch ( $mja_ads_type['ads_type'] ) {
                case 'plain-text-and-code':
                  $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
                  break;
                case 'rich-content':
                  $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
                  break;
                case 'image-ad':
                  $this->mja_ads_display_image_ads( $mja_ads_get_ad );
                  break;
              }
            }
          } else {
            $did_action = did_action( $mja_ads_activity_position );
            if ( $did_action !== $mja_ads_enject_position && 0 !== $did_action % $mja_ads_enject_position ) {
              continue;
            }
            switch ( $mja_ads_type['ads_type'] ) {
              case 'plain-text-and-code':
                $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
                break;
              case 'rich-content':
                $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
                break;
              case 'image-ad':
                $this->mja_ads_display_image_ads( $mja_ads_get_ad );
                break;
            }
          }
        }
      }
      if ( in_array( 'group_activity', $mja_ads_activity_type, true ) ) {
        if ( ! function_exists( 'bp_is_group_activity' ) || bp_is_group_activity() ) {
          if ( 'yes' !== $mja_ads_enject_repeat ) {
            if ( $activities_template->current_activity == $mja_ads_enject_position ) {
              switch ( $mja_ads_type['ads_type'] ) {
                case 'plain-text-and-code':
                  $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
                  break;
                case 'rich-content':
                  $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
                  break;
                case 'image-ad':
                  $this->mja_ads_display_image_ads( $mja_ads_get_ad );
                  break;
              }
            }
          } else {
            $did_action = did_action( $mja_ads_activity_position );
            if ( $did_action !== $mja_ads_enject_position && 0 !== $did_action % $mja_ads_enject_position ) {
              continue;
            }
            switch ( $mja_ads_type['ads_type'] ) {
              case 'plain-text-and-code':
                $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
                break;
              case 'rich-content':
                $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
                break;
              case 'image-ad':
                $this->mja_ads_display_image_ads( $mja_ads_get_ad );
                break;
            }
          }
        }
      }
      if ( in_array( 'members_activity', $mja_ads_activity_type, true ) ) {
        if ( ! function_exists( 'bp_is_user_activity' ) || bp_is_user_activity() ) {
          if ( 'yes' !== $mja_ads_enject_repeat ) {
            if ( $activities_template->current_activity == $mja_ads_enject_position ) {
              switch ( $mja_ads_type['ads_type'] ) {
                case 'plain-text-and-code':
                  $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
                  break;
                case 'rich-content':
                  $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
                  break;
                case 'image-ad':
                  $this->mja_ads_display_image_ads( $mja_ads_get_ad );
                  break;
              }
            }
          } else {
            $did_action = did_action( $mja_ads_activity_position );
            if ( $did_action !== $mja_ads_enject_position && 0 !== $did_action % $mja_ads_enject_position ) {
              continue;
            }
            switch ( $mja_ads_type['ads_type'] ) {
              case 'plain-text-and-code':
                $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
                break;
              case 'rich-content':
                $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
                break;
              case 'image-ad':
                $this->mja_ads_display_image_ads( $mja_ads_get_ad );
                break;
            }
          }
        }
      }
    }

  }
  public function mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad ) {

    $mja_ads_get_values = get_post_meta( $mja_ads_get_ad, 'mja_ads_values', true );
    $mja_ads_enable     = get_post_meta( $mja_ads_get_ad, 'mja_ads_enable', true );
    $mja_ads_position   = isset( $mja_ads_get_values['ads_position'] ) ? $mja_ads_get_values['ads_position'] : '';
    if ( 'disable' === $mja_ads_enable ) {
      return;
    }
    $mja_ads_position = '';
    switch ( $mja_ads_position ) {
      case 'default':
        $mja_ads_position = 'initial';
        break;
      case 'left':
        $mja_ads_position = 'left';
        break;
      case 'center':
        $mja_ads_position = 'center';
        break;
      case 'right':
        $mja_ads_position = 'right';
        break;
    }

    $mja_ads_get_plain_text  = isset( $mja_ads_get_values['plain_text_and_code'] ) ? $mja_ads_get_values['plain_text_and_code'] : '';
    $mja_ads_container_class = isset( $mja_ads_get_values['container_classes'] ) ? $mja_ads_get_values['container_classes'] : '';
    $mja_ads_container_id    = isset( $mja_ads_get_values['container_id'] ) ? $mja_ads_get_values['container_id'] : '';
    $mja_ads_visitor_view            = isset( $mja_ads_get_values['to_whom']['logged_in_visitor'] ) ? $mja_ads_get_values['to_whom']['logged_in_visitor'] : '';
    $mja_ads_visitor_device          = isset( $mja_ads_get_values['to_whom']['device'] ) ? $mja_ads_get_values['to_whom']['device'] : '';
    $mja_ads_margin_top      = isset( $mja_ads_get_values['margin']['top'] ) ? $mja_ads_get_values['margin']['top'] : '';
    $mja_ads_margin_right    = isset( $mja_ads_get_values['margin']['right'] ) ? $mja_ads_get_values['margin']['right'] : '';
    $mja_ads_margin_bottom   = isset( $mja_ads_get_values['margin']['bottom'] ) ? $mja_ads_get_values['margin']['bottom'] : '';
    $mja_ads_margin_left     = isset( $mja_ads_get_values['margin']['left'] ) ? $mja_ads_get_values['margin']['left'] : '';
    $mja_ads_clearfix        = isset( $mja_ads_get_values['clearfix'] ) ? $mja_ads_get_values['clearfix'] : '';
    $mja_ads_allow_shortcode = isset( $mja_ads_get_values['allow_shortcode'] ) ? $mja_ads_get_values['allow_shortcode'] : '';

    if ( empty( $mja_ads_container_id ) ) {
      $mja_ads_container_id = 'mja_ads_' . $mja_ads_get_ad;
    }
    if ( ! is_user_logged_in() && 'login_in' === $mja_ads_visitor_view ) {
      return;
    } elseif ( is_user_logged_in() && 'logout_out' === $mja_ads_visitor_view ) {
      return;
    }

    if ( 'desktop' === $mja_ads_visitor_device ) {
      if ( wp_is_mobile() ) {
        return;
      }
    } elseif ( 'mobile' === $mja_ads_visitor_device ) {
      if ( ! wp_is_mobile() ) {
        return;
      }
    }
    $allowed_atts              = array(
      'type'              => array(),
      'id'                => array(),
      'src'               => array(),
      'width'             => array(),
      'height'            => array(),
      'scrolling'         => array(),
      'frameborder'       => array(),
      'allowtransparency' => array(),
      'allow'             => array(),
      'allowfullscreen'   => array(),
    );
    $allowedposttags['script'] = $allowed_atts;
    $allowedposttags['iframe'] = $allowed_atts;

    if ( 'nouveau' === bp_get_theme_compat_id() ) {
      $is_iframe = '';
      if ( strpos( $mja_ads_get_plain_text, '<script' ) !== false || strpos( $mja_ads_get_plain_text, 'iframe' ) !== false ) {
        $is_iframe = 'wb-ads-iframe';
      }
      echo '<div class="wb-ads-rotator bp-ads-nouveau ' . esc_attr( $is_iframe ) . ' plain-text-and-code-ads ' . esc_attr( $mja_ads_container_class ) . '" style=" text-align:' . esc_attr( $mja_ads_position ) . '; margin-top:' . esc_attr( $mja_ads_margin_top ) . 'px; margin-right:' . esc_attr( $mja_ads_margin_right ) . 'px; margin-bottom:' . esc_attr( $mja_ads_margin_bottom ) . 'px;margin-left:' . esc_attr( $mja_ads_margin_left ) . 'px; " id="' . esc_attr( $mja_ads_container_id ) . '">';
    } else {
      echo '<div class="wb-ads-rotator plain-text-and-code-ads ' . esc_attr( $mja_ads_container_class ) . '" style=" text-align:' . esc_attr( $mja_ads_position ) . '; margin-top:' . esc_attr( $mja_ads_margin_top ) . 'px; margin-right:' . esc_attr( $mja_ads_margin_right ) . 'px; margin-bottom:' . esc_attr( $mja_ads_margin_bottom ) . 'px;margin-left:' . esc_attr( $mja_ads_margin_left ) . 'px; " id="' . esc_attr( $mja_ads_container_id ) . '">';
    }
    if ( 'yes' === $mja_ads_allow_shortcode ) {
      echo do_shortcode( $mja_ads_get_plain_text );
    } else {
      echo wp_kses( apply_filters( 'mja_ads_plain_text_code_ads_content', $mja_ads_get_plain_text ), $allowedposttags );
    }

    echo '</div>';
    if ( 'yes' === $mja_ads_clearfix ) {
      echo '<br style="clear: both; display: block; float: none;">';
    }

  }
  public function mja_ads_display_rich_content_ads( $mja_ads_get_ad ) {
    global $post;
    $mja_ads_get_values = get_post_meta( $mja_ads_get_ad, 'mja_ads_values', true );
    $mja_ads_enable     = get_post_meta( $mja_ads_get_ad, 'mja_ads_enable', true );
    $mja_ads_position   = isset( $mja_ads_get_values['ads_position'] ) ? $mja_ads_get_values['ads_position'] : '';
    if ( 'disable' === $mja_ads_enable ) {
      return;
    }
    $mja_ads_position = '';
    switch ( $mja_ads_position ) {
      case 'default':
        $mja_ads_position = 'initial';
        break;
      case 'left':
        $mja_ads_position = 'left';
        break;
      case 'center':
        $mja_ads_position = 'center';
        break;
      case 'right':
        $mja_ads_position = 'right';
        break;
    }

    $mja_ads_get_rich_content = isset( $mja_ads_get_values['rich_content'] ) ? $mja_ads_get_values['rich_content'] : '';
    $mja_ads_container_class  = isset( $mja_ads_get_values['container_classes'] ) ? $mja_ads_get_values['container_classes'] : '';
    $mja_ads_container_id     = isset( $mja_ads_get_values['container_id'] ) ? $mja_ads_get_values['container_id'] : '';
    $mja_ads_visitor_view             = isset( $mja_ads_get_values['to_whom']['logged_in_visitor'] ) ? $mja_ads_get_values['to_whom']['logged_in_visitor'] : '';
    $mja_ads_visitor_device           = isset( $mja_ads_get_values['to_whom']['device'] ) ? $mja_ads_get_values['to_whom']['device'] : '';
    $mja_ads_margin_top       = isset( $mja_ads_get_values['margin']['top'] ) ? $mja_ads_get_values['margin']['top'] : '';
    $mja_ads_margin_right     = isset( $mja_ads_get_values['margin']['right'] ) ? $mja_ads_get_values['margin']['right'] : '';
    $mja_ads_margin_bottom    = isset( $mja_ads_get_values['margin']['bottom'] ) ? $mja_ads_get_values['margin']['bottom'] : '';
    $mja_ads_margin_left      = isset( $mja_ads_get_values['margin']['left'] ) ? $mja_ads_get_values['margin']['left'] : '';
    $mja_ads_clearfix         = isset( $mja_ads_get_values['clearfix'] ) ? $mja_ads_get_values['clearfix'] : '';
    $mja_ads_font_size        = isset( $mja_ads_get_values['font_size'] ) ? $mja_ads_get_values['font_size'] : '';
    $mja_ads_txt_color        = isset( $mja_ads_get_values['text_color'] ) ? $mja_ads_get_values['text_color'] : '';
    $mja_ads_bg_color         = isset( $mja_ads_get_values['bg_color'] ) ? $mja_ads_get_values['bg_color'] : '';

    if ( empty( $mja_ads_container_id ) ) {
      $mja_ads_container_id = 'mja_ads_' . $mja_ads_get_ad;
    }
    if ( ! is_user_logged_in() && 'login_in' === $mja_ads_visitor_view ) {
      return;
    } elseif ( is_user_logged_in() && 'logout_out' === $mja_ads_visitor_view ) {
      return;
    }

    if ( 'desktop' === $mja_ads_visitor_device ) {
      if ( wp_is_mobile() ) {
        return;
      }
    } elseif ( 'mobile' === $mja_ads_visitor_device ) {
      if ( ! wp_is_mobile() ) {
        return;
      }
    }
    $allowed_atts              = array(
      'type'              => array(),
      'id'                => array(),
      'src'               => array(),
      'scrolling'         => array(),
      'width'             => array(),
      'height'            => array(),
      'frameborder'       => array(),
      'allowtransparency' => array(),
      'allow'             => array(),
      'allowfullscreen'   => array(),
    );
    $allowedposttags['script'] = $allowed_atts;
    $allowedposttags['iframe'] = $allowed_atts;

    if ( 'nouveau' === bp_get_theme_compat_id() ) {
      echo '<div class="wb-ads-rotator bp-ads-nouveau wb-ads-iframe rich-content-ads ' . esc_attr( $mja_ads_container_class ) . '" style="background-color:' . esc_attr( $mja_ads_bg_color ) . ';color:' . esc_attr( $mja_ads_txt_color ) . ';font-size:' . esc_attr( $mja_ads_font_size ) . 'px;text-align:' . esc_attr( $mja_ads_position ) . ';margin-top:' . esc_attr( $mja_ads_margin_top ) . 'px; margin-right:' . esc_attr( $mja_ads_margin_right ) . 'px; margin-bottom:' . esc_attr( $mja_ads_margin_bottom ) . 'px;margin-left:' . esc_attr( $mja_ads_margin_left ) . 'px; " id="' . esc_attr( $mja_ads_container_id ) . '">';
    } else {
      echo '<div class="wb-ads-rotator rich-content-ads ' . esc_attr( $mja_ads_container_class ) . '" style="background-color:' . esc_attr( $mja_ads_bg_color ) . ';color:' . esc_attr( $mja_ads_txt_color ) . ';font-size:' . esc_attr( $mja_ads_font_size ) . 'px;text-align:' . esc_attr( $mja_ads_position ) . ';margin-top:' . esc_attr( $mja_ads_margin_top ) . 'px; margin-right:' . esc_attr( $mja_ads_margin_right ) . 'px; margin-bottom:' . esc_attr( $mja_ads_margin_bottom ) . 'px;margin-left:' . esc_attr( $mja_ads_margin_left ) . 'px; " id="' . esc_attr( $mja_ads_container_id ) . '">';
    }
    echo wp_kses( apply_filters( 'mja_ads_rich_content_ads_content', $mja_ads_get_rich_content ), $allowedposttags );

    echo '</div>';
    if ( 'yes' === $mja_ads_clearfix ) {
      echo '<br style="clear: both; display: block; float: none;">';
    }
    if ( 'yes' === $mja_ads_clearfix ) {
      echo '<br style="clear: both; display: block; float: none;">';
    }
  }
  public function mja_ads_display_image_ads( $mja_ads_get_ad ) {
    $mja_ads_get_values = get_post_meta( $mja_ads_get_ad, 'mja_ads_values', true );
    $mja_ads_enable     = get_post_meta( $mja_ads_get_ad, 'mja_ads_enable', true );
    $mja_ads_position   = isset( $mja_ads_get_values['ads_position'] ) ? $mja_ads_get_values['ads_position'] : '';
    if ( 'disable' === $mja_ads_enable ) {
      return;
    }
    $mja_ads_position = '';
    switch ( $mja_ads_position ) {
      case 'default':
        $mja_ads_position = 'initial';
        break;
      case 'left':
        $mja_ads_position = '-webkit-left';
        break;
      case 'center':
        $mja_ads_position = '-webkit-center';
        break;
      case 'right':
        $mja_ads_position = '-webkit-right';
        break;
    }

    $mja_ads_get_image_id    = isset( $mja_ads_get_values['ads_image'] ) ? $mja_ads_get_values['ads_image'] : '';
    $mja_ads_get_image_link  = isset( $mja_ads_get_values['image_link'] ) ? $mja_ads_get_values['image_link'] : '';
    $mja_ads_container_class = isset( $mja_ads_get_values['container_classes'] ) ? $mja_ads_get_values['container_classes'] : '';
    $mja_ads_container_id    = isset( $mja_ads_get_values['container_id'] ) ? $mja_ads_get_values['container_id'] : '';
    $mja_ads_visitor_view            = isset( $mja_ads_get_values['to_whom']['logged_in_visitor'] ) ? $mja_ads_get_values['to_whom']['logged_in_visitor'] : '';
    $mja_ads_visitor_device          = isset( $mja_ads_get_values['to_whom']['device'] ) ? $mja_ads_get_values['to_whom']['device'] : '';
    $mja_ads_image_width     = isset( $mja_ads_get_values['size']['width'] ) ? $mja_ads_get_values['size']['width'] : '';
    $mja_ads_image_height    = isset( $mja_ads_get_values['size']['height'] ) ? $mja_ads_get_values['size']['height'] : '';
    $mja_ads_margin_top      = isset( $mja_ads_get_values['margin']['top'] ) ? $mja_ads_get_values['margin']['top'] : '';
    $mja_ads_margin_right    = isset( $mja_ads_get_values['margin']['right'] ) ? $mja_ads_get_values['margin']['right'] : '';
    $mja_ads_margin_bottom   = isset( $mja_ads_get_values['margin']['bottom'] ) ? $mja_ads_get_values['margin']['bottom'] : '';
    $mja_ads_margin_left     = isset( $mja_ads_get_values['margin']['left'] ) ? $mja_ads_get_values['margin']['left'] : '';
    $mja_ads_clearfix        = isset( $mja_ads_get_values['clearfix'] ) ? $mja_ads_get_values['clearfix'] : '';

    $mja_ads_get_ads_img_src = wp_get_attachment_url( $mja_ads_get_image_id );
    if ( empty( $mja_ads_container_id ) ) {
      $mja_ads_container_id = 'mja_ads_' . $mja_ads_get_ad;
    }
    if ( ! is_user_logged_in() && 'login_in' === $mja_ads_visitor_view ) {
      return;
    } elseif ( is_user_logged_in() && 'logout_out' === $mja_ads_visitor_view ) {
      return;
    }

    if ( 'desktop' === $mja_ads_visitor_device ) {
      if ( wp_is_mobile() ) {
        return;
      }
    } elseif ( 'mobile' === $mja_ads_visitor_device ) {
      if ( ! wp_is_mobile() ) {
        return;
      }
    }
    echo '<div class="wb-ads-rotator image-ads ' . esc_attr( $mja_ads_container_class ) . '" style=" text-align:' . esc_attr( $mja_ads_position ) . ';margin-top:' . esc_attr( $mja_ads_margin_top ) . 'px; margin-right:' . esc_attr( $mja_ads_margin_right ) . 'px; margin-bottom:' . esc_attr( $mja_ads_margin_bottom ) . 'px;margin-left:' . esc_attr( $mja_ads_margin_left ) . 'px; " id="' . esc_attr( $mja_ads_container_id ) . '">';
    echo '<a href="' . esc_url( $mja_ads_get_image_link ) . '" target="blank">';
    echo '<img src=" ' . esc_url( $mja_ads_get_ads_img_src ) . '" width="' . esc_attr( $mja_ads_image_width ) . '" height="' . esc_attr( $mja_ads_image_height ) . '">';
    echo '</a>';
    echo '</div>';
    if ( 'yes' === $mja_ads_clearfix ) {
      echo '<br style="clear: both; display: block; float: none;">';
    }
  }
  public function mja_ads_shortcode_ads( $atts ) {
    // Attributes.
    $atts = shortcode_atts(
      array(
        'id' => null,
      ),
      $atts,
      MJA_ADS_NAME
    );

    $mja_ads_display = '';
    if ( empty( $atts['id'] ) ) {
      $mja_ads_display .= __( 'Please Enter the id parameter value', "buddypress-advertising" );
      return $mja_ads_display;
    }
    ob_start();
    $args = array(
      'post_type'      => MJA_ADS_NAME,
      'posts_per_page' => 1,
      'publish_status' => 'active',
      'p'              => $atts['id'],
    );

    $query = mja_get_wp_query( $args );
    // $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) :

        $query->the_post();
        $id        = get_the_ID();
        $mja_ads_values = get_post_meta( $id, 'mja_ads_values', true );
        $mja_ads_type   = isset( $mja_ads_values['ads_type'] ) ? $mja_ads_values['ads_type'] : '';
        $mja_ads_type   = get_post_meta( $id, 'mja_ads_values', true );
        if ( 'rich-content' === $mja_ads_type ) {
          $mja_ads_display = $this->mja_ads_display_rich_content_ads( $id );
        } elseif ( 'plain-text-and-code' === $mja_ads_type ) {
          $mja_ads_display = $this->mja_ads_display_plain_text_and_code_ads( $id );
        } else {
          $mja_ads_display = $this->mja_ads_display_image_ads( $id );
        }

      endwhile;
    } else {
      return __( 'Nothing ads found', "buddypress-advertising" );
    }

    wp_reset_postdata();
    $mja_ads_display = ob_get_clean();
    return $mja_ads_display;
  }
  public function mja_ads_check_ads() {
    if ( 'nouveau' === bp_get_theme_compat_id() ) {
      $args = array(
        'post_type' => MJA_ADS_NAME,
        'fields'    => 'ids',
      );

      $mja_ads_get_ads = get_posts( $args );
      echo '<div id="rich-ads">';
      foreach ( $mja_ads_get_ads as $mja_ads_get_ad ) {
        $mja_ads_type = get_post_meta( $mja_ads_get_ad, 'mja_ads_values', true );
        switch ( $mja_ads_type['ads_type'] ) {
          case 'plain-text-and-code':
            $this->mja_ads_display_plain_text_and_code_ads( $mja_ads_get_ad );
            break;
          case 'rich-content':
            $this->mja_ads_display_rich_content_ads( $mja_ads_get_ad );
            break;
        }
      }
      echo '</div>';
    }
  }
}
new Mja_Show_Ads();
