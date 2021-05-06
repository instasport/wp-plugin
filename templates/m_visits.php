<!-- Мои тренировки -->
<div class="ic-modal-title">
	<?php _e( "Мои тренировки", 'instasport' ) ?>
    <span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
    <# if(data.user.visits.length){ #>
    <# for(let visit of data.user.visits){
    console.log('visit', visit);
    #>
    <div class="ic-visit">
        <div class="ic-visit-title">{{visit.event.title}}</div>
        <div>
            <span><?php _e( "Дата", 'instasport' ) ?>:</span>
            {{ moment(visit.event.date).format('dd, D MMMM YYYY')}}
        </div>
        <div><span><?php _e( "Начало", 'instasport' ) ?>:</span> {{moment(visit.event.date).format('HH:mm')}}</div>
        <div><span><?php _e( "Продолжительность", 'instasport' ) ?>:</span> {{visit.event.duration}}</div>
        <div><span><?php _e( "Студия", 'instasport' ) ?>:</span> {{visit.event.hall.title}}</div>
        <div>
            <span>
            <#       if(visit.paid == '-1'){ #> <?php _e( 'не оплачено', 'instasport' ) ?>
            <# }else if(visit.paid == '0') { #> <?php _e( 'подарок', 'instasport' ) ?>
            <# }else if(visit.paid == '1') { #> <?php _e( 'оплачено за наличные', 'instasport' ) ?>
            <# }else if(visit.paid == '2') { #> <?php _e( 'оплачено онлайн через Liqpay', 'instasport' ) ?>
            <# }else if(visit.paid == '3') { #> <?php _e( 'оплачено с абонемента', 'instasport' ) ?>
            <# }else if(visit.paid == '4') { #> <?php _e( 'оплачено со внутреннего счета', 'instasport' ) ?>
            <# }else if(visit.paid == '5') { #> <?php _e( 'посещение забронировано', 'instasport' ) ?>
            <# }else if(visit.paid == '6') { #> <?php _e( 'оплачено по безналу (терминал в клубе)', 'instasport' ) ?>
            <# }else if(visit.paid == '10'){ #> <?php _e( 'оплачено онлайн через Portmone', 'instasport' ) ?>
            <# }else if(visit.paid == '11'){ #> <?php _e( 'оплачено онлайн через WayForPay', 'instasport' ) ?>
            <# }#>
            </span>
        </div>

        <# if(visit.refundable){ #>
        <div class="ic-visit-cancel" data-func="delete_visit" data-visit="{{visit.id}}"
             data-alert="<?php  _e( "Вы уверены что хотите отменить", 'instasport' ) ?> {{visit.event.title}} {{moment(visit.event.date).utc(0).format('dd, D MMMM YYYY HH:mm')}} ?">
            <?php _e( "Отменить", 'instasport' ) ?>
        </div>
        <# } #>
    </div>
    <# } #>
    <#}else{#>
	<?php _e( 'У вас нет запланированных посещений', 'instasport' ) ?>
    <#}#>
</div>
<div class="ic-modal-buttons">
    <# if(data.modal.data.event){ #>
    <a href="#" class="" data-modal="event">
		<?php _e( "Вернуться назад", 'instasport' ) ?>
    </a>
    <# } #>
</div>

