<!-- Профиль пользователя -->
<div class="ic-modal-title">
	<?php _e( "Профиль", 'instasport' ) ?>
	<span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
	<table class="ic-modal-user-profile">
		<tr>
			<th><?php _e('ID', 'instasport') ?></th>
			<td>{{data.user.id}}</td>
		</tr>
		<tr>
			<th><?php _e('Имя', 'instasport') ?></th>
			<td>{{data.user.firstName}}</td>
		</tr>
		<tr>
			<th><?php _e('Фамилия', 'instasport') ?></th>
			<td>{{data.user.lastName}}</td>
		</tr>
		<tr>
			<th><?php _e('Email', 'instasport') ?></th>
			<td>{{data.user.email}}</td>
		</tr>
		<tr>
			<th><?php _e('Телефон', 'instasport') ?></th>
			<td>{{data.user.phone}}</td>
		</tr>
	</table>
</div>
<div class="ic-modal-buttons">
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>