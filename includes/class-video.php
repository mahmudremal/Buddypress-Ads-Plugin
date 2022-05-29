<?php
if ( ! defined( 'ABSPATH' ) ) {exit;}

class Widget_Video extends Widget_Base {

	public function get_name() {
		return 'video';
	}

	public function get_title() {
		return esc_html__( 'Video', "buddypress-advertising" );
	}

	public function get_icon() {
		return 'eicon-youtube';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'video', 'player', 'embed', 'youtube', 'vimeo', 'dailymotion' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_video',
			[
				'label' => esc_html__( 'Video', "buddypress-advertising" ),
			]
		);

		$this->add_control(
			'video_type',
			[
				'label' => esc_html__( 'Source', "buddypress-advertising" ),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => esc_html__( 'YouTube', "buddypress-advertising" ),
					'vimeo' => esc_html__( 'Vimeo', "buddypress-advertising" ),
					'dailymotion' => esc_html__( 'Dailymotion', "buddypress-advertising" ),
					'hosted' => esc_html__( 'Self Hosted', "buddypress-advertising" ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'youtube_url',
			[
				'label' => esc_html__( 'Link', "buddypress-advertising" ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', "buddypress-advertising" ) . ' (YouTube)',
				'default' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				'label_block' => true,
				'condition' => [
					'video_type' => 'youtube',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'vimeo_url',
			[
				'label' => esc_html__( 'Link', "buddypress-advertising" ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', "buddypress-advertising" ) . ' (Vimeo)',
				'default' => 'https://vimeo.com/235215203',
				'label_block' => true,
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'dailymotion_url',
			[
				'label' => esc_html__( 'Link', "buddypress-advertising" ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => esc_html__( 'Enter your URL', "buddypress-advertising" ) . ' (Dailymotion)',
				'default' => 'https://www.dailymotion.com/video/x6tqhqb',
				'label_block' => true,
				'condition' => [
					'video_type' => 'dailymotion',
				],
			]
		);

		$this->add_control(
			'insert_url',
			[
				'label' => esc_html__( 'External URL', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'hosted_url',
			[
				'label' => esc_html__( 'Choose File', "buddypress-advertising" ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type' => 'video',
				'condition' => [
					'video_type' => 'hosted',
					'insert_url' => '',
				],
			]
		);

		$this->add_control(
			'external_url',
			[
				'label' => esc_html__( 'URL', "buddypress-advertising" ),
				'type' => Controls_Manager::URL,
				'autocomplete' => false,
				'options' => false,
				'label_block' => true,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'media_type' => 'video',
				'placeholder' => esc_html__( 'Enter your URL', "buddypress-advertising" ),
				'condition' => [
					'video_type' => 'hosted',
					'insert_url' => 'yes',
				],
			]
		);

		$this->add_control(
			'start',
			[
				'label' => esc_html__( 'Start Time', "buddypress-advertising" ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Specify a start time (in seconds)', "buddypress-advertising" ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'end',
			[
				'label' => esc_html__( 'End Time', "buddypress-advertising" ),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Specify an end time (in seconds)', "buddypress-advertising" ),
				'condition' => [
					'video_type' => [ 'youtube', 'hosted' ],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'video_options',
			[
				'label' => esc_html__( 'Video Options', "buddypress-advertising" ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__( 'Autoplay', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'show_image_overlay',
							'value' => '',
						],
						[
							'name' => 'image_overlay[url]',
							'value' => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'play_on_mobile',
			[
				'label' => esc_html__( 'Play On Mobile', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'mute',
			[
				'label' => esc_html__( 'Mute', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type!' => 'dailymotion',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'controls',
			[
				'label' => esc_html__( 'Player Controls', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'default' => 'yes',
				'condition' => [
					'video_type!' => 'vimeo',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'showinfo',
			[
				'label' => esc_html__( 'Video Info', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'default' => 'yes',
				'condition' => [
					'video_type' => [ 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'modestbranding',
			[
				'label' => esc_html__( 'Modest Branding', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'video_type' => [ 'youtube' ],
					'controls' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'logo',
			[
				'label' => esc_html__( 'Logo', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'default' => 'yes',
				'condition' => [
					'video_type' => [ 'dailymotion' ],
				],
			]
		);

		// YouTube.
		$this->add_control(
			'yt_privacy',
			[
				'label' => esc_html__( 'Privacy Mode', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', "buddypress-advertising" ),
				'condition' => [
					'video_type' => 'youtube',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'lazy_load',
			[
				'label' => esc_html__( 'Lazy Load', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'video_type',
							'operator' => '===',
							'value' => 'youtube',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'show_image_overlay',
									'operator' => '===',
									'value' => 'yes',
								],
								[
									'name' => 'video_type',
									'operator' => '!==',
									'value' => 'hosted',
								],
							],
						],
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'rel',
			[
				'label' => esc_html__( 'Suggested Videos', "buddypress-advertising" ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Current Video Channel', "buddypress-advertising" ),
					'yes' => esc_html__( 'Any Video', "buddypress-advertising" ),
				],
				'condition' => [
					'video_type' => 'youtube',
				],
			]
		);

		// Vimeo.
		$this->add_control(
			'vimeo_title',
			[
				'label' => esc_html__( 'Intro Title', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_portrait',
			[
				'label' => esc_html__( 'Intro Portrait', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_byline',
			[
				'label' => esc_html__( 'Intro Byline', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'default' => 'yes',
				'condition' => [
					'video_type' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Controls Color', "buddypress-advertising" ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'video_type' => [ 'vimeo', 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'download_button',
			[
				'label' => esc_html__( 'Download Button', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'poster',
			[
				'label' => esc_html__( 'Poster', "buddypress-advertising" ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'video_type' => 'hosted',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'View', "buddypress-advertising" ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'youtube',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_overlay',
			[
				'label' => esc_html__( 'Image Overlay', "buddypress-advertising" ),
			]
		);

		$this->add_control(
			'show_image_overlay',
			[
				'label' => esc_html__( 'Image Overlay', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', "buddypress-advertising" ),
				'label_on' => esc_html__( 'Show', "buddypress-advertising" ),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'image_overlay',
			[
				'label' => esc_html__( 'Choose Image', "buddypress-advertising" ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_image_overlay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_overlay', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_overlay_size` and `image_overlay_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'show_image_overlay' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_play_icon',
			[
				'label' => esc_html__( 'Play Icon', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
			]
		);

		$this->add_control(
			'lightbox',
			[
				'label' => esc_html__( 'Lightbox', "buddypress-advertising" ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'label_off' => esc_html__( 'Off', "buddypress-advertising" ),
				'label_on' => esc_html__( 'On', "buddypress-advertising" ),
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_style',
			[
				'label' => esc_html__( 'Video', "buddypress-advertising" ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Aspect Ratio', "buddypress-advertising" ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'169' => '16:9',
					'219' => '21:9',
					'43' => '4:3',
					'32' => '3:2',
					'11' => '1:1',
					'916' => '9:16',
				],
				'default' => '169',
				'prefix_class' => 'plugin-name-aspect-ratio-',
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .plugin-name-wrapper',
			]
		);

		$this->add_control(
			'play_icon_title',
			[
				'label' => esc_html__( 'Play Icon', "buddypress-advertising" ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'play_icon_color',
			[
				'label' => esc_html__( 'Color', "buddypress-advertising" ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plugin-name-custom-embed-play i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plugin-name-custom-embed-play svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'play_icon_size',
			[
				'label' => esc_html__( 'Size', "buddypress-advertising" ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 300,
					],
				],
				'selectors' => [
					// Not using a CSS vars because the default size value is coming from a global scss file.
					'{{WRAPPER}} .plugin-name-custom-embed-play i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .plugin-name-custom-embed-play svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'play_icon_text_shadow',
				'selector' => '{{WRAPPER}} .plugin-name-custom-embed-play i',
				'fields_options' => [
					'text_shadow_type' => [
						'label' => _x( 'Shadow', 'Text Shadow Control', "buddypress-advertising" ),
					],
				],
				'condition' => [
					'show_image_overlay' => 'yes',
					'show_play_icon' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			[
				'label' => esc_html__( 'Lightbox', "buddypress-advertising" ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image_overlay' => 'yes',
					'image_overlay[url]!' => '',
					'lightbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'lightbox_color',
			[
				'label' => esc_html__( 'Background Color', "buddypress-advertising" ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#plugin-name-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color',
			[
				'label' => esc_html__( 'UI Color', "buddypress-advertising" ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#plugin-name-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
					'#plugin-name-lightbox-{{ID}} .dialog-lightbox-close-button svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'lightbox_ui_color_hover',
			[
				'label' => esc_html__( 'UI Hover Color', "buddypress-advertising" ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'#plugin-name-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
					'#plugin-name-lightbox-{{ID}} .dialog-lightbox-close-button:hover svg' => 'fill: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'lightbox_video_width',
			[
				'label' => esc_html__( 'Content Width', "buddypress-advertising" ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 30,
					],
				],
				'selectors' => [
					'(desktop+)#plugin-name-lightbox-{{ID}} .plugin-name-video-container' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'lightbox_content_position',
			[
				'label' => esc_html__( 'Content Position', "buddypress-advertising" ),
				'type' => Controls_Manager::SELECT,
				'frontend_available' => true,
				'options' => [
					'' => esc_html__( 'Center', "buddypress-advertising" ),
					'top' => esc_html__( 'Top', "buddypress-advertising" ),
				],
				'selectors' => [
					'#plugin-name-lightbox-{{ID}} .plugin-name-video-container' => '{{VALUE}}; transform: translateX(-50%);',
				],
				'selectors_dictionary' => [
					'top' => 'top: 60px',
				],
			]
		);

		$this->add_responsive_control(
			'lightbox_content_animation',
			[
				'label' => esc_html__( 'Entrance Animation', "buddypress-advertising" ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	public function print_a11y_text( $image_overlay ) {
		if ( empty( $image_overlay['alt'] ) ) {
			echo esc_html__( 'Play Video', "buddypress-advertising" );
		} else {
			echo esc_html__( 'Play Video about', "buddypress-advertising" ) . ' ' . esc_attr( $image_overlay['alt'] );
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_url = $settings[ $settings['video_type'] . '_url' ];

		if ( 'hosted' === $settings['video_type'] ) {
			$video_url = $this->get_hosted_video_url();
		} else {
			$embed_params = $this->get_embed_params();
			$embed_options = $this->get_embed_options();
		}

		if ( empty( $video_url ) ) {
			return;
		}

		if ( 'youtube' === $settings['video_type'] ) {
			$video_html = '<div class="mja_ads-video"></div>';
		}

		if ( 'hosted' === $settings['video_type'] ) {
			$this->add_render_attribute( 'video-wrapper', 'class', 'e-hosted-video' );

			ob_start();

			$this->render_hosted_video();

			$video_html = ob_get_clean();
		} else {
			$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();
			$post_id = get_queried_object_id();

			if ( $is_static_render_mode ) {
				$video_html = Embed::get_embed_thumbnail_html( $video_url, $post_id );
				// YouTube API requires a different markup which was set above.
			} else if ( 'youtube' !== $settings['video_type'] ) {
				$video_html = Embed::get_embed_html( $video_url, $embed_params, $embed_options );
			}
		}

		if ( empty( $video_html ) ) {
			echo esc_url( $video_url );

			return;
		}

		$this->add_render_attribute( 'video-wrapper', 'class', 'mja_ads-wrapper' );

		if ( ! $settings['lightbox'] ) {
			$this->add_render_attribute( 'video-wrapper', 'class', 'mja_ads-fit-aspect-ratio' );
		}

		$this->add_render_attribute( 'video-wrapper', 'class', 'mja_ads-open-' . ( $settings['lightbox'] ? 'lightbox' : 'inline' ) );
		?>
		<div <?php $this->print_render_attribute_string( 'video-wrapper' ); ?>>
			<?php
			if ( ! $settings['lightbox'] ) {
				Utils::print_unescaped_internal_string( $video_html ); // XSS ok.
			}

			if ( $this->has_image_overlay() ) {
				$this->add_render_attribute( 'image-overlay', 'class', 'mja_ads-custom-embed-image-overlay' );

				if ( $settings['lightbox'] ) {
					if ( 'hosted' === $settings['video_type'] ) {
						$lightbox_url = $video_url;
					} else {
						$lightbox_url = Embed::get_embed_url( $video_url, $embed_params, $embed_options );
					}

					$lightbox_options = [
						'type' => 'video',
						'videoType' => $settings['video_type'],
						'url' => $lightbox_url,
						'modalOptions' => [
							'id' => 'mja_ads-lightbox-' . $this->get_id(),
							'entranceAnimation' => $settings['lightbox_content_animation'],
							'entranceAnimation_tablet' => $settings['lightbox_content_animation_tablet'],
							'entranceAnimation_mobile' => $settings['lightbox_content_animation_mobile'],
							'videoAspectRatio' => $settings['aspect_ratio'],
						],
					];

					if ( 'hosted' === $settings['video_type'] ) {
						$lightbox_options['videoParams'] = $this->get_hosted_params();
					}

					$this->add_render_attribute( 'image-overlay', [
						'data-mja_ads-open-lightbox' => 'yes',
						'data-mja_ads-lightbox' => wp_json_encode( $lightbox_options ),
						'e-action-hash' => Plugin::instance()->frontend->create_action_hash( 'lightbox', $lightbox_options ),
					] );

					if ( Plugin::$instance->editor->is_edit_mode() ) {
						$this->add_render_attribute( 'image-overlay', [
							'class' => 'mja_ads-clickable',
						] );
					}
				} else {
					// When there is an image URL but no ID, it means the overlay image is the placeholder. In this case, get the placeholder URL.
					if ( empty( $settings['image_overlay']['id'] && ! empty( $settings['image_overlay']['url'] ) ) ) {
						$image_url = $settings['image_overlay']['url'];
					} else {
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $settings['image_overlay']['id'], 'image_overlay', $settings );
					}

					$this->add_render_attribute( 'image-overlay', 'style', 'background-image: url(' . $image_url . ');' );
				}
				?>
				<div <?php $this->print_render_attribute_string( 'image-overlay' ); ?>>
					<?php if ( $settings['lightbox'] ) : ?>
						<?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'image_overlay' ); ?>
					<?php endif; ?>
					<?php if ( 'yes' === $settings['show_play_icon'] ) : ?>
						<div class="mja_ads-custom-embed-play" role="button" aria-label="<?php $this->print_a11y_text( $settings['image_overlay'] ); ?>" tabindex="0">
							<?php
								Icons_Manager::render_icon( [
									'library' => 'eicons',
									'value' => 'eicon-play',
								], [ 'aria-hidden' => 'true' ] );
							?>
							<span class="mja_ads-screen-only"><?php $this->print_a11y_text( $settings['image_overlay'] ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	public function render_plain_content() {
		$settings = $this->get_settings_for_display();

		if ( 'hosted' !== $settings['video_type'] ) {
			$url = $settings[ $settings['video_type'] . '_url' ];
		} else {
			$url = $this->get_hosted_video_url();
		}

		echo esc_url( $url );
	}

	public function get_embed_params() {
		$settings = $this->get_settings_for_display();

		$params = [];

		if ( $settings['autoplay'] && ! $this->has_image_overlay() ) {
			$params['autoplay'] = '1';

			if ( $settings['play_on_mobile'] ) {
				$params['playsinline'] = '1';
			}
		}

		$params_dictionary = [];

		if ( 'youtube' === $settings['video_type'] ) {
			$params_dictionary = [
				'loop',
				'controls',
				'mute',
				'rel',
				'modestbranding',
			];

			if ( $settings['loop'] ) {
				$video_properties = Embed::get_video_properties( $settings['youtube_url'] );

				$params['playlist'] = $video_properties['video_id'];
			}

			$params['start'] = $settings['start'];

			$params['end'] = $settings['end'];

			$params['wmode'] = 'opaque';
		} elseif ( 'vimeo' === $settings['video_type'] ) {
			$params_dictionary = [
				'loop',
				'mute' => 'muted',
				'vimeo_title' => 'title',
				'vimeo_portrait' => 'portrait',
				'vimeo_byline' => 'byline',
			];

			$params['color'] = str_replace( '#', '', $settings['color'] );

			$params['autopause'] = '0';
		} elseif ( 'dailymotion' === $settings['video_type'] ) {
			$params_dictionary = [
				'controls',
				'mute',
				'showinfo' => 'ui-start-screen-info',
				'logo' => 'ui-logo',
			];

			$params['ui-highlight'] = str_replace( '#', '', $settings['color'] );

			$params['start'] = $settings['start'];

			$params['endscreen-enable'] = '0';
		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = $settings[ $setting_name ] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}

	protected function has_image_overlay() {
		$settings = $this->get_settings_for_display();

		return ! empty( $settings['image_overlay']['url'] ) && 'yes' === $settings['show_image_overlay'];
	}

	private function get_embed_options() {
		$settings = $this->get_settings_for_display();

		$embed_options = [];

		if ( 'youtube' === $settings['video_type'] ) {
			$embed_options['privacy'] = $settings['yt_privacy'];
		} elseif ( 'vimeo' === $settings['video_type'] ) {
			$embed_options['start'] = $settings['start'];
		}

		$embed_options['lazy_load'] = ! empty( $settings['lazy_load'] );

		return $embed_options;
	}

	private function get_hosted_params() {
		$settings = $this->get_settings_for_display();

		$video_params = [];

		foreach ( [ 'autoplay', 'loop', 'controls' ] as $option_name ) {
			if ( $settings[ $option_name ] ) {
				$video_params[ $option_name ] = '';
			}
		}

		if ( $settings['mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( $settings['play_on_mobile'] ) {
			$video_params['playsinline'] = '';
		}

		if ( ! $settings['download_button'] ) {
			$video_params['controlsList'] = 'nodownload';
		}

		if ( $settings['poster']['url'] ) {
			$video_params['poster'] = $settings['poster']['url'];
		}

		return $video_params;
	}

	private function get_hosted_video_url() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['insert_url'] ) ) {
			$video_url = $settings['external_url']['url'];
		} else {
			$video_url = $settings['hosted_url']['url'];
		}

		if ( empty( $video_url ) ) {
			return '';
		}

		if ( $settings['start'] || $settings['end'] ) {
			$video_url .= '#t=';
		}

		if ( $settings['start'] ) {
			$video_url .= $settings['start'];
		}

		if ( $settings['end'] ) {
			$video_url .= ',' . $settings['end'];
		}

		return $video_url;
	}

	private function render_hosted_video() {
		$video_url = $this->get_hosted_video_url();
		if ( empty( $video_url ) ) {
			return;
		}

		$video_params = $this->get_hosted_params();
		?>
		<video class="mja_ads-video" src="<?php echo esc_attr( $video_url ); ?>" <?php Utils::print_html_attributes( $video_params ); ?>></video>
		<?php
	}
}
