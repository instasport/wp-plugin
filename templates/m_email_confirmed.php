<!-- Экран 7 (E-mail подтвержден) -->
<form>
    <div class="ic-modal-title">
		<?php _e( "E-mail подтвержден!", 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span>
    </div>
    <div class="ic-modal-content">

    </div>
    <div class="ic-modal-buttons">
        <label class="ic-modal-button ic-submit">
            <# if(data.event.id){ #>
            <input type="submit" value="<?php _e( 'Дальше', 'instasport' ) ?>">
            <# } #>
        </label>
    </div>
</form>
