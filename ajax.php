<?php

class InstaCalendarAjax {

	public function __construct() {

		// Actions
		$actions = [
			'init', // Инициализация клуба
			'events', // События
			'clientEvent', // Событие
			'cardTemplate', // Абонемент
			'user', // Текущий пользователь
			'profile', // Текущий профиль
			'cards', // Абонементы пользователя
			'visits', // Просмотр записей на тренировку
			'phoneLogin', // Проверка телефона
			'phoneVerify', // Авторизация
			'phoneSignup', // Регистрация
			'emailUpdate', // Email пользователя
			'emailMerge', // Объединение аккаунтов
			'acceptRules', // Согласие с правилами клуба
			'createProfile', // Создание профиля в клубе
			'payCardFromAccount', // Покупка абонемента

			'activateCard', // Активация абонемента
			'freezeCard', // Заморозка абонемента
			'unfreezeCard', // Разморозка абонемента

			'requestVisit', // Подача заявки на тренировку
			'payVisitFromAccount', // Оплата тренировки со счета
			'bookVisit', // Бронирование тренировки
			'payVisitByCard', // Оплата тренировки абонементом

			'deleteVisit', // Отмена тренировки

			'createLead', // Cоздание заявки на связь с менеджером/пробную тренировку
		];

		foreach ( $actions as $val ) {
			add_action( 'wp_ajax_instasport_' . $val, [ $this, $val . '_callback' ] );
			add_action( 'wp_ajax_nopriv_instasport_' . $val, [ $this, $val . '_callback' ] );
		}

	}

	/**
	 * Инициализация клуба
	 */
	function init_callback() {
		$ARGS = json_decode( file_get_contents( 'php://input' ), true );
		$data = [];
		$id   = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;

		// Данные клуба
		$club = InstaCalendarAPI::query( '{ "query": "{ club{id title  titleShort slug  halls{id title timeOpen timeClose zones(zoneType: 2){id title}}  activities{id title}  rules offer serviceAgreement primaryColor secondaryColor primaryTextColor secondaryTextColor} }"}', false, false, $id );
		$data = $club->data->club;

		// Шаблоны абонементов
		$cardTemplates       = InstaCalendarAPI::query( '{ "query": "{ cardTemplates{id  title  description  descriptionHtml  subtitle  group{ id  title  order }  amount  duration  price}}"}', false, false, $id );
		$data->cardTemplates = $cardTemplates->data->cardTemplates ?: [];
		$data->cardGroups    = [];
		foreach ( $data->cardTemplates as $cardTemplate ) {
			$data->cardGroups[ $cardTemplate->group->id ] = $cardTemplate->group;
		}
		usort( $data->cardGroups, function ( $a, $b ) {
			return $a->order > $b->order;
		} );

		wp_send_json_success( $data );
	}

	/**
	 * События
	 */
	function events_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$hall         = intval( isset( $ARGS['hall'] ) ? $ARGS['hall'] : 0 );
		$zone         = intval( isset( $ARGS['zone'] ) ? $ARGS['zone'] : 0 );
		$startDate    = date( 'Y-m-d', strtotime( isset( $ARGS['startDate'] ) ? $ARGS['startDate'] : 'first day of' ) );
		$endDate      = date( 'Y-m-d', strtotime( isset( $ARGS['endDate'] ) ? $ARGS['endDate'] : 'last day of' ) );
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;

		$method = $accessToken && $refreshToken ? 'clientEvents' : 'events';

		$query = '{ "query": "{ ' . $method . ' (';
		$query .= 'hall:' . $hall . ' ';
		$query .= 'startDate:\"' . $startDate . '\" ';
		$query .= 'endDate:\"' . $endDate . '\"';
		if($zone){
		    $query .= 'zone:' . $zone . ' ';
        }

		$query .= ')';
		$query .= '{ id date title ';
		$query .= 'activity {slug title} ';
		$query .= 'instructors {id firstName lastName instructorImage instructorDescription isInstructorVisible} ';
		$query .= 'description color textColor duration price seats ';
		$query .= 'hall{title} ';
		$query .= 'zone{id,title} ';
		$query .= 'complexity{id title} ';

		if ( $method == 'clientEvents' ) {
			$query .= 'status';
		}
		$query .= '}}"}';


		$response = InstaCalendarAPI::query( $query, $accessToken, $refreshToken, $id );
		$this->sendJson( $response, $method );
	}

	/**
	 * Событие
	 */
	function clientEvent_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$event_id     = isset( $ARGS['event_id'] ) && $ARGS['event_id'] ? $ARGS['event_id'] : false;
		$next         = isset( $ARGS['next'] ) && $ARGS['next'] ? $ARGS['next'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;


		$query = '{clientEvent(id:' . $event_id . ' ){';
		$query .= 'id status payment account image ';
		$query .= 'cards{id amount dueDate template{title subtitle group{title}}} ';
		$query .= 'liqpay(clientReturnUrl: \"' . $next . '\"){data signature action price} ';
		$query .= 'wayforpay(clientReturnUrl: \"' . $next . '\"){merchantAccount merchantDomainName merchantSignature orderReference orderDate amount currency productName productCount productPrice returnUrl serviceUrl action price} ';
		$query .= '}}';

		$response = InstaCalendarAPI::query( '{ "query": "' . $query . '"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'clientEvent' );
	}

	/**
	 * Абонемент
	 */
	function cardTemplate_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$card_id      = isset( $ARGS['card_id'] ) && $ARGS['card_id'] ? $ARGS['card_id'] : false;
		$next         = isset( $ARGS['next'] ) && $ARGS['next'] ? $ARGS['next'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;


		$query = '{cardTemplate(id:' . $card_id . '){';
		$query .= 'id title description descriptionHtml subtitle ';
		$query .= 'group{id title order} ';
		$query .= 'amount duration price payment ';
		$query .= 'liqpay(clientReturnUrl: \"' . $next . '\"){data signature action price} ';
		$query .= 'wayforpay(clientReturnUrl: \"' . $next . '\"){merchantAccount merchantDomainName merchantSignature orderReference orderDate amount currency productName productCount productPrice returnUrl serviceUrl action price} ';
		$query .= '}}';

		$response = InstaCalendarAPI::query( '{ "query": "' . $query . '"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'cardTemplate' );
	}

	/**
	 * Пользователь
	 */
	function user_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "{ user { id phone email emailConfirmed firstName lastName birthday gender}}"}', $accessToken, $refreshToken, $id );

		$this->sendJson( $response, 'user' );
	}

	/**
	 * Профиль
	 */
	function profile_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "{ profile { id relation rulesAccepted leadAllowed account}}"}', $accessToken, $refreshToken, $id );

		$this->sendJson( $response, 'profile' );
	}

	/**
	 *  Абонементы пользователя
	 */
	function cards_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "{ cards { id authorized activated amount price dueDate pauses paused freezeEnabled template{id title description subtitle group{title}}}}"}', $accessToken, $refreshToken, $id );

		$this->sendJson( $response, 'cards' );
	}

	/**
	 *  Просмотр записей на тренировку
	 */
	function visits_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$event_id     = isset( $ARGS['event_id'] ) && $ARGS['event_id'] ? $ARGS['event_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$query        = '{ "query": "{ visits';
		if($event_id){
			$query    .= '(event: '.$event_id.')';
		}
		$query        .= '{ id event{ id date duration title hall{title}} authorized paid refundable paidByCard{id} }}"}';
		$response     = InstaCalendarAPI::query( $query, $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'visits' );
	}

	/**
	 * Создание профиля в клубе
	 */
	function createProfile_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { createProfile(origin:3){ profile { id relation rulesAccepted leadAllowed account} } }"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'profile' );
	}

	/**
	 * Проверка телефона
	 */
	function phoneLogin_callback() {
		$ARGS     = json_decode( file_get_contents( 'php://input' ), true );
		$id       = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$phone    = isset( $ARGS['phone'] ) && $ARGS['phone'] ? $ARGS['phone'] : false;
		$response = InstaCalendarAPI::query( '{ "query": "mutation { phoneLogin(phone:\"' . $phone . '\"){ user{id firstName lastName}}}"}', 0, 0, $id );
		$this->sendJson( $response, 'phoneLogin' );
	}

	/**
	 * Авторизация
	 */
	function phoneVerify_callback() {
		$ARGS     = json_decode( file_get_contents( 'php://input' ), true );
		$id       = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$phone    = isset( $ARGS['phone'] ) && $ARGS['phone'] ? $ARGS['phone'] : false;
		$code     = isset( $ARGS['code'] ) && $ARGS['code'] ? $ARGS['code'] : false;
		$response = InstaCalendarAPI::query( '{ "query": "mutation {  phoneVerify(code: \"' . $code . '\" phone:\"' . $phone . '\"){ token{accessToken refreshToken} }}"}', 0, 0, $id );
		/*$response->data->phoneVerify->token->accessToken
		$response->data->phoneVerify->token->refreshToken*/
		$this->sendJson( $response, 'phoneVerify' );
	}

	/**
	 * Регистрация
	 */
	function phoneSignup_callback() {
		$ARGS      = json_decode( file_get_contents( 'php://input' ), true );
		$id        = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$phone     = isset( $ARGS['phone'] ) && $ARGS['phone'] ? $ARGS['phone'] : false;
		$firstName = isset( $ARGS['firstName'] ) && $ARGS['firstName'] ? $ARGS['firstName'] : false;
		$lastName  = isset( $ARGS['lastName'] ) && $ARGS['lastName'] ? $ARGS['lastName'] : false;
		$gender    = intval( isset( $ARGS['gender'] ) ? $ARGS['gender'] : false );
		$birthday  = strtotime( isset( $ARGS['birthday'] ) ? $ARGS['birthday'] : false );

		$params = 'phone:\"' . $phone . '\"';
		$params .= ' firstName:\"' . $firstName . '\"';
		$params .= ' lastName:\"' . $lastName . '\"';
		$params .= ' gender:' . $gender;
		if ( $birthday ) {
			$params .= ' birthday:\"' . date( 'Y-m-d', $birthday ) . '\"';
		}

		$response = InstaCalendarAPI::query( '{ "query": "mutation {  phoneSignup(' . $params . ' origin: 6){ user { id phone email emailConfirmed firstName lastName birthday gender} }}"}', 0, 0, $id );

		$this->sendJson( $response, 'phoneSignup' );
	}

	/**
	 *  Email пользователя
	 */
	function emailUpdate_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$email        = isset( $ARGS['email'] ) && $ARGS['email'] ? $ARGS['email'] : false;
		$next         = isset( $ARGS['next'] ) && $ARGS['next'] ? $ARGS['next'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation {  emailUpdate(email: \"' . $email . '\" next:\"' . $next . '\"){ ok }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'emailUpdate' );
	}

	/**
	 *  Объединение аккаунтов
	 */
	function emailMerge_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$email        = isset( $ARGS['email'] ) && $ARGS['email'] ? $ARGS['email'] : false;
		$next         = isset( $ARGS['next'] ) && $ARGS['next'] ? $ARGS['next'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation {  emailMerge(email: \"' . $email . '\" next:\"' . $next . '\"){ ok }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'emailMerge' );
	}

	/**
	 * Согласие с правилами клуба
	 */
	function acceptRules_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { acceptRules{ profile { id relation rulesAccepted account} } }"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'acceptRules' );
	}

	/**
	 * Покупка абонемента
	 */
	function payCardFromAccount_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$card_id      = isset( $ARGS['card_id'] ) && $ARGS['card_id'] ? $ARGS['card_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { payCardFromAccount(templateId:' . $card_id . '){ card{id}}}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'payCardFromAccount' );
	}

	/**
	 * Активация абонемента
	 */
	function activateCard_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$card_id      = isset( $ARGS['card_id'] ) && $ARGS['card_id'] ? $ARGS['card_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { activateCard(cardId:' . $card_id . '){ card{id}}}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'activateCard' );
	}

	/**
	 * Заморозка абонемента
	 */
	function freezeCard_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$card_id      = isset( $ARGS['card_id'] ) && $ARGS['card_id'] ? $ARGS['card_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { freezeCard(cardId:' . $card_id . '){ card{id}}}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'freezeCard' );
	}

	/**
	 * Разморозка абонемента
	 */
	function unfreezeCard_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$card_id      = isset( $ARGS['card_id'] ) && $ARGS['card_id'] ? $ARGS['card_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { unfreezeCard(cardId:' . $card_id . '){ card{id}}}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'unfreezeCard' );
	}

	/**
	 * Подача заявки на тренировку
	 */
	function requestVisit_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$event_id     = isset( $ARGS['event_id'] ) && $ARGS['event_id'] ? $ARGS['event_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { requestVisit(eventId: ' . $event_id . ', origin: 3, familyProfileId: 0 ){ visit{id} }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'requestVisit' );
	}

	/**
	 * Оплата тренировки со счета
	 */
	function payVisitFromAccount_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$event_id     = isset( $ARGS['event_id'] ) && $ARGS['event_id'] ? $ARGS['event_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { payVisitFromAccount(event: ' . $event_id . ', origin: 3, familyProfileId: 0 ){ visit{id} }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'payVisitFromAccount' );
	}

	/**
	 * Бронирование тренировки
	 */
	function bookVisit_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$event_id     = isset( $ARGS['event_id'] ) && $ARGS['event_id'] ? $ARGS['event_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { bookVisit(event: ' . $event_id . ', origin: 3, familyProfileId: 0 ){ visit{id} }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'bookVisit' );
	}

	/**
	 * Оплата тренировки абонементом
	 */
	function payVisitByCard_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$event_id     = isset( $ARGS['event_id'] ) && $ARGS['event_id'] ? $ARGS['event_id'] : false;
		$card_id      = isset( $ARGS['card_id'] ) && $ARGS['card_id'] ? $ARGS['card_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { payVisitByCard(event: ' . $event_id . ', card: ' . $card_id . ', origin: 3, familyProfileId: 0 ){ visit{id} }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'payVisitByCard' );
	}

	/**
	 * Отмена тренировки
	 */
	function deleteVisit_callback() {
		$ARGS         = json_decode( file_get_contents( 'php://input' ), true );
		$id           = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$visit_id     = isset( $ARGS['visit_id'] ) && $ARGS['visit_id'] ? $ARGS['visit_id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response     = InstaCalendarAPI::query( '{ "query": "mutation { deleteVisit(visit: ' . $visit_id . '){ ok }}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'deleteVisit' );
	}


	/**
	 * Cоздание заявки на связь с менеджером/пробную тренировку
	 */
	function createLead_callback() {
		$ARGS     = json_decode( file_get_contents( 'php://input' ), true );
		$id       = isset( $ARGS['id'] ) && $ARGS['id'] ? $ARGS['id'] : false;
		$accessToken  = isset( $ARGS['accessToken'] ) && $ARGS['accessToken'] ? $ARGS['accessToken'] : false;
		$refreshToken = isset( $ARGS['refreshToken'] ) && $ARGS['refreshToken'] ? $ARGS['refreshToken'] : false;
		$response = InstaCalendarAPI::query( '{ "query": "mutation { createLead(origin:3){ profile{id}}}"}', $accessToken, $refreshToken, $id );
		$this->sendJson( $response, 'createLead' );
	}


	function sendJson( $response, $key = false ) {
		$data = $key && isset( $response->data->$key ) ? $response->data->$key : $response->data;
		if ( $response->token ) {
			$data->token = $response->token;
		}

		wp_send_json_success( $data );
	}

}

new InstaCalendarAjax();