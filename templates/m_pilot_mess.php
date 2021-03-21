<form>
    <div class="ic-modal-title">
        <?php _e( 'Запись на пробную тренировку', 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span></div>
    <div class="ic-modal-content">
        <div style="text-align: center;font-size: 16px;padding: 20px;">
            <# console.log(data) #>
            <# if(data.error){ #>
                {{ data.error }}
            <# }else{ #>
	        <?php _e( 'Заявка отправлена', 'instasport' ) ?>
            <# }#>
        </div>
    </div>

    <div class="ic-modal-buttons">
    </div>
</form>