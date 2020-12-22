<div class="mw-header">
    <div class="mw-switch_days">
        <div class="insta_table">
            <div class="insta_table-tr">
                <div class="insta_table-td">
                    <# let date = new Date(data.v.startDate); #>
                    <# for(let i = 0; i < 7 ; i++){ #>
                    <div class="insta_table-td {{instaGetDay(data.v.cdate) == instaGetDay(date) ? 'active' : ''}}">
                        <div class="mw-for_day">
                            <a href="#">{{{instaDateStr(date, '{S}<br><span>{d}</span>')}}}</a>
                        </div>
                    </div>
                    <# date.setDate(date.getDate() + 1) } #>
                </div>
            </div>
        </div>

    </div>
    <div class="mw-header_title">
        <div class="ic-controls">
            <div class="ic-control ic-control_left">
                <a href="#" class="prev"><span class="dashicons dashicons-arrow-left-alt2"></span></a>
            </div>
            <div class="ic-title_month">
                {{{instaDateStr(data.v.cdate, '{l}<br><span>{d} {K}</span>')}}}
            </div>
            <div class="ic-control ic-control_right">
                <a href="#" class="next"><span class="dashicons dashicons-arrow-right-alt2"></span></a>
            </div>
        </div>

    </div>
</div>