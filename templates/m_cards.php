<!-- Профиль пользователя -->
<div class="ic-modal-title">
	<?php _e( "Абонементы", 'instasport' ) ?>
    <span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
    <div class="ic-cards">
    <# if(data.club.cardTemplates.length){ #>

        <# for(let cardGroup of data.club.cardGroups){ #>
        <div class="ic-card-group">
            <div class="ic-card-group-title">{{cardGroup.title}}</div>
            <# for(let card of data.club.cardTemplates){ #>
                <# if(card.group.id != cardGroup.id)continue; #>
                <div class="ic-card" data-card="{{card.id}}">
                    <div class="ic-card-title">{{card.title}}</div>
                    <div class="ic-card-subtitle">{{card.subtitle}}</div>
                    <div class="ic-card-price">{{card.price}}</div>
                </div>
            <# } #>
        </div>
        <# } #>

    <# }else{#>
	    <?php _e( 'Нет доступных абонементов!', 'instasport' ) ?>
        <#}#>
    </div>
</div>
<div class="ic-modal-buttons">
    <# if(data.modal.data.event){ #>
    <a href="#" class="" data-modal="event">
		<?php _e( "Вернуться назад", 'instasport' ) ?>
    </a>
    <# } #>
</div>