<?php

class InstaCalendarShortcode {

	public function __construct() {

		add_shortcode( 'instasport-calendar', function ( $atts, $tag ) {
			//var_dump(InstaCalendarAPI::query('{"query": "{ club {title} }"}'));
            $id = $atts['id'] ?? '';
			ob_start();
			$options = insta_get_options();

			$club = InstaCalendarAPI::query( '{ "query": "{ club {title, halls{id,title,timeOpen,timeClose}, activities{id,title}} }"}',false,false, $id );

			if(isset($club->errors)){
				echo '<p style="background: lightgray;border: 1px solid red;padding: 10px;">INSTASPORT <b>'.$club->errors[0]->result.': '.$club->errors[0]->message.'<b></p>';
				return ob_get_clean();
            }

			$script_data = [
				'ajax_url' => admin_url( 'admin-ajax.php?id='.$id ),
				'club'     => $id ? $options['club_'.$id] : $options['club'],
				'settings' => $this->get_settings(),
				'lang'     => $this->get_translations(),
				'api'      => [
					'club'   => $club->data ? $club->data->club : [],
					'events' => [],
					'cards'  => [],
					'cardGroups'  => [],
				],
				'values'   => [
					'hall'      => 0,
					'view'      => $options['default_view_to_show'],
					'year'      => wp_date( 'Y' ),
					'month'     => wp_date( 'm' ) - 1,
					'day'       => wp_date( 'j' ),
					'startDate' => '',
					'endDate'   => '',
					'minH'      => 24,
					'maxH'      => 0,
					'filters'   => [
						'training'   => 0,
						'instructor' => 0,
						'complexity' => 0,
						'activity'   => 0,
					]
				],
			];

			$gmt_offset                     = get_option( 'gmt_offset' );
			$sign                           = ( $gmt_offset < 0 ) ? '-' : '+';
			$script_data['settings']['gmt'] = $sign . $gmt_offset;


			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( "instasport-calendar", plugins_url( "css/instasport.css", __FILE__ ), [], time() ); //todo time
			wp_enqueue_script( "instasport-calendar", plugins_url( "js/instasport.js", __FILE__ ), [
				'jquery',
				'wp-util'
			], time(), true ); //todo time
			wp_localize_script( 'instasport-calendar', 'instasport', $script_data );

			echo ' <div id="instaCalendar" class="ic-loading"><div class="ic-loader"></div></div>';
			echo ' <div id="instaModal" style="display: none"></div>';

			echo '<script type="text/html" id="tmpl-instaCalendar">';
			$this->get_template( 'calendar' );
			echo '</script>';

			echo '<script type="text/html" id="tmpl-instaModal">';
			$this->get_template( 'modal' );
			echo '</script>';

			//
			$modals = [
				'login',
				'register',
				'sms',
				'email',
				'email_confirmed',
				'email_wait',
				'merge',
				'profile',
				'visits',
				'booking',
				'event',
				'cards',
				'card',
				'card_pay',
				'instructor',
			];
			foreach ( $modals as $modal ) {
				echo '<script type="text/html" id="tmpl-instaModal-' . $modal . '">';
				$this->get_template( 'm_' . $modal );
				echo '</script>';
			}

			$this->get_styles();

			return ob_get_clean();
		} );
	}

	/**
	 * Настраиваемые стили
	 */
	function get_styles() {
		$options = insta_get_options();
		?>
        <style type="text/css">
            #instaCalendar, #instaModal {
                --primaryColor: <?php echo $options['primaryColor']?>;
                --secondaryColor: <?php echo $options['secondaryColor']?>;
                --primaryTextColor: <?php echo $options['primaryTextColor']?>;
                --secondaryTextColor: <?php echo $options['secondaryTextColor']?>;
                --desktop_nav_filter_font: <?php echo $options['desktop_nav_filter_font']?>;
                --desktop_event_title_time_font: <?php echo $options['desktop_event_title_time_font']?>;
                --desktop_event_dur_seats_font: <?php echo $options['desktop_event_dur_seats_font']?>;
                --desktop_date_font: <?php echo $options['desktop_date_font']?>;
                --desktop_days_of_week_font: <?php echo $options['desktop_days_of_week_font']?>;
                --desktop_month_days_numbers_font: <?php echo $options['desktop_month_days_numbers_font']?>;
                --desktop_week_hours_font: <?php echo $options['desktop_week_hours_font']?>;
                --desktop_filter_list_font: <?php echo $options['desktop_filter_list_font']?>;
                --mobile_nav_filter_font: <?php echo $options['mobile_nav_filter_font']?>;
                --mobile_event_title_time_font: <?php echo $options['mobile_event_title_time_font']?>;
                --mobile_event_dur_seats_font: <?php echo $options['mobile_event_dur_seats_font']?>;
                --mobile_title_font: <?php echo $options['mobile_title_font']?>;
                --mobile_days_of_week_font: <?php echo $options['mobile_days_of_week_font']?>;
            }

            /*#instaCalendar .ic-bg{
                background:



            <?php echo $options['primaryColor']?>



												 ;
															}
															#instaCalendar .ic-bg2{
																background:



            <?php echo $options['secondaryColor']?>



												 ;
															}
															#instaCalendar .ic-tx{
																background:



            <?php echo $options['primaryTextColor']?>



												 ;
															}
															#instaCalendar .ic-tx2{
																background:



            <?php echo $options['secondaryTextColor']?>



												 ;
															}*/


            <?php if(!$options['desktop_filter_train_show']):?>
            #instaCalendar .ic-calendar .ic-filters .ic-filter-item-training {
                display: none !important;
            }

            <?php endif;?>
            <?php if(!$options['desktop_filter_couch_show']):?>
            #instaCalendar .ic-calendar .ic-filters .ic-filter-item-couch {
                display: none !important;
            }

            <?php endif;?>
            <?php if(!$options['desktop_filter_activity_show']):?>
            #instaCalendar .ic-calendar .ic-filters .ic-filter-item-activity {
                display: none !important;
            }

            <?php endif;?>
            <?php if(!$options['desktop_filter_complexity_show']):?>
            #instaCalendar .ic-calendar .ic-filters .ic-filter-item-complexity {
                display: none !important;
            }

            <?php endif;?>

            @media (max-width: 768px) {
                #instaCalendar .ic-calendar .ic-halls ul li a,
                #instaCalendar .ic-calendar .ic-filters ul li a {
                    font-size: <?php echo $options['mobile_nav_filter_font'];?> !important;
                }

                #instaCalendar .ic-table-week .ic-thead .ic-mobile {
                    font-size: <?php echo $options['mobile_days_of_week_font'];?> !important;
                }

                #instaCalendar .ic-controls .ic-mobile {
                    font-size: <?php echo $options['mobile_title_font'];?> !important;
                }

                #instaCalendar .ic-event .ic-begin-time,
                #instaCalendar .ic-event .ic-title {
                    font-size: <?php echo $options['mobile_event_title_time_font'];?> !important;
                    line-height: <?php echo $options['mobile_event_title_time_font'];?> !important;
                }

                #instaCalendar .ic-event .ic-duration,
                #instaCalendar .ic-event .ic-seats {
                    font-size: <?php echo $options['mobile_event_dur_seats_font'];?> !important;
                    line-height: <?php echo $options['mobile_event_dur_seats_font'];?> !important;
                }


            <?php if(!$options['mobile_filter_train_show']):?>
                #instaCalendar .ic-calendar .ic-filters .ic-filter-item-training {
                    display: none !important;
                }

            <?php endif;?>
            <?php if(!$options['mobile_filter_couch_show']):?>
                #instaCalendar .ic-calendar .ic-filters .ic-filter-item-couch {
                    display: none !important;
                }

            <?php endif;?>
            <?php if(!$options['mobile_filter_activity_show']):?>
                #instaCalendar .ic-calendar .ic-filters .ic-filter-item-activity {
                    display: none !important;
                }

            <?php endif;?>
            <?php if(!$options['mobile_filter_complexity_show']):?>
                #instaCalendar .ic-calendar .ic-filters .ic-filter-item-complexity {
                    display: none !important;
                }

            <?php endif;?>
            }
        </style>
		<?php
	}

	/**
	 * Настройки
	 * @return array[]
	 */
	function get_settings() {
		$options = insta_get_options();

		return [
			'desktop' => [
				'useApiColors' => ! ! $options['use_api_colors'],
				'defaultView'  => $options['default_view_to_show'],
				'desktopWidth' => $options['desktop_width'],
				'filters'      => [
					'train'      => $options['desktop_filter_train_show'],
					'couch'      => $options['desktop_filter_couch_show'],
					'activity'   => $options['desktop_filter_activity_show'],
					'complexity' => $options['desktop_filter_complexity_show'],
				],
				'monthView'    => [
					'showDuration'     => ! ! $options['additional_info_month_duration'],
					'showSeats'        => ! ! $options['additional_info_month_seats'],
					'showEventsPerDay' => $options['desktop_month_quantity_trainings'],
					'moreText'         => $options['desktop_month_more_text'],
				],
				'weekView'     => [
					'showDuration'      => ! ! $options['additional_info_week_duration'],
					'showSeats'         => ! ! $options['additional_info_week_seats'],
					'showEventsPerHour' => $options['desktop_week_quantity_trainings'],
					'moreText'          => $options['desktop_week_more_text'],
					'hideEmptyRows'     => $options['desktop_week_hide_empty_rows'],
				],
			],
			'mobile'  => [
				//'useApiColors' => ! ! $options['mobile_use_api_colors'],
				'filters'      => [
					'train'      => $options['mobile_filter_train_show'],
					'couch'      => $options['mobile_filter_couch_show'],
					'activity'   => $options['mobile_filter_activity_show'],
					'complexity' => $options['mobile_filter_complexity_show'],
				],
				'showDuration' => ! ! $options['mobile_additional_info_week_duration'],
				'showSeats'    => ! ! $options['mobile_additional_info_week_seats'],
			]
		];
	}

	/**
	 * Перевод
	 * @return array
	 */
	function get_translations() {
		return [
			'month1'  => [
				__( 'Январь', 'instasport' ),
				__( 'Февраль', 'instasport' ),
				__( 'Март', 'instasport' ),
				__( 'Апрель', 'instasport' ),
				__( 'Май', 'instasport' ),
				__( 'Июнь', 'instasport' ),
				__( 'Июль', 'instasport' ),
				__( 'Август', 'instasport' ),
				__( 'Сентябрь', 'instasport' ),
				__( 'Октябрь', 'instasport' ),
				__( 'Ноябрь', 'instasport' ),
				__( 'Декабрь', 'instasport' ),
			],
			'month2'  => [ // Склонение
				__( 'Января', 'instasport' ),
				__( 'Февраля', 'instasport' ),
				__( 'Марта', 'instasport' ),
				__( 'Апреля', 'instasport' ),
				__( 'Мая', 'instasport' ),
				__( 'Июня', 'instasport' ),
				__( 'Июля', 'instasport' ),
				__( 'Августа', 'instasport' ),
				__( 'Сентября', 'instasport' ),
				__( 'Октября', 'instasport' ),
				__( 'Ноября', 'instasport' ),
				__( 'Декабря', 'instasport' ),
			],
			'week_f'  => [
				__( "Понедельник", 'instasport' ),
				__( "Вторник", 'instasport' ),
				__( "Среда", 'instasport' ),
				__( "Четверг", 'instasport' ),
				__( "Пятница", 'instasport' ),
				__( "Суббота", 'instasport' ),
				__( "Воскресенье", 'instasport' ),
			],
			'week_s'  => [
				__( "Пн", 'instasport' ),
				__( "Вт", 'instasport' ),
				__( "Ср", 'instasport' ),
				__( "Чт", 'instasport' ),
				__( "Пт", 'instasport' ),
				__( "Сб", 'instasport' ),
				__( "Вс", 'instasport' ),
			],
			'form'    => [
				'errors' => [
					'empty'    => __( "Поле должно быть заполнено", 'instasport' ),
					'sms_code' => __( "Код не совпадает с отправленным", 'instasport' ),

				]
			],
			'payment' => [
				'2'  => __( 'Оплатить через Liqpay', 'instasport' ),
				'3'  => __( 'Абонемент', 'instasport' ),
				'4'  => __( 'Оплатить со счета', 'instasport' ),
				'5'  => __( 'Забронировать', 'instasport' ),
				'10' => __( 'Оплатить через Portmone', 'instasport' ),
				'11' => __( 'Оплатить через WayForPay', 'instasport' ),
			]
		];
	}

	function get_template( $name ) {
		$path = get_stylesheet_directory() . '/instasport/' . $name . '.php';
		if ( file_exists( $path ) ) {
			include( $path );
		} else {
			include( __DIR__ . '/templates/' . $name . '.php' );
		}
	}
}

new InstaCalendarShortcode();


add_action( 'wp_enqueue_scripts', function () {
	$options = insta_get_options();
	//wp_register_script( "instasport-calendar", plugins_url( "js/instasport.js", __FILE__ ), [ 'jquery' ] );

} );

