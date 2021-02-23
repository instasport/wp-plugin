<!-- Экран 6 (Ожидание подтверждения E-mail) -->
<form>
    <div class="ic-modal-title">
		<?php _e( "Подтвердите e-mail в письме на вашей почте", 'instasport' ) ?> {{data.user.email}}
        <span class="dashicons dashicons-no-alt"></span>
    </div>
    <div class="ic-modal-content">
        <div class="ic-message-error" style="display: none">
			<?php _e( "E-mail не подтвержден", 'instasport' ) ?>
        </div>
    </div>
    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit ic-loader">
            <input type="submit" value="<?php _e( 'Подтвержден!', 'instasport' ) ?>">
        </label>
        <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
    </div>
</form>