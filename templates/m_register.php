<!-- Экран 2 (Signup) -->
<form>
    <div class="ic-modal-title">
		<?php _e( "Регистрация в системе", 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content">
        <label class="ic-form-field">
            <input type="text" name="firstName" value="{{data.user.firstName}}"
                   placeholder="<?php _e( "Имя", 'instasport' ) ?>">
        </label>
        <label class="ic-form-field">
            <input type="text" name="lastName" value="{{data.user.lastName}}"
                   placeholder="<?php _e( "Фамилия", 'instasport' ) ?>">
        </label>
        <div class="ic-form-fields">
            <label class="ic-form-field-radio">
                <input type="radio" name="gender" value="1">
				<?php _e( "Мужчина", 'instasport' ) ?>
            </label>
            <label class="ic-form-field-radio">
                <input type="radio" name="gender" value="2">
				<?php _e( "Женщина", 'instasport' ) ?>
            </label>
        </div>
        <label class="ic-form-field">
            <span><?php _e( "Введите дату рождения", 'instasport' ) ?></span>
            <input type="text" name="birthday" value="{{data.user.birthday}}" class="ic-date"
                   placeholder="<?php _e( "__.__.____", 'instasport' ) ?>">
        </label>
    </div>


    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit ic-loader">
            <input type="submit" value="<?php _e( 'Дальше', 'instasport' ) ?>">
        </label>
        <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
    </div>
</form>