jQuery(document).ready(function ($) {

    let $instaModal = $('#instaModal');
    // Шаблоны
    let templateInstaCalendar = wp.template('instaCalendar');
    let templateInstaModal = wp.template('instaModal');
    let templateInstaButtonProfile = wp.template('instaButtonProfile');
    let templateInstaButtonPilot = wp.template('instaButtonPilot');
    let bodyOverflow = $('body').css('overflow');

    // Инициализация
    async function init() {
        // Отображаем кнопки
        button_profile();
        button_pilot();

        moment.locale(instasport.locale.split('_')[0]);

        // Получаем параметры с url
        let url_data = {};
        let re = /instasport-(\w*)=(\w*)/gm;
        while (match = re.exec(window.location.search)) {
            url_data[match[1]] = match[2];
        }
        history.pushState(null, null, window.location.href.replace(/\??\&?instasport-\w*=\w*/gm, ''));



        // Календари
        let loadEvents = false;
        $('.instaCalendar').each(function () {
            let id = $(this).data('id');
            instasport.clubs[id] = {calendar:true };
        });

        // Загрузка данных
        for (let id in instasport.clubs) {
            // Клуб
            let response = await api('init', id, {},);

            if (!response.success) {
                $('#instaCalendar' + id).removeClass('ic-loading').append('<div class="ic-errors">' + response.data.errors[0].message + '</div>');
                return;
            }
            instasport.clubs[id] = {...instasport.clubs[id],...response.data};
            let club = instasport.clubs[id];

            // Стили
            if(id == 0 && instasport.settings.desktop.useApiColors){
                let css = '';
                css += '--primaryColor:' + club.primaryColor + ';';
                css += '--primaryColorRGB:' + hexToRgb(club.primaryColor) + ';';
                css += '--secondaryColor:' + club.secondaryColor + ';';
                css += '--secondaryColorRGB:' + hexToRgb(club.secondaryColor) + ';';
                css += '--primaryTextColor:' + club.primaryTextColor + ';';
                css += '--primaryTextColorRGB:' + hexToRgb(club.primaryTextColor) + ';';
                css += '--secondaryTextColor:' + club.secondaryTextColor + ';';
                css += '--secondaryTextColorRGB:' + hexToRgb(club.secondaryTextColor) + ';';
                $('body').append('<style>.instaCalendar,.instasportProfileButton,.instasportPilotButton,#instaModal{'+css+'}</style>');
            }

            // Модальное окно
            club.modal = {
                step: 0,
                event: false,
                story: []
            };

            // Пользователь
            await userInit(id);

            // События для календаря
            if (club.calendar) {
                club.args = {
                    view: instasport.settings.desktop.defaultView,
                    hall: response.data.halls[0].id,
                    zone: 0,
                    date: moment().utc(false),
                    startDate: false,
                    endDate: false,
                    events: [],
                    filters: {
                        training: 0,
                        instructor: 0,
                        complexity: 0,
                        activity: 0,
                    }
                };
                await getEvents(id);
            }

            // Открываем модалку если есть параметры url
            if (url_data.id === id + '') {
                modal(id, url_data.modal);
            }

        }
    }

    init();

    /**
     * Инициализация объекта user значениями по умолчанию
     */
    async function userInit(id) {
        let defaults = {
            id: 0,
            phone: '',
            sms: '',
            email: '',
            emailConfirmed: '',
            emailSkip: '',
            merge: false,
            firstName: '',
            lastName: '',
            gender: 0,
            birthday: '',
            visits: [],
            events: [],
            cards: [],
            profile: {
                id: false,
                relation: false,
                rulesAccepted: false,
                leadAllowed: false,
                account: 0,
            }
        };
        let club = instasport.clubs[id];
        let slug = club.slug;

        // Пользователь не определен - значения по умолчанию
        if (!club.user) {
            club.user = defaults;
            club.modal.step = 'login';
        }


        // Запрос данных API
        let accessToken = getCookie('instasport_accessToken');
        let refreshToken = getCookie('instasport_refreshToken');
        if (accessToken && refreshToken) {
            let response = await api('user', id, {}, true);
            if (response.success) {
                // Проверка почты
                if (~response.data.email.indexOf('phone.xyz')) {
                    response.data.email = club.user.email ? club.user.email : '';
                    if (getCookie('instasport_emailSkip')) {
                        club.user.emailSkip = true;
                    }
                }

                // Установка данных пользователя с апи
                club.user = {...club.user, ...response.data};

                // Получаем профиль пользователя
                response = await api('profile', id, {}, true);
                if (response.success) {
                    club.user.profile = {...club.user.profile, ...response.data};
                }
                // Если нет - добавляем
                else {
                    response = await api('createProfile', id, {}, true);
                    if (response.success) {
                        club.user.profile = {...club.user.profile, ...response.data};
                    }
                }

                // Абонементы
                response = await api('cards', id, {}, true);
                if (response) {
                    club.user.cards = response.data;
                }

                // Записи
                response = await api('visits', id, {}, true);
                if (response) {
                    club.user.visits = response.data;
                }

            } else {
                deleteCookie('instasport_accessToken');
                deleteCookie('instasport_refreshToken');
                deleteCookie('instasport_emailSkip');
                club.user = false;
                userInit(id);
            }
        }
        button_profile();
        button_pilot();
    }

    /**
     * Подгружаем события
     * @returns {Promise<void>}
     */
    async function getEvents(id, args = {}) {
        let club = instasport.clubs[id];
        if (!club.args) return;

        $('#instaCalendar' + id).addClass('ic-loading');

        // view
        if (instasport.is_mobile() && club.args.view !== 'week') {
            args.view = 'week';
        }

        // filters
        if (args.view || args.hall) {
            club.args.filters.training = 0;
            club.args.filters.instructor = 0;
            club.args.filters.complexity = 0;
            club.args.filters.activity = 0;
        }

        // args
        let eventsArgs = {
            hall: args.hall ? args.hall : club.args.hall,
            zone: args.zone !== undefined ? args.zone : club.args.zone,
        };

        // view
        let view = args.view ? args.view : club.args.view;
        eventsArgs.view = view;

        let date = club.args.date;
        // startDate
        if (args.startDate) {
            eventsArgs.startDate = args.startDate;
        } else {
            eventsArgs.startDate = date.clone().startOf(view);
        }

        // endDate
        if (args.endDate) {
            eventsArgs.endDate = args.endDate;
        } else {
            eventsArgs.endDate = date.clone().endOf(view);
        }

        // set club args
        club.args = {...club.args, ...eventsArgs};

        // request
        let response = await api('events', id, eventsArgs, !!club.user.id);
        if (!response.success) {
            $('#instaCalendar' + id).removeClass('ic-loading').append('<div class="ic-errors">' + response.data.errors[0].message + '</div>');
            return;
        }

        club.events = response.data;


        for (let k in club.events) {
            club.events[k].date = moment(club.events[k].date);
        }

        updateCalendar(id);
        $('#instaCalendar' + id).removeClass('ic-loading');
    }

    /**
     * Запрос к апи
     * @param method
     * @param data
     * @returns {Promise<*>}
     */
    async function api(method, id, data, token = false) {
        data.id = id;

        if (token) {
            data.accessToken = getCookie('instasport_accessToken');
            data.refreshToken = getCookie('instasport_refreshToken');
        }

        if (token && (!data.accessToken || !data.refreshToken)) {
            await instasport_exit(id, {});
            return {data: {errors: ['exit']}};
        }

        let response = false;
        do {
            if (instasport.api_lock && token) {
                await sleep(100);
                continue;
            } else if (!instasport.api_lock && token) {
                instasport.api_lock = true;
            }

            response = await axios.post(instasport.ajax_url + '?action=instasport_' + method, data);

            if (token) {
                instasport.api_lock = false;
            }

        } while (response === false);


        if (!response.data.success || response.data.data.errors) {
            if (response.data.data.errors[0].result == 1 && token) {
                instasport_exit(id, {});
            }
        } else if (response.data.data.token) {
            if (
                data.accessToken != response.data.data.token.accessToken
                || data.refreshToken != response.data.data.token.refreshToken) {
                setCookie('instasport_accessToken', response.data.data.token.accessToken);
                setCookie('instasport_refreshToken', response.data.data.token.refreshToken);
            }

            delete response.data.data.token;
        }
        return response.data;
    }


    /**
     * Перерисовка календаря
     */
    function updateCalendar(id) {
        let club = instasport.clubs[id];

        let templateData = {
            settings: instasport.settings,
            club: club,
            lang: instasport.lang,
            filters: {},
            events: {},
        };


        // Фильтры
        let filters = {
            training: {},
            instructor: {},
            complexity: {},
            activity: {},
        };
        for (let event of club.events) {
            //training
            if (event.title) {
                if (!filters.training[event.title]) {
                    filters.training[event.title] = {
                        title: event.title,
                        color: event.color,
                        duration: event.duration,
                    };
                }
            }
            //instructors
            if (event.instructors) {
                for (let instructor of event.instructors) {
                    if (!filters.instructor[instructor.id]) {
                        filters.instructor[instructor.id] = {
                            title: instructor.firstName + ' ' + instructor.lastName,
                        };
                    }
                }
            }

            //complexity
            if (event.complexity) {
                if (!filters.complexity[event.complexity.id]) {
                    filters.complexity[event.complexity.id] = {
                        title: event.complexity.title,
                    };
                }
            }
            //activity
            if (event.activity) {
                if (!filters.activity[event.activity.slug]) {
                    filters.activity[event.activity.slug] = {
                        title: event.activity.title,
                    };
                }
            }
        }
        templateData.filters = filters;


        // Подготовка событий
        let filteredEvents = {};
        club.args.minH = 24;
        club.args.maxH = 0;
        club.args.aH = [];
        for (let event of club.events) {
            let key = event.date.format('YYYY-MM-DD');

            // Записываем минимальное и максимальное время для формирования таблицы
            if (club.args.view == 'week') {

                if (!event.date.isSame(club.args.date, 'isoWeek')) {
                    continue;
                }


                key = event.date.format('YYYY-MM-DD H')
                let H = parseInt(event.date.format('H'));
                if (club.args.minH > H) {
                    club.args.minH = H;
                }
                if (club.args.maxH < H) {
                    club.args.maxH = H;
                }

                if (!~club.args.aH.indexOf(H)) {
                    club.args.aH.push(H);
                }

            }

            // Тренировка
            if (club.args.filters.training) {
                if (!event.title || event.title !== club.args.filters.training) {
                    continue;
                }
            }
            // Инструктор
            if (club.args.filters.instructor) {
                //   if(!event.instructor)continue;
                let r = false;
                for (let instructor of event.instructors) {
                    if (instructor.id == club.args.filters.instructor) {
                        r = true;
                    }
                }
                if (!r) {
                    continue;
                }
            }
            // Сложность
            if (club.args.filters.complexity) {
                if (!event.complexity || event.complexity.id != club.args.filters.complexity) {
                    continue;
                }
            }
            // Направление
            if (club.args.filters.activity) {
                if (!event.activity || event.activity.slug != club.args.filters.activity) {
                    continue;
                }
            }

            // Проверка записи
            event.hasUser = !!(club.user.events.indexOf(event.id) > -1);


            // Добавляем елемент в список
            if (!filteredEvents[key]) {
                filteredEvents[key] = []
            }
            filteredEvents[key].push(event);
        }

        templateData.events = filteredEvents;
        $('#instaCalendar' + id).html(templateInstaCalendar(templateData));
    }

    /**
     * Открываем модалку
     * @param template
     * @param data
     */
    async function modal(id = false, template = false, data = {}) {
        id = id !== false ? id : $instaModal.data('id');
        if (id === undefined) return;
        let club = instasport.clubs[id];
        if(!club)return;
        let prev_step = club.modal.story.length ? club.modal.story[club.modal.story.length - 1] : false;

        // Если вызов без указания шаблона
        if (!template) {
            template = club.modal.step ? club.modal.step : 'event';
        }

        // Параметры
        club.modal.data = {...club.modal.data, ...data};
        let mdata = {
            id,
            step: club.modal.step,
            user: club.user,
            modal: club.modal,
            club: club,
            ...data
        };

        // Проверка события
        if (template === 'event' || template === 'profile') {
            // Не авторизован
            if (!club.user.id) {
                template = prev_step ? prev_step : 'login';
            }
            // Не дал согласия на правила
            else if (!club.user.profile.rulesAccepted && (club.rules || club.offer || club.serviceAgreement)) {
                template = 'rules';
            }
            // Не подтвердил почту
            else if (!club.user.emailConfirmed && !club.user.emailSkip) { // Email
                template = 'email';
            }
            // Нет события для отображения - переводим в профиль
            else if (!club.modal.data.event) {
                template = 'profile'
            }
        }

        // Установка шаблона
        club.modal.step = template;
        if (prev_step !== template) {
            club.modal.story.push(template);
        }

        // Отображение
        let templateModal = wp.template('instaModal-' + template);
        let html = templateModal(mdata);
        $instaModal.html(templateInstaModal(mdata));
        $instaModal.find('.ic-modal').prepend(html);
        $instaModal.data('id', id);

        // Открыть
        if (template && $instaModal.css('display') === 'none') {
            $instaModal.fadeIn(100);
            bodyOverflow = $('body').css('overflow');
            $('body').css('overflow', 'hidden');
        }
        $instaModal.removeClass('ic-loading');

        // Подгружаем детали события
        if (club.modal.step == 'event' && !club.modal.data.event.payment) {
            $instaModal.addClass('ic-loading');
            $instaModal.find('.ic-modal-content').addClass('ic-loader');
            let next = instaUrl(id, 'profile');
            let response = await api('clientEvent', id, {event_id: club.modal.data.event.id, next}, true);
            if (!response.success) {
                modal_errors({modal: response.data.errors[0].message});
            } else {
                club.modal.data.event = {...club.modal.data.event, ...response.data};
                club.modal.data.event.visit = club.user.visits.find(visit => visit.event.id == club.modal.data.event.id);
                modal(id, 'event');
            }
        }

        // Корректировка стилей окна
        let $content = $instaModal.find('.ic-modal-content').first();
        let ch = $content.height();

        // Высота
        let th = 40;

        if ($instaModal.find('.ic-modal-title').length) {
            th += $instaModal.find('.ic-modal-title').height();
        }

        if ($instaModal.find('.ic-modal-user-panel').length) {
            th += $instaModal.find('.ic-modal-user-panel').height();
        }

        if ($instaModal.find('.ic-modal-buttons').length) {
            th += $instaModal.find('.ic-modal-buttons').height();
        }

        th = document.documentElement.clientHeight - th;
        $content.css('max-height', th);

        // Контент
        /*let bp = $instaModal.find('.ic-modal-buttons').first()
        bp = bp.offset().top - bp.parent().offset().top;*/
        let tp = $content.offset().top - $content.parent().offset().top;
        let max_h = th - tp;
        if (ch > max_h) {
            $content.css('max-height', max_h).css('overflow', 'auto');
        } else {
            $content.css('max-height', false).css('overflow', false);
        }

        // Положение
        $instaModal.find('.ic-modal').css('top', parseInt((document.documentElement.clientHeight - $('#instaModal .ic-modal').height()) / 2 - 10));

        // Маска ввода
        init_mask('.ic-phone', '+380_________');
        init_mask('.ic-date', '__.__.____');

        // Фокус первого поля ввода
        $instaModal.find('input[type=text]').first().focus();
    }

    /**
     * Переключение клуба в профиле
     */
    $(document).on('click', '#instaModal .ic-select.ic-profile', function () {
        $(this).toggleClass('open');
    })
    $(document).on('click', '#instaModal .ic-select.ic-profile.open .ic-select-item', function () {
        if($(this).hasClass('active'))return;
        modal(parseInt($(this).data('id')), 'profile');
    })


    /**
     * Отображение кнопок Профиль
     */
    function button_profile() {
        $('.instasportProfileButton').each(function () {
            let id = $(this).data('id');
            let club = instasport.clubs[id];
            let data = {};
            if (club) {
                data.user = club.user;
            }
            let html = templateInstaButtonProfile(data);
            $(this).html(html);
        });
    }

    /**
     * Отображение кнопок Пробная тренировка
     */
    function button_pilot() {
        $('.instasportPilotButton').each(function () {
            let id = $(this).data('id');
            let club = instasport.clubs[id];
            let data = {};
            if (club) {
                data.user = club.user;
            }
            let html = templateInstaButtonPilot(data);
            $(this).html(html);
        });
    }

    /**
     * more
     */
    $('.instaCalendar').on('click', '.ic-more', function (e) {
        e.preventDefault();
        $(this).hide();
        $(this).prev().find('.ic-event').slideDown(100);

    })

    /**
     * Переключение вида
     */
    $('.instaCalendar').on('click', '.ic-row_1 .ic-view a', function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        getEvents(id, {view: $(this).data('val')});
    });


    /**
     * Переключение месяца/недели/дня
     */
    $('.instaCalendar').on('click', '.ic-controls .ic-control_left, .ic-controls .ic-control_right', function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        let club = instasport.clubs[id];
        let next = $(this).hasClass('ic-control_right');

        let w = club.args.date.format('W');

        console.log(club.args.view, instasport.is_mobile());
        console.log(next);


        // День (мобильная версия)
        if (instasport.is_mobile()) {
            if (next) {
                club.args.date.add(1, 'd');
            } else {
                club.args.date.subtract(1, 'd');
            }

            if (w == club.args.date.format('W')) {
                updateCalendar(id);
                return;
            }

        }

        // Неделя
        else if (club.args.view == 'week') {
            console.log(club.args.date.format('DD.MM.YYYY HH:MM'))
            if (next) {
                club.args.date.day('monday').add(8, 'd');
            } else {
                club.args.date.day('sunday').subtract(1, 'd');
            }
            console.log(club.args.date.format('DD.MM.YYYY HH:MM'))
        }

        // Месяц
        else if (club.args.view == 'month') {
            if (next) {
                club.args.date.add(1, 'M');
            } else {
                club.args.date.subtract(1, 'M');
            }
        }
        getEvents(id);
    });

    /**
     * Переключение дня (mobile)
     */
    $('.instaCalendar').on('click', '.ic-table-week .ic-thead .ic-td', function (e) {
        if (instasport.is_mobile()) {
            let id = $(e.delegateTarget).data('id');
            let day = parseInt($(this).data('day'));
            let month = parseInt($(this).data('month'));
            let year = parseInt($(this).data('year'));
            instasport.clubs[id].args.date.date(day);
            instasport.clubs[id].args.date.month(month-1);
            instasport.clubs[id].args.date.year(year);
            updateCalendar(id);
        }
    });

    /**
     * Студия
     */
    $('.instaCalendar').on('click', '.ic-row_1 .ic-halls a', function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        getEvents(id, {hall: $(this).data('val'), zone:0});
    });

    /**
     * Зона
     */
    $('.instaCalendar').on('click', '.ic-row_1 .ic-zones a', function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        getEvents(id, {zone: $(this).data('val')});
    });

    /**
     * Тренировка Инструктор Сложность Направление
     */
    $('.instaCalendar').on('click', '.ic-row_2 .ic-filter-item', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let id = $(e.delegateTarget).data('id');
        let dropdown = $(this).next();
        if (dropdown.css('display') == 'none') {
            $('#instaCalendar' + id + ' .insta_dropdown-window').slideUp(100);
            $('#instaCalendar' + id + ' .ic-row_2 li').removeClass('active');
            dropdown.slideDown(100);
            dropdown.parents('li').addClass('active');
        } else {
            dropdown.slideUp(100);
            dropdown.parents('li').removeClass('active');
        }
    })
    $(document).on('click', function (e) {
        $('.instaCalendar .insta_dropdown-window').slideUp(100);
        $('.instaCalendar .ic-row_2 li').removeClass('active');
    })
    $('.instaCalendar').on('click', '.ic-row_2 .insta_dropdown-content a', function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        let filter = $(this).data('filter');
        let value = $(this).data('val');

        instasport.clubs[id].args.filters[filter] = value ? value : false;
        updateCalendar(id);
    });


    /**
     * Открываем Профиль/авторизация
     */
    $('.instasportProfileButton').on('click', async function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        let club = instasport.clubs[id];
        if(!club || !club.modal)return;
        modal(id, 'profile');
    });

    /**
     * Открываем Форму записи на пробную тренировку
     */
    $('.instasportPilotButton').on('click', async function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');
        let club = instasport.clubs[id];
        if(!club || !club.modal)return;
        modal(id, 'pilot');
    });

    /**
     * Открываем событие/авторизация
     */
    $('.instaCalendar').on('click', '.ic-event', async function (e) {
        e.preventDefault();
        let id = $(e.delegateTarget).data('id');

        let club = instasport.clubs[id];
        let event_id = $(this).data('event');
        let event = club.events.find(event => event.id == event_id);

        modal(id, 'event', {event});
    });

    /**
     * Закрываем окно при нажатии на кнопку или фон
     */
    $instaModal.on('click', '.ic-wrapper, .ic-modal-title span', function (e) {
        if ($instaModal.hasClass('ic-loading')) {
            return;
        }

        if (e.currentTarget === e.target) {
            $instaModal.fadeOut(100);
            $('body').css('overflow', bodyOverflow);
            for (let id in instasport.clubs) {
                if(instasport.clubs[id].modal){
                    instasport.clubs[id].modal.story = [];
                }
            }
        }
    });

    /**
     * Записываем значения полей в user
     */
    $instaModal.on('keyup change paste mouseup input', 'form input', function () {
        let id = $instaModal.data('id');
        instasport.clubs[id].user[$(this).attr('name')] = $(this).val();
    });

    /**
     * Отправка формы
     */
    $instaModal.on('submit', '.ic-modal>form', function (e) {
        e.preventDefault();
        let id = $instaModal.data('id');
        let club = instasport.clubs[id];
        if ($(this).hasClass('ic-loading')) return;

        /*
         Проверка данных
         */
        let errors = {};
        switch (club.modal.step) {
            // Экран 1 (Login)
            case 'login':
                if (!club.user.phone || ~club.user.phone.indexOf('_')) {
                    errors.phone = instasport.lang.form.errors.empty;
                }
                break;
            // Экран 2 (Signup)
            case 'register':
                if (!club.user.firstName) {
                    errors.firstName = instasport.lang.form.errors.empty;
                }
                if (!club.user.lastName) {
                    errors.lastName = instasport.lang.form.errors.empty;
                }
                break;
            // Экран 3 (Verify)
            case 'sms':
                if (!club.user.sms) {
                    errors.sms = instasport.lang.form.errors.empty;
                }
                break;
            // Экран 4 (Ввод E-mail)
            case 'email':
                if (!club.user.email) {
                    errors.email = instasport.lang.form.errors.empty;
                }
                break;
            // Пробная тренировка
            case 'pilot':
                if(!club.user.id) {
                    if (!club.user.phone || ~club.user.phone.indexOf('_')) {
                        errors.phone = instasport.lang.form.errors.empty;
                    }
                    if (!club.user.firstName) {
                        errors.firstName = instasport.lang.form.errors.empty;
                    }
                    if (!club.user.lastName) {
                        errors.lastName = instasport.lang.form.errors.empty;
                    }
                }
                break;
        }

        // Выход если есть ошибки
        modal_errors(errors);
        if (Object.keys(errors).length) {
            return;
        }

        // Обработка
        eval('instasport_' + club.modal.step + '(' + id + ')');


    });

    /**
     * Быстрый переход в модалке
     */
    $instaModal.on('click', '[data-modal]', function (e) {
        e.preventDefault();
        let id = $instaModal.data('id');
        let template = $(this).data('modal');
        modal(id, template);
    });

    /**
     * Обработка кнопок
     */
    $instaModal.on('click', '[data-func]', function (e) {
        e.preventDefault();
        $(this).addClass('ic-loader');
        let id = $instaModal.data('id');
        let func = $(this).data('func');
        let mess = $(this).data('alert');
        let data = $(this).data();
        if (!mess || confirm(mess)) {
            eval('instasport_' + func + '(' + id + ', data)');
        }
    });

    /**
     * Чекбокс правил
     */
    $instaModal.on('change', '.ic-rules', function (e) {
        let $buttons = $(this).parents('.ic-modal-buttons');
        let status = !!$buttons.find('input[type=checkbox]:not(:checked)').length;
        $buttons.find('.ic-submit input').prop('disabled', status);
    });

    /**
     * Отображение ошибок в модалке
     * @param errors
     */
    function modal_errors(errors) {
        // let id = $instaModal.data('id');
        $instaModal.find('.ic-modal-field-error').fadeOut(200, function () {
            $(this).remove();
        });
        if (Object.keys(errors).length) {
            for (let [key, text] of Object.entries(errors)) {
                $instaModal.find('[name=' + key + ']').after('<div class="ic-modal-field-error" style="display: none">' + text + '</div>');
            }
            if (errors.modal) {
                $instaModal.find('.ic-modal-content').prepend('<div class="ic-modal-field-error" style="display: none">' + errors.modal + '</div>');
            }
            $instaModal.find('.ic-modal-field-error').fadeIn(200);
        }
    }

    /**
     * Пробная тренировка - форма
     * @param id
     */
    async function instasport_pilot(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];

        if(club.user.id){
            instasport_pilot_send(id);
            return;
        }

        let response = await api('phoneLogin', id, {phone: club.user.phone});
        if (response.data.errors) {
            if (response.data.errors[0].result == 2) {
                response = await api('phoneSignup', id, {
                    phone: club.user.phone,
                    birthday: club.user.birthday,
                    firstName: club.user.firstName,
                    lastName: club.user.lastName,
                    gender: club.user.gender,
                });
                if (response.data.errors) {
                    modal_errors({modal: response.data.errors[0].message});
                } else {
                    instasport.clubs[id].user = {...instasport.clubs[id].user, ...response.data.user};
                    modal(id, 'sms');
                }
            } else {
                modal_errors({phone: response.data.errors[0].message});
                $instaModal.removeClass('ic-loading');
            }
        } else {
            club.user = {...club.user, ...response.data.user};
            modal(id, 'sms');
        }
    }

    /**
     * Пробная тренировка - запись
     */
    async function instasport_pilot_send(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let response = await api('createLead', id, {}, true);
        let error = '';
        if (response.data.errors) {
            error = response.data.errors[0].message;
        }
        modal(id, 'pilot_mess', {error});
    }

    /**
     * Проверка телефона
     * @param id
     */
    async function instasport_login(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let response = await api('phoneLogin', id, {phone: club.user.phone});
        if (response.data.errors) {
            if (response.data.errors[0].result == 2) {
                modal(id, 'register');
            } else {
                modal_errors({phone: response.data.errors[0].message});
                $instaModal.removeClass('ic-loading');
            }
        } else {
            club.user = {...club.user, ...response.data.user};
            modal(id, 'sms');
        }
    }

    /**
     * Регистрация
     */
    async function instasport_register(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let response = await api('phoneSignup', id, {
            phone: club.user.phone,
            birthday: club.user.birthday,
            firstName: club.user.firstName,
            lastName: club.user.lastName,
            gender: club.user.gender,
        });
        if (response.data.errors) {
            modal_errors({modal: response.data.errors[0].message});
        } else {
            instasport.clubs[id].user = {...instasport.clubs[id].user, ...response.data.user};
            modal(id, 'sms');
        }
    }

    /**
     * SMS подтверждение
     */
    async function instasport_sms(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let response = await api('phoneVerify', id, {phone: club.user.phone, code: club.user.sms});
        if (response.data.errors) {
            modal_errors({sms: response.data.errors[0].message});
            $instaModal.removeClass('ic-loading');
        } else {
            for (let id_ in instasport.clubs) {
                let club = instasport.clubs[id_];

                await userInit(id_);
                await getEvents(id_);

                $instaModal.removeClass('ic-loading');
                if (club.modal.story.indexOf('pilot') === 0){
                    instasport_pilot_send();
                }else if (id_ == id) {
                    modal(id, 'event');
                }

                club.modal.story = [];
            }
        }
    }

    /**
     * Правила клуба
     */
    async function instasport_rules(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let response = await api('acceptRules', id, {}, true);
        if (response.data.errors) {
            modal_errors({modal: response.data.errors[0].message});
        } else {
            club.user.profile = {...club.user.profile, ...response.data.profile};
            modal(id, 'event');
        }
        $instaModal.removeClass('ic-loading');
    }

    /**
     * ВВод email
     */
    async function instasport_email(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let next = instaUrl(id, 'email_confirmed');
        let response = await api('emailUpdate', id, {next, email: club.user.email}, true);
        if (response.data.errors) {
            if (response.data.errors[0].result == 3) {
                modal(id, 'merge');
            } else {
                modal_errors({phone: response.data.errors[0].message});
            }
        } else {
            modal(id, 'email_wait');
        }
        $instaModal.removeClass('ic-loading');
    }

    /**
     * ВВод объединение аккаунтов
     */
    async function instasport_merge(id) {
        $instaModal.addClass('ic-loading');
        let club = instasport.clubs[id];
        let next = instaUrl(id, 'email_confirmed');
        let response = await api('emailMerge', id, {next, email: club.user.email}, true);
        if (response.data.errors) {
            modal_errors({phone: response.data.errors[0].message});
        } else {
            modal(id, 'email_wait');
        }
        $instaModal.removeClass('ic-loading');
    }

    /**
     * Проверка подтверждения email
     */
    async function instasport_email_wait(id) {
        $instaModal.addClass('ic-loading');
        for (let id_ in instasport.clubs) {
            let club = instasport.clubs[id_];
            await userInit(id_);
            if (club.user.emailConfirmed) {
                club.modal.story = [];
                if (id_ === id) {
                    modal(id, 'event');
                }
            }
            if (id_ === id) {
                $instaModal.removeClass('ic-loading');
            }
        }
    }

    /**
     * Выход
     */
    async function instasport_exit(id, d) {
        $instaModal.addClass('ic-loading');

        deleteCookie('instasport_accessToken');
        deleteCookie('instasport_refreshToken');

        for (let id in instasport.clubs) {
            let club = instasport.clubs[id];
            if (!club.id) continue;
            club.user = false;
            club.modal.step = false;
            club.modal.story = [];
            await userInit(id);
            await getEvents(id);
        }

        $instaModal.removeClass('ic-loading');
        $('.ic-wrapper').click();
    }

    /**
     * Отмена тренировки
     */
    async function instasport_delete_visit(id, d) {
        $instaModal.addClass('ic-loading');
        let visit_id = d.visit;
        let response = await api('deleteVisit', id, {visit_id}, true);
        if (response.data.errors) {
            modal_errors({modal: response.data.errors[0].message});
        } else {
            await userInit(id);
            modal(id);
        }
        $instaModal.removeClass('ic-loading');
    }

    /**
     * Отмена тренировки 'event'
     */
    async function instasport_delete_event_visit(id, d) {
        let club = instasport.clubs[id];
        club.modal.data.event.payment = false;
        club.modal.data.event.visit = false;
        instasport_delete_visit(id, d);
    }


    /**
     * Просмотр инструктора
     */
    async function instasport_instructor(id, d) {
        let club = instasport.clubs[id];
        modal(id, 'instructor', {instructor: club.modal.data.event.instructors[d.instructor]});
    }

    /**
     * Пропуск ввода email
     */
    function instasport_skip_email(id, d) {
        instasport.clubs[id].user.emailSkip = true;
        setCookie('instasport_emailSkip', true);
        modal(id, 'event');
    }


    /**
     * Шаг назад
     */
    $instaModal.on('click', 'a.ic-back', function (e) {
        e.preventDefault();
        let id = $instaModal.data('id');
        let club = instasport.clubs[id];
        if (club.modal.story.length) {
            club.modal.story.pop();
            modal(id, club.modal.story[club.modal.story.length - 1]);
        }
    })

    /**
     * Переключение страниц профиля
     */
    $instaModal.on('click', '.ic-modal-user-button', function (e) {
        let id = $instaModal.data('id');
        let template = $(this).data('step');
        modal(id, template);
    });


    /**
     * Просмотр абонемента
     */
    $instaModal.on('click', '.ic-card', async function (e) {
        $instaModal.addClass('ic-loading');
        $(this).addClass('ic-loader');
        let id = $instaModal.data('id');
        let card_id = $(this).data('card');
        let response = await api('cardTemplate', id, {card_id}, true);
        if (response.data.errors) {
            modal_errors({modal: response.data.errors[0].message});
        } else {
            modal(id, 'card', {card: response.data});
        }
        $instaModal.removeClass('ic-loading');
    });

    /**
     * Запись на тренировку / Покупка абонемента
     */
    $instaModal.on('click', '.ic-payment', async function () {
        $instaModal.addClass('ic-loading');
        let id = $instaModal.data('id');
        let club = instasport.clubs[id];
        let type = $(this).data('type');

        $(this).addClass('ic-loader');

        // Тренировка
        if (club.modal.step == 'event' || club.modal.step == 'event_pay_list') {
            let event_id = club.modal.data.event.id;
            switch (type) {
                case 'wayforpay':
                case 'liqpay':
                    if ($(this).find('form').length) {
                        $(this).find('form').submit();
                    }
                    break;
                case 'requestVisit':
                case 'payVisitFromAccount':
                case 'bookVisit':
                case 'payVisitByCard':

                    let card_id = parseInt($(this).data('card'));
                    let response = await api(type, id, {event_id, card_id}, true);
                    if (response.data.errors) {
                        modal_errors({modal: response.data.errors[0].message});
                        $(this).removeClass('ic-loader');
                    } else {
                        await userInit(id);
                        delete club.modal.data.event;
                        modal(id, 'visits');
                    }
                    break;
            }
        }

        // Абонемент
        if (club.modal.step == 'card_pay') {
            let card_id = parseInt(club.modal.data.card.id);
            switch (type) {
                case 'wayforpay':
                case 'liqpay':
                    if ($(this).find('form').length) {
                        $(this).find('form').submit();
                    }
                    break;
                case 'account':
                    let response = await api('payCardFromAccount', id, {card_id}, true);
                    if (response.data.errors) {
                        modal_errors({modal: response.data.errors[0].message});
                        $(this).removeClass('ic-loader');
                    } else {
                        await userInit(id);
                        modal(id, 'profile');
                    }
                    break;
            }
        }
        $instaModal.removeClass('ic-loading');
    })

    /**
     * Действия с абонементом
     */
    $instaModal.on('click', '.ic-user-card .ic-modal-button', async function () {
        $instaModal.addClass('ic-loading');
        $(this).addClass('ic-loader');
        let id = $instaModal.data('id');
        let card_id = parseInt($(this).data('card'));
        let type = $(this).data('type');

        let response = await api(type + 'Card', id, {card_id}, true);
        if (response.data.errors) {
            let errors = {};
            errors['card-' + card_id] = response.data.errors[0].message;
            modal_errors(errors);
        } else {
            await userInit(id);
            modal(id);
        }
        $instaModal.removeClass('ic-loading');
        $(this).removeClass('ic-loader');
    })

    /**
     * Перерисовка при изменении разрешения екрана
     */
    $(window).on('resize', resize);

    function resize() {
        $(window).off('resize', resize);
        setTimeout(function () {
            for (let id in instasport.clubs) {
                if (instasport.clubs[id].args && instasport.is_mobile() && instasport.clubs[id].args.view != 'week') {
                    getEvents(id, {view: 'week'});
                }
            }
            if ($instaModal.css('display') !== 'none') {
                modal();
            }
            $(window).on('resize', resize);
        }, 10);

    }

    /**
     * Маска ввода номера
     */
    function init_mask(selector, mask) {
        $(selector).on("keyup change paste mouseup input", function (e) {
            mask_check(selector, mask);
        });
        mask_check(selector, mask);
    }

    function mask_check(selector, mask) {
        let first = mask.indexOf('_');
        let $ell = $(selector);
        if ($ell.length === 0 || $ell.data('mask')) return;
        $ell.data('mask', true);
        let cs = false;
        let val = $ell.val();
        let new_val = '';
        let s = true;
        let m = true;
        for (let i = 0; i < mask.length; i++) {
            let v = val[i];
            if (mask[i] !== 'n' && mask[i] !== '_') {
                if (s && cs < $ell[0].selectionStart) {
                    if (s && (val[i] === mask[i])) {
                        cs = i;
                    } else if (!m && s && (val[i + 1] !== mask[i])) {
                        cs = i - 2;
                        new_val = new_val.substring(0, new_val.length - 1) + '_';
                    }
                }
                new_val += mask[i];
            } else {
                m = false;
                v = parseInt(val[i]);
                if (v >= 0 && s) {
                    new_val += v;
                    cs = i;
                } else {
                    s = false;
                    new_val += '_';
                }
            }
        }
        $ell.val(new_val);
        $ell[0].selectionStart = cs !== false ? cs + 1 : first;
        $ell[0].selectionEnd = cs !== false ? cs + 1 : first;
        $ell.data('mask', false);
    }

    /*----------------------------------*/


    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        let v = matches ? decodeURIComponent(matches[1]) : undefined;
        // let l = localStorage.getItem(name);
        return v;
    }

    function setCookie(name, value, options = {}) {

        options = {
            path: '/',
            'max-age': 2500000,
            'SameSite': 'Strict',
            ...options
        };

        if (options.expires instanceof Date) {
            options.expires = options.expires.toUTCString();
        }

        let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

        for (let optionKey in options) {
            updatedCookie += "; " + optionKey;
            let optionValue = options[optionKey];
            if (optionValue !== true) {
                updatedCookie += "=" + optionValue;
            }
        }

        //localStorage.setItem(name, value);
        document.cookie = updatedCookie;
    }

    function deleteCookie(name) {
        setCookie(name, "", {
            'max-age': -1
        })
        // localStorage.removeItem(name);
    }

    /**
     * Кодируем параметры вызова окна в ссылке
     * @param id
     * @param key
     * @returns {*}
     */
    function instaUrl(id, key) {
        let url = window.location.origin + window.location.pathname;
        url += window.location.search ? window.location.search + '&' : '?';
        url += 'instasport-modal=' + key + '&instasport-id=' + id;
        return url;
    }

    instasport.is_mobile = function () {
            return window.innerWidth <= 768;
    }

    function sleep(ms) {
        return new Promise(
            resolve => setTimeout(resolve, ms)
        );
    }

    function hexToRgb(hex) {
           return hex.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i ,(m, r, g, b) => '#' + r + r + g + g + b + b)
                .substring(1).match(/.{2}/g)
                .map(x => parseInt(x, 16))
    }

});