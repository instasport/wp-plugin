<div class="ic-filters">
	<!-- Период -->
	<div class="ic-row_1">

        <# if(data.club.halls.length > 1){ #>
		<div class="ic-halls">
			<ul>
				<# for( let hall of data.club.halls){ #>
				<li class="{{ hall.id == data.club.args.hall ? 'active' : '' }}">
					<a class="ic-ell ic-filter-item" data-val="{{hall.id}}" href="#">{{ hall.title }}</a>
				</li>
				<# } #>
			</ul>
		</div>
        <# } #>

        <# let hall = data.club.halls.find( e => e.id == data.club.args.hall); #>
        <# if(hall.zones.length > 1){ #>
        <div class="ic-zones">
            <ul>
                <li class="{{ ! data.club.args.zone ? 'active' : '' }}">
                    <a class="ic-ell ic-filter-item" data-val="0" href="#"><?php _e('Все зоны', 'instasport') ?></a>
                </li>
                <# for( let zone of hall.zones){ #>
                <li class="{{ zone.id == data.club.args.zone ? 'active' : '' }}">
                    <a class="ic-ell ic-filter-item" data-val="{{zone.id}}" href="#">{{ zone.title }}</a>
                </li>
                <# } #>
            </ul>
        </div>
        <# } #>

		<div class="ic-view ic-desktop">
			<ul>
				<li class="{{ 'month' == data.club.args.view ? 'active' : '' }}">
					<a class="ic-ell ic-filter-item" href="#" data-val="month"><?php _e( 'Месяц', 'instasport' ) ?></a>
				</li>
				<li class="{{ 'week' == data.club.args.view ? 'active' : '' }}">
					<a class="ic-ell ic-filter-item" href="#" data-val="week"><?php _e( 'Неделя', 'instasport' ) ?></a>
				</li>
			</ul>
		</div>
	</div>

	<!-- Фильтр -->
	<div class="ic-row_2">
		<ul>
			<# // Фильтры 2
			let filters = {
			training : '<?php _e( 'Тренировка', 'instasport' ) ?>',
			instructor: '<?php _e( 'Инструктор', 'instasport' ) ?>',
			complexity: '<?php _e( 'Сложность', 'instasport' ) ?>',
			activity : '<?php _e( 'Направление', 'instasport' ) ?>',
			};

			for(let [key, title] of Object.entries(filters)){
			#>
			<!-- Тренировка -->
			<li class="{{data.club.args.filters[key] ? 'choosed' : ''}}">
				<div class="insta_dropdown insta_dropdown-training">
					<a class="ic-ell insta_dropdown-title ic-filter-item ic-filter-item-{{key}}" href="">
						{{title}}
						<span class="ic-ell">
							{{data.club.args.filters[key] ? data.filters[key][data.club.args.filters[key]].title : ''}}
						</span>
					</a>
					<div class="ic-bg2 insta_dropdown-window" style="display: none;">
						<div class="insta_dropdown-content">
							<div class="insta_filter_1">
								<!-- Все -->
								<div class="insta_filter_1-item">
									<div class="insta_table">
										<div class="insta_table-tr">
											<div class="insta_table-td" style="background-color: {{data.club.secondaryTextColor}};"></div>
											<div class="insta_table-td">
												<a href="#" data-filter="{{key}}"
												   data-val="0"><?php _e( 'Все', 'instasport' ) ?></a>
											</div>
										</div>
									</div>
								</div>
								<# // Варианты
								for (let [k,item] of Object.entries(data.filters[key])) {
								#>
								<div class="insta_filter_1-item">
									<div class="insta_table">
										<div class="insta_table-tr">
											<div class="insta_table-td"
											     style="background-color: {{ item.color ? item.color : data.club.primaryTextColor }};"></div>
											<div class="insta_table-td">
												<a href="#" data-filter="{{key}}"
												   data-val="{{k}}">{{item.title}}</a>
												<# // Продолжительность
												if(item.duration){
												#>
												<span>({{item.duration}} <?php _e( 'мин.', 'instasport' ) ?>)</span>
												<# } #>
											</div>
										</div>
									</div>
								</div>
								<# } #>
							</div>
						</div>
					</div>
			</li>
			<# } #>
		</ul>
	</div><!--	/filters-row-2	-->
</div><!--	/filters-view	-->