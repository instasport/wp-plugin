<div class="instasport-button">
<# if(!data.user){ // Инициализация #>
...
<# } else if(!data.user.id){ // Не авторизован #>
	<?php _e( 'Войти', 'instasport' ) ?>
<# } else { // Авторизован #>
    <span class="dashicons dashicons-admin-users"></span> {{data.user.firstName}}
<# } #>
</div>