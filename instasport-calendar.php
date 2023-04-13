<?php
/**
 * Plugin Name: Instasport Calendar
 * Description: Instasport Calendar plugin
 * Version: 2.1.10
 * Author: Instasport
 * Author URI: https://info.instasport.co
 * License: GPLv2 or later versions
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: Instasport
 */

define( 'INSTASPORT_URL', plugins_url( '', __FILE__ ) );
include_once __DIR__ . '/api.php';
include_once __DIR__ . '/admin.php';
include_once __DIR__ . '/ajax.php';
include_once __DIR__ . '/shortcode.php';


/**
 * Возвращает настройки отображения
 * @return array|object|string
 */
function insta_get_options() {
    //delete_option('insta_calendar');exit;
	$defaults = [
		'api_url'                              => 'https://instasport.ua/admin/api',
		'club'                                 => '',
		'key'                                  => '',
		'code'                                 => '',
		'next'                                 => '',
		//COLORS
		'use_api_colors'                       => 1,
		'primaryColor'                         => '#222222',
		'secondaryColor'                       => '#2b2b2b',
		'primaryTextColor'                     => '#ffffff',
		'secondaryTextColor'                   => '#e5fc03',
		//DESKTOP STYLES
		'default_view_to_show'                 => 'month',
		'use_api_colors'                       => 0,
		'additional_info_month_duration'       => '1',
		'additional_info_month_seats'          => '1',
		'additional_info_week_duration'        => '1',
		'additional_info_week_seats'           => '1',
		'desktop_width'                        => '900px',
		'desktop_month_quantity_trainings'     => '2',
		'desktop_week_quantity_trainings'      => '2',
		'desktop_month_more_text'              => ' ...',
		'desktop_week_more_text'               => ' ...',
		'desktop_week_show_empty_rows'         => '1',
		'desktop_filter_train_show'            => '1',
		'desktop_filter_instructor_show'       => '1',
		'desktop_filter_complexity_show'       => '1',
		'desktop_filter_activity_show'         => '1',
		'desktop_nav_filter_font'              => '14px',
		'desktop_event_title_time_font'        => '12px',
		'desktop_event_dur_seats_font'         => '10px',
		'desktop_date_font'                    => '16px',
		'desktop_days_of_week_font'            => '14px',
		'desktop_month_days_numbers_font'      => '12px',
		'desktop_week_hours_font'              => '14px',
		'desktop_filter_list_font'             => '14px',
		//MOBILE STYLES
		'mobile_additional_info_week_duration' => '1',
		'mobile_additional_info_week_seats'    => '1',
		'mobile_filter_train_show'             => '1',
		'mobile_filter_instructor_show'        => '1',
		'mobile_filter_complexity_show'        => '1',
		'mobile_filter_activity_show'          => '1',
		'mobile_nav_filter_font'               => '14px',
		'mobile_event_title_time_font'         => '14px',
		'mobile_event_dur_seats_font'          => '14px',
		'mobile_title_font'                    => '14px',
		'mobile_days_of_week_font'             => '14px',
	];
    $options = get_option( 'insta_calendar', false );
    return wp_parse_args( $options, $defaults );
}


add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'instasport', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );
