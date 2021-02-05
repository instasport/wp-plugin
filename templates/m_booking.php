<!-- Запись на тренировку -->
<div class="ic-modal-title">
	<?php _e( "Запись на тренировку", 'instasport' ) ?>
    <span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
    <# let payments = data.event.payment.filter(item => item !== -1); #>
    <div class="ic-payments">
        <# for(let pcode of payments){ #>

        <# if(pcode == 2){ // Liqpay #>
        <div class="ic-payment" data-type="liqpay">
            <span>{{instasport.lang.payment[pcode]}}</span>
            <img src="<?php echo INSTASPORT_URL . '/img/card.png' ?>">
            <form method="post" action="{{data.event.liqpay.action}}" accept-charset="utf-8">
                <input type="hidden" name="data" value="{{data.event.liqpay.data}}">
                <input type="hidden" name="signature" value="{{data.event.liqpay.signature}}">
            </form>
        </div>
        <a class="ic-payment-offer" target="_blank"
           href="https://instasport.co/club/{{instasport.club}}/offer/"><?php echo sprintf( __( 'Нажимая "%s" вы соглашаетесь с договором', 'instasport' ), '{{instasport.lang.payment[pcode]}}' ) ?></a>

        <# }else if(pcode == 4){ // Счет #>
        <div class="ic-payment" data-type="account">
            <span>{{instasport.lang.payment[pcode]}}</span>
        </div>

        <# }else if(pcode == 5){ // Бронирование #>
        <div class="ic-payment" data-type="book">
            <span>{{instasport.lang.payment[pcode]}}</span>
        </div>

        <# }else if(pcode == 11){ // WayForPay #>
        <div class="ic-payment" data-type="wayforpay">
            {{instasport.lang.payment[pcode]}}
            <img src="<?php echo INSTASPORT_URL . '/img/card.png' ?>">
            <form method="post" action="{{data.event.wayforpay.action}}" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="{{data.event.wayforpay.merchantAccount}}">
                <input type="hidden" name="merchantAuthType" value="SimpleSignature">
                <input type="hidden" name="merchantDomainName" value="{{data.event.wayforpay.merchantDomainName}}">
                <input type="hidden" name="merchantSignature" value="{{data.event.wayforpay.merchantSignature}}">
                <input type="hidden" name="orderReference" value="{{data.event.wayforpay.orderReference}}">
                <input type="hidden" name="orderDate" value="{{data.event.wayforpay.orderDate}}">
                <input type="hidden" name="amount" value="{{data.event.wayforpay.amount}}">
                <input type="hidden" name="currency" value="{{data.event.wayforpay.currency}}">
                <input type="hidden" name="productName[]" value="{{data.event.wayforpay.productName}}">
                <input type="hidden" name="productCount[]" value="{{data.event.wayforpay.productCount}}">
                <input type="hidden" name="productPrice[]" value="{{data.event.wayforpay.productPrice}}">
                <input type="hidden" name="returnUrl" value="{{ instaUrl('visits') }}">
                <input type="hidden" name="serviceUrl" value="{{ instaUrl('visits') }}">
            </form>
        </div>
        <a class="ic-payment-offer" target="_blank"
           href="https://instasport.co/club/{{instasport.club}}/offer/"><?php echo sprintf( __( 'Нажимая "%s" вы соглашаетесь с договором', 'instasport' ), '{{instasport.lang.payment[pcode]}}' ) ?></a>
        <# } #>

        <# } #>

        <# if(data.event.cards.length){ // Абонементы #>
        <# for(let card of data.event.cards){ #>
        <div class="ic-payment ic-user-card" data-type="card" data-card="{{card.id}}">
            <div class="ic-card-title">{{card.template.group.title}} / {{card.template.title}} {{card.template.subtitle}}</div>
            <div class="ic-card-subtitle">{{card.template.subtitle}}</div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Количество посещений', 'instasport') ?></div>
                <span>{{card.amount}}</span>
            </div>
            <div class="ic-modal-row">
                <div class="ic-modal-row-title"><?php _e('Дата окончания', 'instasport') ?></div>
                <span>{{card.dueDate}}</span>
            </div>
        </div>
        <# } #>
        <# } #>
    </div>
</div>
<div class="ic-modal-buttons">
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>

