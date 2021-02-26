<?php

class InstaCalendarShortcode {

	public function __construct() {

		add_shortcode( 'instasport-calendar', function ( $atts, $tag ) {
			$id = isset( $atts['id'] ) ? $atts['id'] : 0;
			ob_start();
			echo ' <div id="instaCalendar' . $id . '" class="instaCalendar ic-loading" data-id="' . $id . '"><div class="ic-loader"></div></div>';


			// Подключаем скрипты и шаблоны
			if ( ! wp_style_is( 'instasport-calendar' ) ) {
				$script_data = [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'settings' => $this->get_settings(),
					'locale'   => get_locale(),
					'lang'     => $this->get_translations(),
				];

				$gmt_offset                     = get_option( 'gmt_offset' );
				$sign                           = ( $gmt_offset < 0 ) ? '-' : '+';
				$script_data['settings']['gmt'] = $sign . $gmt_offset;


				wp_enqueue_style( 'dashicons' );
				wp_enqueue_style( "instasport-calendar", plugins_url( "css/instasport.css", __FILE__ ), [], time() ); //todo time
				wp_enqueue_script( "axios", plugins_url( "js/axios.min.js", __FILE__ ), [], '0.21.1', true );
				wp_enqueue_script( "moment", plugins_url( "js/moment.js", __FILE__ ), [], '2.29.12', true );
				wp_enqueue_script( "moment-locales", plugins_url( "js/locales.js", __FILE__ ), [ 'moment' ], '2.29.12', true );
				wp_enqueue_script( "instasport-calendar", plugins_url( "js/instasport.js", __FILE__ ), [
					'jquery',
					'wp-util',
					'axios',
					'moment',
				], time(), true ); //todo time
				wp_localize_script( 'instasport-calendar', 'instasport', $script_data );

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
					'event',
					'event_pay_list',
					'cards',
					'card',
					'card_pay',
					'instructor',
					'rules',
					'offer',
					'service_agreement',
				];
				foreach ( $modals as $modal ) {
					echo '<script type="text/html" id="tmpl-instaModal-' . $modal . '">';
					$this->get_template( 'm_' . $modal );
					echo '</script>';
				}

				$this->get_styles();
			}

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
            .instaCalendar, #instaModal {
                /*Основной фон*/
                --primaryColor: <?php echo $options['primaryColor']?>;
                /*Дополнительный фон*/
                --secondaryColor: <?php echo $options['secondaryColor']?>;
                /*Цвет текста*/
                --primaryTextColor: <?php echo $options['primaryTextColor']?>;
                /*Акцентированный цвет текста*/
                --secondaryTextColor: <?php echo $options['secondaryTextColor']?>;
                /*Шрифт залов, фильтров и типа календаря*/
                --desktop_nav_filter_font: <?php echo $options['desktop_nav_filter_font']?>;
                /*Шрифт названия и времени события*/
                --desktop_event_title_time_font: <?php echo $options['desktop_event_title_time_font']?>;
                /*Шрифт продолжительности и мест события*/
                --desktop_event_dur_seats_font: <?php echo $options['desktop_event_dur_seats_font']?>;
                /*Шрифт текущей даты*/
                --desktop_date_font: <?php echo $options['desktop_date_font']?>;
                /*Шрифт названия дней недели*/
                --desktop_days_of_week_font: <?php echo $options['desktop_days_of_week_font']?>;
                /*Шрифт числа дня (месяц)*/
                --desktop_month_days_numbers_font: <?php echo $options['desktop_month_days_numbers_font']?>;
                /*Шрифт времени (неделя)*/
                --desktop_week_hours_font: <?php echo $options['desktop_week_hours_font']?>;
                /*Шрифт списка в фильтре*/
                --desktop_filter_list_font: <?php echo $options['desktop_filter_list_font']?>;
                /*Шрифт залов, фильтров*/
                --mobile_nav_filter_font: <?php echo $options['mobile_nav_filter_font']?>;
                /*Шрифт названия и времени события*/
                --mobile_event_title_time_font: <?php echo $options['mobile_event_title_time_font']?>;
                /*Шрифт продолжительности и мест события*/
                --mobile_event_dur_seats_font: <?php echo $options['mobile_event_dur_seats_font']?>;
                /*Шрифт тайтла*/
                --mobile_title_font: <?php echo $options['mobile_title_font']?>;
                /*Шрифт названия дней недели*/
                --mobile_days_of_week_font: <?php echo $options['mobile_days_of_week_font']?>;
            }

            .instaCalendar, .instaCalendar .ic-calendar {
                max-width: <?php echo $options['desktop_width']?> !important;
                width: <?php echo $options['desktop_width']?> !important;
                font-size: 14px;
            }


            <?php if(!$options['desktop_filter_train_show']):?>
            .instaCalendar .ic-calendar .ic-filters .ic-filter-item-training {
                display: none !important;
            }

            <?php endif;?>
            <?php if(!$options['desktop_filter_couch_show']):?>
            .instaCalendar .ic-calendar .ic-filters .ic-filter-item-couch {
                display: none !important;
            }

            <?php endif;?>
            <?php if(!$options['desktop_filter_activity_show']):?>
            .instaCalendar .ic-calendar .ic-filters .ic-filter-item-activity {
                display: none !important;
            }

            <?php endif;?>
            <?php if(!$options['desktop_filter_complexity_show']):?>
            .instaCalendar .ic-calendar .ic-filters .ic-filter-item-complexity {
                display: none !important;
            }

            <?php endif;?>

            @media (max-width: 768px) {
                .instaCalendar .ic-calendar .ic-halls ul li a,
                .instaCalendar .ic-calendar .ic-filters ul li a {
                    font-size: <?php echo $options['mobile_nav_filter_font'];?> !important;
                }

                .instaCalendar .ic-table-week .ic-thead .ic-mobile {
                    font-size: <?php echo $options['mobile_days_of_week_font'];?> !important;
                }

                .instaCalendar .ic-controls .ic-mobile {
                    font-size: <?php echo $options['mobile_title_font'];?> !important;
                }

                .instaCalendar .ic-event .ic-begin-time,
                .instaCalendar .ic-event .ic-title {
                    font-size: <?php echo $options['mobile_event_title_time_font'];?> !important;
                    line-height: <?php echo $options['mobile_event_title_time_font'];?> !important;
                }

                .instaCalendar .ic-event .ic-duration,
                .instaCalendar .ic-event .ic-seats {
                    font-size: <?php echo $options['mobile_event_dur_seats_font'];?> !important;
                    line-height: <?php echo $options['mobile_event_dur_seats_font'];?> !important;
                }


            <?php if(!$options['mobile_filter_train_show']):?>
                .instaCalendar .ic-calendar .ic-filters .ic-filter-item-training {
                    display: none !important;
                }

            <?php endif;?>
            <?php if(!$options['mobile_filter_couch_show']):?>
                .instaCalendar .ic-calendar .ic-filters .ic-filter-item-couch {
                    display: none !important;
                }

            <?php endif;?>
            <?php if(!$options['mobile_filter_activity_show']):?>
                .instaCalendar .ic-calendar .ic-filters .ic-filter-item-activity {
                    display: none !important;
                }

            <?php endif;?>
            <?php if(!$options['mobile_filter_complexity_show']):?>
                .instaCalendar .ic-calendar .ic-filters .ic-filter-item-complexity {
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
			'form'    => [
				'errors' => [
					'empty'    => __( "Поле должно быть заполнено", 'instasport' ),
					'sms_code' => __( "Код не совпадает с отправленным", 'instasport' ),

				]
			],
			'payment' => [
				'-1' => __( 'Оставить заявку', 'instasport' ),
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

