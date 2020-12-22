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
        <div><span><?php _e( "Дата", 'instasport' ) ?>:</span> {{visit.event.date.substring(0, 10)}}</div>
        <div><span><?php _e( "Начало", 'instasport' ) ?>:</span> {{instaGetTime(visit.event.date)}}</div>
        <div><span><?php _e( "Продолжительность", 'instasport' ) ?>:</span> {{visit.event.duration}}</div>
        <div><span><?php _e( "Зал", 'instasport' ) ?>:</span> {{visit.event.hall.title}}</div>
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
        <div class="ic-visit-cancel" data-visit="{{visit.id}}" data-alert="<?php  _e( "Вы уверены что хотите отменить", 'instasport' ) ?> {{visit.event.title}} {{visit.event.date.substring(0, 10)}} {{instaGetTime(visit.event.date)}}?">
            <?php _e( "Отменить", 'instasport' ) ?>
        </div>
        <# } #>
    </div>
    <# } #>
    <#}else{#>
	<?php _e( 'Вы не записаны ни на одну тренировку!', 'instasport' ) ?>
    <#}#>
</div>
<div class="ic-modal-buttons">
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>

