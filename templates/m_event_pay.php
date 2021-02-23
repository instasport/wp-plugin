    <# let payments = data.modal.data.event.payment; #>
    <div class="ic-payments">
        <# for(let pcode of payments){ #>

        <# if(pcode === -1){ // Счет #>
        <div class="ic-payment" data-type="requestVisit">
            <span>{{instasport.lang.payment[pcode]}}</span>
        </div>

        <# }else if(pcode === 2){ // Liqpay #>
        <div class="ic-payment" data-type="liqpay">
            <span>{{instasport.lang.payment[pcode]}}</span>
            <img src="<?php echo INSTASPORT_URL . '/img/card.png' ?>">
            <form method="post" action="{{data.modal.data.event.liqpay.action}}" accept-charset="utf-8">
                <input type="hidden" name="data" value="{{data.modal.data.event.liqpay.data}}">
                <input type="hidden" name="signature" value="{{data.modal.data.event.liqpay.signature}}">
            </form>
        </div>
        <a class="ic-payment-offer" target="_blank"
           href="https://instasport.co/club/{{data.club.slug}}/offer/">
			<?php echo sprintf( __( 'Нажимая "%s" вы соглашаетесь с договором', 'instasport' ), '{{instasport.lang.payment[pcode]}}' ) ?>
        </a>

        <# }else if(pcode === 4){ // Счет #>
        <div class="ic-payment" data-type="payVisitFromAccount">
            <span>{{instasport.lang.payment[pcode]}}</span>
        </div>

        <# }else if(pcode === 5){ // Бронирование #>
        <div class="ic-payment" data-type="bookVisit">
            <span>{{instasport.lang.payment[pcode]}}</span>
        </div>

        <# }else if(pcode === 11){ // WayForPay #>
        <div class="ic-payment" data-type="wayforpay">
            {{instasport.lang.payment[pcode]}}
            <img src="<?php echo INSTASPORT_URL . '/img/card.png' ?>">
            <form method="post" action="{{data.modal.data.event.wayforpay.action}}" accept-charset="utf-8">
                <input type="hidden" name="merchantAccount" value="{{data.modal.data.event.wayforpay.merchantAccount}}">
                <input type="hidden" name="merchantAuthType" value="SimpleSignature">
                <input type="hidden" name="merchantDomainName"
                       value="{{data.modal.data.event.wayforpay.merchantDomainName}}">
                <input type="hidden" name="merchantSignature"
                       value="{{data.modal.data.event.wayforpay.merchantSignature}}">
                <input type="hidden" name="orderReference" value="{{data.modal.data.event.wayforpay.orderReference}}">
                <input type="hidden" name="orderDate" value="{{data.modal.data.event.wayforpay.orderDate}}">
                <input type="hidden" name="amount" value="{{data.modal.data.event.wayforpay.amount}}">
                <input type="hidden" name="currency" value="{{data.modal.data.event.wayforpay.currency}}">
                <input type="hidden" name="productName[]" value="{{data.modal.data.event.wayforpay.productName}}">
                <input type="hidden" name="productCount[]" value="{{data.modal.data.event.wayforpay.productCount}}">
                <input type="hidden" name="productPrice[]" value="{{data.modal.data.event.wayforpay.productPrice}}">
                <input type="hidden" name="returnUrl" value="{{ data.modal.data.event.wayforpay.returnUrl }}">
                <input type="hidden" name="serviceUrl" value="{{ data.modal.data.event.wayforpay.serviceUrl }}">
            </form>
        </div>
        <a class="ic-payment-offer" target="_blank"
           href="https://instasport.co/club/{{data.club.slug}}/offer/"><?php echo sprintf( __( 'Нажимая "%s" вы соглашаетесь с договором', 'instasport' ), '{{instasport.lang.payment[pcode]}}' ) ?></a>
        <# } #>

        <# } #>

         <# if(data.modal.data.event.cards.length){ // Абонементы #>
            <# for(let card of data.modal.data.event.cards){ #>
            <div class="ic-payment ic-user-card" data-type="payVisitByCard" data-card="{{card.id}}">
                <div class="ic-card-title">{{card.template.group.title}} / {{card.template.title}}
                    {{card.template.subtitle}}
                </div>
                <div class="ic-card-subtitle">{{card.template.subtitle}}</div>
                <div class="ic-modal-row">
                    <div class="ic-modal-row-title"><?php _e( 'Количество посещений', 'instasport' ) ?></div>
                    <span>{{card.amount}}</span>
                </div>
                <div class="ic-modal-row">
                    <div class="ic-modal-row-title"><?php _e( 'Дата окончания', 'instasport' ) ?></div>
                    <span>{{card.dueDate}}</span>
                </div>
            </div>
            <# } #>
        <# } #>
    </div>

