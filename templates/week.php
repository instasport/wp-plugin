<div class="ic-table-week">
    <div class="ic-table">
        <div class="ic-thead">
            <div class="ic-tr">
                <#
                let date = new Date(data.v.startDate);
                let cdate = new Date();
                #>
                <# for(let i = 0; i < 7 ; i++){ #>
                <div class="ic-td {{date.getDate() == cdate.getDate() ? 'ic-today' : ''}} {{ data.v.day == date.getDate() ? 'active' : ''}}"
                     data-day="{{date.getDate()}}"
                >
                    <div class="ic-desktop">
                        {{{instaDateStr(date, '{S}')}}}
                        <div>{{{ instaDateStr(date, '{d}.{m}.{Y}') }}}</div>
                    </div>
                    <div class="ic-mobile">
                        {{{instaDateStr(date, '{S}<br><span>{d}</span>')}}}
                    </div>
                </div>
                <# date.setDate(date.getDate() + 1) } #>

            </div>
        </div>
    </div>
    <div class="ic-table ic-events">
        <# // Час
        let nowStr = instaDateStr(cdate);
        let count = 0;
        for(let H = data.v.minH ; H <= data.v.maxH + 1; H++){
        #>
        <div class="ic-tr">
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
            date = new Date(data.v.startDate);
            for(let i = 0 ; i < 7; i++){
            let dateStr = instaDateStr(date, '{Y}-{m}-{d} '+H);

            let hclass = [];
            // Активный день
            if(data.v.day == date.getDate()){
            hclass.push('active');
            }
            // Сегодня
            if(instaDateStr(date) == nowStr){
            hclass.push('ic-today');
            }
            // Не активный
            if(date < cdate ){
            hclass.push('off');
            }
            // Нет событий
            if( !data.events[dateStr] ){
            hclass.push('empty');
            }else if(data.v.day == date.getDate()){
            count++;
            }
            hclass = hclass.join(' ');
            #>
            <div class="ic-td {{hclass}} ">
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
                            <div class="ic-begin_time">{{instaGetTime(event.date)}}</div>
                        </div>
                        <div class="ic-td">
                            <# // продолжительность тренировки
                            if((!is_mobile() && instasport.settings.desktop.weekView.showDuration) ||
                            (is_mobile() && instasport.settings.mobile.showDuration)){
                            #>
                            <div class="ic-duration">
                                {{event.duration}} <?php _e( 'мин.', 'instasport' ) ?>
                            </div>
                            <# } #>
                        </div>
                    </div>
                </div>

                <div class="ic-activity">{{event.activity ? event.activity.title : ''}}</div>
                <div class="ic-title">
                    {{event.title}}
                </div>
                <# // свободные места
                if(
                (!is_mobile() && instasport.settings.desktop.weekView.showSeats) ||
                (is_mobile() && instasport.settings.mobile.showSeats)
                ){ #>
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
            <# }} #>
        </div>
        <# date.setDate(date.getDate() + 1) } #>
    </div>
    <# } #>
    <# if(is_mobile() && !count){ #>
    <div class="ic-tr no-events">
        <div class="ic-td"><?php _e( 'Нет тренировок', 'instasport' ) ?></div>
    </div>
    <# } #>
</div>
</div>
