var instaApi = {};
jQuery(document).ready(function ($) {
    let templateInstaCalendar = wp.template('instaCalendar');
    let templateInstaModal = wp.template('instaModal');
    let $instaCalendar = $('#instaCalendar');
    let $instaModal = $('#instaModal');

    // $instaCalendar.addClass('desktop month');
    instasport.values.hall = instasport.api.club.halls[0].id

    instasport.modal = {
        step: 0,
        event: false,
        cards: false,
    };
    userInit(false);
    instasport.user.accessToken = getCookie('insta_accessToken');
    instasport.user.refreshToken = getCookie('insta_refreshToken');

    apiGetCardTemplates();


    instasport.user.exit = userInit; // todo test

    /**
     * Инициализация объекта user значениями по умолчанию
     * @param c - обнулить куки
     */
    function userInit(c = true) {
        instasport.user = {
            id: 0,
            phone: '',
            sms: '',
            email: '',
            emailConfirmed: '',
            merge: false,
            firstName: '',
            lastName: '',
            gender: 0,
            birthday: '',
            visits: [],
            events: [],
            accessToken: false,
            refreshToken: false,
        };
        instasport.modal.step = 'login';
        if (c) {
            deleteCookie('insta_accessToken');
            deleteCookie('insta_refreshToken');
        }
    }

    apiGetUser(function () {
        // сообщение email подтвержден
        if (~window.location.search.indexOf('instasport=email')) {
            modal('email_confirmed');
        }
        apiGetEvents();
    });

    /**
     * Получаем события
     */
    function apiGetEvents() {
        if (is_mobile() && instasport.values.view !== 'week') {
            instasport.values.view = 'week';
        }


        let view = instasport.values.view;
        let date = new Date(instasport.values.year, instasport.values.month,
            view == 'week' ? instasport.values.day : 1);
        let day = instaGetDay(date);
        date.setDate(date.getDate() - day);


        instasport.values.startDate = instaDateStr(date);


        date = new Date(instasport.values.year,
            instasport.values.month + (view != 'week'),
            view == 'week' ? instasport.values.day : 0);
        day = instaGetDay(date);
        date.setDate(date.getDate() + 6 - day);
        instasport.values.endDate = instaDateStr(date);

        instasport.values.filters.training = 0;
        instasport.values.filters.instructor = 0;
        instasport.values.filters.complexity = 0;
        instasport.values.filters.activity = 0;

        let method = 'events';
        let fields = {
            id: '',
            date: '',
            title: '',
            activity: {
                slug: '',
                title: ''
            },
            instructors: {
                id: '',
                firstName: '',
                lastName: '',
            },
            description: '',
            color: '',
            textColor: '',
            duration: '',
            price: '',
            seats: '',
            hall: {
                title: ''
            },
            complexity: {
                id: '',
                title: '',
            }
        };
        if (instasport.user.id) {
            method = 'clientEvents';
            //fields.payment = '';
            fields.status = '';
        }

        let args = {
            hall: parseInt(instasport.values.hall),
            startDate: instasport.values.startDate,
            endDate: instasport.values.endDate,
        };

        let query = graph(method, args, fields);
        api(query,
            function (data) {
                console.log('API events', data);
                if (data.data) {
                    instasport.api.events = data.data.events ? data.data.events : data.data.clientEvents;
                    updateCalendar();
                }
            }, !!instasport.user.id
        )
    }

    /**
     * Получаем событие
     */
    function apiGetEvent(id, func = () => {
    }) {
        let query = graph(
            'clientEvent',
            {
                id
            },
            {
                id: '',
                status: '',
                payment: '',
                account: '',
                cards: {
                    id: '',
                },
                liqpay: {
                    data: '',
                    signature: '',
                    action: '',
                    price: '',
                },
                wayforpay: {
                    merchantAccount: '',
                    merchantDomainName: '',
                    merchantSignature: '',
                    orderReference: '',
                    orderDate: '',
                    amount: '',
                    currency: '',
                    productName: '',
                    productCount: '',
                    productPrice: '',
                    returnUrl: '',
                    serviceUrl: '',
                    action: '',
                    price: '',
                }
            },
            false
        );

        api(query, function (data) {
            console.log('clientEvent', data);
            if (data.data.clientEvent) {
                instasport.modal.event = {...instasport.modal.event, ...data.data.clientEvent};
                for (var i = 0; i < instasport.api.events.length; i++) {
                    if (instasport.api.events[i].id == instasport.modal.event.id) {
                        instasport.api.events[i] = instasport.modal.event;
                        break;
                    }
                }
                func();
                updateCalendar();
            }
        }, true)
    }

    /**
     * Получаем данные пользователя
     * @param func
     */
    function apiGetUser(func = () => {
    }) {
        if (instasport.user.accessToken && instasport.user.refreshToken) {
            let query = graph('user', false, {
                id: '',
                phone: '',
                email: '',
                emailConfirmed: '',
                firstName: '',
                lastName: '',
                birthday: '',
                gender: '',
            });
            api(query, function (data) {
                console.log('API user', data);
                if (data.data && data.data.user) {
                    $.extend(instasport.user, data.data.user);
                    if (instasport.user.email && instasport.user.email.indexOf('phone.xyz')) {
                        instasport.user.email = '';
                    }

                    // Получаем тренировки пользователя
                    let query = graph('visits',
                        {startDate: instaDateStr(new Date())},
                        {
                            id: '',
                            event: {
                                id: '',
                                date: '',
                                duration: '',
                                title: '',
                                hall: {title: ''}
                            },
                            authorized: '',
                            paid: '',
                            refundable: '',
                            paidByCard: {id: ''},
                        });

                    api(query, function (data) {
                        console.log('API visits', data);
                        if (data.data.visits) {
                            instasport.user.visits = data.data.visits;
                            instasport.user.events = [];
                            for (let v of data.data.visits) {
                                instasport.user.events.push(v.event.id);
                            }
                        }

                        // Получаем абонементы пользователя
                        query = graph('cards',
                            false,
                            {
                                id: '',
                                authorized: '',
                                activated: '',
                                amount: '',
                                price: '',
                                dueDate: '',
                                pauses: '',
                                paused: '',
                                template: {id: '', title: '', description: '', subtitle: '', group: {title:''}},
                            });

                        instasport.user.cards = [];
                        api(query, function (data) {
                            console.log('API cards', data);
                            if (data.data.cards) {
                                instasport.user.cards = data.data.cards;
                            }
                            modal();
                            func();
                        }, true);

                    }, true);

                } else {
                    userInit();
                }
                modal();
            }, true);
        } else {
            userInit();
            modal();
            func();
        }
    }

    /**
     * Получаем все абонементы клуба
     */
    function apiGetCardTemplates() {
        let query = graph(
            'cardTemplates',
            false,
            {
                id: '',
                title: '',
                description: '',
                descriptionHtml: '',
                subtitle: '',
                group: {
                    id: '',
                    title: '',
                    order: '',
                },
                amount: '',
                duration: '',
                price: '',
            },
            false
        )
        api(query, function (data) {
            console.log('cardTemplates', data);
            if (data.data.cardTemplates) {
                instasport.api.cards = data.data.cardTemplates;
                for (let card of instasport.api.cards) {
                    if (instasport.api.cardGroups.find((e, k, a) => {
                        return e.id == card.group.id
                    })) {
                        continue;
                    }
                    instasport.api.cardGroups.push(card.group);
                }
                instasport.api.cardGroups.sort(function (a, b) {
                    return parseInt(a.order) - parseInt(b.order);
                });
            }
        }, true);
    }


    /**
     * Запрос к апи
     * @param query
     * @param callback
     */
    function api(query, callback, token = false) {
        if ($instaModal.css('display') === 'block') {
            $instaModal.addClass('ic-loading');
        } else {
            $instaCalendar.addClass('ic-loading');
        }

        let data = {
            'action': 'insta_api',
            'query': query,
        };
        if (token) {
            data.accessToken = instasport.user.accessToken;
            data.refreshToken = instasport.user.refreshToken;
        }
        $.ajax({
            url: instasport.ajax_url,
            method: "POST",
            data: data,
            dataType: "json",
            success: function (data) {
                if (data.errors) {
                    //alert('API ERROR');
                    console.log('API ERROR', data)
                }

                if (data.token) {
                    instasport.user.accessToken = data.token.accessToken;
                    instasport.user.refreshToken = data.token.refreshToken;
                    setCookie('insta_accessToken', data.token.accessToken);
                    setCookie('insta_refreshToken', data.token.refreshToken);
                }

                callback(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            complete: function () {
                $instaModal.removeClass('ic-loading');
                $instaModal.find('.ic-loader').removeClass('ic-loader');
                $instaCalendar.removeClass('ic-loading');
            }
        });
    }

    instaApi.api = api; // todo test
    instaApi.modal = modal;

    /**
     * Перерисовка календаря
     */
    function updateCalendar() {

        let templateData = {
            settings: instasport.settings,
            club: instasport.api.club,
            lang: instasport.lang,
        };


        // Фильтры
        let filters = {
            training: {},
            instructor: {},
            complexity: {},
            activity: {},
        };
        for (let event of instasport.api.events) {
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
        for (let event of instasport.api.events) {

            let date = new Date(event.date);

            // Записываем минимальное и максимальное время для формирования таблицы
            let H = '';
            if (instasport.values.view == 'week') {
                H = +instaDateStr(date, '{G}');
                if (instasport.values.minH > H) {
                    instasport.values.minH = H;
                }
                if (instasport.values.maxH < H) {
                    instasport.values.maxH = H;
                }
                H = ' ' + H;
            }

            // Тренировка
            if (instasport.values.filters.training) {
                if (!event.title || event.title !== instasport.values.filters.training) {
                    continue;
                }
            }
            // Тренер
            if (instasport.values.filters.instructor) {
                //   if(!event.instructor)continue;
                let r = false;
                for (let instructor of event.instructors) {
                    if (instructor.id == instasport.values.filters.instructor) {
                        r = true;
                    }
                }
                if (!r) {
                    continue;
                }
            }
            // Сложность
            if (instasport.values.filters.complexity) {
                if (!event.complexity || event.complexity.id != instasport.values.filters.complexity) {
                    continue;
                }
            }
            // Направление
            if (instasport.values.filters.activity) {
                if (!event.activity || event.activity.slug != instasport.values.filters.activity) {
                    continue;
                }
            }

            // Проверка записи
            event.hasUser = !!(instasport.user.events.indexOf(event.id) > -1);


            // Добавляем елемент в список
            let s = instaDateStr(date, '{Y}-{m}-{d}') + H;
            if (!filteredEvents[s]) {
                filteredEvents[s] = []
            }
            filteredEvents[s].push(event);
        }

        templateData.events = filteredEvents;


        // Текущая дата
        templateData.v = instasport.values;
        templateData.v.cdate = new Date(
            instasport.values.year,
            instasport.values.month,
            instasport.values.day);

        console.log('templateData', templateData);


        $instaCalendar.html(templateInstaCalendar(templateData));
    }


    /**
     * Открываем модалку
     * @param template
     * @param data
     */
    function modal(template = false, data = {}) {
        let show = template && $instaModal.css('display') === 'none';
        if (template) {
            instasport.modal.step = template;
        } else {
            template = instasport.modal.step;
        }


        if (template === 'event' && instasport.modal.event.payment === undefined) {
            apiGetEvent(
                parseInt(instasport.modal.event.id),
                () => {
                    modal('event')
                });
            return;
        }

        let mdata = {
            step: instasport.modal.step,
            user: instasport.user,
            event: instasport.modal.event,
            card: instasport.modal.card,
            ...data
        };
        console.log('MODAL', template, mdata);
        let templateModal = wp.template('instaModal-' + template);
        let html = templateModal(mdata);
        $instaModal.html(templateInstaModal(mdata));
        $instaModal.find('.ic-modal').prepend(html);

        if (show) {
            $instaModal.fadeIn(100);
            $('body').css('overflow', 'hidden');
        }

        $('#instaModal .ic-modal-content').css('max-height', document.documentElement.clientHeight - 200);
        $('#instaModal .ic-modal').css('top', (document.documentElement.clientHeight - $('#instaModal .ic-modal').height()) / 2);

        init_mask('.ic-phone', '+380_________');
        init_mask('.ic-date', '__.__.____');
    }


    /**
     * Скрыть окно
     * @param e
     */
    function close_modal(e) {
        if (e.currentTarget == e.target) {
            $instaModal.fadeOut(100);
            $('body').css('overflow', '');
        }
    }

    /**
     * Отображение ошибок
     * @param errors
     */
    function modal_errors(errors) {
        $instaModal.find('.ic-modal-field-error').fadeOut(200, function () {
            $(this).remove();
        });
        if (Object.keys(errors).length) {
            for (let [key, text] of Object.entries(errors)) {
                $instaModal.find('[name=' + key + ']').after('<div class="ic-modal-field-error" style="display: none">' + text + '</div>');
            }
            $instaModal.find('.ic-modal-field-error').fadeIn(200);
        }
    }

    /**
     * Открываем окно
     */
    $instaCalendar.on('click', '.ic-event', function (e) {
        e.preventDefault();
        let event_id = $(this).data('event');
        for (let event of instasport.api.events) {
            if (event.id == event_id) {
                instasport.modal.event = event;
                break;
            }
        }
        if (!instasport.user.id) {
            modal('login');
        } else {
            modal('event');
        }
    });

    /**
     * Отмена визита
     */
    $instaModal.on('click', '.ic-visit-cancel', function () {
        let visit = parseInt($(this).data('visit'));
        let alert = $(this).data('alert');
        if (visit && confirm(alert)) {
            instasport.user.visits
            let query = graph('deleteVisit', {visit}, {ok: ''}, true);
            api(query, function (data) {
                console.log('deleteVisit', data);
                apiGetUser();
            }, true);
        }
    })

    /**
     * Закрываем окно при нажатии на кнопку или фон
     */
    $instaModal.on('click', '.ic-wrapper, .ic-modal-title span', close_modal);

    /**
     * Значения полей
     */
    $instaModal.on('keyup change paste mouseup input', 'form input', function () {
        instasport.user[$(this).attr('name')] = $(this).val();
    });

    /**
     * Отправка формы
     */
    $instaModal.on('submit', '.ic-modal>form', function (e) {
        e.preventDefault();
        if ($(this).hasClass('ic-loading')) return;

        /*
         Проверка данных
         */
        let errors = {};

        // Экран 1 (Login)
        if (instasport.modal.step == 'login') {
            if (!instasport.user.phone || ~instasport.user.phone.indexOf('_')) {
                errors.phone = instasport.lang.form.errors.empty;
            }
        }
        // Экран 2 (Signup)
        else if (instasport.modal.step == 'register') {
            if (!instasport.user.firstName) {
                errors.firstName = instasport.lang.form.errors.empty;
            }
            if (!instasport.user.lastName) {
                errors.lastName = instasport.lang.form.errors.empty;
            }
        }
        // Экран 3 (Verify)
        else if (instasport.modal.step == 'sms') {
            if (!instasport.user.sms) {
                errors.sms = instasport.lang.form.errors.empty;
            }
        }
        // Экран 4 (Ввод E-mail)
        else if (instasport.modal.step == 'email') {
            if (!instasport.user.email) {
                errors.email = instasport.lang.form.errors.empty;
            }
        }

        // Выход если есть ошибки
        modal_errors(errors);
        if (Object.keys(errors).length) {
            return;
        }

        /*
         Отправка запроса
         */
        $(this).addClass('ic-loading');

        // Шаг 1 (Login)
        if (instasport.modal.step == 'login') {
            let query = graph(
                'phoneLogin',
                {
                    phone: instasport.user.phone
                },
                {
                    user: {
                        id: '',
                        firstName: '',
                        lastName: '',
                    }
                },
                true
            )
            api(query,
                function (data) {
                    // Если нашли пользователя
                    if (data.data && data.data.phoneLogin) {
                        instasport.user.id = data.data.phoneLogin.user.id;
                        instasport.user.firstName = data.data.phoneLogin.user.firstName;
                        instasport.user.lastName = data.data.phoneLogin.user.lastName;
                        modal('sms');
                    } else {
                        let phone = instasport.user.phone;
                        userInit();
                        instasport.user.phone = phone;
                        modal('register');
                    }
                    console.log('API phoneLogin', data.data);
                }
            )
        }
        // Шаг 2 (Signup)
        else if (instasport.modal.step == 'register') {
            let parts = instasport.user.birthday.split('.');
            let date = new Date(parts[2], parts[1] - 1, parts[0]);
            console.log(date);


            let args = {
                phone: instasport.user.phone,
                firstName: instasport.user.firstName,
                lastName: instasport.user.lastName,
                gender: parseInt(instasport.user.gender),
                origin: 6,
            }

            if (!isNaN(date.getTime())) {
                args.birthday = instaDateStr(date)
            }

            let fields = {
                user: {
                    id: '',
                    firstName: '',
                    lastName: '',
                    gender: '',
                    birthday: '',
                }
            }
            let query = graph('phoneSignup', args, fields, true);
            api(query, function (data) {
                    console.log('API phoneSignup', data);
                    if (data.data && data.data.phoneSignup) {
                        instasport.user.id = data.data.phoneSignup.user.id;
                        instasport.user.firstName = data.data.phoneSignup.user.firstName;
                        instasport.user.lastName = data.data.phoneSignup.user.lastName;
                    }
                    modal('sms');
                }
            )
        }
        // Экран 3 (Verify)
        else if (instasport.modal.step == 'sms') {
            let query = graph('phoneVerify', {
                phone: instasport.user.phone,
                code: instasport.user.sms
            }, {token: {accessToken: '', refreshToken: ''}}, true);
            api(query, function (data) {
                console.log('API phoneVerify', data);
                if (data.data && data.data.phoneVerify) {
                    instasport.user.accessToken = data.data.phoneVerify.token.accessToken;
                    setCookie('insta_accessToken', data.data.phoneVerify.token.accessToken);
                    instasport.user.refreshToken = data.data.phoneVerify.token.refreshToken;
                    setCookie('insta_refreshToken', data.data.phoneVerify.token.refreshToken);
                    apiGetUser(function () {
                        if (instasport.user.emailConfirmed) {
                            modal('event'); // Если все ок переходим к бронированию
                        } else {
                            modal('email'); // Если нет email, просим ввести
                        }
                    });
                } else {
                    modal_errors({sms: instasport.lang.form.errors.sms_code});
                    $instaModal.find('form').removeClass('ic-loading');
                }
            });
        }
        // Экран 4 (Ввод E-mail)
        else if (instasport.modal.step == 'email') {
            let next = window.location.href + (~window.location.href.indexOf('?') ? '&' : '?') + 'instasport=email';
            let query = graph('emailUpdate', {
                email: instasport.user.email,
                next
            }, {ok: ''}, true);
            api(query, function (data) {
                console.log('API emailUpdate', data);
                if (data.errors && data.errors[0].result === 3) {
                    modal('merge');
                } else {
                    modal('email_wait');
                }
            }, true);
        }
        // Экран 5 (Объединение аккаунтов)
        else if (instasport.modal.step == 'merge') {
            let query = graph('emailMerge', {email: instasport.user.email, next: instaUrl('email')}, {ok: ''}, true);
            api(query, function (data) {
                modal('email_wait')
            }, true);
        }
        // Экран 6 (Ожидание подтверждения E-mail)
        else if (instasport.modal.step == 'email_wait') {
            apiGetUser(
                function () {
                    if (instasport.user.emailConfirmed) {
                        modal('email_confirmed');
                    } else {
                        $instaModal.find('.ic-message-error').fadeIn(300);
                        $instaModal.find('form').removeClass('ic-loading');
                    }
                });
        }
        // Экран 7 (E-mail подтвержден)
        else if (instasport.modal.step == 'email_confirmed') {
            instasport.user.sms = false;
            modal('event');
        }
        // Экран 8 (Бронирование тренировки)
        else if (instasport.modal.step == 'event') {
            modal('booking');
        }
        // Покупка абонемента
        else if (instasport.modal.step == 'card') {
            modal('card_pay');
        }
        console.log('submit', instasport.user);
    });

    /**
     * Шаг назад
     */
    $instaModal.on('click', 'a.ic-back', function (e) {
        e.preventDefault();
        switch (instasport.modal.step) {
            case 'register':
                modal('login');
                break;
            case 'sms':
                if (instasport.user.id) {
                    modal('login');
                } else {
                    modal('register');
                }
                break;
            case 'merge':
                modal('email');
                break;
            case 'email_wait':
                modal('email');
                break;
            case 'profile':
            case 'visits':
            case 'cards':
            case 'booking':
                modal('event');
                break;
            case 'card':
                modal('cards');
                break;
            case 'card_pay':
                modal('card');
                break;
        }
    })

    /**
     * Шаг вперед (пропуск)
     */
    $instaModal.on('click', 'a.ic-next', function (e) {
        switch (instasport.modal.step) {
            case 'email':
                modal('event');
                break;
        }
    })

    /**
     * Страницы профиля
     */
    $instaModal.on('click', '[data-step]', function (e) {
        let step = $(this).data('step');
        // Выход
        if (step === 'exit') {
            let alert = $(this).data('alert');
            if (confirm(alert)) {
                userInit(true);
                modal();
            }
        } else {
            modal(step);
        }
    })

    /**
     * Запись на тренировку / Покупка абонемента
     */
    $instaModal.on('click', '.ic-payment', function () {
        let type = $(this).data('type');
        $(this).addClass('ic-loader');

        // Тренировка
        if (instasport.modal.step == 'booking') {
            let eventID = parseInt(instasport.modal.event.id);
            switch (type) {
                case 'wayforpay':
                case 'liqpay':
                    if ($(this).find('form').length) {
                        $(this).find('form').submit();
                    }
                    break;
                case 'account':
                    query = graph(
                        'payVisitFromAccount',
                        {
                            event: eventID,
                            origin: 3,
                        },
                        {
                            visit: {id: ''}
                        },
                        true
                    )
                    api(query, function (data) {
                        apiGetUser(function () {
                            modal('visits');
                            apiGetEvent(eventID);
                        });
                    }, true);
                    break;
                    break;
                case 'card':
                    let cardID = parseInt($(this).data('card'));
                    query = graph(
                        'payVisitByCard',
                        {
                            event: eventID,
                            card: cardID,
                            origin: 3,
                        },
                        {
                            visit: {id: ''}
                        },
                        true
                    )
                    api(query, function (data) {
                        apiGetUser(function () {
                            modal('visits');
                            apiGetEvent(eventID);
                        });
                    }, true);
                    break;
                case 'book':
                    query = graph(
                        'bookVisit',
                        {
                            event: eventID,
                            origin: 3,
                        },
                        {
                            visit: {id: ''}
                        },
                        true
                    )
                    api(query, function (data) {
                        apiGetUser(function () {
                            modal('visits');
                            apiGetEvent(eventID);
                        });
                    }, true);
                    break;
            }
        }

        // Абонемент
        if (instasport.modal.step == 'card_pay') {
            let cardID = parseInt(instasport.modal.card.id);
            switch (type) {
                case 'wayforpay':
                case 'liqpay':
                    if ($(this).find('form').length) {
                        $(this).find('form').submit();
                    }
                    break;
                case 'account':
                    query = graph(
                        'payCardFromAccount',
                        {
                            templateId: cardID,
                        },
                        {
                            card: {id: ''}
                        },
                        true
                    )
                    api(query, function (data) {
                        apiGetUser(function () {
                            modal('profile');
                            apiGetEvent(eventID);
                        });
                    }, true);
                    break;
            }
        }
    })

    /**
     * Просмотр абонемента
     */
    $instaModal.on('click', '.ic-card-group .ic-card', function () {
        let cardId = parseInt($(this).data('card'));
        $(this).addClass('ic-loader');

        let query = graph(
            'cardTemplate',
            {
                id: cardId,
            },
            {
                id: '',
                title: '',
                description: '',
                descriptionHtml: '',
                subtitle: '',
                group: {
                    id: '',
                    title: '',
                    order: '',
                },
                amount: '',
                duration: '',
                price: '',
                payment: '',
                liqpay: {
                    data: '',
                    signature: '',
                    action: '',
                    price: '',
                },
                wayforpay: {
                    merchantAccount: '',
                    merchantDomainName: '',
                    merchantSignature: '',
                    orderReference: '',
                    orderDate: '',
                    amount: '',
                    currency: '',
                    productName: '',
                    productCount: '',
                    productPrice: '',
                    returnUrl: '',
                    serviceUrl: '',
                    action: '',
                    price: '',
                }
            },
            false
        )
        api(query, function (data) {
            console.log('apiGetEvent', data);
            instasport.modal.card = data.data.cardTemplate;
            modal('card');
        }, true);
    })

    /**
     * more
     */
    $instaCalendar.on('click', '.ic-more', function (e) {
        e.preventDefault();
        $(this).hide();
        $(this).prev().find('.ic-event').slideDown(100);

    })

    /**
     * Переключение вида
     */
    $instaCalendar.on('click', '.ic-row_1 .ic-view a', function (e) {
        e.preventDefault();
        instasport.values.view = $(this).data('val');
        apiGetEvents();
    });


    /**
     * Переключение месяца/недели/дня
     */
    $instaCalendar.on('click', '.ic-controls .ic-control_left, .ic-controls .ic-control_right', function (e) {
        e.preventDefault();
        let date = new Date(instasport.values.year, instasport.values.month, instasport.values.day);
        let next = $(this).hasClass('ic-control_right');

        if (is_mobile()) {
            let n = instaGetDay(date);
            if (next) {
                if (n < 6) {
                    instasport.values.day++;
                    updateCalendar();
                    return;
                } else {
                    instasport.values.day -= 6;
                }
            } else if (n > 0) {
                instasport.values.day--;
                updateCalendar();
                return;
            } else {
                instasport.values.day += 6;
            }
        }
        if (instasport.values.view == 'week') {
            let day = !next ? +instasport.values.day - 7 : +instasport.values.day + 7;
            date = new Date(instasport.values.year, instasport.values.month, day);
        } else if ((instasport.values.view == 'month')) {
            let month = instasport.values.month - (next ? -1 : 1);
            date = new Date(instasport.values.year, month);
        }


        instasport.values.year = date.getFullYear();
        instasport.values.month = date.getMonth();
        instasport.values.day = date.getDate();

        apiGetEvents();
    });

    /**
     * Переключение дня (mobile)
     */
    $instaCalendar.on('click', '.ic-table-week .ic-thead .ic-td', function () {
        if (is_mobile()) {
            instasport.values.day = parseInt($(this).data('day'));
            updateCalendar();
        }
    });


    /**
     * Фильтр
     */

    // Зал
    $instaCalendar.on('click', '.ic-row_1 .ic-halls a', function (e) {
        e.preventDefault();
        instasport.values.hall = $(this).data('val');
        apiGetEvents();
    });

    // Второй ряд
    $instaCalendar.on('click', '.ic-row_2 .ic-filter-item', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let dropdown = $(this).next();
        if (dropdown.css('display') == 'none') {
            $instaCalendar.find('.insta_dropdown-window').slideUp(100);
            $instaCalendar.find('.ic-row_2 li').removeClass('active');
            dropdown.slideDown(100);
            dropdown.parents('li').addClass('active');
        } else {
            dropdown.slideUp(100);
            dropdown.parents('li').removeClass('active');
        }
    })
    $(document).on('click', function (e) {
        $instaCalendar.find('.insta_dropdown-window').slideUp(100);
        $instaCalendar.find('.ic-row_2 li').removeClass('active');
    })
    $instaCalendar.on('click', '.ic-row_2 .insta_dropdown-content a', function (e) {
        e.preventDefault();
        let filter = $(this).data('filter');
        let value = $(this).data('val');
        if (instasport.values.filters[filter] !== undefined) {
            instasport.values.filters[filter] = value ? value : false;
            updateCalendar();
        }
    });


    //


    $(window).resize(function (e) {
        updateCalendar();
        $('#instaModal .ic-modal-content').css('max-height', document.documentElement.clientHeight - 200);
        $('#instaModal .ic-modal').css('top', (document.documentElement.clientHeight - $('#instaModal .ic-modal').height()) / 2);
    });

    function graph(q, p, r, m = false) {
        let query = m ? 'mutation {' : '{';
        query += q;
        if (p) {
            query += '(';
            let t = [];
            for (let [k, v] of Object.entries(p)) {
                t.push(k + ':' + (Number.isInteger(v) ? v : '"' + v + '"'));
            }
            query += t.join(',');
            query += ')';
        }

        query += '{';
        for (let [k, v] of Object.entries(r)) {
            query += ' ' + (k);
            if (typeof v === 'object') {
                query += '{';
                for (let [k2, v2] of Object.entries(v)) {
                    query += ' ' + (k2);
                    if (typeof v2 === 'object') {
                        query += '{';
                        for (let [k3, v3] of Object.entries(v2)) {
                            query += ' ' + k3;
                        }
                        query += '}';
                    }
                }
                query += '}';
            }
        }
        query += '}}';
        return query;
    }

    /**
     * mask
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

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
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

        document.cookie = updatedCookie;
    }

    function deleteCookie(name) {
        setCookie(name, "", {
            'max-age': -1
        })
    }

});

/**
 * сделать воскресенье последним днем
 */
function instaGetDay(date) {
    let day = date.getDay();
    if (day == 0) {
        day = 7
    }
    ;
    return day - 1;
}

function instaGetTime(time) {
    let d = new Date(time);
    d.setUTCHours(d.getUTCHours() + parseFloat(instasport.settings.gmt));
    return ('0' + d.getUTCHours()).slice(-2) + ':' + ('0' + d.getUTCMinutes()).slice(-2);
}

/**
 * Строковое представление даты аналог date() в php
 * @param d Объект даты
 * @param str Формат
 * @returns {string}
 */
function instaDateStr(d, str = '{Y}-{m}-{d}') {
    let s = {};
    //d.setHours(d.getHours() + parseFloat(instasport.settings.gmt));
    s.d = ('0' + d.getDate()).slice(-2);
    s.m = ('0' + (d.getMonth() + 1)).slice(-2);
    s.Y = d.getFullYear();
    s.H = ('0' + d.getHours()).slice(-2);
    s.G = d.getHours();
    s.i = ('0' + d.getMinutes()).slice(-2);
    s.s = ('0' + d.getSeconds()).slice(-2);
    s.N = instaGetDay(d);
    s.l = instasport.lang.week_f[s.N];
    s.S = instasport.lang.week_s[s.N];
    s.F = instasport.lang.month1[d.getMonth()];
    s.K = instasport.lang.month2[d.getMonth()];
    for (let [k, v] of Object.entries(s)) {
        str = str.replace('{' + k + '}', v);
    }
    return str;
}

function instaUrl(key) {
    return instasport.currentUrl = window.location.href + (~window.location.href.indexOf('?') ? '&' : '?') + 'instasport=' + key;
}

function is_mobile() {
    return document.body.clientWidth < 768;
}