<form>
    <div class="ic-modal-title"><?php _e( "Правила клуба", 'instasport' ) ?><span
                class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content rules">
        <# if(data.club.rules){ #>
        {{{ data.club.rules.replaceAll("\r\n", '<br>') }}} 
        <# } #>
    </div>
    <div class="ic-modal-buttons">
        <label class="ic-modal-checkbox">
            <input type="checkbox" class="ic-rules">
            <a href="#" data-modal="offer" ><?php _e( "Условия договора клуба", 'instasport' ) ?></a>
        </label>
        <label class="ic-modal-checkbox">
            <input type="checkbox" class="ic-rules">
            <a href="#" data-modal="service_agreement" ><?php _e( "Условия конфиденциальности Instasport", 'instasport' ) ?></a>
        </label>

        <label class="ic-modal-button ic-submit ic-loader">
            <input type="submit" disabled="disabled" value="<?php _e( 'Принять', 'instasport' ) ?>">
        </label>
    </div>
</form>