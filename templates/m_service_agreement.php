<form>
    <div class="ic-modal-title"><?php _e( "Условия конфиденциальности Instasport", 'instasport' ) ?><span
                class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content rules">
        {{{ data.club.serviceAgreement.replaceAll("\r\n", '<br>') }}}
    </div>
    <div class="ic-modal-buttons">
        <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
    </div>
</form>