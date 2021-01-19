<?php

class InstaCalendarAdmin {
	public function __construct() {
		// Создаем страницу настроек плагина
		add_action( 'admin_menu', function () {
			add_options_page( 'Instasport Calendar', 'InstaCalendar', 'manage_options', 'insta_calendar', [
				$this,
				'options_page'
			] );
		} );
		// Регистрация опций
		add_action( 'admin_init', [ $this, 'plugin_options' ] );
	}

	/**
	 * Страница настроек
	 */
	function options_page() {
		?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>
            <form action="options.php" method="POST">
				<?php
				settings_fields( 'insta_calendar_options' );
				do_settings_sections( 'insta_calendar' );
				submit_button();
				?>
            </form>
        </div>
        <style>
            .form-table th {
                width: 300px;
            }
        </style>
		<?php
	}

	/**
	 * Регистрация опций
	 */
	function plugin_options() {

		register_setting( 'insta_calendar_options', 'insta_calendar' );

		// Настройки API
		$options = insta_get_options();

		add_settings_section( 'insta_calendar_api', 'API [instasport-calendar-2]', '', 'insta_calendar' );
		$this->add_settings_field( 'club', 'Slug ', 'input', 'insta_calendar_api','', 'calendar_api');
		$this->add_settings_field( 'key', 'Key ', 'input', 'insta_calendar_api','', 'calendar_api' );

        $i = 2;
		do{
			add_settings_section( 'insta_calendar_api_' . $i, 'API-' . $i . ' [instasport-calendar-2 id=' . $i . ']', '', 'insta_calendar' );
			$this->add_settings_field( 'club_' . $i, 'Slug ', 'input', 'insta_calendar_api_' . $i,'', 'calendar_api');
			$this->add_settings_field( 'key_' . $i, 'Key ', 'input', 'insta_calendar_api_' . $i,'', 'calendar_api' );
		    $i++;
		}while(isset($options['club_'.($i-1)]));

		// Цвета
		add_settings_section( 'insta_calendar_colors', 'Цвета', '', 'insta_calendar' );
		$this->add_settings_field( 'use_api_colors', 'Использование API Цветов', 'select', 'insta_calendar_colors',
			[ '0' => 'Выкл', '1' => 'Вкл' ] );
		$this->add_settings_field( 'primaryColor', 'Основной фон', 'input', 'insta_calendar_colors' );
		$this->add_settings_field( 'secondaryColor', 'Дополнительный фон', 'input', 'insta_calendar_colors' );
		$this->add_settings_field( 'primaryTextColor', 'Цвет текста', 'input', 'insta_calendar_colors' );
		$this->add_settings_field( 'secondaryTextColor', 'Акцентированный цвет текста', 'input', 'insta_calendar_colors' );


		// Настройки Десктопной версии календаря
		add_settings_section( 'insta_calendar_desktop', 'Настройки Десктопной версии календаря', '', 'insta_calendar' );
		$this->add_settings_field( 'default_view_to_show', 'Вид календаря (изначально)', 'select', 'insta_calendar_desktop',
			[ 'month' => 'Месяц', 'week' => 'Неделя' ] );
		$this->add_settings_field( 'additional_info_month_duration', 'Дополнительная информация Месяц (продолжительность тренировки)', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'additional_info_month_seats', 'Дополнительная информация Месяц (свободные места)', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'additional_info_week_duration', 'Дополнительная информация Неделя (продолжительность тренировки)', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'additional_info_week_seats', 'Дополнительная информация Неделя (свободные места)', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'desktop_width', 'Максимальная ширина календаря (можно указывать в px или %)', 'input', 'insta_calendar_desktop' );
		$this->add_settings_field( 'desktop_month_quantity_trainings', 'Количество тренировок которое показывается в одной клетке - Месяц', 'input', 'insta_calendar_desktop' );
		$this->add_settings_field( 'desktop_week_quantity_trainings', 'Количество тренировок которое показывается в одной клетке - Неделя', 'input', 'insta_calendar_desktop' );
		$this->add_settings_field( 'desktop_month_more_text', 'Текст для кнопки смотреть больше (...) - Месяц', 'input', 'insta_calendar_desktop' );
		$this->add_settings_field( 'desktop_week_more_text', 'Текст для кнопки смотреть больше (...) - Неделя', 'input', 'insta_calendar_desktop' );
		$this->add_settings_field( 'desktop_week_hide_empty_rows', 'Строки без тренировок - Неделя', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'desktop_filter_train_show', 'Фильтр по тренировкам', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'desktop_filter_couch_show', 'Фильтр по тренерам', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'desktop_filter_activity_show', 'Фильтр по направлениям', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'desktop_filter_complexity_show', 'Фильтр по сложности', 'select', 'insta_calendar_desktop',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );

		//Шрифты
		add_settings_section( 'insta_calendar_desktop_fonts', 'Шрифты', '', 'insta_calendar' );

		$this->add_settings_field( 'desktop_nav_filter_font', 'Шрифт залов, фильтров и типа календаря', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_event_title_time_font', 'Шрифт названия и времени события', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_event_dur_seats_font', 'Шрифт продолжительности и мест события', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_date_font', 'Шрифт текущей даты', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_days_of_week_font', 'Шрифт названия дней недели', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_month_days_numbers_font', 'Шрифт числа дня (месяц)', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_week_hours_font', 'Шрифт времени (неделя)', 'input', 'insta_calendar_desktop_fonts' );
		$this->add_settings_field( 'desktop_filter_list_font', 'Шрифт списка в фильтре', 'input', 'insta_calendar_desktop_fonts' );

		// Настройки Мобильной версии календаря
		add_settings_section( 'insta_calendar_mobile', 'Настройки Мобильной версии календаря', '', 'insta_calendar' );
		$this->add_settings_field( 'mobile_additional_info_week_duration', 'Дополнительная информация (продолжительность тренировки)', 'select', 'insta_calendar_mobile',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'mobile_additional_info_week_seats', 'Дополнительная информация (свободные места)', 'select', 'insta_calendar_mobile',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'mobile_filter_train_show', 'Фильтр по тренировкам', 'select', 'insta_calendar_mobile',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'mobile_filter_couch_show', 'Фильтр по тренерам', 'select', 'insta_calendar_mobile',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'mobile_filter_activity_show', 'Фильтр по направлениям', 'select', 'insta_calendar_mobile',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );
		$this->add_settings_field( 'mobile_filter_complexity_show', 'Фильтр по сложности', 'select', 'insta_calendar_mobile',
			[ '1' => 'Показывать', '0' => 'Не показывать' ] );

		//Шрифты
		add_settings_section( 'insta_calendar_mobile_fonts', 'Шрифты', '', 'insta_calendar' );

		$this->add_settings_field( 'mobile_nav_filter_font', 'Шрифт залов, фильтров', 'input', 'insta_calendar_mobile_fonts' );
		$this->add_settings_field( 'mobile_event_title_time_font', 'Шрифт названия и времени события', 'input', 'insta_calendar_mobile_fonts' );
		$this->add_settings_field( 'mobile_event_dur_seats_font', 'Шрифт продолжительности и мест события', 'input', 'insta_calendar_mobile_fonts' );
		$this->add_settings_field( 'mobile_title_font', 'Шрифт тайтла', 'input', 'insta_calendar_mobile_fonts' );
		$this->add_settings_field( 'mobile_days_of_week_font', 'Шрифт названия дней недели', 'input', 'insta_calendar_mobile_fonts' );

	}

	/**
	 * Добавление полей опций
	 *
	 * @param $slug
	 * @param $title
	 * @param $type
	 * @param $section
	 * @param bool $values
	 * @param string $class
	 */
	function add_settings_field( $slug, $title, $type, $section, $values = false, $class = '' ) {
		add_settings_field( 'insta_' . $slug, $title,
			[ $this, 'option_field_html' ],
			'insta_calendar',
			$section,
			[ 'slug' => $slug, 'type' => $type, 'values' => $values, 'class' => $class ] );
	}

	/**
	 * Отображение полей опций
	 *
	 * @param $args
	 */
	function option_field_html( $args ) {
		$options = insta_get_options();

		if ( $args['type'] == 'input' ) {
			echo '<input type="text" name="insta_calendar[' . $args['slug'] . ']" value="' . esc_attr( $options[ $args['slug'] ] ?? '' ) . '" class="' . $args['class'] . '">';
		}

		if ( $args['type'] == 'select' ) {
			echo '<select name="insta_calendar[' . $args['slug'] . ']">';
			$selected = $options[ $args['slug'] ];
			foreach ( $args['values'] as $key => $value ) {
				echo '<option value="' . $key . '" ' . selected( $selected, $key, false ) . '>' . $value . '</option>';
			}
			echo '</select>';
		}
	}
}

new InstaCalendarAdmin();