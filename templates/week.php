<div class="ic-table-week">
    <div class="ic-table">
        <div class="ic-thead">
            <div class="ic-tr">
                <#
                let date = data.club.args.date.clone().isoWeekday(1);
                let endDate = data.club.args.date.clone().isoWeekday(7);
                let now = moment();
                #>
                <# while (date <= endDate) { #>
                <div class="ic-td {{ date.isSame(now, 'day') ? 'ic-today' : ''}} {{ date.isSame(data.club.args.date, 'day') ? 'active' : ''}}"
                     data-day="{{date.format('D')}}" data-month="{{date.format('M')}}" data-year="{{date.format('YYYY')}}"
                >
                    <div class="ic-desktop">
                        {{ date.format('dddd') }}
                        <div>{{ date.format('DD.MM.YYYY') }}</div>
                    </div>
                    <div class="ic-mobile">
                        {{ date.format('dd') }}<br><span>{{ date.format('D') }}</span>
                    </div>
                </div>
                <# date.add(1, 'days'); } #>

            </div>
        </div>
    </div>
    <div class="ic-table ic-events">
        <# // Час
        let count = 0;
        for(let H = data.club.args.minH ; H <= data.club.args.maxH; H++){

        // Отображение пустых строк
        if (!instasport.settings.desktop.weekView.showEmptyRows && !~data.club.args.aH.indexOf(H))continue;

        #>
        <div class="ic-tr ic-tr-time">
            <div class="ic-td">{{('0' + H).slice(-2)}}:00</div>
            <div class="ic-td"></div>
            <div class="ic-td"></div>
            <div class="ic-td"></div>
            <div class="ic-td"></div>
            <div class="ic-td"></div>
            <div class="ic-td"></div>
        </div>
        <div class="ic-tr">
            <# // День
            let date = data.club.args.startDate.clone().hour(H);

            while (date.isSameOrBefore(endDate, 'day')) {

            let dateStr = date.format('YYYY-MM-DD H');
            let hclass = [];

            // Активный день
            if(date.isSame(data.club.args.date, 'day')){
            hclass.push('active');
            }
            // Сегодня
            if(date.isSame(now, 'day')){
            hclass.push('ic-today');
            }
            // Не активный
            if(date.isBefore(now)){
            hclass.push('off');
            }
            // Нет событий
            if( !data.events[dateStr] ){
            hclass.push('empty');
            }else if(date.isSame(data.club.args.date, 'day')){
            count++;
            }
            hclass = hclass.join(' ');
            #>
            <div class="ic-td {{hclass}} {{dateStr}} ">
                <#
                // События
                if(data.events[dateStr]){
                for(let event of data.events[dateStr]){ // События
                let styles = [];
                // Использование цветов с api
                if(instasport.settings.desktop.useApiColors){
                styles.push('background:'+event.color);
                styles.push('color:'+event.textColor);
                }

                styles = styles.join(';');
                #>
                <div class="ic-event" style="{{styles}}" data-event="{{event.id}}"
                ">

                <div class="ic-table">
                    <div class="ic-tr">
                        <div class="ic-td">
                            <div class="ic-begin_time">{{event.date.format('HH:mm')}}</div>
                        </div>
                        <div class="ic-td">
                            <# // продолжительность тренировки
                            if((!instasport.is_mobile() && instasport.settings.desktop.weekView.showDuration) ||
                            (instasport.is_mobile() && instasport.settings.mobile.showDuration)){
                            #>
                            <div class="ic-duration">
                                {{event.duration}} <?php _e('мин.', 'instasport') ?>
                            </div>
                            <# } #>
                        </div>
                    </div>
                </div>

                <# if(event.activity){ #>
                <div class="ic-activity ic-desktop">{{event.activity.title}}</div>
                <# } #>
                <div class="ic-title">
                    {{event.title}}
                    <# if(event.activity){ #>
                    <div class="ic-activity ic-mobile">({{event.activity.title}})</div>
                    <# } #>
                </div>

                <div class="ic-detail">
                    <# if(event.zone && !data.club.args.zone){ #>
                    <div class="ic-zone">
                        {{event.zone.title}}
                    </div>
                    <# } #>

                    <# // свободные места
                    if(
                    (!instasport.is_mobile() && instasport.settings.desktop.weekView.showSeats) ||
                    (instasport.is_mobile() && instasport.settings.mobile.showSeats)
                    ){ #>
                    <div class="ic-seats">
                        <# if(event.hasUser){ #>
                        <?php _e('Вы уже записаны', 'instasport') ?>
                        <# }else if(event.seats === 0){ #>
                        <?php _e('Мест нет', 'instasport') ?>
                        <# }else if(event.seats === 1){ #>
                        <?php _e('Осталось 1 место', 'instasport') ?>
                        <# }else if(event.seats === 2){ #>
                        <?php _e('Осталось 2 места', 'instasport') ?>
                        <# } #>
                    </div>
                    <# } #>
                </div>
            </div>
            <# }} #>
        </div>
        <# date.add(1, 'days'); } #>
    </div>
    <# } #>
    <# if(instasport.is_mobile() && !count && !data.club.args.aH.length){ #>
    <div class="ic-tr no-events">
        <div class="ic-td"><?php _e('Нет тренировок', 'instasport') ?></div>
    </div>
    <# } #>
</div>
</div>
