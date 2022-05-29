<?php
/**
 * Blog Tab content for settings pages.
 * 
 * @package MujAh.
 */

$option = (array)get_option( 'buddy_ads_setting', [] );

$argv = [
    [
        'name' => 'per_columns',
        'id' => 'per_columns',
        'title' => 'Posts Per Column',
        'desc' => '',
        'type' => 'select',
        'default' => 3,
        'option' => [
            [
                'label' => 'One',
                'value' => 1,
            ],
            [
                'label' => 'Two',
                'value' => 2,
            ],
            [
                'label' => 'Three',
                'value' => 3,
            ],
            [
                'label' => 'Four',
                'value' => 4,
            ],
            [
                'label' => 'Five',
                'value' => 5,
            ],
            [
                'label' => 'Six',
                'value' => 6,
            ],
            [
                'label' => 'Seven',
                'value' => 7,
            ],
            [
                'label' => 'Eight',
                'value' => 8,
            ]
        ]
    ],
    [
        'name' => 'columns',
        'id' => 'columns',
        'title' => 'Columns',
        'desc' => '',
        'type' => 'select',
        'default' => 3,
        'option' => [
            [
                'label' => 'One',
                'value' => 1,
            ],
            [
                'label' => 'Two',
                'value' => 2,
            ],
            [
                'label' => 'Three',
                'value' => 3,
            ],
            [
                'label' => 'Four',
                'value' => 4,
            ]
        ]
    ],
    [
        'type' => 'hr',
        'default' => ''
    ],
    [
        'name' => 'view',
        'id' => 'view',
        'title' => 'Display View',
        'desc' => '',
        'type' => 'radio',
        'default' => 'grid',
        'option' => [
            [
                'label' => 'Grid',
                'value' => 'grid',
            ],
            [
                'label' => 'List',
                'value' => 'List',
            ]
        ]
    ],
    [
        'name' => 'class',
        'id' => 'class',
        'title' => 'Class',
        'desc' => 'Post Card class',
        'type' => 'text',
        'default' => '',
        'placeholder' => 'Post Card Classes',
        'option' => []
    ],
    [
        'name' => 'excerpt_limit',
        'id' => 'excerpt_limit',
        'title' => 'Card Excerpt',
        'desc' => 'Posts card excerpt limit (intiger)',
        'type' => 'number',
        'default' => 300,
        'option' => []
    ],
    [
        'name' => 'margin',
        'id' => 'margin',
        'title' => 'Margin',
        'desc' => 'Margin to each card',
        'placeholder' => 'Margin to each card',
        'type' => 'text',
        'default' => 'px px px px',
        'option' => []
    ],
    [
        'name' => 'padding',
        'id' => 'padding',
        'title' => 'padding',
        'desc' => 'Padding to each card',
        'placeholder' => 'Padding to each card',
        'type' => 'text',
        'default' => 'px px px px',
        'option' => []
    ],
    [
        'name' => 'posts_calander_icon',
        'id' => 'posts_calander_icon',
        'title' => 'Posts Calender Icon',
        'desc' => '',
        'type' => 'text',
        'default' => 'fa-calander-o',
        'option' => [
            [
                'label' => '<i class="fa fa-calander-o"></i>Calander-o',
                'value' => 'fa-calander-o',
                'selected' => true
            ],
            [
                'label' => '<i class="fa fa-calander"></i>Calander',
                'value' => 'fa-calander',
            ]
        ]
    ],
    [
        'name' => 'post_comment_icon',
        'id' => 'post_comment_icon',
        'title' => 'Posts Comment Icon',
        'desc' => '',
        'type' => 'select',
        'default' => 'fa-comments-o',
        'option' => [
            [
                'label' => '<i class="fa fa-comments-o"></i>Comments-o',
                'value' => 'fa-comments-o',
                'selected' => true
            ],
            [
                'label' => '<i class="fa fa-comments"></i>Comments',
                'value' => 'fa-comments',
            ]
        ]
    ],
    [
        'name' => 'posts_calander_icon',
        'id' => 'posts_calander_icon',
        'title' => 'Post Calender Icon',
        'desc' => '',
        'type' => 'select',
        'default' => 1,
        'option' => [
            [
                'label' => '<i class="fa fa-calendar"></i>fa-calendar',
                'value' => 'fa-calendar',
            ],
            [
                'label' => '<i class="fa fa-calendar-o"></i>fa-calendar-o',
                'value' => 'fa-calendar-o',
            ]
        ]
    ],
    [
        'name' => 'post_views_icon',
        'id' => 'post_views_icon',
        'title' => 'Views Icon',
        'desc' => '',
        'type' => 'select',
        'default' => 1,
        'option' => [
            [
                'label' => '<i class="fa fa-eye"></i>fa-eye',
                'value' => 'fa-eye',
            ],[
                'label' => '<i class="fa fa-eye-o"></i>fa-eye-o',
                'value' => 'fa-eye-o',
            ],
        ]
    ],
    [
        'name' => 'post_meta_separator',
        'id' => 'post_meta_separator',
        'title' => 'Meta Separator',
        'desc' => '',
        'type' => 'select',
        'default' => 1,
        'option' => [
            [
                'label' => '/',
                'value' => htmlentities( '<span class="mx-1 text-muted">/</span>' ),
            ],
            [
                'label' => '\ ',
                'value' => htmlentities( '<span class="mx-1 text-muted">\</span>' ),
            ],
            [
                'label' => '|',
                'value' => htmlentities( '<span class="mx-1 text-muted">|</span>' ),
            ]
        ]
    ],
    [
        'name' => 'post_date_created',
        'id' => 'post_date_created',
        'title' => 'Created Time',
        'desc' => 'Show or Hide Posts Created Time',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Show',
                'value' => 1,
            ],
            [
                'label' => 'Hide',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'post_date_updated',
        'id' => 'post_date_updated',
        'title' => 'Updated Time',
        'desc' => 'Show or Hide Posts Updated Time',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Show',
                'value' => 1,
            ],
            [
                'label' => 'Hide',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'post_views',
        'id' => 'post_views',
        'title' => 'Enable Views',
        'desc' => 'Enable or disable post viewing counter functionality',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => 1,
            ],
            [
                'label' => 'Disabled',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'post_author',
        'id' => 'post_author',
        'title' => 'Show author',
        'desc' => '',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Show',
                'value' => 1,
            ],
            [
                'label' => 'Hide',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'post_comment',
        'id' => 'post_comment',
        'title' => 'Posts Comments',
        'desc' => '',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Show',
                'value' => 1,
            ],
            [
                'label' => 'Hide',
                'value' => 0,
            ]
        ]
    ],
    [
        'type' => 'hr',
        'default' => ''
    ],
    [
        'name' => 'single_comments',
        'id' => 'single_comments',
        'title' => 'Public Comments',
        'desc' => '',
        'type' => 'radio',
        'default' => true,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => true,
            ],
            [
                'label' => 'Disabled',
                'value' => false,
            ]
        ]
    ],
    [
        'name' => 'post_single_tags',
        'id' => 'post_single_tags',
        'title' => 'Single Tags',
        'desc' => '',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => 1,
            ],
            [
                'label' => 'Disabled',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'post_single_tags_style',
        'id' => 'post_single_tags_style',
        'title' => 'Tags Style',
        'desc' => 'Select one of the tags style that will be on Single posts',
        'type' => 'select',
        'default' => 1,
        'option' => [
            [
                'label' => 'Style 1 (Inline text)',
                'value' => 1,
            ],
            [
                'label' => 'Style 2 (Boxed Button)',
                'value' => 2,
            ],
            [
                'label' => 'Style 3 (Rounded Button)',
                'value' => 3,
            ],
            [
                'label' => 'Style 4 (Overally)',
                'value' => 4,
            ]
        ]
    ],
    [
        'name' => 'single_has_sidebar',
        'id' => 'single_has_sidebar',
        'title' => 'Single Sidebar',
        'desc' => 'Has Sidebar on Single posts',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => 1,
            ],
            [
                'label' => 'Disabled',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'single_loadmore_has',
        'id' => 'single_loadmore_has',
        'title' => 'Enable loadmore addons',
        'desc' => 'Has Loadmore buttons on Single posts',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => 1,
            ],
            [
                'label' => 'Disabled',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'single_loadmore_count',
        'id' => 'single_loadmore_count',
        'title' => 'Load More posts',
        'desc' => 'How many posts will be appere on loadmore pagination?',
        'type' => 'number',
        'default' => 3,
        'placeholder' => 'Total posts loading in each time (intiger).',
        'option' => []
    ],
    [
        'name' => 'single_readmore_count',
        'id' => 'single_readmore_count',
        'title' => 'Read More posts',
        'desc' => 'How many pages will be appere on readmore pagination?',
        'type' => 'number',
        'default' => 3,
        'placeholder' => 'Total pages loading in each time (intiger).',
        'option' => []
    ],
    [
        'name' => 'single_listing_enable',
        'id' => 'single_listing_enable',
        'title' => 'Enable Listing',
        'desc' => 'Enable listing on Single posts',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => 1,
            ],
            [
                'label' => 'Disabled',
                'value' => 0,
            ]
        ]
    ],
    [
        'name' => 'single_readmore_enable',
        'id' => 'single_readmore_enable',
        'title' => 'Enable Readmore',
        'desc' => 'Enable Readmore on Single posts',
        'type' => 'radio',
        'default' => 1,
        'option' => [
            [
                'label' => 'Enabled',
                'value' => 1,
            ],
            [
                'label' => 'Disabled',
                'value' => 0,
            ]
        ]
    ],
];
?>

<div id="mujah-options-blog">
    <div class="wrap">
        <form method="post" action="<?php echo admin_url('options.php'); ?>">
            <?php settings_fields( 'buddy_ads_setting' ); ?>

            <h1 class="available-options">
                <?php _e('Blogs Settings', "buddypress-advertising" ); ?>
            </h1>
            
            <table class="form-table">
                <tbody>
                <?php
                foreach( $argv as $row ) :
                    if( $row['type'] == 'hr' ) :
                        ?>
                        <tr>
                            <td colspan="2">
                                <hr>
                            </td>
                        </tr>
                        <?php
                        continue;
                    endif;
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="<?php echo $row['id']; ?>0" title="<?php _e( $row['desc'], "buddypress-advertising" ); ?>">
                                <?php _e( $row['title'], "buddypress-advertising" ); ?>
                            </label>
                        </th>
                        <td>
                            <?php
                            if( $row['type'] == 'radio' ) {
                                foreach( $row['option'] as $i => $opt ) {
                            ?>
                            <input id="<?php echo $row['id'].$i; ?>" type="<?php echo $row['type']; ?>" <?php checked($option[$row['name']], $opt['value']); ?> value="<?php echo $opt['value']; ?>" name="buddy_ads_setting[<?php echo $row['name']; ?>]" />
                            <label for="<?php echo $row['id'].$i; ?>">
                                <?php _e( $opt['label'], "buddypress-advertising" ); ?>
                            </label>
                            <?php
                                }
                            } elseif( in_array( $row['type'], ['text','number','date','datetime'] ) ) {
                            ?>
                            <input id="<?php echo $row['id'].$i; ?>" type="<?php echo $row['type']; ?>" value="<?php echo $option[$row['name']]; ?>" class="regular-text" name="buddy_ads_setting[<?php echo $row['name']; ?>]" placeholder="<?php echo isset( $row['placeholder'] ) ? $row['placeholder'] : ''; ?>"/>
                            <?php
                            } elseif( in_array( $row['type'], ['select', 'option'] ) ) {
                            ?>
                            <select name="buddy_ads_setting[<?php echo $row['name']; ?>]" id="<?php echo $row['id'].$i; ?>" class="form-select">
                                <?php
                                foreach( $row['option'] as $opt) {
                                    ?>
                                <option value="<?php echo $opt['value']; ?>" <?php selected( $option[$row['name']], $opt['value'], true ); ?>><?php esc_html_e( $opt['label'], "buddypress-advertising" ); ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <?php
                            }else{
                                ?>
                                <textarea name="buddy_ads_setting[<?php echo $row['name']; ?>]" id="<?php echo $row['id'].$i; ?>" placeholder="<?php echo isset( $row['placeholder'] ) ? $row['placeholder'] : ''; ?>" class="form-control" rows="8"><?php echo $option[$row['name']]; ?></textarea>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', "buddypress-advertising" ); ?>" />
            </p>
        </form>
    </div>
</div>
