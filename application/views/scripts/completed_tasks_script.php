<script>    $.getScript("https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBqFG28QSw6XAtKYjO6xFEMNgkxMiV9FKM", function (data, textStatus, jqxhr) {        $.getScript("<?php echo base_url(); ?>assets/js/jquery.geocomplete.js", function () {            $(document).ready(function () {                $(".geo_complete").geocomplete();            });        });    });</script><link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/cropboard.css"><link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/cropper.css"><script src="https://fengyuanchen.github.io/js/common.js"></script><script src='<?php echo base_url(); ?>assets/js/tiff.js'></script><script src='<?php echo base_url(); ?>assets/js/cropper.js'></script><script src="<?php echo base_url(); ?>assets/js/cropboard.js"></script><style>    .btn_task_done {        border-radius: 100px;        border-color: #08b5a2;        background-color: #08b5a2;    }    .pac-container, .pac-item, .pac-icon, .pac-icon-marker, .pac-item-query{        z-index: 10000 !important;    }    .fa-2 {        font-size: 17px;    }    .updated_mode {        clear: both;        background:url('assets/img/check-mark.png') no-repeat 98% center;        background-color: #f9f9f9 !important;    }    /* auto complete css starts */    .ui-autocomplete {        position: absolute;        top: 100%;        left: 0;        z-index: 1000;        display: none;        float: left;        min-width: 160px;        padding: 5px 0;        margin: 2px 0 0;        list-style: none;        font-size: 14px;        text-align: left;        background-color: #ffffff;        border: 1px solid #cccccc;        border: 1px solid rgba(0, 0, 0, 0.15);        border-radius: 4px;        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);        background-clip: padding-box;    }    .ui-autocomplete > li > div {        display: block;        padding: 3px 20px;        clear: both;        font-weight: normal;        line-height: 1.42857143;        color: #333333;        white-space: nowrap;    }    .ui-state-hover,    .ui-state-active,    .ui-state-focus {        text-decoration: none;        color: #262626;        background-color: #f5f5f5;        cursor: pointer;    }    .ui-helper-hidden-accessible {        border: 0;        clip: rect(0 0 0 0);        height: 1px;        margin: -1px;        overflow: hidden;        padding: 0;        position: absolute;        width: 1px;    }    /* autocomplete css over */    @media (min-width: 768px) {        #save-patient-container.toggled #save-patient-wrapper {            width: 380px;            padding: 30px;            border-radius: 0px 0px 0px 0px;        }        #save-patient-container.toggled #save-patient-wrapper {            /*width: 250px;*/        }        #save-patient-container.toggled #save-patient-wrapper {            overflow-y: visible;            height: 670px;        }    }    @media (min-width: 768px) {        /*    #save-patient-wrapper {                width: 0;            }*/        #save-patient-wrapper {            z-index: 1000;            position: absolute;            right: 0px;            width: 0;            top: 20px;            height: 652px;            margin-right: 0px;            overflow: hidden;            background: #e8f8f7;            -webkit-transition: all 0.5s ease;            -moz-transition: all 0.5s ease;            -o-transition: all 0.5s ease;            transition: all 0.5s ease;        }    }</style><script>    $(document).ready(function () {        $("#submitButton").click();        $("#second_menu_list").find("#li_completed_tasks").addClass("active");//        get_physician_list_save_patient();        //  *** Completed Tasks Datatable        global_data.table_completed_tasks_title = "Completed Tasks";        global_data.table_completed_tasks = $("#table_completed_tasks").DataTable({            "order": [[0, "desc"]],            "processing": true,            "serverSide": true,            "autoWidth": false,            "pageLength": 50,            "language": {                "emptyTable": "No Tasks",                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_completed_tasks_title,                "infoEmpty": "No results found",                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_completed_tasks_title + ")",                "infoPostFix": "",                "thousands": ",",                "lengthMenu": "Show _MENU_ ",                "loadingRecords": "Loading " + global_data.table_completed_tasks_title,                "processing": "Processing " + global_data.table_completed_tasks_title,                "search": "",                "zeroRecords": "No matching " + global_data.table_completed_tasks_title + " found"            },            "ajax": "<?php echo base_url(); ?>completed_tasks/ssp_completed_tasks",            "rowCallback": function (row, data, index) {                $('td:eq(4)', row).html(                        set_completed_tasks_data(data[4], data[5], data[6], data[7], data[8], data[9], row)                        );                $(row).addClass('db-table-link-row');            },            "dom": get_dom_plan(),            "columnDefs": [                {"width": "25%", "targets": 0},                {"width": "25%", "targets": 1},                {"width": "35%", "targets": 2},                {"width": "15%", "targets": 3}            ]        });        $("#table_completed_tasks").wrap('<div class="table-responsive"></div>');        $("#table_completed_tasks_wrapper .dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');        $("#table_completed_tasks_wrapper .dataTables_filter input").attr('placeholder', 'Search');        //  *** My Tasks Datatable Over        //$(row).find("td:not(:last-child)").each(function (index, td) {        $("#table_completed_tasks").on("click", ".completed_tasks_row td:not(:last-child)", function () {            root = $(this).closest("tr");            tiff = $(root).data("fileTif");            pdf = $(root).data("filePdf");            fax = $(root).data("senderFaxNumber");            date = $(root).data("date");            time = $(root).data("time");            id = $(root).data("id");            open_efax(id, tiff, pdf, date, fax);        });        $("#btn_view_print_referral").on("click", function () {            printJS(global_data.pdf_file);        });        $("#btn_view_delete_referral").on("click", function () {            view("modal_delete_referral");        });        $(".btn-toggle-referral").on("click", function () {            $("#wrap-container").toggleClass("toggled");            $("#wrap-container").find("fieldset").each(function (key, value) {                if (key != 0)                    $(value).hide();                else                    $(value).show();            });        });        $.dobPicker({            daySelector: '#form_patient_save #pat_dob_day', /* Required */            monthSelector: '#form_patient_save #pat_dob_month', /* Required */            yearSelector: '#form_patient_save #pat_dob_year', /* Required */            dayDefault: 'Day', /* Optional */            monthDefault: 'Month', /* Optional */            yearDefault: 'Year', /* Optional */            minimumAge: 0, /* Optional */            maximumAge: 120 /* Optional */        });        $('button.btn-next').on('click', function () {            var parent_fieldset = $(this).parents('fieldset');            var next_step = true;            parent_fieldset.find('.required').each(function () {                if ($(this).val() == "") {                    $(this).addClass('input-error');                    next_step = false;                } else {                    $(this).removeClass('input-error');                }            });            parent_fieldset.find('.valid_email').each(function () {                if (validateEmail($(this).val())) {                    $(this).removeClass('input-error');                } else {                    $(this).addClass('input-error');                    next_step = false;                }            });            if (next_step) {                parent_fieldset.fadeOut(400, function () {                    $(this).next().fadeIn();                    $(".toolbar").hide();                });                if (cropper) {                    cropper.destroy();                }            }        });        $("#pat_search_by_name").autocomplete({            source: base + "inbox/patient_autocomplete",            minLength: 2,            select: function (event, ui) {                parent_fieldset = $("#form_patient_save").closest('fieldset');                parent_fieldset.fadeOut(400, function () {                    $(this).next().fadeIn();                    $(".toolbar").hide();                    root = $("#form_save_task");                    template = "Patient match found:<br/>###first_name### ###last_name###<br/>DOB:###dob###<br/>OHIP#:###ohip###";                    template = template.replace(/###first_name###/g, ui.item.fname);                    template = template.replace(/###last_name###/g, ui.item.lname);                    template = template.replace(/###dob###/g, ui.item.dob);                    template = template.replace(/###ohip###/g, ui.item.ohip);                    root.find("#id").val(ui.item.id);                    patient_success(template);                });            }        });        $("#wrap-container").find("#btnStartPatientCrop").on("click", function () {            if (cropper) {                cropper.destroy();            }            $(".toolbar").show();            createCropper();//            $("#save-patient-container").find("#btn_search_patient").hide("slow");//            $("#save-patient-container").find("#btn_extract_patient").show("slow");        });        $("#wrap-container").find("#btn_extract_patient").on("click", function () {            console.log("method btn_extract_patient click");            if (cropper) {                //start loading                $("#wrap-container").find("#btn_extract_patient").button('loading');                file_upload_extract_patient(cropper.getImageData());            } else {                error("Please activate cropper before autofill");            }        });        $("#btn_save_task").on("click", function () {            //form_save_patient_record            $("#btn_save_task").button("loading");            url = base + "completed_tasks/update_task";            data = $("#form_save_task").serialize();            data += "&task_id=" + global_data.task_id;            $.post({                url: url,                data: data            }).success(function (response) {                $("#btn_save_task").button("reset");                if (IsJsonString(response)) {                    response = JSON.parse(response);                    if (response.result == "success") {                        global_data.table_completed_tasks.ajax.reload();                        get_latest_dashboard_counts();                        success("Task updated successfully");                    } else {                        error(response.msg);                    }                } else {                    error("Patient record not saved");                }            }).error(function () {                $("#btn_save_task").button("reset");                error("Patient record not saved");            }).done(function () {                $("#btn_save_task").button("reset");            });        });        $("#btn_save_patient_record").on("click", function () {            //form_save_patient_record            $("#btn_save_patient_record").button("loading");            url = base + "inbox/save_patient_record";            data = $("#form_save_patient_record").serialize();            data += "&task_id=" + global_data.task_id;            $.post({                url: url,                data: data            }).success(function (response) {                $("#btn_save_patient_record").button("reset");                if (IsJsonString(response)) {                    response = JSON.parse(response);                    if (response.result == "success") {                        global_data.table_inbox.ajax.reload();                        success("Patient record saved successfully");                    } else {                        error(response.msg);                    }                } else {                    error("Patient record not saved");                }            }).error(function () {                error("Patient record not saved");            }).done(function () {                $("#btn_save_patient_record").button("reset");                // $("#btn_save_patient_record").                // btn_physician_extract.hide("slow");                // $("#btn_extract_physician").show("slow");            });        });        $("#eFax-modal").on('hidden.bs.modal', function () {            exit();        });        $("#overlay_imagem, #btnStartCrop, #btnAutoFill, #btnNextPage, #btnPrevPage, .cropper-container, #page-content-wrapper, .btn-next, #btnStartCrop2").on("click", function () {            if (!global_data.showing_overlay && $("#overlay_image").css("display") != "none") {                $("#overlay_image").hide("slow");            }        });        $('.patients-details-form .btn-next').on('click', function () {            var parent_fieldset = $(this).parents('fieldset');            var next_step = true;            parent_fieldset.find('.required').each(function () {                if ($(this).val() == "") {                    $(this).addClass('input-error');                    next_step = false;                } else {                    $(this).removeClass('input-error');                }            });            parent_fieldset.find('.valid_email').each(function () {                if (validateEmail($(this).val())) {                    $(this).removeClass('input-error');                } else {                    $(this).addClass('input-error');                    next_step = false;                }            });            if (next_step) {                parent_fieldset.fadeOut(400, function () {                    $(this).next().fadeIn();                    $(".toolbar").hide();                });                if (cropper) {                    cropper.destroy();                }            }        });        $('.patients-details-form .btn-previous, #wrap-container .btn-previous').on('click', function () {            $(this).parents('fieldset').fadeOut(400, function () {                $(this).prev().fadeIn();            });        });        $("table#table_completed_tasks").on("click", ".btn_task_done", function () {            id = $(this).data("id");            $("#sample_form").find("#id").val(id);            url = base + "completed_tasks/task_completed";            $.post({                url: url,                data: $("#sample_form").serialize()            }).done(function (response) {                console.log(response);                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data.result == "success") {                        success("Task removed successfully");                        global_data.table_completed_tasks.ajax.reload();                    } else {                        error(data.msg);                    }                }            });        });    });    function set_completed_tasks_data(id, pdf_file, tif_file, sender_fax_number, date, time, row) {        $(row).attr("data-id", id);        $(row).attr("data-file-pdf", pdf_file);        $(row).attr("data-file-tif", tif_file);        $(row).attr("data-sender-fax-number", sender_fax_number);        $(row).attr("data-date", date);        $(row).attr("data-time", time);        $(row).addClass("completed_tasks_row");        return "<button class='btn btn-success btn_task_done' data-id='" + id + "'><span class='fa fa-check'></span></button>";    }    function open_efax(id, tiff_file_name, pdf_file_name, date, fax) {        $(".toolbar").hide();        modal = $("#eFax-modal");        modal.find("#file_info").html(fax + " ( " + date + " - " + time + " ) ");        date_parts = date.split("-");        date = date_parts[2] + "/" + date_parts[1] + "/" + date_parts[0];        time = ((date_parts[3] < 12) ? date_parts[3] : date_parts[3] - 12) + ":" + date_parts[4] + ((date_parts[3] < 12) ? "am" : "pm");        fax = fax + "";        if (fax.length == 10) {            fax = fax.substr(0, 3) + "-" + fax.substr(3, 3) + "-" + fax.substr(6);        } else if (fax.length == 11) {            fax = fax.substr(0, 1) + "-" + fax.substr(1, 3) + "-" + fax.substr(4, 3) + "-" + fax.substr(7);        }        modal.find("#file_info").html(fax + " ( " + date + " - " + time + " ) ");        global_data.pdf_file = base + "uploads/physician_tasks/pdf/" + pdf_file_name;        global_data.tif_file = base + "uploads/physician_tasks/tiff/" + tiff_file_name;        global_data.task_id = id;        $("#btn_download_referral").attr("href", global_data.pdf_file);        $(".input_fields_wrap").find(".remove_field").click();        view("eFax-modal");        setTimeout(function () {            init(global_data.tif_file);        }, 100);    }    function file_upload_extract_patient(data) {        console.log("method file_upload_extract_patient triage called");        if (uploadingFile) {            error("We are already started fetching data. Please wait");            return;        }        var canvas;        if (cropper) {            uploadingFile = true;            canvas = cropper.getCroppedCanvas();            var imageObj = $('#_blob');            imageObj.attr('src', canvas.toDataURL());            imageObj.css('width', canvas.width + 'px');            imageObj.css('height', canvas.height + 'px');            canvas.toBlob(function (blob) {                var formData = new FormData();                formData.append('file', blob);                if (global_data.release_type === "prod") {                    formData.append('x-application-secret', 'fsk9scdJ1eiU3ZR+vVoanV0RSqlWhLyAp5ri4eXxtC9A61sBmoKlOqg=');                    formData.append('x-client-name', 'scarlet-client');                }                // global_data.api_phy_extract = "running";                $("#btn_extract_patient").button("loading");                // $.ajax('http://159.89.127.142/phy_extract', {                                                $.ajax(global_data.predict_url + global_data.predict_api, {                    method: 'POST',                    data: formData,                    processData: false,                    contentType: false,                    success: function (response) {                        tmp_selector = "#anything_fake";                        root = $("#form_patient_save");                        data_points = 0;                        data_points_captured = {};                        data_points_captured.first_name = "";                        data_points_captured.last_name = "";                        data_points_captured.dob_day = "";                        data_points_captured.dob_month = "";                        data_points_captured.dob_year = "";                        data_points_captured.icn = "";                        data_points_captured.phone = "";                        data_points_captured.email = "";                        data_points_captured.gender = "";                        data_points_captured.address = "";                        data_points_captured.success = response.success;                        if (response.success) {                            if (response.predictions.name.hasOwnProperty('first_name')) {                                if (response.predictions.name.first_name != "") {                                    root.find("#new-patient-firstname").val(                                            response.predictions.name.first_name);                                    tmp_selector += ', #new-patient-firstname';                                    data_points += 1;                                    data_points_captured.first_name = response.predictions.name.first_name;                                }                            }                            if (response.predictions.name.hasOwnProperty('last_name')) {                                if (response.predictions.name.last_name != "") {                                    root.find("#new-patient-lastname").val(                                            response.predictions.name.last_name);                                    tmp_selector += ', #new-patient-lastname';                                    data_points += 1;                                    data_points_captured.last_name = response.predictions.name.last_name;                                }                            }                            if (response.predictions.DOB.hasOwnProperty('Day')) {                                if (response.predictions.DOB.Day <= 9) {                                    response.predictions.DOB.Day = "0" + response.predictions.DOB.Day;                                }                                if (response.predictions.DOB.Day != "") {                                    root.find("#pat_dob_day").val(response.predictions.DOB.Day);                                    tmp_selector += ', #pat_dob_day';                                    data_points += 1;                                    data_points_captured.dob_day = response.predictions.DOB.Day;                                }                            }                            if (response.predictions.DOB.hasOwnProperty('Month')) {                                if (response.predictions.DOB.Month <= 9) {                                    response.predictions.DOB.Month = "0" + response.predictions.DOB.Month;                                }                                if (response.predictions.DOB.Month != "") {                                    root.find("#pat_dob_month").val(response.predictions.DOB.Month);                                    tmp_selector += ', #pat_dob_month';                                    data_points_captured.dob_month = response.predictions.DOB.Month;                                }                            }                            if (response.predictions.DOB.hasOwnProperty('Year')) {                                if (response.predictions.DOB.Year != "") {                                    root.find("#pat_dob_year").val(response.predictions.DOB.Year);                                    tmp_selector += ', #pat_dob_year';                                    data_points_captured.dob_year = response.predictions.DOB.Year;                                }                            }                            if (response.predictions.hasOwnProperty('ICN')) {                                if (response.predictions.ICN.hasOwnProperty('NO')) {                                    if (response.predictions.ICN.NO != "") {                                        root.find("#new-patient-ohip").val(response.predictions.ICN.NO.replace(/\D/g,''));                                        data_points_captured.icn = response.predictions.ICN.NO.replace(/\D/g,'');                                        tmp_selector += ', #new-patient-ohip';                                        data_points += 1;                                    }                                } else {                                    if (response.predictions.ICN != "") {                                        root.find("#new-patient-ohip").val(response.predictions.ICN.replace(/\D/g,''));                                        data_points_captured.icn = response.predictions.ICN.replace(/\D/g,'');                                        tmp_selector += ', #new-patient-ohip';                                        data_points += 1;                                    }                                }                            }                            if (response.predictions.hasOwnProperty('phone')) {                                if (response.predictions.phone != "") {                                    root.find("#patient-cell-phone").val(response.predictions.phone);                                    data_points_captured.phone = response.predictions.phone;                                    tmp_selector += ', #patient-cell-phone';                                    data_points += 1;                                }                            }                            if (response.predictions.hasOwnProperty('email')) {                                if (response.predictions.email != "") {                                    root.find("#patient-email-id").val(response.predictions.email);                                    data_points_captured.email = response.predictions.email;                                    tmp_selector += ', #patient-email-id';                                    data_points += 1;                                }                            }//                            if (response.predictions.hasOwnProperty('gender')) {//                                if (response.predictions.gender != "") {//                                    root.find("#pat_gender").val(response.predictions.gender);//                                    data_points_captured.gender = response.predictions.gender;//                                    tmp_selector += ', #pat_gender';//                                    data_points += 1;//                                }//                            }                            if (response.predictions.hasOwnProperty('address')) {                                if (response.predictions.address != "") {                                    root.find("#pat_geocomplete").val(response.predictions.address);                                    data_points_captured.address = response.predictions.address;                                    tmp_selector += ', #pat_geocomplete';                                    data_points += 1;                                }                            }                            //mark updated animation                            $(root).find(tmp_selector).toggleClass("updated_mode");                            setTimeout(function () {                                $(tmp_selector).toggleClass("updated_mode");                            }, 3000);                            log_data_points(data_points, global_data.task_id, "predict");                        }                        uploadingFile = false;                    },                    error: function (response) {                        console.log("error");                        console.log(response);                        uploadingFile = false;                    },                    complete: function () {                        console.log("completed");                        uploadingFile = false;                        match_patient_data();                    }                }).done(function () {                    uploadingFile = false;                });            });        }    }    function validateEmail(email) {        if (email === "")            return true;        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;        return re.test(String(email).toLowerCase());    }    function match_patient_data() {        form = $("#form_patient_save");        form.find("#id").val(global_data.task_id);        url = base + "inbox/check_patient_data";        data = form.serialize();        var parent_fieldset = $("#form_patient_save").closest('fieldset');        $.post({            url: url,            data: data        }).success(function (response) {            if (IsJsonString(response)) {                response = JSON.parse(response);                if (response.result == "success") {                    data = response.data;                    root = $("#wrap-container");                    console.log("Patient search -> ", response);                    matches = JSON.parse(data);                    if (matches.length == 1) {                        parent_fieldset.fadeOut(400, function () {                            $(this).next().fadeIn();                            $(".toolbar").hide();                        });                        template = "Patient match found:<br/>###name###<br/>DOB:###dob###<br/>OHIP#:###ohip###";                        template = template.replace(/###name###/g, matches[0].name);                        template = template.replace(/###dob###/g, matches[0].dob);                        template = template.replace(/###ohip###/g, matches[0].ohip);                        root.find("#id").val(matches[0].id);                        patient_success(template);                    } else {                        if (global_data.table_clinic_patients) {                            global_data.table_clinic_patients.destroy();                        }                        table_data = "<table class='table table-striped table-bordered table-responsive'>";                        table_data += "<thead><tr><th>Patient Name</th><th>Date of Birth</th><th>OHIP#</th></tr></thead>";                        table_data += "<tbody>";                        for (i = 0; i < matches.length; i++) {                            table_data += "<tr data-id='" + matches[i].id + "'>";                            table_data += "<td>" + matches[i].name + "</td>";                            table_data += "<td>" + matches[i].dob + "</td>";                            table_data += "<td>" + matches[i].ohip + "</td>";                            table_data += "</tr>";                        }                        table_data += "</tbody></table>";                        $("#table_clinic_patients").html(table_data);                        global_data.table_clinic_patients = $("#table_clinic_patients").DataTable();                        $("#save-efax-modal").modal("show");                    }                } else {                    patient_error(response.msg);                }            } else {                patient_error("Something went wrong");            }            $("#btn_extract_patient").button('reset');        }).error(function () {            patient_error("Patient matching not performed");            $("#btn_extract_patient").button('reset');        }).done(function () {            $("#btn_extract_patient").button('reset');        });    }    function log_data_points(data_points, task_id, api) {        $.post({            url: base + "referral/log_data_points",            data: {                "data_points": data_points,                "task_id": task_id,                "api": api,                "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"            }        });    }    function patient_success(msg) {        $("#wrap-container").find("#patient_success").html(msg).show();        $("#wrap-container").find("#patient_error").hide();    }    function patient_error(msg) {        $("#wrap-container").find("#patient_error").html(msg).show().delay(5000).fadeOut();        $("#wrap-container").find("#patient_success").hide();    }    function get_physician_list_save_patient() {        url = base + "inbox/get_physician_list_save_patient";        $.post({            url: url,            data: $("#sample_form").serialize()        }).done(function (response) {            if (IsJsonString(response)) {                data = JSON.parse(response);                options = "<option selected value='admin'>Unassigned</option>";                data.forEach(function (value, index) {                    options += "<option value='" + value.id + "'>" + value.name + "</option>";                });                $("#form_save_task").find("#assign_physician").html(options);                global_data.table_completed_tasks.ajax.reload();            }        });    }</script> 