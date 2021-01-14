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
			{{data.event.account}}
		</div>
	</div>
    <div class="ic-modal-user-cards">
        <div class="ic-modal-title">
            Мои абонементы
        </div>

        <# if(instasport.user.cards.length){ // Абонементы #>
        <# for(let card of instasport.user.cards){ #>
        <div class="ic-modal-user-card {{ card.activated ? 'activated' : 'inactive' }}" data-card="{{card.id}}">
            <div class="ic-card-title">{{card.template.group.title}} / {{card.template.title}} {{card.template.subtitle}}</div>
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

            <# if(!card.activated){ #>
            <div class="ic-modal-button">
                <?php _e('Активировать', 'instasport') ?>
            </div>
            <# } #>


            <# if(card.paused){ #>
            <div class="ic-modal-button">
		        <?php _e('Разморозить', 'instasport') ?>
            </div>
            <# } #>


        </div>
        <# } #>
        <# } else{#>
	    <?php _e( 'У вас нет активных абонементов', 'instasport' ) ?>
        <#}#>


    </div>

</div>
<div class="ic-modal-buttons">
    <div class="ic-modal-button" data-alert="<?php _e('Вы действительно хотите выйти ?', 'instasport')?>" data-step="exit">
        <span class="dashicons dashicons-migrate"></span>
        <span><?php _e( 'Выйти', 'instasport' ) ?></span>
    </div>
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>