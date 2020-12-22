<!-- Запись на тренировку -->
<div class="ic-modal-title">
	<?php _e( "Запись на тренировку", 'instasport' ) ?>
	<span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
	<table class="ic-modal-event">
		<tr>
			<th><?php _e('Название', 'instasport') ?></th>
			<td>{{data.event.title}}</td>
		</tr>
		<tr>
			<th><?php _e('Зал', 'instasport') ?></th>
			<td>{{data.event.hall.title}}</td>
		</tr>
		<tr>
			<th><?php _e('Цена', 'instasport') ?></th>
			<td>{{data.event.price}}</td>
		</tr>
		<tr>
			<th><?php _e('Дата', 'instasport') ?></th>
			<td>{{data.event.date.substring(0, 10)}}</td>
		</tr>
		<tr>
			<th><?php _e('Время', 'instasport') ?></th>
			<td>{{instaGetTime(data.event.date)}}</td>
		</tr>
		<tr>
			<th><?php _e('Продолжительность', 'instasport') ?></th>
			<td>{{data.event.duration}} минут</td>
		</tr>
	</table>
	{{{data.event.description}}}
</div>
<div class="ic-modal-buttons">
    <# if(data.event.payment){ #>
        <# let payments = data.event.payment.filter(item => item !== -1); #>
        <# if(payments.length === 1){ #>
            <label class="ic-modal-button ic-submit ic-loader">
                <input type="submit" value="{{instasport.lang.payment[payments[0]]}}" data-payment="payment[0]">
            </label>
        <# }else if(payments.length > 1){ #>
           <label class="ic-modal-button">
               <input type="button" value="<?php _e('Записаться')?>" data-step="payment">
           </label>
        <# } #>
    <# }else{ #>
        <div class="ic-modal-mess">{{data.event.status}}</div>
    <# } #>
</div>