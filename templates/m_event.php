<!-- Запись на тренировку -->
<form>
    <div class="ic-modal-title">
		<?php _e( "Запись на тренировку", 'instasport' ) ?>
        <span class="dashicons dashicons-no-alt"></span>
    </div>
    <div class="ic-modal-content">
        <div class="ic-modal-event">
            <div class="ic-modal-title">
                {{data.modal.data.event.title}}
            </div>
            <# if(data.modal.data.event.status){ #>
            <div class="ic-modal-mess">{{data.modal.data.event.status}}</div>
            <# } #>

            <# if(data.modal.data.event.image){ #>
            <div class="ic-modal-img">
                <img src="{{data.modal.data.event.image}}">
            </div>
            <# } #>

            <# if(data.modal.data.event.instructors.length){ #>
                <# for(let [key, instructor] of  Object.entries(data.modal.data.event.instructors)){ #>
                <# if(!instructor.isInstructorVisible)continue; #>
                <div class="ic-instructor" data-func="instructor" data-instructor="{{key}}">
                     <?php _e( 'Инструктор', 'instasport' ) ?>
                     {{instructor.firstName}} {{instructor.lastName}}
                </div>
                <# } #>
            <# } #>

            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Студия', 'instasport' ) ?></div>
                {{data.modal.data.event.hall.title}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Цена', 'instasport' ) ?></div>
                {{data.modal.data.event.price}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Дата', 'instasport' ) ?></div>
                {{data.modal.data.event.date.format('dd, D MMMM YYYY')}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Время', 'instasport' ) ?></div>
                {{data.modal.data.event.date.format('HH:mm')}}
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e( 'Продолжительность', 'instasport' ) ?></div>
                {{data.modal.data.event.duration}} <?php _e( 'минут', 'instasport' ) ?>
            </div>
            {{{data.modal.data.event.description}}}
        </div>

    </div>
    <div class="ic-modal-buttons">
        <# let visit = data.modal.data.event.visit; #>
        <# if(visit && visit.refundable){ #>
            <label class="ic-modal-button" data-func="delete_event_visit" data-visit="{{visit.id}}"
                   data-alert="<?php  _e( "Вы уверены что хотите отменить", 'instasport' ) ?> {{visit.event.title}} {{moment(visit.event.date).utc(0).format('dd, D MMMM YYYY HH:mm')}} ?"
            >
                <input type="button" value="<?php _e( 'Отменить', 'instasport' ) ?>">
            </label>
        <# }else if(data.modal.data.event.payment && data.modal.data.event.payment.length){ #>
            <# if(data.modal.data.event.payment.length === 1 && data.modal.data.event.payment[0] != 3){ #>
	            <?php include 'm_event_pay.php' ?>
            <# }else{ #>
                <label class="ic-modal-button"  data-modal="event_pay_list" >
                    <input type="submit" value="<?php _e( 'Записаться', 'instasport' ) ?>">
                </label>
            <# } #>
        <# } #>
    </div>
</form>