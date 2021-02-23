<!-- Профиль пользователя -->
<div class="ic-modal-title">
	<?php _e( "Профиль", 'instasport' ) ?>
	<span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
	<div class="ic-modal-user-profile">
        <div class="ic-modal-title">
            {{data.user.firstName}} {{data.user.lastName}}
        </div>
		<div class="ic-modal-row">
			<div class="ic-modal-row-title"><?php _e('Электронная почта', 'instasport') ?></div>
			{{data.user.email}}
		</div>
		<div class="ic-modal-row">
			<div class="ic-modal-row-title"><?php _e('Телефон', 'instasport') ?></div>
			{{data.user.phone}}
		</div>
        <div class="ic-modal-row">
			<div class="ic-modal-row-title"><?php _e('Текущий баланс', 'instasport') ?></div>
			{{data.user.profile.account}}
		</div>
	</div>
    <div class="ic-user-cards">
        <div class="ic-modal-title">
            Мои абонементы
        </div>
        <# if(data.user.cards.length){ // Абонементы #>
        <# for(let card of data.user.cards){ #>
        <div class="ic-user-card {{ card.activated ? 'activated' : 'inactive' }}" data-card="{{card.id}}">
            <div class="ic-card-title">{{card.template.group.title}} / {{card.template.title}}</div>
            <div class="ic-card-subtitle">{{card.template.subtitle}}</div>
            <div class="ic-card-status">{{ !card.activated ? '<?php _e('Не активирован', 'instasport') ?>'  : (card.paused ? '<?php _e('Заморожен', 'instasport') ?>' : '')}}</div>
            <br>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Цена', 'instasport') ?></div>
                <span>{{card.price}}</span>
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Количество посещений', 'instasport') ?></div>
                <span>{{card.amount}}</span>
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Количество заморозок', 'instasport') ?></div>
                <span>{{card.pauses}}</span>
            </div>
            <# if(card.activated){ #>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Дата окончания', 'instasport') ?></div>
                <span>{{moment(card.pausedDueDate).utc(0).format('dd, D MMMM YYYY')}}</span>
            </div>
             <# } #>

            <# if(card.paused){ #>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Дата разморозки', 'instasport') ?></div>
                <span>{{moment(card.dueDate).utc(0).format('dd, D MMMM YYYY')}}</span>
            </div>
            <# } #>

            <# if(!card.activated){ #>
                    <input type="button"  class="ic-modal-button" data-type="activate" data-card="{{card.id}}" name="card-{{card.id}}" value="<?php _e('Активировать', 'instasport') ?>">
            <# } #>

            <# if(card.activated && !card.paused && card.pauses && card.freezeEnabled){ #>
                    <input type="button"  class="ic-modal-button" data-type="freeze" data-card="{{card.id}}" name="card-{{card.id}}" value="<?php _e('Заморозить', 'instasport') ?>">
            <# } #>

            <# if(card.paused){ #>
                    <input type="button"  class="ic-modal-button" data-type="unfreeze" data-card="{{card.id}}" name="card-{{card.id}}" value="<?php _e('Разморозить', 'instasport') ?>">
            <# } #>


        </div>
        <# } #>
        <# } else{#>
	    <?php _e( 'У вас нет активных абонементов', 'instasport' ) ?>
        <#}#>


    </div>

</div>
<div class="ic-modal-buttons">
    <div class="ic-modal-button" data-alert="<?php _e('Вы действительно хотите выйти ?', 'instasport')?>" data-func="exit">
        <span class="dashicons dashicons-migrate"></span>
        <span><?php _e( 'Выйти', 'instasport' ) ?></span>
    </div>

    <# if(data.modal.data.event){ #>
    <a href="#" class="" data-modal="event">
        <?php _e( "Вернуться назад", 'instasport' ) ?>
    </a>
    <# } #>
</div>