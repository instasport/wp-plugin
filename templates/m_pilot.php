<form>
    <div class="ic-modal-title">
        <?php _e( 'Запись на пробную тренировку', 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content">
        <# if(!data.user.id){ #>
        <label class="ic-form-field">
            <input type="text" name="firstName" value="{{data.user.firstName}}"
                   placeholder="<?php _e( "Имя", 'instasport' ) ?>">
        </label>
        <label class="ic-form-field">
            <input type="text" name="lastName" value="{{data.user.lastName}}"
                   placeholder="<?php _e( "Фамилия", 'instasport' ) ?>">
        </label>
        <label class="ic-form-field">
            <input type="text" name="phone" value="{{data.user.phone}}" class="ic-phone">
        </label>
        <# } #>
    </div>


    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit ic-loader">
            <input type="submit" value="<?php _e( 'Записаться', 'instasport' ) ?>">
        </label>
    </div>
</form>