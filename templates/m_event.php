<!-- Запись на тренировку -->
<form>
    <div class="ic-modal-title">
		<?php _e( "Запись на тренировку", 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span>
    </div>
    <div class="ic-modal-content">
        <div class="ic-modal-event">
            <div class="ic-modal-title">
                {{data.event.title}}
            </div>
            <# if(data.event.status){ #>
            <div class="ic-modal-mess">{{data.event.status}}</div>
            <# } #>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Зал', 'instasport' ) ?></div>
                {{data.event.hall.title}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Цена', 'instasport' ) ?></div>
                {{data.event.price}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Дата', 'instasport' ) ?></div>
                {{data.event.date.substring(0, 10)}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Время', 'instasport' ) ?></div>
                {{instaGetTime(data.event.date)}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Продолжительность', 'instasport' ) ?></div>
                {{data.event.duration}} минут
            </div>
        </div>
        {{{data.event.description}}}
    </div>
    <div class="ic-modal-buttons">
        <# if(data.event.payment.length){ #>
        <label class="ic-modal-button ic-submit">
            <input type="submit" value="<?php _e( 'Записаться', 'instasport' ) ?>">
        </label>
        <# } #>
    </div>
</form>