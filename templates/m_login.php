<!-- Экран 1 (Login) -->
<form>
    <div class="ic-modal-title">
		<?php _e( "Введите номер телефона", 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content">
        <label class="ic-form-field">
            <input type="text" name="phone" value="{{data.user.phone}}" class="ic-phone">
        </label>
    </div>
    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit ic-loader">
            <input type="submit" value="<?php _e( 'Дальше', 'instasport' ) ?>">
        </label>
    </div>
</form>