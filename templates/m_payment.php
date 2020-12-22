<!-- Профиль пользователя -->
<div class="ic-modal-title">
	<?php _e( "Запись на тренировку", 'instasport' ) ?>
    <span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
    <# let payments = data.event.payment.filter(item => item !== -1); #>
    <# for(let payment of payments){ #>
    <label class="ic-modal-button">
        <input type="button" value="{{instasport.lang.payment[payment]}}">
    </label>
    <# } #>
</div>
<div class="ic-modal-buttons">
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>