<!-- Покупка абонемента -->
<div class="ic-modal-title">
    {{data.instructor.firstName}} {{data.instructor.lastName}}
    <span class="dashicons dashicons-no-alt"></span>
</div>
<div class="ic-modal-content">
    <# if(data.instructor.instructorImage){ #>
    <div class="ic-modal-img">
        <img src="{{data.instructor.instructorImage}}">
    </div>
    <# } #>
    {{data.instructor.instructorDescription}}
</div>
<div class="ic-modal-buttons">
    <a href="#" class="ic-back"><?php _e( "Вернуться назад", 'instasport' ) ?></a>
</div>

