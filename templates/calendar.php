<div class="ic-loader"></div>
<div class="ic-calendar">
	<?php include 'filters.php'?>

	<div class="ic-controls">
		<div class="ic-ell ic-control ic-control_left dashicons dashicons-arrow-left-alt2"></div>
		<div class="ic-title_month">
            <# if(data.v.view == 'month'){ #>
            <span class="ic-desktop ic-month">
                {{{ instaDateStr(data.v.cdate, "{F} {Y}") }}}
            </span>
            <# } else{ #>
            <span class="ic-desktop ic-week">
                {{{ instaDateStr(new Date(data.v.startDate), "{d} - ") }}}{{{ instaDateStr(new Date(data.v.endDate), "{d} {K}") }}}
            </span>
            <# } #>
            <span class="ic-mobile">
                {{{instaDateStr(data.v.cdate, '{l}<br><span>{d} {K}</span>')}}}
            </span>
		</div>
        <div class="ic-ell ic-control ic-control_right dashicons dashicons-arrow-right-alt2"></div>
	</div>
	<div class="ic-for_events">
		<# // Таблица - месяц
		if(data.v.view == 'month'){ #>
		<?php include 'month.php' ?>
		<# } #>

		<#  // Таблица - неделя
		if(data.v.view == 'week'){ #>
		<?php include 'week.php' ?>
		<# } #>
	</div><!--	/calendar_events	-->
</div>