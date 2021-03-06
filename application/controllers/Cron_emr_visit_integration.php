<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_emr_visit_integration extends CI_Controller {

//Confirmation cron job 2day (dynamic) to visit datetime
//    public function index() {
//        if (isset($argv)) {
//            if (!empty($argv[1])) {
//                switch ($argv[1]) {
//                    case "ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE":
//                        $this->ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE();
////                        log_message("error", "Called function ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE");
//                        break;
//                }
//            }
//        }
//    }

    public function ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE() {
        log_message("error", "Cron for expired links");
        $current_time = new DateTime(date("Y-m-d H:i:s"));
        $expire_time = $current_time->sub(new DateInterval('PT5M'))->format("Y-m-d H:i:s");
        $this->db->where(array(
            "status" => "Sent",
            "active" => 0,
            "create_datetime <" => $expire_time
        ))->update("clinic_physician_info", array(
            "status" => "Expired"
        ));
        //log_message("error", "expire q = " . $this->db->last_query());

        log_message("error", "Cron_emr_visit_integration called $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
        //get all clinic and loop all
        //find new requests from integration table
        $new_entries = $this->db->select("id, clinic_id, pat_ohip, pat_fname, pat_lname, pat_dob, "
                                . "pat_cell_phone, pat_home_phone, pat_work_phone, pat_email_id,"
                                . "start_time, end_time, appointment_date, is_confirmed, "
                                . "emr_demographic_id")
                        ->from("records_patient_visit_integration")
                        ->where(array(
                            "active" => 1,
                            "status" => "NEW",
                            "created_by" => "emr",
                        ))
                        ->get()->result();

        //log_message("error", "new entries q = " . $this->db->last_query());

        foreach ($new_entries as $key => $entry) {
            //log_message("error", "Processing visit integration with " . json_encode($entry));

            $emr_id_matches = $this->db->select("id")
                            ->from("referral_patient_info")
                            ->where(array(
                                "active" => 1,
                                "emr_demographic_id" => $entry->emr_demographic_id
                            ))
                            ->get()->result();

            //check if existing emr id matches. 
            if ($emr_id_matches) {
                $patient_visit_data = array(
                    "patient_id" => $emr_id_matches[0]->id,
                    "visit_name" => "",
                    "visit_date" => $entry->appointment_date,
                    "visit_time" => $entry->start_time,
                    "visit_end_time" => $entry->end_time,
                    "notify_type" => "sms",
                    "visit_confirmed" => ($entry->is_confirmed === "yes") ?
                    "Confirmed" : "Awaiting Confirmation"
                );
                //log_message("error", "EMR MATCHES => Integrated data = " . json_encode($patient_visit_data));
                $this->db->insert("records_patient_visit", $patient_visit_data);
            } else {
                //First look for ohip match
                $primary_matches = $this->db->select("id, cell_phone, home_phone, work_phone, "
                                        . "email_id, create_datetime")
                                ->from("referral_patient_info")
                                ->where(array(
                                    "active" => 1,
                                    "ohip" => $entry->pat_ohip
                                ))->get()->result();
                //log_message("error", "ohip matches q = " . $this->db->last_query());
                //log_message("error", "ohip matches found = " . json_encode($primary_matches));
                $patient_data = null;

                //check any ohip match found
                if ($primary_matches) {
                    if (sizeof($primary_matches) > 1) {
                        //If multiple matches found --> look for unique phone number or e-mail
                        foreach ($primary_matches as $key => $primary_match) {
                            foreach ($primary_matches as $key => $primary_match) {
                                if (($entry->pat_cell_phone === $primary_match->cell_phone &&
                                        $entry->pat_cell_phone !== "") ||
                                        ($entry->pat_home_phone === $primary_match->home_phone &&
                                        $entry->pat_home_phone !== "") ||
                                        ($entry->pat_work_phone === $primary_match->work_phone &&
                                        $entry->pat_work_phone !== "") ||
                                        $entry->pat_email_id === $primary_match->email_id) {
                                    //match found based on contact or email
                                    $patient_data = $primary_match;
                                    //log_message("error", "match found from contact and email = " . $patient_data);
                                    break;
                                }
                            }
                        }
                    } else {
                        //If single match found --> take that patient
                        $patient_data = $primary_matches[0];
                        //log_message("error", "match found as only one ohip match = " . json_encode($patient_data));
                    }
                }

                if (!$patient_data) {
                    //if not yet found
                    // Look for  lname + fname, + dob match
                    //check if it matches email id, fname, lname
                    $primary_matches = $this->db->select("id, cell_phone, home_phone, work_phone, "
                                            . "email_id, create_datetime")
                                    ->from("referral_patient_info")
                                    ->where(array(
                                        "active" => 1,
                                        "dob" => $entry->pat_dob,
                                        "fname" => $entry->pat_fname,
                                        "lname" => $entry->pat_lname
                                    ))->get()->result();
                    //log_message("error", "email id, fname, lname matches q = " . $this->db->last_query());
                    //log_message("error", "email id, fname, lname matches found = " . json_encode($primary_matches));

                    if ($primary_matches) {
                        if (sizeof($primary_matches) > 1) {
                            //log_message("error", "matches > 1");
                            $latest = null;
                            //if multiple matches - use phone or email field to find unique one
                            foreach ($primary_matches as $key => $primary_match) {
                                if (($entry->pat_cell_phone === $primary_match->cell_phone &&
                                        $entry->pat_cell_phone !== "") ||
                                        ($entry->pat_home_phone === $primary_match->home_phone &&
                                        $entry->pat_home_phone !== "") ||
                                        ($entry->pat_work_phone === $primary_match->work_phone &&
                                        $entry->pat_work_phone !== "") ||
                                        $entry->pat_email_id === $primary_match->email_id) {
                                    //match found based on contact or email
                                    $patient_data = $primary_match;
                                    //log_message("error", "match found from contact and email = " . $patient_data);
                                    break;
                                } else {
                                    if ($latest) {
                                        if ($latest->create_datetime < $primary_match->create_datetime) {
                                            $latest = $primary_match;
                                        }
                                    } else {
                                        $latest = $primary_match;
                                    }
                                }
                            }


                            //If phone and email don’t find unique, then pick one that was created most recently
                            if (!$patient_data) {
                                $patient_data = $latest;
                                //log_message("error", "match found from latest = " . json_encode($patient_data));
                            }
                        } else {
                            //if single match found, this is the match
                            $patient_data = $primary_matches[0];
                            //log_message("error", "match found as only one match = " . json_encode($patient_data));
                        }
                    }
                }

                if ($patient_data) {
                    //now process and make entries accordingly
                    $patient_visit_data = array(
                        "patient_id" => $patient_data->id,
                        "visit_name" => "",
                        "visit_date" => $entry->appointment_date,
                        "visit_time" => $entry->start_time,
                        "visit_end_time" => $entry->end_time,
                        "notify_type" => "sms",
                        "visit_confirmed" => ($entry->is_confirmed === "yes") ?
                        "Confirmed" : "Awaiting Confirmation"
                    );
                    //log_message("error", "Integrated data = " . json_encode($patient_visit_data));
                    $this->db->insert("records_patient_visit", $patient_visit_data);
                } else {
                    $this->db->trans_start();
                    //create patient first
                    $this->db->insert("efax_info", array(
                        "to" => $entry->clinic_id,
                        "file_name" => "1234",
                        "tiff_file_name" => "1234.tif",
                        "pages" => 3,
                        "sender_fax_number" => "1234",
                        "referred" => TRUE
                    ));
                    $efax_id = $this->db->insert_id();
                    //log_message("error", "insert fake efax  = " . $this->db->last_query());
                    //add referral
                    $referral_reason = "";
                    //                $assigned_physician = get_decrypted_id($data["assigned_physician"], "clinic_physician_info");
                    $first_status = "Scheduled";
                    $insert_data = array(
                        "efax_id" => $efax_id,
                        "referral_reason" => $referral_reason,
                        "status" => $first_status
                    );

                    //If clinic has only 1 physician account, then assign by default 
                    $physicians = $this->db->select("id")->from("clinic_physician_info")->where(array(
                                "clinic_id" => $entry->clinic_id
                            ))->get()->result();
                    if ($physicians && sizeof($physicians) === 1) {
                        $insert_data["assigned_physician"] = $physicians[0]->id;
                    }
                    $this->db->insert("clinic_referrals", $insert_data);
                    $referral_id = $this->db->insert_id();
                    //new referral record added
                    //log_message("error", "insert clinic referrals  = " . $this->db->last_query());
                    //store patient details
                    //                $patient_location = get_decrypted_id($data["patient_location"], "clinic_locations");
                    //                $custom = (isset($data["custom"])) ?
                    //                        get_decrypted_id($data["custom"], "clinic_custom") : 0;
                    $patient_data = array(
                        "referral_id" => $referral_id,
                        "fname" => $entry->pat_fname,
                        "lname" => $entry->pat_lname,
                        "dob" => $entry->pat_dob,
                        "ohip" => $entry->pat_ohip,
                        "cell_phone" => $entry->pat_cell_phone,
                        "home_phone" => $entry->pat_home_phone,
                        "work_phone" => $entry->pat_work_phone,
                        "next_visit" => "Initial consult",
                        "emr_demographic_id" => $entry->emr_demographic_id
                    );
                    $this->db->insert("referral_patient_info", $patient_data);
                    $patient_id = $this->db->insert_id();
                    //log_message("error", "insert patient = " . $this->db->last_query());
                    //                //log_message("error", "dr_fax trimmed = " . $data["dr_fax"]);
                    //store referring physician data linked to patient id
                    $physician_data = array(
                        "patient_id" => $patient_id
                    );
                    $this->db->insert("referral_physician_info", $physician_data);
                    //log_message("error", "insert physician  = " . $this->db->last_query());
                    //store clinical triage info linked to patient id
                    $clinical_triage_data = array(
                        "patient_id" => $patient_id
                    );
                    $this->db->insert("referral_clinic_triage", $clinical_triage_data);
                    $clinic_triage_id = $this->db->insert_id();
                    //log_message("error", "triage referral = " . $this->db->last_query());
                    //insert referral checklist
                    //                if (isset($data["referral_checklist"]))
                    //                    $referral_checklist = $data["referral_checklist"];
                    //                else
                    $referral_checklist = array();

                    //log_message("error", "checklist array = " . json_encode($referral_checklist));
                    //insert default checklist info
                    $this->db->select("md5(id) as id, id as plain_id");
                    $this->db->from("clinic_referral_checklist_items");
                    $this->db->where(array(
                        "active" => 1,
                        "clinic_id" => $entry->clinic_id
                    ));
                    $default_checklist = $this->db->get()->result();
                    //log_message("error", "checklist query = " . $this->db->last_query());
                    //log_message("error", "default checklist = " . json_encode($default_checklist));

                    foreach ($default_checklist as $key => $value) {
                        $exist = array_search($value->id, $referral_checklist);
                        $checked = ($exist === false) ? "false" : "true";
                        // //log_message("error", "val = " . $value->id . " and ref = " . json_encode($referral_checklist));
                        $check_type = "stored";
                        $this->db->insert("referral_checklist", array(
                            "patient_id" => $patient_id,
                            "checklist_type" => $check_type,
                            "checklist_id" => $value->plain_id,
                            "attached" => $checked
                        ));
                        //log_message("error", "insert for default = " . $this->db->last_query());
                    }

                    //insert new checlist info
                    //log_message("error", "at custome checklist");
                    $data["new_checklists"] = (isset($data["new_checklists"])) ? $data["new_checklists"] : "";
                    $new_checklist = explode(",", $data["new_checklists"]);
                    foreach ($new_checklist as $key => $value) {
                        if ($value == "")
                            continue;
                        $exist = array_search($value, $referral_checklist);
                        // //log_message("error", "val = " . $value . " and ref = " . json_encode($referral_checklist));
                        $checked = ($exist === false) ? "false" : "true";
                        $check_type = "typed";
                        $this->db->insert("referral_checklist", array(
                            "patient_id" => $patient_id,
                            "checklist_type" => $check_type,
                            "checklist_name" => $value,
                            "attached" => $checked
                        ));

                        //log_message("error", "insert custom = " . $this->db->last_query());
                    }


                    //now set the appointment 
                    $patient_visit_data = array(
                        "patient_id" => $patient_id,
                        "visit_date" => $entry->appointment_date,
                        "visit_time" => $entry->start_time,
                        "visit_end_time" => $entry->end_time,
                        "notify_type" => "sms",
                        "visit_confirmed" => ($entry->is_confirmed === "yes") ?
                        "Confirmed" : "Awaiting Confirmation"
                    );
                    //log_message("error", "Integrated data = " . json_encode($patient_visit_data));
                    $this->db->insert("records_patient_visit", $patient_visit_data);


                    $this->db->trans_complete();
                }
            }

            $this->db->where(array(
                "id" => $entry->id
            ))->update("records_patient_visit_integration", array(
                "active" => 0
            ));
            log_message("error", "deactivated the " . $entry->id);
        }


        //Patient match
        //1) HIN
        //2) LN + fN + DOB (if multiple matches - use phone or email field to find unique one). 
        //3) If phone and email don’t find unique, then pick one that was created most recently
//        1) First look for ohip match
//        If single match found --> take that patient
//        If multiple matches found --> look for unique phone number or e-mail
//        If no matches found --> move to (2)
//        2) Look for  lname + fname, + dob match
//        If single match found --> take that patient
//        If multiple matches found --> look for unique phone number or e-mail
//        If still no single match --> take most recently created account
//        3) If match not found --> create new patient entry + appointment in our db
    }

    //latest logic
//    $latest = null;
//                        //if multiple matches - use phone or email field to find unique one
//                        foreach ($primary_matches as $key => $primary_match) {
//                            if (($entry->pat_cell_phone === $primary_match->cell_phone &&
//                                    $entry->pat_cell_phone !== "") ||
//                                    ($entry->pat_home_phone === $primary_match->home_phone &&
//                                    $entry->pat_home_phone !== "") ||
//                                    ($entry->pat_work_phone === $primary_match->work_phone &&
//                                    $entry->pat_work_phone !== "") ||
//                                    $entry->pat_email_id === $primary_match->email_id) {
//                                //match found based on contact or email
//                                $patient_data = $primary_match;
//                                log_message("error", "match found from contact and email = " . $patient_data);
//                                break;
//                            } else {
//                                if ($latest) {
//                                    if ($latest->create_datetime < $primary_match->create_datetime) {
//                                        $latest = $primary_match;
//                                    }
//                                } else {
//                                    $latest = $primary_match;
//                                }
//                            }
//                        }
}
