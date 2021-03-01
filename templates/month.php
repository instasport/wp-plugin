<div class="ic-table">
    <div class="ic-thead">
        <div class="ic-tr">
            <div class="ic-th"><?php _e( 'Пн', 'instasport' ) ?></div>
            <div class="ic-th"><?php _e( 'Вт', 'instasport' ) ?></div>
            <div class="ic-th"><?php _e( 'Ср', 'instasport' ) ?></div>
            <div class="ic-th"><?php _e( 'Чт', 'instasport' ) ?></div>
            <div class="ic-th"><?php _e( 'Пт', 'instasport' ) ?></div>
            <div class="ic-th"><?php _e( 'Сб', 'instasport' ) ?></div>
            <div class="ic-th"><?php _e( 'Вс', 'instasport' ) ?></div>
        </div>
    </div>
    <div class="ic-tbody ic-events">
        <#
        let date = data.club.args.startDate.clone().isoWeekday(1);
        let endDate = data.club.args.endDate.clone().isoWeekday(7);



        let now = moment();
        #>
        <div class="ic-tr">
            <# // Вывод дней
            while (date < endDate) {
            #>
            <div class="ic-td ic-for_day {{ date.isSame(now, 'day') ? 'ic-day_today' : ''}} {{ date.isBefore(now) ? 'off' : ''}}">
                <div class="ic-day">
                    <div class="ic-day_number">{{date.format('D')}}</div>

                    <# // События если есть
                    let dateStr = date.format('YYYY-MM-DD');
                    if(data.events[dateStr]){
                    #>
                    <div class="ic-per_day">
                        <#
                        let i = 0;
                        for(let event of data.events[dateStr]){
                        i++;
                        let styles = [];
                        // Использование цветов с api
                        if(instasport.settings.desktop.useApiColors){
                        styles.push('background:'+event.color);
                        styles.push('color:'+event.textColor);
                        }
                        // Скрываем если количество в одной клетке больше допустимого
                        if(i > instasport.settings.desktop.monthView.showEventsPerDay){
                        styles.push('display:none');
                        }
                        styles = styles.join(';');
                        #>
                        <div class="ic-event" style="{{styles}}" data-event="{{event.id}}">
                            <div class="ic-table">
                                <div class="ic-tr">
                                    <div class="ic-td">
                                        <div class="ic-begin_time">{{moment(event.date).format('HH:mm')}}</div>
                                    </div>
                                    <div class="ic-td">
                                        <# // продолжительность тренировки
                                        if(instasport.settings.desktop.monthView.showDuration){
                                        #>
                                        <div class="ic-duration">
                                            {{event.duration}} <?php _e( 'мин.', 'instasport' ) ?>
                                        </div>
                                        <# } #>
                                    </div>
                                </div>
                            </div>

                            <div class="ic-activity">{{event.activity ? event.activity.title : ''}}
                            </div>
                            <div class="ic-title">
                                {{event.title}}
                            </div>


                            <# // свободные места
                            if(instasport.settings.desktop.monthView.showSeats){ #>
                            <div class="ic-seats">
                                <# if(event.hasUser){ #>
						        <?php _e( 'Вы уже записаны', 'instasport' ) ?>
                                <# }else if(event.seats === 0){ #>
						        <?php _e( 'Мест нет', 'instasport' ) ?>
                                <# }else if(event.seats === 1){ #>
						        <?php _e( 'Осталось 1 место', 'instasport' ) ?>
                                <# }else if(event.seats === 2){ #>
						        <?php _e( 'Осталось 2 места', 'instasport' ) ?>
                                <# } #>
                            </div>
                            <# } #>
                        </div>
                        <# } #>
                    </div>

                    <# // кнопка показать еще
                    if(data.events[dateStr].length > instasport.settings.desktop.monthView.showEventsPerDay){
                    #>
                    <div class="ic-more" style="display:block">
                        <a href="#">{{instasport.settings.desktop.monthView.moreText}}</a>
                    </div>
                    <# } #>
                    <# } #>
                </div>
            </div>

            <# if (date.format('E') == 7) { #>
        </div>
        <div class="ic-tr">
            <# }
            date.add(1, 'days');
            } #>
        </div>
    </div>
</div>