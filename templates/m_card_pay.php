<!-- Покупка абонемента -->
<div class="ic-modal-title">
	<?php _e( "Покупка абонемента", 'instasport' ) ?>
    <span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
    <# let payments = data.modal.data.card.payment.filter(item => item !== -1); #>
    <div class="ic-payments">
        <# for(let pcode of payments){ #>

        <# if(pcode == 2){ // Liqpay #>
        <div class="ic-payment" data-type="liqpay">
            <span>{{instasport.lang.payment[pcode]}}</span>
            <img src="<?php echo INSTASPORT_URL . '/img/card.png' ?>">
            <form method="post" action="{{data.modal.data.card.liqpay.action}}" accept-charset="utf-8">
                <input type="hidden" name="data" value="{{data.modal.data.card.liqpay.data}}">
                <input type="hidden" name="signature" value="{{data.modal.data.card.liqpay.signature}}">
            </form>
        </div>
        <a class="ic-payment-offer" target="_blank"
           href="https://instasport.co/club/{{data.club.slug}}/offer/"><?php echo sprintf( __( 'Нажимая "%s" вы соглашаетесь с договором', 'instasport' ), '{{instasport.lang.payment[pcode]}}' ) ?></a>

        <# }else if(pcode == 4){ // Счет #>
        <div class="ic-payment" data-type="account">
            <span>{{instasport.lang.payment[pcode]}}</span>
        </div>

        <# }else if(pcode == 11){ // WayForPay #>
        <div class="ic-payment" data-type="wayforpay">
            {{instasport.lang.payment[pcode]}}
            <img src="<?php echo INSTASPORT_URL . '/img/card.png' ?>">
            <form method="post" action="{{data.modal.data.card.wayforpay.action}}" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="{{data.modal.data.card.wayforpay.merchantAccount}}">
                <input type="hidden" name="merchantAuthType" value="SimpleSignature">
                <input type="hidden" name="merchantDomainName" value="{{data.modal.data.card.wayforpay.merchantDomainName}}">
                <input type="hidden" name="merchantSignature" value="{{data.modal.data.card.wayforpay.merchantSignature}}">
                <input type="hidden" name="orderReference" value="{{data.modal.data.card.wayforpay.orderReference}}">
                <input type="hidden" name="orderDate" value="{{data.modal.data.card.wayforpay.orderDate}}">
                <input type="hidden" name="amount" value="{{data.modal.data.card.wayforpay.amount}}">
                <input type="hidden" name="currency" value="{{data.modal.data.card.wayforpay.currency}}">
                <input type="hidden" name="productName[]" value="{{data.modal.data.card.wayforpay.productName}}">
                <input type="hidden" name="productCount[]" value="{{data.modal.data.card.wayforpay.productCount}}">
                <input type="hidden" name="productPrice[]" value="{{data.modal.data.card.wayforpay.productPrice}}">
                <input type="hidden" name="returnUrl" value="{{data.modal.data.card.wayforpay.returnUrl}}">
                <input type="hidden" name="serviceUrl" value="{{data.modal.data.card.wayforpay.serviceUrl}}">
            </form>
        </div>
        <a class="ic-payment-offer" target="_blank"
           href="https://instasport.co/club/{{data.club.slug}}/offer/"><?php echo sprintf( __( 'Нажимая "%s" вы соглашаетесь с договором', 'instasport' ), '{{instasport.lang.payment[pcode]}}' ) ?></a>
        <# } #>
        <# } #>
    </div>
</div>
<div class="ic-modal-buttons">
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>

