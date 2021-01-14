<!-- Экран 4 (Ввод E-mail) -->
<form>
    <div class="ic-modal-title"><?php _e( "Введите ваш E-mail", 'instasport' ) ?><span
                class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content">
        <label class="ic-form-field">
            <input type="text" name="email" value="{{data.user.email}}" class="ic-email">
        </label>
    </div>
    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit">
            <input type="submit" value="<?php _e( 'Дальше', 'instasport' ) ?>">
            <a href="#" class="ic-next"><?php _e( "Пропустить", 'instasport' ) ?></a>
        </label>
    </div>
</form>