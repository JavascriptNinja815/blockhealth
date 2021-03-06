<script>
    $.getScript("https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBqFG28QSw6XAtKYjO6xFEMNgkxMiV9FKM", function (data, textStatus, jqxhr) {
        $.getScript("<?php echo base_url(); ?>assets/js/jquery.geocomplete.js", function () {
            $(document).ready(function () {
                $(".geo_complete").geocomplete();
            });
        });
    });


</script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/cropboard.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/cropper.css">
<script src="https://fengyuanchen.github.io/js/common.js"></script>
<script src='<?php echo base_url(); ?>assets/js/tiff.js'></script>
<script src='<?php echo base_url(); ?>assets/js/cropper.js'></script>
<script src="<?php echo base_url(); ?>assets/js/cropboard.js"></script>

<style>

    #modal_preview{
        background: rgba(255,255,255,0.5) !important;
    }

    .imggy-preview::before{
        background:rgba(255,255,255,0.5);
        position: absolute;
        width: 100%;
        height: 100%;
    }


    #hover-img-preview {
        left: 75%!important;
        top: 50%;
        z-index:1000!important;
    }


    .popup_content {
        margin: 10px;
        padding: 0 10px;
        max-width: 100%;
        background: white;
        -webkit-box-shadow: 0 5px 15px rgba(0,0,0,.5);
        box-shadow: 0 5px 15px rgba(0,0,0,.5);
    }

    .popup_wrapper{

        top: 10%!important;
    }

    .popup_background{
        z-index: 0!important;
    }

</style>

<style>
    .pdfobject-container { height: 650px;}
    .pdfobject { border: 1px solid #666; }
    form .form-bottom .input-error {
        border-color: #d03e3e;
        color: #d03e3e;
    }
    .img-container img {
        max-width: 100%;
    }
    .fa-3 {
        font-size: 4em;
    }

    .ov_image_close::before{
        position: absolute;
        z-index: 10000;
        left: 20%;
        top: 20%;
        width: 20px;
        height:20px;
        content:"X";
    }   


    #form_patient_save .col-lg-12 {
        margin-top: 10px;
    }
    #task_h4 {
        margin-bottom: 0px !important;
    }
</style>
<style>
    .pac-container, .pac-item, .pac-icon, .pac-icon-marker, .pac-item-query{
        z-index: 10000 !important;
    }
    .fa-2 {
        font-size: 17px;
    }
    .updated_mode {
        clear: both;
        background:url('assets/img/check-mark.png') no-repeat 98% center;
        background-color: #f9f9f9 !important;
    }

    /* preview image css */
    #hover-img-preview {
        /*display: none;*/
        /*width: 100%;
        height: 100%;
        max-width:500px;*/
        width: 300px;
        height: 400px;
        position: absolute;
        left: 50%;
        top: 50%; 
        margin-left: -150px;
        margin-top: -150px;

        background-color: white;
        box-shadow: 0 1px 50px rgba(34, 25, 25, 0.4);
        -moz-box-shadow: 0 1px 50px rgba(34,25,25,0.4);
        -webkit-box-shadow: 0 1px 50px rgba(34, 25, 25, 0.4);
    }

    /* auto complete css starts */
    .ui-autocomplete {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        display: none;
        float: left;
        min-width: 160px;
        padding: 5px 0;
        margin: 2px 0 0;
        list-style: none;
        font-size: 14px;
        text-align: left;
        background-color: #ffffff;
        border: 1px solid #cccccc;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
        background-clip: padding-box;
    }

    .ui-autocomplete > li > div {
        display: block;
        padding: 3px 20px;
        clear: both;
        font-weight: normal;
        line-height: 1.42857143;
        color: #333333;
        white-space: nowrap;
    }

    .ui-state-hover,
    .ui-state-active,
    .ui-state-focus {
        text-decoration: none;
        color: #262626;
        background-color: #f5f5f5;
        cursor: pointer;
    }

    .ui-helper-hidden-accessible {
        border: 0;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
    }
    /* autocomplete css over */


    .btn-disabled {
        background-color: #675e67 !important;
        border-bottom: 3px solid #7c8584 !important;
    }

    /* thumbnail slider css */

    .carousel-inner {
        //height: 100%; /*Note: set specific height here if not, there will be some issues with IE browser*/
    }
    .carousel-inner > .item {
        -webkit-transition: .6s ease-in-out top;
        -o-transition: .6s ease-in-out top;
        transition: .6s ease-in-out top;
    }

    @media all and (transform-3d),
    (-webkit-transform-3d) {
        .carousel-inner > .item {
            -webkit-transition: -webkit-transform .6s ease-in-out;
            -o-transition: -o-transform .6s ease-in-out;
            transition: transform .6s ease-in-out;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-perspective: 1000;
            perspective: 1000;
        }
        .carousel-inner > .item.next,
        .carousel-inner > .item.active.right {
            -webkit-transform: translate3d(0, 33.33%, 0);
            transform: translate3d(0, 33.33%, 0);
            top: 0;
        }
        .carousel-inner > .item.prev,
        .carousel-inner > .item.active.left {
            -webkit-transform: translate3d(0, -33.33%, 0);
            transform: translate3d(0, -33.33%, 0);
            top: 0;
        }
        .carousel-inner > .item.next.left,
        .carousel-inner > .item.prev.right,
        .carousel-inner > .item.active {
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
            top: 0;
        }
    }

    .carousel-inner > .active {
        top: 0;
    }
    .carousel-inner > .next,
    .carousel-inner > .prev {
        top: 0;
        height: 100%;
        width: auto;
    }
    .carousel-inner > .next {
        left: 0;
        top: 33.33%;
        right:0;
    }
    .carousel-inner > .prev {
        left: 0;
        top: -33.33%;
        right:0;
    }
    .carousel-inner > .next.left,
    .carousel-inner > .prev.right {
        top: 0;
    }
    .carousel-inner > .active.left {
        left: 0;
        top: -33.33%;
        right:0;
    }
    .carousel-inner > .active.right {
        left: 0;
        top: 33.33%;
        right:0;
    }

    #carousel-pager .carousel-control.left {
        bottom: initial;
        width: 100%;
    }
    #carousel-pager .carousel-control.right {
        top: initial;
        width: 100%;
    }
    div#carousel-pager {
        width: 15%;
        background: #eee;
        height: 100%;
        display: inline-block;
        left: 0;
        padding: 24px;
        text-align: center;
        margin-top: 20px;
        overflow-y: scroll;
        float: left;
    }
    .inner {
        margin-bottom: 20px !important;
    }
    .thumbnail {
        margin-bottom: 5px;
    }
    .canvas {
        background-color: #696565;
        width: 82.9%;
        /*width: 82.9%;
        padding-top: 15px !important;
        padding-top: 30px !important;
        width: calc(100% - 230px);*/
    }
    #cropboard {
        position: absolute;
        top: 35px !important;
    }
    .cropheader {
        padding-right: 15px;
    }
    ./*cropheader {
        top: -20px !important;
    }*/
    .visHide{
        visibility : hidden;
    }
    .toggle-bar {
        width: 1%;
        background: #eee;
        height: 100%;
        float: left;
        display: table;
    }
    .toggle-bar span{
        display: table-cell;
        vertical-align: middle;
        margin: 0 auto;
        width: 75%;
    }

    .cropper-container {
        position: absolute;
        top: 35px !important;
    }
    .fullView {
        width: 97.9%;
    }
    .carousel-item{
        display:block !important
    }
    .splitBtndiv{
        float:left;
    }
    .active_btn_split {
        background: #aa1d1f !important;
        color: #fff !important;
    }
    #txt_split{
        position: absolute;
        height: 40px !important;
        width: 150px;
        background: #fff;
        box-shadow: none;
    }

    /* Scrollbar css */
    ::-webkit-scrollbar {
        width: 10px;
        border-radius:10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #9e9e9e; 
        border-radius:30px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555; 
    }

    .cropb_header_btn button{
        background : #fff;
    }
    .thumb-active .thumbnail {
        border: 1.2px solid #7e7eff;
    }
    @media only screen and (max-width: 991px) {
        .canvas {
            width: 81.9%;
        }
    }
    @media only screen and (max-width: 768px) {
        div#carousel-pager{
            display:none;
        }
        .toggle-bar{
            display:none;
        }
    }
</style>
<script>
    global_data.referral_form_use = "<?= $this->session->userdata("referral_form_use"); ?>";

    $('#btn_split').click(function () {
        if ($(this).hasClass("active_btn_split")) {
            let split_text = $("#txt_split").val();
            if (split_text !== "") {
                global_data.split_text = split_text;
                view("modal_confirm_split");
            }
        }

        $(this).toggleClass('active_btn_split');
        $('#txt_split').toggle(500);
    });

    $("#btn_confirm_split_fax").on("click", function () {
        let split_text = global_data.split_text;
        let efax_id = global_data.efax_id;
        let form = $("#sample_form");

        form.find("#id").val(efax_id);
        form.find("#target").val(split_text);

        $.post({
            url: base + "inbox/perform_fax_split",
            data: form.serialize()
        }).success(function (response) {
            console.log(response);
//            debugger
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result === "success") {
                    $(".modal").hide();
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    error(response.message);
                }
            } else {
                error("Failed to split fax");
            }
        }).error(function () {
            error("Failed to split fax");
        });
    });

    $('.toggle-bar').click(function () {
        $('#carousel-pager').toggle();
        $('.canvas').toggleClass('fullView');
        $('.hideShow').toggleClass('glyphicon-chevron-right').toggleClass('glyphicon-chevron-left');
    });

    function loadVerticalSliderJS() {
        $('.carousel-item').first().addClass('active');

        $('.carousel .vertical .item').click(function () {
            $('.thumb-active').toggleClass('thumb-active');
            $(this).addClass('thumb-active');
        });
    }

    function set_row3(id, fax, tif_file_name, pdf_file_name, row) {
        $(row).attr("data-id", id);
        $(row).addClass("clinic_patients_row");
    }
    function set_inbox_data(id, date, time, fax, tif_file_name, pdf_file_name, row) {
        $(row).attr("data-id", id);
        $(row).attr("data-date", date);
        $(row).attr("data-time", time);
        $(row).attr("data-fax", fax);
        $(row).attr("data-file", pdf_file_name);
        $(row).attr("data-file-tif", tif_file_name);
        $(row).addClass("inbox_row");
    }
    var global_data;
    global_data.monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    function set_inbox_date(date_time) {
        //expected format = Apr 06, 18
        date_parts = date_time.split(",");
        date_obj = new Date(date_parts[0], date_parts[1] - 1, date_parts[2], date_parts[3], date_parts[4], date_parts[5]);
        short_month_name = global_data.monthNames[date_obj.getMonth()];
        return short_month_name + " " + date_obj.getDate() + ", " + date_obj.getFullYear().toString().substr(2);
    }
    function set_inbox_time(date_time) {
        //expected format = 01:08 PM
        date_parts = date_time.split(",");
        return small(((date_parts[3] > 12) ? date_parts[3] - 12 : date_parts[3])) + ":" + date_parts[4] + " " + ((date_parts[3] > 12) ? "PM" : "AM");
    }

    function small(digit) {
        if (parseInt(digit) > 9) {
            return parseInt(digit);
        } else {
            return "0" + parseInt(digit);
        }
    }


    function get_first_page_from_tif(url) {

        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            var reader = new FileReader();
            reader.onload = function (e) {
                var image = new Tiff({
                    buffer: e.target.result
                });
                image.setDirectory(0);
                temp_canvas = image.toCanvas();
                $("#image_for_preview").attr("src", temp_canvas.toDataURL());
                global_data.preview_images.push(temp_canvas.toDataURL());
            };
            reader.readAsArrayBuffer(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }

    global_data.slider_images = [];

    function get_slider_images(url, page) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function () {
            var reader = new FileReader();
            reader.onload = function (e) {
                var image = new Tiff({
                    buffer: e.target.result
                });
                pages = image.countDirectory();
                slider_html = "";
                slider_template = $("#templates").find("#template_slider").html();
                for (i = 0; i < pages; i++) {
                    image.setDirectory(i);
                    temp_canvas = image.toCanvas();
                    slider = slider_template;
                    slider = slider.replace(/###img_src###/g, temp_canvas.toDataURL());
                    slider = slider.replace(/##current##/g, (i));
                    slider = slider.replace(/###number###/g, (i + 1));
                    slider_html += slider;
                    global_data.slider_images.push(temp_canvas);
                }
                $("#slider_container").html(slider_html);
                loadVerticalSliderJS();
                //now get doc type
                //check if serer have saved doc types
                form = $("#sample_form");
                form.find("#id").val(global_data.efax_id);

                $.post({
                    url: base + "inbox/check_slider_saved",
                    data: form.serialize()
                }).success(function (response) {
                    response = JSON.parse(response);

                    if (response.result === "success") {
                        if (response.saved === "yes") {
                            if (response.data.length < fileList.length) {
                                clear_old_docs();
                                console.log("get custom doc");
                                global_data.slider_ajax_counter = 0;
                                get_doc_types();
                            } else {
                                console.log("got saved docs");
                                data = response.data;

                                for (i = 0; i < data.length; i++) {
                                    if (data[i].success === "true") {
                                        $("#slider_" + (parseInt(data[i].counter) + 1)).text(data[i].output);
                                    } else {
                                        $("#slider_" + (parseInt(data[i].counter) + 1)).text("");
                                    }
                                }
                            }
                        } else {
                            console.log("get custom doc");
                            global_data.slider_ajax_counter = 0;
                            get_doc_types();
                        }
                    } else {
                        console.log("get custom doc");
                        global_data.slider_ajax_counter = 0;
                        get_doc_types();
                    }
                }).error(function () {
                    $("#btn_save_task").button("reset");
                    error("Patient record not saved");
                });

            };
            reader.readAsArrayBuffer(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }
    
    function clear_old_docs() {
        form = $("#sample_form");
        form.find("#id").val(global_data.efax_id);
        $.post({
            url: base + "inbox/clear_old_docs",
            data: form.serialize()
        }).success(function (response) {
            console.log(response);
        }).error(function () {
            console.log("error clear_old_docs");
        });
    }

    function save_slider_response(efax_id, counter, output, success) {
        form = $("#sample_form");
        form.find("#id").val(efax_id);
        form.find("#target").val(counter);
        form.find("#param").val(output);
        form.find("#param2").val(success);
        $.post({
            url: base + "inbox/save_slider_response",
            data: form.serialize()
        }).success(function (response) {
            console.log(response);
        }).error(function () {
            console.log("error saving slider data");
        });
    }

    global_data.slider_ajax_counter = 0;

    function get_doc_types() {
        images = global_data.slider_images;
        console.log("on doc call " + global_data.slider_ajax_counter + " images len = " + images.length);
        if (global_data.slider_ajax_counter < images.length) {
            canvas = images[global_data.slider_ajax_counter];
            canvas.toBlob(function (blob) {
                var formData = new FormData();
                formData.append('file', blob);
                formData.append('blockhealth_validation_token', $("#sample_form").find("input[name='blockhealth_validation_token']").val());
                console.log("sending ajax for " + global_data.slider_ajax_counter);
                $.ajax(base + "inbox/doc_classifier", {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {

                        //save it in db
                        save_slider_response(global_data.efax_id,
                                global_data.slider_ajax_counter,
                                response.output,
                                response.success);

                        console.log("output for" + global_data.slider_ajax_counter + "=>");
                        console.log(response);
                        if (response.success === true) {
                            $("#slider_" + (global_data.slider_ajax_counter + 1)).text(response.output);
                        } else {
                            $("#slider_" + (global_data.slider_ajax_counter + 1)).text("result = false");
                        }
                    },
                    error: function (response) {
                        console.log("error");
                        console.log(response);
                        uploadingFile = false;
                    },
                    complete: function (response) {
//                        uploadingFile = false;
                        console.log("got response for " + global_data.slider_ajax_counter);
                        setTimeout(function () {
                            global_data.slider_ajax_counter += 1;
                            //get next image
                            images = global_data.slider_images;
                            if (global_data.slider_ajax_counter < images.length) {
                                get_doc_types();
                            } else {
                                console.log("all requests completed");
                            }
                        }, 100);

                    }

                });
            });
        } else {
            global_data.slider_ajax_counter = 0;
        }
    }


    function open_efax(id, tiff_file_name, pdf_file_name, date, time, fax) {


        if (cropper !== null) {
            cropper.destroy();
            cropper_activated = false;
        }

        if (typeof $("#eFax-modal").find("form")[0] !== "undefined") {
            $("#eFax-modal").find("form")[0].reset();
        }
        if (typeof $("#eFax-modal").find("form")[1] !== "undefined") {
            $("#eFax-modal").find("form")[1].reset();
        }
        if (typeof $("#eFax-modal").find("form")[2] !== "undefined") {
            $("#eFax-modal").find("form")[2].reset();
        }
        // $("#eFax-modal").find("[name='dr_fax']").val(fax);
        $("#eFax-modal").find("fieldset").hide();
        $("#eFax-modal").find($("fieldset")[0]).show();
        $("#eFax-modal").find(".input-error").removeClass("input-error");
        $(".toolbar").hide();
        $("#btn_extract_physician").hide();
        $("#btn_find_physician_match").show();
        $("#wrap-container").removeClass("toggled");
        $("#patient_success_display").hide();
        $("#overlay_image").attr("src", "").hide();
        $("#eFax-modal").find(".updated_mode").removeClass("updated_mode");


        $("#btn_save_task").attr("disabled", false);
        $("#btn_save_task").removeClass("btn-disabled");


        modal = $("#eFax-modal");
        modal.find("#file_info").html(fax + " ( " + date + " - " + time + " ) ");
        date_parts = date.split(",");
        date = date_parts[2] + "/" + date_parts[1] + "/" + date_parts[0];
        time = ((date_parts[3] < 12) ? date_parts[3] : date_parts[3] - 12) + ":" + date_parts[4] + ((date_parts[3] < 12) ? "am" : "pm");
        fax = fax + "";
        if (fax.length === 10) {
            fax = fax.substr(0, 3) + "-" + fax.substr(3, 3) + "-" + fax.substr(6);
        } else if (fax.length === 11) {
            fax = fax.substr(0, 1) + "-" + fax.substr(1, 3) + "-" + fax.substr(4, 3) + "-" + fax.substr(7);
        }
        modal.find("#file_info").html(fax + " ( " + date + " - " + time + " ) ");
        global_data.pdf_file = base + "referral/uploads/efax/" + pdf_file_name;
        global_data.tif_file = base + "referral/uploads/efax_tiff/" + tiff_file_name;
        global_data.efax_id = id;

        $("#btn_download_referral").attr("href", global_data.pdf_file + ".pdf");
        $(".input_fields_wrap").find(".remove_field").click();
        view("eFax-modal");
        setTimeout(function () {
            init(global_data.tif_file);
        }, 100);

        //slider
        global_data.slider_images = [];
        $("#slider_container").html("");
        get_slider_images(global_data.tif_file);
    }

    function get_referral_checklist() {
        url = base + "inbox/get_referral_checklist";
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                data = JSON.parse(response);
                checklists = "";
                data.forEach(function (value, index) {
                    checklists +=
                            '<div class="checkbox">' +
                            '<label>' +
                            '<input type="checkbox" value="' + value.id + '" name="referral_checklist[]" ' +
                            'data-name="' + value.name + '">' +
                            '<span class="cr"><i class="cr-icon fa fa-check"></i></span>' +
                            value.name +
                            '</label>' +
                            '</div>';
                });
                $("#referral_checklist").html(checklists);
            }
        });
    }



    function add_diseases(text) {
        if (x_diseases < max_fields) { //max input box allowed
            x_diseases++; //text box increment
            wrapper = $("#btn_add_diseases").closest("div.wrapper_div").find(".edit_diseases");
            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="diseases[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }
    function add_symptoms(text) {
        if (x_symptoms < max_fields) { //max input box allowed
            x_symptoms++; //text box increment
            wrapper = $("#btn_add_symptoms").closest("div.wrapper_div").find(".edit_symptoms");
            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="symptoms[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }

    function add_tests(text) {
        if (x_tests < max_fields) { //max input box allowed
            x_tests++; //text box increment
            wrapper = $("#btn_add_tests").closest("div.wrapper_div").find(".edit_tests");
            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="tests[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }

    function add_devices(text) {
        if (x_devices < max_fields) { //max input box allowed
            x_devices++; //text box increment
            wrapper = $("#btn_add_devices").closest("div.wrapper_div").find(".edit_devices");
            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="devices[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }

    function add_medications(text) {
        if (x_medications < max_fields) { //max input box allowed
            x_medications++; //text box increment
            wrapper = $("#btn_add_medications").closest("div.wrapper_div").find(".edit_medications");

            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="medications[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }


    max_fields = 10;


    //for reason referral
    var x_reason = 0; //initlal text box count


    function add_reason(text) {
        if (x_reason < 1) { //max input box allowed
            x_reason++; //text box increment
            wrapper = $("#btn_add_reason").closest("div.wrapper_div").find(".edit_reasons");

            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="reasons[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }


    var x_diseases = 0; //initlal text box count
    var x_symptoms = 0; //initlal text box count
    var x_tests = 0; //initlal text box count
    var x_medications = 0; //initlal text box count
    var x_documents = 0; //initlal text box count
    var x_devices = 0; // initial text box count


    function add_documents(text) {
        if (x_documents < max_fields) { //max input box allowed
            x_documents++; //text box increment
            wrapper = $("#btn_add_documents").closest("div.wrapper_div").find(".edit_documents");
            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="referral_checklist[]" value="' + text + '"><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }

    function file_upload_labtest(data) {
        console.log("method file_upload_labtest called");
        if (uploadingFile) {
            error("We are already started fetching data. Please wait");
            return;
        }
        var canvas;

        if (cropper) {
            uploadingFile = true;
            console.log("cropper form data");
            canvas = cropper.getCroppedCanvas();

            console.log(canvas);
            var imageObj = $('#_blob');
            imageObj.attr('src', canvas.toDataURL());
            imageObj.css('width', canvas.width + 'px');
            imageObj.css('height', canvas.height + 'px');
            canvas.toBlob(function (blob) {
                var formData = new FormData();
                formData.append('file', blob);
                console.log("building form data");
                $.ajax('http://165.227.45.30/lab_test', {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);

                        tmp_selector = "#anything_fake";
                        root = $("#signupForm");
                        if (response.success) {
                            for (i = 0; i < response.predictions.length; i++) {
                                if (response.predictions[i].hasOwnProperty('array')) {
                                    tmp = response.predictions[i].array;
                                    add_tests(tmp);
                                }
                            }

                            //mark updated animation
                            $(tmp_selector).toggleClass("updated_mode");
                            setTimeout(function () {
                                $(tmp_selector).toggleClass("updated_mode");
                            }, 3000);
                        }
//                        btn_autofill.button('reset');
                        uploadingFile = false;
                    },
                    error: function (response) {
                        console.log("error");
                        console.log(response);
                        uploadingFile = false;
                    },
                    complete: function () {
                        console.log("completed");
                        uploadingFile = false;
                    }
                });
            });
        }
    }

    function log_data_points(data_points, efax_id, api) {
        $.post({
            url: base + "referral/log_data_points",
            data: {
                "data_points": data_points,
                "efax_id": efax_id,
                "api": api,
                "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"
            }
        });
    }


    function patient_success(msg) {
        $("#save-patient-wrapper").find("#patient_success").html(msg).show();
        $("#save-patient-wrapper").find("#patient_error").hide();
    }

    function patient_error(msg) {
        $("#save-patient-wrapper").find("#patient_error").html(msg).show().delay(5000).fadeOut();
        $("#save-patient-wrapper").find("#patient_success").hide();
    }

    function get_physician_list_save_patient() {
        url = base + "inbox/get_physician_list_save_patient";
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                data = JSON.parse(response);
                options = "<option selected value='admin'>Unassigned</option>";
                data.forEach(function (value, index) {
                    options += "<option value='" + value.id + "'>" + value.name + "</option>";
                });
                $("#form_patient_save").find("#assign_physician").html(options);
                global_data.table_inbox.ajax.reload();
            }
        });
    }

    function get_patient_list_save_patient() {
        url = base + "inbox/get_patient_list_save_patient";
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                data = JSON.parse(response);
                options = "<option selected value='0'>Select Patient</option>";
                data.forEach(function (value, index) {
                    options += "<option value='" + value.id + "'>" + value.name + "</option>";
                });
                $("#form_patient_save").find("#patient_dropdown").html(options);
            }
        });
    }

    function get_clinic_referral_usage_forms() {
        $("#container_form_usage_yes").show();
        $("#container_form_usage_no").hide();

        url = base + "inbox/get_clinic_referral_usage_form1";
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result === "success") {
                    data = response.data;
                    //patient location
                    options = "";
                    data.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.form_name + "</option>";
                    });
                    $("#signupForm").find("#referral_form_type").html(options);
                    get_clinic_referral_usage_subsection();
                } else {
                    error("Error getting referral usage form");
                }
            } else {
                error("Error getting referral usage form");
            }
        });
    }

    function get_clinic_referral_usage_subsection() {
        id = $("#signupForm").find("#referral_form_type").val();
        url = base + "inbox/get_clinic_referral_usage_subsection";
        $("#sample_form").find("#id").val(id);
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result === "success") {
                    data = response.data;
//                    console.log(data);
                    template_title = $("#templates").find("#clinic_triage_title").html();
                    template_checkbox = $("#templates").find("#template_subsection_checkbox").html();
                    let html = "";
                    data.forEach(function (form2, index) {
                        let str_checkboxes = "";
                        checkboxes = form2.checkboxes;
                        checkboxes.forEach(function (form3, index) {
                            template_form3 = template_checkbox;
                            template_form3 = template_form3.replace(/###id###/g, form3.id);
                            template_form3 = template_form3.replace(/###item_name###/g, form3.form_name);
                            str_checkboxes += template_form3;
                        });

                        let str_title = template_title;
                        str_title = str_title.replace(/###title_text###/g, form2.label);
                        str_title = str_title.replace(/###checkboxes###/g, str_checkboxes);

                        html += str_title;
                    });

                    $("#signupForm").find("#referral_form_subsection").html(html);
                } else {
                    error("Error getting referral usage subsection");
                }
            } else {
                error("Error getting referral usage subsection");
            }
        });
    }


    function get_location_and_custom() {
        url = base + "referral/get_location_and_custom";
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result === "success") {
                    data = response.data;
                    //patient location
                    options = "<option disabled>Select Patient Location</option>";
                    data.locations.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    $("#signupForm").find("#patient_location").html(options);
                    //if only one then select it by default
//                    if(data.locations.length >= 1) {
//                        $("#signupForm").find("#patient_location").val(data.locations[0].id);
//                    }

                    //custom
                    options = "<option selected disabled>Custom Fields</option>";
                    data.customs.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    $("#signupForm").find("#custom").html(options);
                } else {
                    error("Error getting locations and customs");
                }
            } else {
                error("Error getting locations and customs");
            }
        });
    }

    function fill_physician_info(physician_id) {

        let form = $("#sample_form");
        form.find("#id").val(physician_id);

        $.post({
            url: base + "inbox/get_fill_physician_details",
            data: form.serialize()
        }).success(function (response) {
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result === "success") {
                    data = response.data;
                    form = $("#signupForm");
                    form.find("#dr_fname").val(data.FIRST_NAME);
                    form.find("#dr_lname").val(data.LAST_NAME);
                    form.find("#dr_phone_number").val(data.PHONE_1);
                    form.find("#dr_fax").val(data.FAX_1);
                    form.find("#dr_geocomplete").val(data.ADDRESS_1);
                } else {
                    error(response.msg);
                }
            } else {
                error("Patient record not saved");
            }
        }).error(function () {
//            $("#btn_save_task").button("reset");
            error("Internal server error");
        });
    }

    $(document).ready(function () {

        $("#txt_physician_search").autocomplete({
            source: base + "inbox/search_physician",
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                fill_physician_info(ui.item.value);
            }
        });

        $('#carousel-pager').bind('mousewheel', function (e) {
            //if (e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0) {
            if (e.originalEvent.wheelDelta / 120 > 0) {
                $(this).carousel('prev');
            } else {
                $(this).carousel('next');
            }

        });
        get_referral_checklist();
        get_physician_list_save_patient();
        get_patient_list_save_patient();
        get_location_and_custom();
        get_clinic_physicians();
        if (global_data.referral_form_use === "yes") {
//            get_clinic_referral_usage_forms();
        }

        $("#li_inbox").addClass("active");

        $("#signupForm").find("#referral_form_type").on("change", function () {
//            get_clinic_referral_usage_subsection();
            if ($(this).val() === "1") {
                $("#subsection2").hide();
                $("#subsection1").show();
            } else if ($(this).val() === "2") {
                $("#subsection1").hide();
                $("#subsection2").show();
            }
        });

        $("#btn_view_print_referral").on("click", function () {
            printJS(global_data.pdf_file + ".pdf");
        });
        $("#btn_view_delete_referral").on("click", function () {
            view("modal_delete_referral");
        });

        $("#pat_geocomplete").on("input propertychange paste", function () {
            let address_prefix = "Patient address:<br/>";
            $("#show_pat_address").html(address_prefix + $(this).val());
        });

        $("#btn_view_save_referral").on("click", function () {
            if ($("#wrap-container").hasClass("toggled") &&
                    $("#wrap-container").find("#sidebar-wrapper").css("display") === "block") {

            } else {
                $("#wrap-container").toggleClass("toggled");
            }
            $("#wrap-container").find("#sidebar-wrapper").hide();
            $("#wrap-container").find("#save-patient-wrapper").show("slow");
            $("#wrap-container").find("fieldset").hide();

            $("#save-patient-wrapper").find("fieldset").each(function (key, value) {
                if (key != 0)
                    $(value).hide();
                else
                    $(value).show();
            });

//            $("#save-patient-wrapper").find("form")[0].reset();
            $(".input_fields_wrap").find(".remove_field").click();
        });
        //btn_extract_patient, btn_search_patient

        $("#pat_search_by_name").autocomplete({
            source: base + "inbox/patient_autocomplete",
            minLength: 2,
            select: function (event, ui) {
//                parent_fieldset = $("#form_patient_save").closest('fieldset');
//
//                parent_fieldset.fadeOut(400, function () {
//                    $(this).next().fadeIn();
                $(".toolbar").hide();
                root = $("#form_patient_save");

                template = "Patient match found:<br/>###first_name### ###last_name###<br/>DOB:###dob###<br/>OHIP#:###ohip###";
                template = template.replace(/###first_name###/g, ui.item.fname);
                template = template.replace(/###last_name###/g, ui.item.lname);
                template = template.replace(/###dob###/g, ui.item.dob);
                template = template.replace(/###ohip###/g, ui.item.ohip);

                root.find("#id").val(ui.item.id);
                patient_success(template);
//                });
            }
        });

        $("#save-patient-wrapper").find("#btnStartPatientCrop").on("click", function () {
            if (!cropper_activated) {
                createCropper();
            }
            $(".toolbar").show();
        });


        $(".btn_add_dummy").on("click", function () {
            $(this).closest(".add_wrapper").find(".inside_wrapper").toggle();
        });

        $("#save-patient-wrapper").find("#btn_extract_patient").on("click", function () {
            console.log("method btn_extract_patient click");
            if (cropper) {
                //start loading
                $("#save-patient-wrapper").find("#btn_extract_patient").button('loading');
                file_upload_extract_patient(cropper.getImageData());
            } else {
                error("Please activate cropper before autofill");
            }
        });

        $("#save-patient-wrapper").find("#btn_search_patient").on("click", function () {
            btn_patient_extract = $(this);
            btn_patient_extract.button("loading");
            form = $("#form_patient_save");
            form.find("#id").val(global_data.efax_id);
            url = base + "inbox/check_patient_data";
            data = form.serialize();
            var parent_fieldset = $(this).parents('fieldset');
            $.post({
                url: url,
                data: data
            }).success(function (response) {
//                debugger
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result == "success") {
                        data = response.data;
                        root = $("#save-patient-wrapper");
                        console.log("Patient search -> ", response);
                        matches = JSON.parse(data);
                        if (matches.length == 1) {
                            parent_fieldset.fadeOut(400, function () {
                                $(this).next().fadeIn();
                                $(".toolbar").hide();
                            });

                            template = "Patient match found:<br/>###name###<br/>DOB:###dob###<br/>OHIP#:###ohip###";
                            template = template.replace(/###name###/g, matches[0].name);
                            template = template.replace(/###dob###/g, matches[0].dob);
                            template = template.replace(/###ohip###/g, matches[0].ohip);

                            root.find("#id").val(matches[0].id);
                            patient_success(template);
                        } else {
                            if (global_data.table_clinic_patients) {
                                global_data.table_clinic_patients.destroy();
                            }
                            table_data = "<table class='table table-striped table-bordered table-responsive'>";
                            table_data += "<thead><tr><th>Patient Name</th><th>Date of Birth</th><th>OHIP#</th></tr></thead>";
                            table_data += "<tbody>";
                            for (i = 0; i < matches.length; i++) {
                                table_data += "<tr data-id='" + matches[i].id + "'>";
                                table_data += "<td>" + matches[i].name + "</td>";
                                table_data += "<td>" + matches[i].dob + "</td>";
                                table_data += "<td>" + matches[i].ohip + "</td>";
                                table_data += "</tr>";
                            }
                            table_data += "</tbody></table>";

                            $("#table_clinic_patients").html(table_data);
                            global_data.table_clinic_patients = $("#table_clinic_patients").DataTable();
                            $("#save-efax-modal").modal("show");
                        }
                    } else {
                        patient_error(response.msg);
                    }

                } else {
                    patient_error("Something went wrong");
                    btn_patient_extract.button("reset");
                }
            }).error(function () {
                patient_error("Patient matching not performed");
                btn_patient_extract.button("reset");
            }).done(function () {
                btn_patient_extract.button("reset");
            });
        });

        $("#table_clinic_patients").on("click", "tr", function () {
            parent_fieldset = $("#btn_search_patient").closest("fieldset");

            parent_fieldset.fadeOut(400, function () {
                $(this).next().fadeIn();
                $(".toolbar").hide();
            });

            template = "Patient match found:<br/>###name###<br/>DOB:###dob###<br/>OHIP#:###ohip###";
            template = template.replace(/###name###/g, $($(this).find("td")[0]).html());
            template = template.replace(/###dob###/g, $($(this).find("td")[1]).html());
            template = template.replace(/###ohip###/g, $($(this).find("td")[2]).html());

            root = $("#save-patient-wrapper");
            root.find("#id").val($(this).data("id"));
            $("#save-efax-modal").modal("hide");
            patient_success(template);
        });


        $("#eFax-modal").on("click", "#btn_save_task", function () {
            //form_save_patient_record
            $("#btn_save_task").button("loading");
            url = base + "inbox/save_task";
            data = $("#form_patient_save").serialize();
            data += "&efax_id=" + global_data.efax_id;
            $.post({
                url: url,
                data: data
            }).success(function (response) {
                $("#btn_save_task").button("reset");

                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result == "success") {
                        global_data.table_inbox.ajax.reload();
                        get_latest_dashboard_counts();
                        // success("Patient record saved successfully");
                        $("#patient_success_display").show("slow");

                        $("#btn_save_task").button("reset");
                        $("#eFax-modal").modal("hide");
                        // setTimeout(function() {
                        //        $("#btn_save_task").attr("disabled", true);
                        //        $("#btn_save_task").addClass("btn-disabled");
                        //        setTimeout(function() {
                        //          $("#eFax-modal").modal("hide");
                        //        }, 3000);
                        //        // $("#btn_save_task").addClass("btn-disabled");
                        // }, 200);

                    } else {
                        error(response.msg);
                    }
                } else {
                    error("Patient record not saved");
                }
            }).error(function () {
                $("#btn_save_task").button("reset");
                error("Patient record not saved");
            });
        });

        $("#btn_save_patient_record").on("click", function () {
            //form_save_patient_record
            $("#btn_save_patient_record").button("loading");
            url = base + "inbox/save_patient_record";
            data = $("#form_save_patient_record").serialize();
            data += "&efax_id=" + global_data.efax_id;
            $.post({
                url: url,
                data: data
            }).success(function (response) {
                $("#btn_save_patient_record").button("reset");

                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result == "success") {
                        global_data.table_inbox.ajax.reload();
                        success("Patient record saved successfully");
                    } else {
                        error(response.msg);
                    }
                } else {
                    error("Patient record not saved");
                }
            }).error(function () {
                error("Patient record not saved");

            }).done(function () {
                $("#btn_save_patient_record").button("reset");
                // $("#btn_save_patient_record").
                // btn_physician_extract.hide("slow");
                // $("#btn_extract_physician").show("slow");
            });
        });



        $("#eFax-modal").on('hidden.bs.modal', function () {
            exit();
        });

        $("#overlay_imagem, #btnStartCrop, #btnAutoFill, #btnNextPage, #btnPrevPage, .cropper-container, #page-content-wrapper, .btn-next, #btnStartCrop2").on("click", function () {
            if (!global_data.showing_overlay && $("#overlay_image").css("display") != "none") {
                $("#overlay_image").hide("slow");
            }
        });


        $('.patients-details-form .btn-next').on('click', function () {
            //set patient address
            let address_prefix = "Patient address:<br/>";
            $("#show_pat_address").html(address_prefix + $("#pat_geocomplete").val());

            //move next process
            if (typeof ("cropper_activated") !== "undefined" && cropper_activated) {
                global_data.crop_data = cropper.getCropBoxData();
                global_data.crop_rotate = cropper.getData().rotate;
            }

            var parent_fieldset = $(this).parents('fieldset');
            var next_step = true;
            parent_fieldset.find('.required').each(function () {
                if ($(this).val() === "") {
                    $(this).addClass('input-error');
                    next_step = false;
                } else {
                    $(this).removeClass('input-error');
                }
            });

            parent_fieldset.find('.valid_email').each(function () {
                if (validateEmail($(this).val())) {
                    $(this).removeClass('input-error');
                } else {
                    $(this).addClass('input-error');
                    next_step = false;
                }
            });

            if (next_step) {
                parent_fieldset.fadeOut(400, function () {
                    $(this).next().fadeIn();
                    $(".toolbar").hide();
                });
//                if (cropper) {
//                    cropper.destroy();
//                    createCropper();
//                    setTimeout(function () {
////                        cropper.setCropBoxData(global_data.crop_data);
//                        cropper.rotate(global_data.crop_rotate);
//                    }, 100);
//                }
            }
        });


        $('.patients-details-form .btn-previous, #save-patient-wrapper .btn-previous').on('click', function () {
            $(this).parents('fieldset').fadeOut(400, function () {
                $(this).prev().fadeIn();
            });
        });

        $("#signupForm").on("click", "#btn_add_reason", function (e) { //on add input button click
            e.preventDefault();
            add_reason("");
        });

        $("#signupForm").on("click", ".edit_reasons .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_reason--;
        });

        $("#signupForm").on("keyup", ".edit_reasons .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='reasons[]']").val($(this).val());
        });


        //for diseases
        $("#signupForm").on("click", "#btn_add_diseases", function (e) { //on add input button click
            e.preventDefault();
            add_diseases("");
        });

        $("#signupForm").on("click", ".edit_diseases .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_diseases--;
        });

        $("#signupForm").on("keyup", ".edit_diseases .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='diseases[]']").val($(this).val());
        });

        //for symptoms
        $("#signupForm").on("click", "#btn_add_symptoms", function (e) { //on add input button click
            e.preventDefault();
            add_symptoms("");
        });

        $("#signupForm").on("click", ".edit_symptoms .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_symptoms--;
        });

        $("#signupForm").on("keyup", ".edit_symptoms .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='symptoms[]']").val($(this).val());
        });

        //for tests
        $("#signupForm").on("click", "#btn_add_tests", function (e) { //on add input button click
            e.preventDefault();
            add_tests("");
        });

        $("#signupForm").on("click", ".edit_tests .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_tests--;
        });

        $("#signupForm").on("keyup", ".edit_tests .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='tests[]']").val($(this).val());
        });

        //for devices
        $("#signupForm").on("click", "#btn_add_devices", function (e) { //on add input button click
            e.preventDefault();
            add_devices("");
        });

        $("#signupForm").on("click", ".edit_devices .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_devices--;
        });

        $("#signupForm").on("keyup", ".edit_devices .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='devices[]']").val($(this).val());
        });

        //for medications
        $("#signupForm").on("click", "#btn_add_medications", function (e) { //on add input button click
            e.preventDefault();
            add_medications("");
        });

        $("#signupForm").on("click", ".edit_medications .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_medications--;
        });

        $("#signupForm").on("keyup", ".edit_medications .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='medications[]']").val($(this).val());
        });

        //for referral document checklist
        $("#signupForm").on("click", "#btn_add_documents", function (e) { //on add input button click
            e.preventDefault();
            add_documents("");
        });

        $("#signupForm").on("keyup", ".edit_documents .dummy_checkbox", function () {
            $(this).closest(".checkbox").find("[name='referral_checklist[]']").val($(this).val());
        });

        $("#signupForm").on("click", ".edit_documents .remove_field", function (e) { //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_documents--;
        });
        // add fields code completed.



        $("#btn_view_request_missing_items_inbox").on("click", function () {
            $("#btn_view_request_missing_items_inbox").button("loading");
            form = $("#signupForm");
            form.find("#id").val(global_data.efax_id);
            url = base + "inbox/missing_items_details";
            data = form.serialize();
            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        data = JSON.parse(response);
                        if (data.hasOwnProperty("result")) {
                            if (data.result === "success") {
                                $("#modal_missing_items").find(".content").html(data.data);
                                view("modal_missing_items");
                            } else {
                                error("Unexpected Error Occured");
                            }
                        } else {
                            error("Something went wrong");
                        }
                    } else {
                        error("Unexpected Error Occured");
                    }
                },
                error: function (response) {
                    handle_ajax_error(response);
                },
                complete: function () {
                    $("#btn_view_request_missing_items_inbox").button("reset");
                }
            });
        });

        $("#btn_request_missing_items_inbox").on("click", function () {
            form = $("#signupForm");
            url = base + "inbox/request_missing_items";
            data = form.serialize();

            $("#referral_checklist").find("input:unchecked").each(function (index, value) {
                a = $(value).closest("label").text();
                data += "&missing_item[]=" + a;
            });

            $("div.edit_documents").find("input[type='checkbox']:unchecked").each(function (index, value) {
                a = $(value).closest("label").find(".dummy_checkbox").val();
                data += "&missing_item[]=" + a;
            });
            data += "&new_checklists=";
            $(".input_fields_wrap").find("[name='referral_checklist[]']").each(function (index, value) {
                data += $(value).val() + ",";
            });

            $("#btn_request_missing_items_inbox").button('loading');

            $.post({
                url: url,
                data: data,
                error: function (response) {
                    handle_ajax_error(response);
                },
                complete: function () {
                    $("#btn_request_missing_items_inbox").button('reset');
                }
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data === true) {
                        $(".modal").modal("hide");
                        success("Missing item request has been sent");
                    } else {
                        error(JSON.parse(response));
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });


        $("#btn_add_referral").on("click", function () {
            //new referral
            $("#btn_add_referral").button('loading');
            form = $("#signupForm");
            form.find("#id").val(global_data.efax_id);
            url = base + "inbox/new_referral";
            data = form.serialize();
            data += "&new_checklists=";
            $(".input_fields_wrap").find("[name='referral_checklist[]']").each(function (index, value) {
                data += $(value).val() + ",";
            });
            $.post({
                url: url,
                data: data,
                complete: function () {
                    $("#btn_add_referral").attr('disabled', false);
                }
            }).done(function (response) {
                $("#btn_add_referral").button('reset');
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data[0] == true) {
                        location.href = data[1];
                    } else {
                        error(data[1]);
                    }
                } else {
                    error("Failed to add referral. Please try again.");
                }
            });
        });
        $('table#table_inbox').on("click", ".inbox_row", function () {
            let id = $(this).data('id');
            let date = $(this).data('date');
            let time = $(this).data('time');
            let fax = $(this).data('fax');
            let tiff_file_name = $(this).data('file-tif');
            let pdf_file_name = $(this).data('file');
            open_efax(id, tiff_file_name, pdf_file_name, date, time, fax);
//             $("#pat_geocomplete").geocomplete({
// //            map: "#pat_geocomplete_map"
//             });  

        });
        $('table#table_clinic_patients').on("click", ".clinic_patients_row", function () {
            let id = $(this).data('id');
            global_data.clinic_patient_id = id;
            $(this).closest(".modal").modal("hide");
            view("add_health_record");
        });
        $("#btn_save_health_record").click(function (e) {
            form = $("#form_health_record");
            form.find("#id").val(global_data.efax_id);
            form.find("#target").val(global_data.clinic_patient_id);
            data = form.serialize();
            url = base + "inbox/add_health_record";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Health Record saved for patient");
                        global_data.table_inbox.ajax.reload();
                    } else {
                        error(data);
                    }
                }
            });
        });
        $("#sidebar-wrapper").find("fieldset").each(function (key, value) {
            if (key != 0)
                $(value).hide();
            else
                $(value).show();
        });
        $(".btn-toggle-referral").on("click", function () {
            setTimeout(function () {
                createCropper();
            }, 1000);

            if ($("#wrap-container").hasClass("toggled") && $("#wrap-container").find("#save-patient-wrapper").css("display") === "block") {
            } else {
                $("#wrap-container").toggleClass("toggled");
            }

            $("#wrap-container").find("#save-patient-wrapper").hide();
            $("#wrap-container").find("fieldset").hide();
            $("#wrap-container").find("#sidebar-wrapper").show("slow");

            $("#sidebar-wrapper").find("fieldset").each(function (key, value) {
                if (key != 0)
                    $(value).hide();
                else
                    $(value).show();
            });
        });


        $.dobPicker({
            daySelector: '#signupForm #pat_dob_day', /* Required */
            monthSelector: '#signupForm #pat_dob_month', /* Required */
            yearSelector: '#signupForm #pat_dob_year', /* Required */
            dayDefault: 'Day', /* Optional */
            monthDefault: 'Month', /* Optional */
            yearDefault: 'Year', /* Optional */
            minimumAge: 0, /* Optional */
            maximumAge: 120 /* Optional */
        });

        $.dobPicker({
            daySelector: '#form_patient_save #pat_dob_day', /* Required */
            monthSelector: '#form_patient_save #pat_dob_month', /* Required */
            yearSelector: '#form_patient_save #pat_dob_year', /* Required */
            dayDefault: 'Day', /* Optional */
            monthDefault: 'Month', /* Optional */
            yearDefault: 'Year', /* Optional */
            minimumAge: 0, /* Optional */
            maximumAge: 120 /* Optional */
        });

        $('#new_referral_form').on('click', '.btn-next', function () {
            var parent_fieldset = $(this).parents('fieldset');
            var next_step = true;
            parent_fieldset.find('input[type="text"],input[type="email"]').each(function () {
                if ($(this).val() == "") {
                    $(this).addClass('input-error');
                    next_step = false;
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (next_step) {
                parent_fieldset.fadeOut(400, function () {
                    $(this).next().fadeIn();
                });
            }
        });
        $("#btn_delete_referral").on("click", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.efax_id);
            url = base + "inbox/delete_referral";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        global_data.table_inbox.ajax.reload();
                        $(".modal").modal("hide");
//                        success("Referral Successfully Deleted");
                        get_latest_dashboard_counts();
                    }
                }
            });
        });

        $("#btnStartCrop").on("click", function () {
            console.log("method btnStartCrop click");
            if (!cropper_activated) {
                createCropper();
            }
            $(".toolbar").show();
        });

        $("#btn_labtest_autofill").on("click", function () {
            console.log("method btn_labtest_autofill click");
            if (!cropper_activated) {
                createCropper();
            }
            setTimeout(execute_file_upload_labtest, 1000);
        });

        function execute_file_upload_labtest() {
            if (!cropper || cropper == null) {
                setTimeout(execute_file_upload_labtest, 1000);
                return;
            }
            file_upload_labtest(cropper.getImageData());
        }



        $("#btnStartCrop2").on("click", function () {
            if (!cropper_activated) {
                createCropper();
            }
            $(".toolbar").show();
            $("#btn_find_physician_match").hide("slow");
            $("#btn_extract_physician").show("slow");
        });

        $("#btn_extract_physician").on("click", function () {
            console.log("method btn_extract_physician click");
            if (cropper) {
                //start loading
                $("#btn_extract_physician").button('loading');
                file_upload_extract_physician(cropper.getImageData());
            } else {
                error("Please activate cropper before autofill");
            }
        });

        $("#btn_find_physician_match").on("click", function () {
            btn_physician_extract = $(this);
            btn_physician_extract.button("loading");
            form = $("#signupForm");

            tmp_fname = form.find("#dr_fname").val();
            tmp_lname = form.find("#dr_lname").val();

            form.find("#dr_fname").val(tmp_fname.trim().split(" ")[0]);
            form.find("#dr_lname").val(tmp_lname.trim().split(" ")[0]);

            form.find("#id").val(global_data.efax_id);
            url = base + "inbox/check_physician_data";
            data = form.serialize();

            form.find("#dr_fname").val(tmp_fname);
            form.find("#dr_lname").val(tmp_lname);

            $.post({
                url: url,
                data: data
            }).success(function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result == "success") {
                        data = response.data;
                        root = $("#signupForm");
                        set_val(root.find("#dr_fname"), data.first_name);
                        set_val(root.find("#dr_lname"), data.last_name);
                        set_val(root.find("#dr_phone_number"), data.phone);
                        set_val(root.find("#dr_fax"), data.fax);
                        set_val(root.find("#dr_geocomplete"), data.address);
                        physician_success("Physician match found: " + data.first_name + " " + data.last_name + " CPSO:" + data.cpso);
                        $(".toolbar").hide();
                    } else {
                        physician_error(response.msg);
                    }

                } else {
                    physician_error("Something went wrong");
                }
            }).error(function () {
                physician_error("Physician matching not performed");
            }).done(function () {
                btn_physician_extract.button("reset");
                // btn_physician_extract.hide("slow");
                // $("#btn_extract_physician").show("slow");
            });
        });




        //  *** Inbox Datatable
        global_data.table_inbox_title = "Inbox Efax Referral";
        global_data.table_inbox = $("#table_inbox").DataTable({
            "order": [[0, "desc"]],
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "pageLength": 50,
            "language": {
                "emptyTable": "Inbox is empty",
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_inbox_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_inbox_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_inbox_title,
                "processing": "Processing " + global_data.table_inbox_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_inbox_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>inbox/ssp_inbox",
            "rowCallback": function (row, data, index) {
                $('td:eq(4)', row).html(
                        set_inbox_data(data[4], data[0], data[1], data[2], data[5], data[6], row)
                        //set_inbox_data(id, date, time, fax, tif_file_name, pdf_file_name, row) {
                        );
                $('td:eq(0)', row).html(
                        set_inbox_date(data[0], row)
                        );
                $('td:eq(1)', row).html(
                        set_inbox_time(data[1], row)
                        );
                $(row).addClass('db-table-link-row');
            },
            "dom": get_dom_plan(),
            "drawCallback": set_inbox_table,
            "columnDefs": [
                {"width": "25%", "targets": 0},
                {"width": "25%", "targets": 1},
                {"width": "25%", "targets": 2},
                {"width": "25%", "targets": 3}
            ]
        });
        $("#table_inbox").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_inbox_wrapper .dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_inbox_wrapper .dataTables_filter input").attr('placeholder', 'Search');
        //  *** Inbox Datatable Over
        //  *** Clinic Patients Datatable
        global_data.table_clinic_patients_title = "Clinic Patients";
        global_data.table_clinic_patients = $("#table_clinic_patients").DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_clinic_patients_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_clinic_patients_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_clinic_patients_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_clinic_patients_title,
                "processing": "Processing " + global_data.table_clinic_patients_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_clinic_patients_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>inbox/ssp_clinic_patients",
            "rowCallback": function (row, data, index) {
                $('td:eq(3)', row).html(
                        set_row3(data[3], row)
                        );
                $(row).addClass('db-table-link-row');
            },
            "dom": "ftp",
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "40%", "targets": 0},
                {"width": "30%", "targets": 1},
                {"width": "30%", "targets": 2}
            ]
        });
        $("#table_clinic_patients").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_clinic_patients_wrapper .dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_clinic_patients_wrapper .dataTables_filter input").attr('placeholder', 'Search');
        //  *** Clinic Patients Over
    });

    function set_inbox_table() {
        tableActionTO = null;

        datas = global_data.table_inbox.rows().data();
        pagelength = datas.length;
        global_data.preview_images = [];

//        for (i = 0; i < pagelength; i++) {
//            tiff_file = base + "uploads/efax_tiff/" + datas[i][5];
//            get_first_page_from_tif(tiff_file);
//              .data().fileTif
//        }

        $('#table-action').hover(function () {
            clearTimeout(tableActionTO);
            let row = $('tr[data-id="' + $(this).data('id') + '"]');
//            $(row).addClass("inbox_row");
            row.addClass('hover');
        }, function () {
            let id = $(this).data('id');
            let row = $('tr[data-id="' + id + '"]');
            row.removeClass('hover');
            tableActionTO = setTimeout(function () {
                $('#table-action').css('top', '-2000px');
                $('#table-action').data('id', '0');
            }, 500);
        });

        $('table#table_inbox .db-table-link-row').click(function () {
            let id = $(this).data('id');
//            location.href = "patients/patient_info/" + id;
        });

        $('table#table_inbox .db-table-link-row').mouseenter(function () {
            clearTimeout(tableActionTO);
            let topOffset = $(this).offset().top - $(window).scrollTop() - 15;
            $('#table-action').css('top', topOffset + $(window).scrollTop());
            $('#table-action').data('id', $(this).data('id'));
            $('#table-action').data('file-pdf', $(this).data('file'));
            $('#table-action').data('file-tif', $(this).data('file-tif'));
            $('#table-action').data('sender-fax-number', $(this).data('fax'));
            $('#table-action').data('date', $(this).data('date'));
            $('#table-action').data('time', $(this).data('time'));
            $("#table-action").data("preview-index", global_data.table_inbox.row($(this)).index());


            $('#table-action').find('#table-hover-delete-trigger').attr("href", base + "referral/uploads/efax/" + $(this).data('file') + ".pdf");
        });

        $('table#table_inbox .db-table-link-row').mouseleave(function () {
            tableActionTO = setTimeout(function () {
                if (!$('#table-action').is(':hover')) {
                    $('#table-action').css('top', '-100px');
                    $('#table-action').data('id', '0');
                }
            }, 500);
        });


        // $("#image1").on("click", function () {
        //     // $("#preview_image_container").hide();
        //     $("#image1").attr("src", "").hide();
        // });

        // $("#image1").css("display", "none");

        $('button.popup2_open').click(function () {
            $(this).attr("id", "table-hover-view-trigger-disabled");

            let tiff_file_name = $(this).parent().data("file-tif");

            tiff_file = base + "referral/uploads/efax_tiff/" + tiff_file_name;
            $("#image_for_preview").attr("src", "");
            get_first_page_from_tif(tiff_file);
        });


        $('#table-hover-edit-trigger').click(function () {
            let id = $(this).parent().data("id");
            let tiff = $(this).parent().data("file-tif");
            let pdf = $(this).parent().data("file-pdf");
            let date = $(this).parent().data("date");
            let time = $(this).parent().data("time");
            let fax = $(this).parent().data("sender-fax-number");
            open_efax(id, tiff, pdf, date, time, fax);
        });
    }

    function physician_success(msg) {
        $("#signupForm").find("#physician_success").html(msg).show().delay(5000).fadeOut();
        $("#signupForm").find("#physician_error").hide();
    }

    function physician_error(msg) {
        $("#signupForm").find("#physician_error").html(msg).show();
        $("#signupForm").find("#physician_success").hide();
    }

    function validateEmail(email) {
        if (email === "")
            return true;
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
    function match_patient_data() {
        form = $("#form_patient_save");
        form.find("#id").val(global_data.efax_id);
        url = base + "inbox/check_patient_data";
        data = form.serialize();
        var parent_fieldset = $("#form_patient_save").closest('fieldset');
        $.post({
            url: url,
            data: data
        }).success(function (response) {

            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result == "success") {
                    data = response.data;
                    root = $("#save-patient-wrapper");

                    matches = JSON.parse(data);
                    if (matches.length == 1) {
                        parent_fieldset.fadeOut(400, function () {
                            $(this).next().fadeIn();
                            $(".toolbar").hide();
                        });

                        template = "Patient match found:<br/>###name###<br/>DOB:###dob###<br/>OHIP#:###ohip###";
                        template = template.replace(/###name###/g, matches[0].name);
                        template = template.replace(/###dob###/g, matches[0].dob);
                        template = template.replace(/###ohip###/g, matches[0].ohip);

                        root.find("#id").val(matches[0].id);
                        patient_success(template);
                    } else {
                        if (global_data.table_clinic_patients) {
                            global_data.table_clinic_patients.destroy();
                        }
                        table_data = "<table class='table table-striped table-bordered table-responsive'>";
                        table_data += "<thead><tr><th>Patient Name</th><th>Date of Birth</th><th>OHIP#</th></tr></thead>";
                        table_data += "<tbody>";
                        for (i = 0; i < matches.length; i++) {
                            table_data += "<tr data-id='" + matches[i].id + "'>";
                            table_data += "<td>" + matches[i].name + "</td>";
                            table_data += "<td>" + matches[i].dob + "</td>";
                            table_data += "<td>" + matches[i].ohip + "</td>";
                            table_data += "</tr>";
                        }
                        table_data += "</tbody></table>";

                        $("#table_clinic_patients").html(table_data);
                        global_data.table_clinic_patients = $("#table_clinic_patients").DataTable();
                        $("#save-efax-modal").modal("show");
                    }
                } else {
                    patient_error(response.msg);
                }

            } else {
                patient_error("Something went wrong");
            }
            $("#btn_extract_patient").button('reset');
        }).error(function () {
            patient_error("Patient matching not performed");
            $("#btn_extract_patient").button('reset');
        }).done(function () {
            $("#btn_extract_patient").button('reset');
        });
    }

    function file_upload_extract_physician(data) {
        console.log("method file_upload_extract_physician triage called");
        if (uploadingFile) {
            error("We are already started fetching data. Please wait");
            return;
        }
        var canvas;

        if (cropper) {
            uploadingFile = true;
            canvas = cropper.getCroppedCanvas();

            global_data.overlay_image = canvas.toDataURL();
            $("#overlay_image").attr("src", global_data.overlay_image);
            $("#overlay_image").show("slow");
            global_data.showing_overlay = true;
            setTimeout(function () {
                global_data.showing_overlay = false;
            }, 2000);

            var imageObj = $('#_blob');
            imageObj.attr('src', canvas.toDataURL());
            imageObj.css('width', canvas.width + 'px');
            imageObj.css('height', canvas.height + 'px');
            canvas.toBlob(function (blob) {
                var formData = new FormData();
                formData.append('file', blob);
                formData.append('blockhealth_validation_token', $("#sample_form").find("input[name='blockhealth_validation_token']").val());

                // global_data.api_phy_extract = "running";
                $("#btn_extract_physician").button("loading");
                // $.ajax('http://159.89.127.142/phy_extract', {
                $.ajax(base + 'inbox/phy_extract_api', {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        global_data.api_drug_test = "completed";
                        tmp_selector = "#anything_fake";
                        root = $("#signupForm");
                        data_points = 0;

                        if (response.success) {
                            if (response.predictions.hasOwnProperty('address')) {
                                root.find("#dr_geocomplete").val(response.predictions.address);
                                tmp_selector += ', #dr_geocomplete';
                                data_points += 1;
                            }
                            if (response.predictions.hasOwnProperty('fax')) {
                                root.find("#dr_fax").val(make_number(response.predictions.fax));
                                tmp_selector += ', #dr_fax';
                                data_points += 1;
                            }
                            if (response.predictions.hasOwnProperty('name')) {
                                if (response.predictions.name.hasOwnProperty('first_name')) {
                                    root.find("#dr_fname").val(response.predictions.name.first_name);
                                    tmp_selector += ', #dr_fname';
                                    data_points += 1;
                                }
                                if (response.predictions.name.hasOwnProperty('last_name')) {
                                    root.find("#dr_lname").val(response.predictions.name.last_name);
                                    tmp_selector += ', #dr_lname';
                                    data_points += 1;
                                }
                            }
                            if (response.predictions.hasOwnProperty('phone')) {
                                root.find("#dr_phone_number").val(make_number(response.predictions.phone));
                                tmp_selector += ', #dr_phone_number';
                                data_points += 1;
                            }
                            if (response.predictions.hasOwnProperty('msp')) {
                                root.find("#dr_billing_num").val(response.predictions.msp);
                                tmp_selector += ', #dr_billing_num';
                                data_points += 1;
                            }
                        }

                        //mark updated animation
                        $(tmp_selector).toggleClass("updated_mode");
                        setTimeout(function () {
                            $(tmp_selector).toggleClass("updated_mode");
                        }, 3000);

                        log_data_points(data_points, global_data.efax_id, "phy_extract");
                        //toogle to find match / autofill
                        $("#btn_extract_physician").hide("slow");
                        $("#btn_find_physician_match").show("slow");
                    },
                    error: function (response) {
                        // global_data.api_drug_test = "completed";
                        console.log("error");
                        console.log(response);
                    }
                }).done(function () {
                    uploadingFile = false;
                    $("#btn_extract_physician").button('reset');
                });

            });
        }
    }


    function file_upload_extract_patient(data) {
        console.log("method file_upload_extract_patient triage called");
        if (uploadingFile) {
            error("We already started fetching data. Please wait");
            return;
        }
        var canvas;

        if (cropper) {
            uploadingFile = true;
            canvas = cropper.getCroppedCanvas();

            var imageObj = $('#_blob');
            imageObj.attr('src', canvas.toDataURL());
            imageObj.css('width', canvas.width + 'px');
            imageObj.css('height', canvas.height + 'px');
            canvas.toBlob(function (blob) {
                var formData = new FormData();
                formData.append('file', blob);
                formData.append('blockhealth_validation_token', $("#sample_form").find("input[name='blockhealth_validation_token']").val());
                $("#btn_extract_patient").button("loading");
                // $.ajax('http://159.89.127.142/phy_extract', {
                $.ajax(base + "inbox/predict_api", {
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
//                        debugger
                        tmp_selector = "#anything_fake";
                        root = $("form#signupForm, form#form_patient_save");
                        data_points = 0;

                        data_points_captured = {};
                        data_points_captured.first_name = "";
                        data_points_captured.last_name = "";
                        data_points_captured.dob_day = "";
                        data_points_captured.dob_month = "";
                        data_points_captured.dob_year = "";
                        data_points_captured.icn = "";
                        data_points_captured.phone = {};
                        data_points_captured.phone.phone = "";
                        data_points_captured.phone.cell = "";
                        data_points_captured.phone.work = "";
                        data_points_captured.email = "";
                        data_points_captured.gender = "";
                        data_points_captured.address = "";
                        data_points_captured.success = response.success;

                        if (response.success) {
                            if (response.predictions.name.hasOwnProperty('first_name')) {
                                if (response.predictions.name.first_name !== "") {
                                    root.find("#new-patient-firstname").val(response.predictions.name.first_name);
                                    tmp_selector += ', #new-patient-firstname';
                                    data_points += 1;
                                    data_points_captured.first_name = response.predictions.name.first_name;
                                }
                            }
                            if (response.predictions.name.hasOwnProperty('last_name')) {
                                if (response.predictions.name.last_name !== "") {
                                    root.find("#new-patient-lastname").val(response.predictions.name.last_name);
                                    tmp_selector += ', #new-patient-lastname';
                                    data_points += 1;
                                    data_points_captured.last_name = response.predictions.name.last_name;
                                }
                            }
                            if (response.predictions.DOB.hasOwnProperty('Day')) {
                                if (response.predictions.DOB.Day <= 9) {
                                    response.predictions.DOB.Day = "0" + response.predictions.DOB.Day;
                                }
                                if (response.predictions.DOB.Day !== "") {
                                    root.find("#pat_dob_day").val(response.predictions.DOB.Day);
                                    tmp_selector += ', #pat_dob_day';
                                    data_points += 1;
                                    data_points_captured.dob_day = response.predictions.DOB.Day;
                                }
                            }
                            if (response.predictions.DOB.hasOwnProperty('Month')) {
                                if (response.predictions.DOB.Month <= 9) {
                                    response.predictions.DOB.Month = "0" + response.predictions.DOB.Month;
                                }
                                if (response.predictions.DOB.Month !== "") {
                                    root.find("#pat_dob_month").val(response.predictions.DOB.Month);
                                    tmp_selector += ', #pat_dob_month';
                                    data_points_captured.dob_month = response.predictions.DOB.Month;
                                }
                            }
                            if (response.predictions.DOB.hasOwnProperty('Year')) {
                                if (response.predictions.DOB.Year !== "") {
                                    root.find("#pat_dob_year").val(response.predictions.DOB.Year);
                                    tmp_selector += ', #pat_dob_year';
                                    data_points_captured.dob_year = response.predictions.DOB.Year;
                                }
                            }

                            if (response.predictions.hasOwnProperty('ICN')) {
                                if (response.predictions.ICN.hasOwnProperty('NO')) {
                                    if (response.predictions.ICN.NO !== "") {
                                        root.find("#new-patient-ohip").val(response.predictions.ICN.NO.replace(/\D/g, ''));
                                        data_points_captured.icn = response.predictions.ICN.NO.replace(/\D/g, '');
                                        tmp_selector += ', #new-patient-ohip';
                                        data_points += 1;
                                    }
                                } else {
                                    if (response.predictions.ICN !== "") {
                                        root.find("#new-patient-ohip").val(response.predictions.ICN.replace(/\D/g, ''));
                                        data_points_captured.icn = response.predictions.ICN.replace(/\D/g, '');
                                        tmp_selector += ', #new-patient-ohip';
                                        data_points += 1;
                                    }
                                }
                            }
                            if (response.predictions.hasOwnProperty('phone')) {
                                if (response.predictions.phone.hasOwnProperty('phone')) {
                                    if (response.predictions.phone.phone !== "") {
                                        root.find("#patient-cell-phone").val(response.predictions.phone.phone);
                                        data_points_captured.phone.phone = response.predictions.phone.phone;
                                        tmp_selector += ', #patient-cell-phone';
                                        data_points += 1;
                                    }
                                }
                                if (response.predictions.phone.hasOwnProperty('phone')) {
                                    if (response.predictions.phone.phone !== "") {
                                        root.find("#patient-cell-phone").val(response.predictions.phone.phone);
                                        data_points_captured.phone.phone = response.predictions.phone.phone;
                                        tmp_selector += ', #patient-cell-phone';
                                        data_points += 1;
                                    }
                                }
                                if (response.predictions.phone.hasOwnProperty('cell')) {
                                    if (response.predictions.cell !== "") {
                                        root.find("#patient-cell-phone").val(response.predictions.phone.cell);
                                        data_points_captured.phone.cell = response.predictions.phone.cell;
                                        tmp_selector += ', #patient-cell-phone';
                                        data_points += 1;
                                    }
                                }
                                if (response.predictions.phone.hasOwnProperty('home')) {
                                    if (response.predictions.phone.home !== "") {
                                        root.find("#patient-home-phone").val(response.predictions.phone.home);
                                        data_points_captured.phone.home = response.predictions.phone.home;
                                        tmp_selector += ', #patient-home-phone';
                                        data_points += 1;
                                    }
                                }
                                if (response.predictions.phone.hasOwnProperty('work')) {
                                    if (response.predictions.phone.work !== "") {
                                        root.find("#patient-work-phone").val(response.predictions.phone.work);
                                        data_points_captured.phone.work = response.predictions.phone.work;
                                        tmp_selector += ', #patient-work-phone';
                                        data_points += 1;
                                    }
                                }
                                if (response.predictions.phone.hasOwnProperty('business')) {
                                    if (response.predictions.phone.business !== "") {
                                        root.find("#patient-work-phone").val(response.predictions.phone.business);
                                        data_points_captured.phone.business = response.predictions.phone.business;
                                        tmp_selector += ', #patient-work-phone';
                                        data_points += 1;
                                    }
                                }
                            }
                            if (response.predictions.hasOwnProperty('email')) {
                                if (response.predictions.email !== "") {
                                    root.find("#patient-email-id").val(response.predictions.email);
                                    data_points_captured.email = response.predictions.email;
                                    tmp_selector += ', #patient-email-id';
                                    data_points += 1;
                                }
                            }

                            if (response.predictions.hasOwnProperty('address')) {
                                if (response.predictions.address !== "") {
                                    root.find("#pat_geocomplete").val(response.predictions.address);
                                    data_points_captured.address = response.predictions.address;
                                    tmp_selector += ', #pat_geocomplete';
                                    data_points += 1;
                                }
                            }

                            //mark updated animation
                            root.find(".updated_mode").removeClass("updated_mode");
                            root.find(tmp_selector).addClass("updated_mode");
                            setTimeout(function () {
                                root.find(tmp_selector).removeClass("updated_mode");
                            }, 3000);
                            log_data_points(data_points, global_data.efax_id, "predict");
                            // save_predict_data_points(data_points_captured);
                            //toogle to find match / autofill
//                            $("#btn_extract_patient").hide("slow");
//                            $("#btn_search_patient").show("slow");
                        }
                        uploadingFile = false;
                    },
                    error: function (response) {
                        console.log("error");
                        console.log(response);
                        uploadingFile = false;
                    },
                    complete: function () {
                        console.log("completed");
                        uploadingFile = false;
//                        match_patient_data();
                        $("#btn_extract_patient").button('reset');
                    }
                }).done(function () {
                    uploadingFile = false;
                });
            });
        }
    }

    function get_clinic_physicians() {
        form = $("#sample_form");
        url = base + "referral/get_clinic_physicians";
        data = form.serialize();
        $.post({
            url: url,
            data: data
        }).done(function (response) {
            if (IsJsonString(response)) {
                data = JSON.parse(response);
                options = "<option value='unassign'>Unassigned</option>";
                for (i = 0; i < data.length; i++) {
                    options += "<option value='" + data[i].id + "'>" + data[i].physician_name + "</option>";
                }
                $("form#signupForm").find("#assigned_physician").html(options);
            }
        });
    }

    function save_predict_data_points(data_points_captured) {
        url = base + "inbox/save_data_points_predict";
        data = {
            first_name: data_points_captured.first_name,
            last_name: data_points_captured.last_name,
            dob_day: data_points_captured.dob_day,
            dob_month: data_points_captured.dob_month,
            dob_year: data_points_captured.dob_year,
            icn: data_points_captured.icn,
            phone: data_points_captured.phone,
            email_id: data_points_captured.email,
            gender: data_points_captured.gender,
            address: data_points_captured.address,
            success: data_points_captured.success,
            "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"
        };

        $.post({
            url: url,
            data: data
        });
    }

    function save_drug_data_points(data_points_captured) {
        url = base + "inbox/save_data_points_drug";
        data = {
            disease_words: JSON.stringify(data_points_captured.disease_words),
            sign_and_synd_words: JSON.stringify(data_points_captured.sign_and_synd_words),
            devices_and_procedures: JSON.stringify(data_points_captured.devices_and_procedures),
            pharmacologic_substance: JSON.stringify(data_points_captured.pharmacologic_substance),
            "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"
        };

        $.post({
            url: url,
            data: data
        });
    }
</script> 

<script src="<?php echo base_url(); ?>assets/libraries/popup-overlay/jquery.popupoverlay.min.js"></script>


<script>
    $preview_popover = $('#popup2, #popup3').popup({
        pagecontainer: '#page',
        type: 'tooltip',
        background: true,
        color: '#fff',
        escape: true,
        horizontal: 'left',
        vertical: 'middle',

        onopen: function () {
            $('button.popup2_open').attr("disabled", true);
        },
        closetransitionend: function () {
            $('button.popup2_open').attr("disabled", false);
        }
    });
</script>
