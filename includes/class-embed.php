<?php
if ( ! defined( 'ABSPATH' ) ) {exit;}
class MJA_VideoEmbed {

  private static $provider_match_masks = [
		'youtube' => '/^.*(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/',
		'vimeo' => '/^.*vimeo\.com\/(?:[a-z]*\/)*([‌​0-9]{6,11})[?]?.*/',
		'dailymotion' => '/^.*dailymotion.com\/(?:video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/',
	];
	
	private static $embed_patterns = [
		'youtube' => 'https://www.youtube{NO_COOKIE}.com/embed/{VIDEO_ID}?feature=oembed',
		'vimeo' => 'https://player.vimeo.com/video/{VIDEO_ID}#t={TIME}',
		'dailymotion' => 'https://dailymotion.com/embed/video/{VIDEO_ID}',
	];

	public static function get_video_properties( $video_url ) {
		foreach ( self::$provider_match_masks as $provider => $match_mask ) {
			preg_match( $match_mask, $video_url, $matches );

			if ( $matches ) {
				return [
					'provider' => $provider,
					'video_id' => $matches[1],
				];
			}
		}

		return null;
	}

	public static function get_embed_url( $video_url, array $embed_url_params = [], array $options = [] ) {
		$video_properties = self::get_video_properties( $video_url );

		if ( ! $video_properties ) {
			return null;
		}

		$embed_pattern = self::$embed_patterns[ $video_properties['provider'] ];

		$replacements = [
			'{VIDEO_ID}' => $video_properties['video_id'],
		];

		if ( 'youtube' === $video_properties['provider'] ) {
			$replacements['{NO_COOKIE}'] = ! empty( $options['privacy'] ) ? '-nocookie' : '';
		} elseif ( 'vimeo' === $video_properties['provider'] ) {
			$time_text = '';

			if ( ! empty( $options['start'] ) ) {
				$time_text = date( 'H\hi\ms\s', $options['start'] );
			}

			$replacements['{TIME}'] = $time_text;

			$h_param = [];
			preg_match( '/(?|(?:[\?|\&]h={1})([\w]+)|\d\/([\w]+))/', $video_url, $h_param );

			if ( ! empty( $h_param ) ) {
				$embed_url_params['h'] = $h_param[1];
			}
		}

		$embed_pattern = str_replace( array_keys( $replacements ), $replacements, $embed_pattern );

		return add_query_arg( $embed_url_params, $embed_pattern );
	}

	/*
		<iframe id="myIFrame" src="thispage.html"
			width="100%" height="600"
			frameBorder="2">
		</iframe>
		// Get the iframe
		const iFrame = document.getElementById('myIFrame');
		// Let's say that you want to access a button with the ID `'myButton'`,
		// you can access via the following code:
		const buttonInIFrame = iFrame.contentWindow.document.getElementById('myButton');
		// If you need to call a function in the iframe, you can call it as follows:
		iFrame.contentWindow.yourFunction();
	*/
	/*
			Docs https://developers.google.com/youtube/player_parameters
			https://www.youtube.com/embed?listType=playlist&list=PLAYLIST_ID . Note that you need to prepend the playlist ID with the letters PL as shown in the following example: https://www.youtube.com/embed?listType=playlist&list=PLC77007E23FF423C6
			https://www.youtube.com/embed?listType=user_uploads&list=USERNAME

	*/
	public static function get_embed_html( $video_url, array $embed_url_params = [], array $options = [], array $frame_attributes = [] ) {
		$video_properties = self::get_video_properties( $video_url );

		$default_frame_attributes = [
			'class' => 'mja_ads-video-iframe',
			'allowfullscreen',
			'title' => sprintf(
				/* translators: %s: Video provider */
				__( '%s Video Player', "buddypress-advertising" ),
				$video_properties['provider']
			),
		];

		$video_embed_url = self::get_embed_url( $video_url, $embed_url_params, $options );
		if ( ! $video_embed_url ) {
			return null;
		}
		if ( ! $options['lazy_load'] ) {
			$default_frame_attributes['src'] = $video_embed_url;
		} else {
			$default_frame_attributes['data-lazy-load'] = $video_embed_url;
		}

		$frame_attributes = array_merge( $default_frame_attributes, $frame_attributes );

		$attributes_for_print = [];

		foreach ( $frame_attributes as $attribute_key => $attribute_value ) {
			$attribute_value = esc_attr( $attribute_value );

			if ( is_numeric( $attribute_key ) ) {
				$attributes_for_print[] = $attribute_value;
			} else {
				$attributes_for_print[] = sprintf( '%1$s="%2$s"', $attribute_key, $attribute_value );
			}
		}

		$attributes_for_print = implode( ' ', $attributes_for_print );

		$iframe_html = "<iframe $attributes_for_print></iframe>";

		/** This filter is documented in wp-includes/class-oembed.php */
		return apply_filters( 'oembed_result', $iframe_html, $video_url, $frame_attributes );
	}

	public static function get_oembed_data( $oembed_url, $cached_post_id ) {
		$cached_oembed_data = json_decode( get_post_meta( $cached_post_id, '_mja_ads_oembed_cache', true ), true );

		if ( isset( $cached_oembed_data[ $oembed_url ] ) ) {
			return $cached_oembed_data[ $oembed_url ];
		}

		$normalize_oembed_data = self::fetch_oembed_data( $oembed_url );

		if ( ! $cached_oembed_data ) {
			$cached_oembed_data = [];
		}

		update_post_meta( $cached_post_id, '_mja_ads_oembed_cache', wp_json_encode( array_merge(
			$cached_oembed_data,
			[
				$oembed_url => $normalize_oembed_data,
			]
		) ) );

		return $normalize_oembed_data;
	}

	public static function fetch_oembed_data( $oembed_url ) {
		$oembed_data = _wp_oembed_get_object()->get_data( $oembed_url );

		if ( ! $oembed_data ) {
			return null;
		}

		return [
			'thumbnail_url' => $oembed_data->thumbnail_url,
			'title' => $oembed_data->title,
		];
	}

	public static function get_embed_thumbnail_html( $oembed_url, $cached_post_id = null ) {
		$oembed_data = self::get_oembed_data( $oembed_url, $cached_post_id );

		if ( ! $oembed_data ) {
			return null;
		}

		return '<div class="mja_ads-image">' . sprintf( '<img src="%1$s" alt="%2$s" title="%2$s" width="%3$s" />', $oembed_data['thumbnail_url'], esc_attr( $oembed_data['title'] ), '100%' ) . '</div>';
	}
}