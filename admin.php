<?php

class InstaCalendarAdmin {
	public function __construct() {
		// Создаем страницу настроек плагина
		add_action( 'admin_menu', function () {
			add_options_page( 'Instasport Calendar', 'Instasport Calendar', 'manage_options', 'insta_calendar', [
				$this,
				'options_page'
			] );
		} );
		// Регистрация опций
		add_action( 'admin_init', [ $this, 'plugin_options' ] );

        add_action( 'customize_register', [ $this, 'customize_register' ] );

        add_action( 'customize_update_instasport', [ $this, 'customize_update_instasport' ], 10, 2);
	}

    function customize_register(WP_Customize_Manager $wp_customize ) {
        $wp_customize->add_panel( 'instasport_panel', array(
            'priority'       => 1020,
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
            'title'          => 'INSTASPORT',
        ) );

        /*
         * Цвета
         */
        $wp_customize->add_section( 'instasport_colors_section', array(
            'title'    => 'Цвета',
            'priority' => 10,
            'panel'    => 'instasport_panel',
        ) );
        $this->customize_add_field($wp_customize, 'use_api_colors', [
            'section'  => 'instasport_colors_section',
            'type'     => 'select',
            'label' => 'Использование API',
            'choices' => ['0' => 'Выкл', '1' => 'Вкл'],
        ]);
        $this->customize_add_field($wp_customize, 'primaryColor', [
            'section'  => 'instasport_colors_section',
            'label' => 'Основной фон',
        ]);
        $this->customize_add_field($wp_customize, 'secondaryColor', [
            'section'  => 'instasport_colors_section',
            'label' => 'Дополнительный фон',
        ]);
        $this->customize_add_field($wp_customize, 'primaryTextColor', [
            'section'  => 'instasport_colors_section',
            'label' => 'Цвет текста',
        ]);
        $this->customize_add_field($wp_customize, 'secondaryTextColor', [
            'section'  => 'instasport_colors_section',
            'label' => 'Акцентированный цвет текста',
        ]);


        /*
         * Десктопная версия
         */
        $wp_customize->add_section( 'instasport_desktop_section', array(
            'title'    => 'Десктопная версия',
            'priority' => 20,
            'panel'    => 'instasport_panel',
        ) );
        $this->customize_add_field($wp_customize, 'default_view_to_show', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Вид календаря (изначально)',
            'choices' => ['month' => 'Месяц', 'week' => 'Неделя'],
        ]);
        $this->customize_add_field($wp_customize, 'additional_info_month_duration', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Дополнительная информация Месяц (продолжительность тренировки)',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'additional_info_month_seats', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Дополнительная информация Месяц (свободные места)',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'additional_info_week_duration', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Дополнительная информация Неделя (продолжительность тренировки)',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'additional_info_week_seats', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Дополнительная информация Неделя (свободные места)',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'desktop_width', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Максимальная ширина календаря (можно указывать в px или %)',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_month_quantity_trainings', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Количество тренировок которое показывается в одной клетке - Месяц',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_week_quantity_trainings', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Количество тренировок которое показывается в одной клетке - Неделя',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_month_more_text', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Текст для кнопки смотреть больше (...) - Месяц',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_week_more_text', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Текст для кнопки смотреть больше (...) - Неделя',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_week_show_empty_rows', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Строки без тренировок - Неделя',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'desktop_filter_train_show', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Фильтр по тренировкам',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'desktop_filter_instructor_show', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Фильтр по инструкторам',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'desktop_filter_activity_show', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Фильтр по направлениям',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'desktop_filter_complexity_show', [
            'section'  => 'instasport_desktop_section',
            'type'     => 'select',
            'label' => 'Фильтр по сложности',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'desktop_nav_filter_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт студий, фильтров и типа календаря',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_event_title_time_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт названия и времени события',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_event_dur_seats_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт продолжительности и мест события',
        ]);

        $this->customize_add_field($wp_customize, 'desktop_date_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт текущей даты',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_days_of_week_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт названия дней недели',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_month_days_numbers_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт числа дня (месяц)',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_week_hours_font', [
            'section'  => 'Шрифт времени (неделя)',
            'label' => 'Шрифт названия дней недели',
        ]);
        $this->customize_add_field($wp_customize, 'desktop_filter_list_font', [
            'section'  => 'instasport_desktop_section',
            'label' => 'Шрифт списка в фильтре',
        ]);


        /*
         * Мобильная версия
         */
        $wp_customize->add_section( 'instasport_mobile_section', array(
            'title'    => 'Мобильная версия',
            'priority' => 20,
            'panel'    => 'instasport_panel',
        ) );
        $this->customize_add_field($wp_customize, 'mobile_additional_info_week_duration', [
            'section'  => 'instasport_mobile_section',
            'type'     => 'select',
            'label' => 'Дополнительная информация (продолжительность тренировки)',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'mobile_additional_info_week_seats', [
            'section'  => 'instasport_mobile_section',
            'type'     => 'select',
            'label' => 'Дополнительная информация (свободные места)',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'mobile_filter_train_show', [
            'section'  => 'instasport_mobile_section',
            'type'     => 'select',
            'label' => 'Фильтр по тренировкам',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'mobile_filter_instructor_show', [
            'section'  => 'instasport_mobile_section',
            'type'     => 'select',
            'label' => 'Фильтр по инструкторам',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'mobile_filter_activity_show', [
            'section'  => 'instasport_mobile_section',
            'type'     => 'select',
            'label' => 'Фильтр по направлениям',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'mobile_filter_complexity_show', [
            'section'  => 'instasport_mobile_section',
            'type'     => 'select',
            'label' => 'Фильтр по сложности',
            'choices' => ['0' => 'Не показывать', '1' => 'Показывать'],
        ]);
        $this->customize_add_field($wp_customize, 'mobile_nav_filter_font', [
            'section'  => 'instasport_mobile_section',
            'label' => 'Шрифт студий, фильтров',
        ]);
        $this->customize_add_field($wp_customize, 'mobile_event_title_time_font', [
            'section'  => 'instasport_mobile_section',
            'label' => 'Шрифт названия и времени события',
        ]);
        $this->customize_add_field($wp_customize, 'mobile_event_dur_seats_font', [
            'section'  => 'instasport_mobile_section',
            'label' => 'Шрифт продолжительности и мест события',
        ]);
        $this->customize_add_field($wp_customize, 'mobile_title_font', [
            'section'  => 'instasport_mobile_section',
            'label' => 'Шрифт тайтла',
        ]);
        $this->customize_add_field($wp_customize, 'mobile_days_of_week_font', [
            'section'  => 'instasport_mobile_section',
            'label' => 'Шрифт названия дней недели',
        ]);
    }

    function customize_add_field($wp_customize, $key, $data){
        $wp_customize->add_setting( 'insta_calendar['.$key.']', array(
            'type' => 'option',
            'capability' => 'manage_options',
            'transport' => 'refresh'
        ) );
        $args = [
            'type'     => 'text',
            'settings' => 'insta_calendar['.$key.']',
        ];
        $args = wp_parse_args($data, $args);
        $wp_customize->add_control( 'instasport_colors_control_'.$key, $args);
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

		register_setting( 'insta_calendar_options', 'insta_calendar', [$this, 'sanitize_callback'] );

		// Настройки API
		$options = insta_get_options();

        add_settings_section( 'insta_calendar_api', 'API', '', 'insta_calendar' );
        $this->add_settings_field( 'api_url', 'URL ', 'input', 'insta_calendar_api','', 'calendar_api');

        add_settings_section( 'insta_calendar_api', '[instasport-calendar]', '', 'insta_calendar' );
		$this->add_settings_field( 'club', 'Slug ', 'input', 'insta_calendar_api','', 'calendar_api');
		$this->add_settings_field( 'key', 'Key ', 'input', 'insta_calendar_api','', 'calendar_api' );

        $i = 2;
		do{
			add_settings_section( 'insta_calendar_api_' . $i, ' [instasport-calendar id=' . $i . ']', '', 'insta_calendar' );
			$this->add_settings_field( 'club_' . $i, 'Slug ', 'input', 'insta_calendar_api_' . $i,'', 'calendar_api');
			$this->add_settings_field( 'key_' . $i, 'Key ', 'input', 'insta_calendar_api_' . $i,'', 'calendar_api' );
		    $i++;
		}while(isset($options['club_'.($i-1)]) && $options['club_'.($i-1)]);

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
			echo '<input type="text" name="insta_calendar[' . $args['slug'] . ']" value="' . esc_attr( isset($options[ $args['slug'] ]) ? $options[ $args['slug'] ] : '' ) . '" class="' . $args['class'] . '">';
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

    function sanitize_callback($options){
        $old_options = insta_get_options();
        return wp_parse_args( $options, $old_options );
    }
}

new InstaCalendarAdmin();