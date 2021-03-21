<div class="ic-wrapper">
    <div class="ic-modal">
            <# // Панель пользователя
            if(data.user.id && data.user.profile.rulesAccepted && (data.user.emailConfirmed || data.user.emailSkip)){ #>
            <div class="ic-modal-user-panel">
                <div class="ic-modal-user-button {{data.modal.step == 'cards' ? 'current' : ''}}" data-step="cards">
                    <span class="dashicons dashicons-tickets-alt"></span>
                    <span><?php _e( 'Абонементы', 'instasport' ) ?></span>
                </div>
                <div class="ic-modal-user-button {{data.modal.step == 'visits' ? 'current' : ''}}" data-step="visits">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <span><?php _e( 'Тренировки', 'instasport' ) ?></span>
                </div>
                <div class="ic-modal-user-button {{data.modal.step == 'profile' ? 'current' : ''}}" data-step="profile">
                    <span class="dashicons dashicons-admin-users"></span>
                    <span><?php _e( 'Профиль', 'instasport' ) ?></span>
                </div>
                <div class="ic-modal-user-button" data-alert="<?php _e('Вы действительно хотите выйти ?', 'instasport')?>" data-func="exit">
                    <span class="dashicons dashicons-migrate"></span>
                    <span><?php _e( 'Выйти', 'instasport' ) ?></span>
                </div>
            </div>
            <# } #>
    </div>
</div>
