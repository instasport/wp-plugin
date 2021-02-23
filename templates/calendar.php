<div class="ic-loader"></div>
<div class="ic-calendar">
	<?php include 'filters.php'?>

	<div class="ic-controls">
		<div class="ic-ell ic-control ic-control_left dashicons dashicons-arrow-left-alt2"></div>
		<div class="ic-title_month">
            <# if(data.club.args.view == 'month'){ #>
            <span class="ic-desktop ic-month">
                {{ data.club.args.date.format('MMMM YYYY') }}
            </span>
            <# } else{ #>
            <span class="ic-desktop ic-week">
                {{data.club.args.startDate.format('D')}} - {{data.club.args.endDate.format('D MMMM')}}
            </span>
            <# } #>
            <span class="ic-mobile">
                {{ data.club.args.date.format('dddd') }}  <br>
                <span>{{data.club.args.date.format('D MMMM')}}</span>
            </span>
		</div>
        <div class="ic-ell ic-control ic-control_right dashicons dashicons-arrow-right-alt2"></div>
	</div>
	<div class="ic-for_events">
		<# // Таблица - месяц
		if(data.club.args.view == 'month'){ #>
		<?php include 'month.php' ?>
		<# } #>

		<#  // Таблица - неделя
		if(data.club.args.view == 'week'){ #>
		<?php include 'week.php' ?>
		<# } #>
	</div><!--	/calendar_events	-->
</div>