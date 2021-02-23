<!-- Просмотр абонемента -->
<form>
    <div class="ic-modal-title">
		{{data.modal.data.card.group.title}}
        <span class="dashicons dashicons-no-alt"></span>
    </div>
    <div class="ic-modal-content">
        <div class="ic-modal-event">
            <div class="ic-modal-title">
                {{data.modal.data.card.title}}
            </div>
            {{data.modal.data.card.subtitle}}

            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Количество тренировок', 'instasport' ) ?></div>
                {{data.modal.data.card.amount}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Срок действия', 'instasport' ) ?></div>
                {{data.modal.data.card.duration}} <?php _e( 'дней', 'instasport' ) ?>
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Цена', 'instasport' ) ?></div>
                {{data.modal.data.card.price}}
            </div>
        </div>
        {{{data.modal.data.card.description}}}
    </div>
    <div class="ic-modal-buttons">
        <# if(data.modal.data.card.payment.length){ #>
        <label class="ic-modal-button ic-submit">
            <input type="submit" data-modal="card_pay" value="<?php _e( 'Купить', 'instasport' ) ?>">
        </label>
        <# } #>
        <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
    </div>
</form>