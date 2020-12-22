<div class="ic-wrapper">
    <div class="ic-modal">
        <form>
            <# // Панель пользователя
            if(data.user.accessToken){ #>
            <div class="ic-modal-user-panel">
                <div class="ic-modal-user-button {{data.step == 'profile' ? 'current' : ''}}" data-step="profile">
                    <span class="dashicons dashicons-admin-users"></span>
                    <span><?php _e( 'Профиль', 'instasport' ) ?></span>
                </div>
                <div class="ic-modal-user-button {{data.step == 'visits' ? 'current' : ''}}" data-step="visits">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <span><?php _e( 'Мои тренировки', 'instasport' ) ?></span>
                </div>
                <div class="ic-modal-user-button" data-alert="<?php _e('Вы действительно хотите выйти ?', 'instasport')?>" data-step="exit">
                    <span class="dashicons dashicons-migrate"></span>
                    <span><?php _e( 'Выход', 'instasport' ) ?></span>
                </div>
            </div>
            <# } #>
        </form>
    </div>
</div>
