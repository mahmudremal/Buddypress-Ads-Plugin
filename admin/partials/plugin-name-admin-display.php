<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<main class="main">
    <div class="container">
        <h1 class="heading">Settings</h1>
      <?php
/**
 * Admin Customization Page Template
 * 
 * @package MujAh.
 */
$argv = [
	'args' => $args
];
?>

<?php
/**
 * Blog Tab content for settings pages.
 * 
 * @package MujAh.
 */


$option = (array)get_option( 'buddy_ads_setting', [] );
if( ! function_exists( 'mja_options_for_multy_currencies' ) ) {
    function mja_options_for_multy_currencies() {
        $arr = [];
        foreach( MJA_ADS_CURRENCY as $a => $v ) {
            $arr[] = [
                'label' => __( $v, "buddypress-advertising" ),
                'value' => $a
            ];
        }
        return $arr;
    }
}
$argv = [
  [
      'name' => 'ads_every_loop',
      'id' => 'ads_every_loop',
      'title' => __( 'Ads. Position', "buddypress-advertising" ),
      'desc' => __( 'Ad will appere after ( How many? ) post except loop.', "buddypress-advertising" ),
      'type' => 'number',
      'default' => 3,
      'option' => []
  ],
  [
    'name' => 'multi_currency',
    'id' => 'multi_currency',
    'title' => __( 'Choose Currency', "buddypress-advertising" ),
    'desc' => '',
    'type' => 'select',
    'default' => 3,
    'option' => mja_options_for_multy_currencies(),
  ],
  [
    'name' => 'price',
    'id' => 'set_price',
    'title' => __( 'Set prices', "buddypress-advertising" ),
    'desc' => '',
    'type' => 'select',
    'default' => 3,
    'option' => [
      [
          'label' => __( 'Only activity feed & One week', "buddypress-advertising" ),
          'value' => 'activity-1-week',
      ],
      [
          'label' => __( 'Only activity feed & Two weeks', "buddypress-advertising" ),
          'value' => 'activity-2-week',
      ],
      [
          'label' => __( 'Only activity feed & One month', "buddypress-advertising" ),
          'value' => 'activity-1-month',
      ],
      [
          'label' => __( 'activity and featured feed & duration One week', "buddypress-advertising" ),
          'value' => 'featured-1-week',
      ],
      [
          'label' => __( 'activity and featured feed & duration Two week', "buddypress-advertising" ),
          'value' => 'featured-1-week',
      ],
      [
          'label' => __( 'activity and featured feed & duration One week', "buddypress-advertising" ),
          'value' => 'featured-2-week',
      ],
      [
          'label' => __( 'activity and featured feed & duration One month', "buddypress-advertising" ),
          'value' => 'featured-1-month',
      ],
    ]
  ]
];
?>

    <div id="mujah-options-blog">
        <div class="wrap">
            <form method="post" action="<?php echo admin_url('options.php'); ?>">
                <?php settings_fields( 'buddy_ads_setting' ); ?>
                <?php do_settings_sections( 'buddy_ads_setting' ); ?>

                <!-- <h1 class="available-options">
                    <?php _e('Blogs Settings', "MUJAH_TEXT_DOMAIN" ); ?>
                </h1> -->
                
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
                                <label for="<?php echo $row['id']; ?>0" title="<?php _e( $row['desc'], "MUJAH_TEXT_DOMAIN" ); ?>">
                                    <?php _e( $row['title'], "MUJAH_TEXT_DOMAIN" ); ?>
                                </label>
                            </th>
                            <td>
                                <?php
                                if( $row['type'] == 'radio' ) {
                                    foreach( $row['option'] as $i => $opt ) {
                                ?>
                                <input id="<?php echo $row['id'].$i; ?>" type="<?php echo $row['type']; ?>" <?php checked($option[$row['name']], $opt['value']); ?> value="<?php echo $opt['value']; ?>" name="buddy_ads_setting[<?php echo $row['name']; ?>]" />
                                <label for="<?php echo $row['id'].$i; ?>">
                                    <?php _e( $opt['label'], "MUJAH_TEXT_DOMAIN" ); ?>
                                </label>
                                <?php
                                    }
                                } elseif( in_array( $row['type'], ['text','number','date','datetime'] ) ) {
                                ?>
                                <input id="<?php echo $row['id'].$i; ?>" type="<?php echo $row['type']; ?>" value="<?php echo isset($option[$row['name']])?$option[$row['name']]:(isset($row['default'])?$row['default']:''); ?>" class="regular-text" name="buddy_ads_setting[<?php echo $row['name']; ?>]" placeholder="<?php echo isset( $row['placeholder'] ) ? $row['placeholder'] : ''; ?>"/>
                                <?php
                                } elseif( in_array( $row['type'], ['select', 'option'] ) ) {
                                ?>
                                <select name="buddy_ads_setting[<?php echo $row['name']; ?>]" id="<?php echo $row['id'].$i; ?>" class="form-select">
                                    <?php
                                    foreach( $row['option'] as $opt) {
                                        ?>
                                    <option value="<?php echo $opt['value']; ?>" <?php selected( $option[$row['name']], $opt['value'], true ); ?>><?php esc_html_e( $opt['label'], "MUJAH_TEXT_DOMAIN" ); ?></option>
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
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', "MUJAH_TEXT_DOMAIN" ); ?>" />
                </p>
            </form>
        </div>
    </div>








    </div>
</main>

<style>
input[type=text],
input[type=number],
input[type=phone],
input[type=hidden],
input[type=email],
input[type=password],
textarea{}
select{
    width: 100%;
}
</style>
<script>
    
</script>