<!-- Экран 3 (Verify) -->
<form>
    <div class="ic-modal-title"><?php _e( "Введите код из SMS", 'instasport' ) ?><span
                class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content">
        <label class="ic-form-field">
            <input type="text" name="sms" value="{{data.user.sms}}" class="ic-sms">
        </label>
    </div>
    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit ic-loader">
            <input type="submit" value="<?php _e( 'Дальше', 'instasport' ) ?>">
        </label>
        <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
    </div>
</form>