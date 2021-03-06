<?php

class Referral_model extends CI_Model {

    public function fetch_dashboard_counts_model() {
        $match = "";
        $col = "";
        $clinic_id = $this->session->userdata("user_id");
        $where_for_task_count = null;
        if ($this->session->userdata("login_role") == "clinic_admin") {
            $match = $this->session->userdata("user_id");
            $col = "clinic_admin";
            $where_for_task_count = "clinic_id = $clinic_id";
        } else if ($this->session->userdata("login_role") == "clinic_physician") {
            $match = $this->session->userdata("physician_id");
            $col = "physician_id";
            $where_for_task_count = "assigned_to = $match and clinic_id = $clinic_id";
        }
        $sql = "select ";
        $sql .= "(select count(*) from inbox_dash where clinic_id ='" . $clinic_id . "') as count_inbox,";
        $sql .= "(select count(*) from view_dash_referral_triage where clinic_admin ='" . $clinic_id . "') as count_physician,";
        $sql .= "(select count(*) from accepted_dash where " . $col . " ='" . $match . "' and  clinic_admin ='" . $clinic_id . "') as count_accepted,";
        $sql .= "(select count(r_pv.id) from 
            records_patient_visit r_pv, referral_patient_info pat, clinic_referrals c_ref, efax_info efax  
            where r_pv.patient_id = pat.id AND pat.referral_id = c_ref.id AND c_ref.status = 'Scheduled' AND
            concat(r_pv.visit_date, ' ', r_pv.visit_time) > now() and r_pv.active = 1 and c_ref.efax_id = efax.id and 
            pat.active = 1 and c_ref.active = 1 and efax.active = 1 and efax.to ='" . $clinic_id . "') as count_scheduled,";
        $sql .= "(select count(*) from view_my_tasks where $where_for_task_count) as count_my_tasks,";
        $sql .= "(select count(*) from view_completed_tasks where $where_for_task_count) as count_completed_tasks,";
        $sql .= "(select count(*) from call_center_well_health where clinic_id = {$clinic_id}) as count_call_center";


        //count_all_records
        $result = $this->db->query($sql)->result();
        // //log_message("error", $this->db->last_query());
        return $result;
    }
    
    
    public function uploads_model() {
//        echo "show file here";
//        exit();
        
        $uri = $this->uri->segments;
        
        array_shift($uri);
        $file_path = implode($uri, "/");
        log_message("error", "file path = " . $file_path);
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        $mime = get_mime_by_extension($file_path);
        if($ext === "tif" || $ext === "tiff") {
            $mime = "image/tiff";
            log_message("error", "tiff uploads");
        }
        
        

        if (file_exists($file_path)) { // check the file is existing 
            header('Content-Type: ' . $mime);
            readfile($file_path);
        } else {
            show_404();
        }
        exit();
    }


    public function check_valid_referral_state_model($state, $state2 = "blank") {
        $md5_id = $this->uri->segment(3);
        $this->db->select("pat.id");
        $this->db->from("clinic_referrals c_ref, efax_info efax, referral_patient_info pat");
        $this->db->where(
                array(
                    "efax.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "md5(pat.id)" => $md5_id,
                    "efax.to" => $this->session->userdata("user_id")
                )
        );
        $this->db->where("c_ref.efax_id", "efax.id", false);
        $this->db->where("pat.referral_id", "c_ref.id", false);
        if ($state2 == "blank") {
            $this->db->where(array(
                "c_ref.status" => $state
            ));
        } else {
            $this->db->where_in("c_ref.status", array($state, $state2));
        }
        $result = $this->db->get()->result();
        //log_message("error", "msg = " . $this->db->last_query());
        return ($result) ? true : false;
    }

    public function search_patient_model() {
        $term = $this->input->get("term");
        //date_format(pat.dob,'%b %D, %Y')
        $this->db->select("concat(pat.fname, ' ', pat.lname, "
                        . "if(pat.ohip <> '', concat(' (', pat.ohip, ')'), '') "
                        . ") as label," .
                        "md5(pat.id) as id, " .
                        "REPLACE(LOWER(status),' ','_') as value")
                ->from("referral_patient_info pat, clinic_referrals c_ref, efax_info efax")
                ->where(array(
                    "pat.active" => 1,
                    "c_ref.active" => 1,
                    "efax.active" => 1,
                    "efax.to" => $this->session->userdata("user_id")
                ))
                ->where("pat.referral_id", "c_ref.id", false)
                ->where("c_ref.efax_id", "efax.id", false)
                ->group_start()
                ->like("pat.fname", $term)
                ->or_like("pat.lname", $term)
                ->or_like("pat.ohip", $term)
                ->group_end();
        $result = $this->db->get()->result();
        //log_message("error", "Patient search q = " . $this->db->last_query());
        return $result;
//        return array();
    }

    public function get_location_and_custom_model() {
        try {
            $location_info = $this->db->select("md5(id) as id, location as name")
                            ->from("clinic_locations")
                            ->where(array(
                                "clinic_id" => $this->session->userdata("user_id"),
                                "active" => 1
                            ))->get()->result();
            $custom_info = $this->db->select("md5(id) as id, name")
                            ->from("clinic_custom")
                            ->where(array(
                                "clinic_id" => $this->session->userdata("user_id"),
                                "active" => 1
                            ))->get()->result();

            return array(
                "result" => "success",
                "data" => array(
                    "locations" => $location_info,
                    "customs" => $custom_info
                )
            );
        } catch (Exception $e) {
            return array(
                "result" => "error",
                "message" => "Operation is not completed"
            );
        }
    }

    public function set_next_visit_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('param', 'Next Visit', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {
                    $this->db->trans_start();
                    $visit_info = $this->db->select("visit_type")
                                    ->from("clinic_visit_timings")
                                    ->where(array(
                                        "clinic_id" => $this->session->userdata("user_id"),
                                        "md5(id)" => $data["param"]
                                    ))->get()->result();
                    if ($visit_info) {
                        $next_visit = $visit_info[0]->visit_type;
                        $updated = $this->db->where(array(
                                    "md5(id)" => $data["id"]
                                ))->update("referral_patient_info", array(
                            "next_visit" => $next_visit
                        ));
                        //log_message("error", "Updated next visit = " . $this->db->last_query());
                        if ($updated) {
                            $this->db->trans_complete();
                            return array(
                                "result" => "success",
                                "message" => "Next visit settings are updated"
                            );
                        } else {
                            return array(
                                "result" => "error",
                                "message" => "Operation is not completed"
                            );
                        }
                    } else {
                        return array(
                            "result" => "error",
                            "message" => "Visit info not found for this clinic"
                        );
                    }
                } catch (Exception $e) {
                    return array(
                        "result" => "error",
                        "message" => "Operation is not completed"
                    );
                }
            } else {
                return array(
                    "result" => "error",
                    "message" => "You are not authorized for such Operation"
                );
            }
        } else {
            return validation_errors();
        }
    }

    public function set_custom_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('param', 'Custom', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {
                    $this->db->trans_start();
                    $custom_info = $this->db->select("id")
                                    ->from("clinic_custom")
                                    ->where(array(
                                        "clinic_id" => $this->session->userdata("user_id"),
                                        "md5(id)" => $data["param"]
                                    ))->get()->result();
                    if ($custom_info) {
                        $updated = $this->db->where(array(
                                    "md5(id)" => $data["id"]
                                ))->update("referral_patient_info", array(
                            "custom_id" => $custom_info[0]->id
                        ));
                        if ($updated) {
                            $this->db->trans_complete();
                            return array(
                                "result" => "success",
                                "message" => "Custom field updated"
                            );
                        } else {
                            return array(
                                "result" => "error",
                                "message" => "Operation is not completed"
                            );
                        }
                    } else {
                        return array(
                            "result" => "error",
                            "message" => "Custom field info not found for this clinic"
                        );
                    }
                } catch (Exception $ex) {
                    //log_message("error", "exception at set_custom => " . $ex->getMessage());
                    return array(
                        "result" => "error",
                        "message" => "Operation is not completed"
                    );
                }
            } else {
                return array(
                    "result" => "error",
                    "message" => "You are not authorized for such Operation"
                );
            }
        } else {
            return validation_errors();
        }
    }

    public function set_patient_location_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('param', 'Patient Location', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {
                    $this->db->trans_start();
                    $location_info = $this->db->select("id")
                                    ->from("clinic_locations")
                                    ->where(array(
                                        "clinic_id" => $this->session->userdata("user_id"),
                                        "md5(id)" => $data["param"]
                                    ))->get()->result();
                    if ($location_info) {
                        $updated = $this->db->where(array(
                                    "md5(id)" => $data["id"]
                                ))->update("referral_patient_info", array(
                            "location_id" => $location_info[0]->id
                        ));
                        if ($updated) {
                            $this->db->trans_complete();
                            return array(
                                "result" => "success",
                                "message" => "Patient location updated"
                            );
                        } else {
                            return array(
                                "result" => "error",
                                "message" => "Operation is not completed"
                            );
                        }
                    } else {
                        return array(
                            "result" => "error",
                            "message" => "Custom field info not found for this clinic"
                        );
                    }
                } catch (Exception $ex) {
                    //log_message("error", "exception at set_location => " . $ex->getMessage());
                    return array(
                        "result" => "error",
                        "message" => "Operation is not completed"
                    );
                }
            } else {
                return array(
                    "result" => "error",
                    "message" => "You are not authorized for such Operation"
                );
            }
        } else {
            return validation_errors();
        }
    }

    public function update_patient_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('pat_fname', 'First Name', 'required');
        $this->form_validation->set_rules('pat_lname', 'Last Name', 'required');
        $this->form_validation->set_rules('dobday', 'Day - Date of Birth', 'required');
        $this->form_validation->set_rules('dobmonth', 'Month - Date of Birth', 'required');
        $this->form_validation->set_rules('dobyear', 'Year - Date of Birth', 'required');
        // $this->form_validation->set_rules('pat_ohip', 'Patient OHIP', 'callback_valid_ohip');
        $this->form_validation->set_rules('pat_email_id', 'Patient Email', 'valid_email');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {
                    $this->db->trans_start();
                    $this->db->where(array(
                        "active" => 1,
                        "md5(id)" => $data["id"]
                    ));
                    $new_data = array(
                        "fname" => $data["pat_fname"],
                        "lname" => $data["pat_lname"],
                        "dob" => filter_only_numbers($data["dobyear"]) . "-"
                        . filter_only_numbers($data["dobmonth"]) . "-"
                        . filter_only_numbers($data["dobday"]),
                        "ohip" => $data["pat_ohip"],
                        "cell_phone" => filter_only_numbers($data["pat_cell_phone"]),
                        "home_phone" => filter_only_numbers($data["pat_home_phone"]),
                        "work_phone" => filter_only_numbers($data["pat_work_phone"]),
                        "email_id" => $data["pat_email_id"],
                        "address" => $data["pat_address"],
                    );
                    $this->db->update("referral_patient_info", $new_data);
                    //log_message("error", "updated rows = " . $this->db->affected_rows());
                    // //log_message("error", "updated = " . $updated);
                    $this->db->select("referral_id");
                    $this->db->from("referral_patient_info");
                    $this->db->where(
                            array(
                                "active" => 1,
                                "md5(id)" => $data["id"]
                            )
                    );
                    $result = $this->db->get()->result();
                    $referral_id = $result[0]->referral_id;
                    $this->db->set("last_updated", "now()", false);
                    $this->db->where(array(
                        "active" => 1,
                        "id" => $referral_id
                    ));
                    $this->db->update("clinic_referrals", array());
                    $this->db->trans_complete();
                    return true;
                } catch (Exception $e) {
                    return "Failed to update patient information";
                }
            } else
                return "You are not authorized for such Operation";
        } else {
            return validation_errors();
        }
    }

    public function update_physician_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('dr_fname', 'First Name', 'required');
        $this->form_validation->set_rules('dr_lname', 'Last Name', 'required');
        // $this->form_validation->set_rules('dr_fax', 'Fax Number', 'required');
        $this->form_validation->set_rules('dr_email_id', 'Physician Email', 'valid_email');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {

                    $new_data = array(
                        "fname" => $data["dr_fname"],
                        "lname" => $data["dr_lname"],
                        "phone" => $data["dr_phone_number"],
                        "fax" => $data["dr_fax"],
                        "email" => $data["dr_email_id"],
                        "address" => $data["dr_address"],
                        "billing_num" => $data["dr_billing_num"]
                    );
                    $this->db->where(array(
                        "active" => 1,
                        "md5(patient_id)" => $data["id"]
                    ));
                    $this->db->update("referral_physician_info", $new_data);
                    //log_message("error", "updated rows = " . $this->db->affected_rows());
                    $updated = ($this->db->affected_rows() == 1) ? true : "Physician information remains same.";
                    //log_message("error", "updated = " . $updated);
//                    $this->db->select("referral_id");
//                    $this->db->from("referral_patient_info");
//                    $this->db->where(array(
//                        "active" => 1,
//                        "md5(id)" => $data["id"]
//                    ));
//                    $result = $this->db->get()->result();
//                    $referral_id = $result[0]->referral_id;
//                    $this->db->where(array(
//                        "active" => 1,
//                        "id" => $referral_id
//                    ));
//                    $this->db->set("last_updated", "now()", false);
//                    $this->db->update("clinic_referrals", array());
                    return true;
                } catch (Exception $e) {
                    return "Failed to update physician information";
                }
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function cancel_referral_model() {
        $this->form_validation->set_rules('id', 'Referral Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                // get referral id based on patient id
                $this->db->select("referral_id");
                $this->db->from("referral_patient_info");
                $this->db->where(array(
                    "active" => 1,
                    "md5(id)" => $data["id"]
                        )
                );
                $result = $this->db->get()->result();
                $referral_id = $result[0]->referral_id;
                $this->db->where(
                        array(
                            "active" => 1,
                            "id" => $referral_id
                        )
                );
                $this->db->set("cancelled_datetime", "now()", false);
                $this->db->update("clinic_referrals", array("status" => "Cancelled"));
                return ($this->db->affected_rows() == 1) ? true : "Referral already Cancelled";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function decline_referral_model() {
        $this->form_validation->set_rules('id', 'Referral Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                // get referral id based on patient id
                $referral_id = $this->get_referral_id($data["id"]);
                $this->db->where(array(
                    "active" => 1,
                    "id" => $referral_id
                ));
                $this->db->set("cancelled_datetime", "now()", false);
                $this->db->update("clinic_referrals", array("status" => "Declined"));
                $reply = ($this->db->affected_rows() == 1) ? true : "Referral already Declined";


                $this->db->select("c_usr.clinic_institution_name, "
                        . "date_format(c_ref.create_datetime, '%M %D') as referral_received, "
                        . "if(pat.dob, DATE_FORMAT(pat.dob, '(%b %d, %Y)'), '') as pat_dob, "
                        . "dr.fax, c_ref.referral_code, pat.fname, pat.lname");
                $this->db->from("clinic_user_info c_usr, efax_info efax, clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
                $this->db->where(array(
                    "c_ref.id" => $referral_id,
                    "efax.active" => 1,
                    "c_usr.active" => 1,
                    "c_ref.active" => 1,
                    "dr.active" => 1,
                    "pat.active" => 1
                ));
                $this->db->where("pat.id", "dr.patient_id", false);
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $this->db->where("efax.to", "c_usr.id", false);
                $this->db->where("efax.id", "c_ref.efax_id", false);
                $result = $this->db->get()->result()[0];

                $patient_id = $this->get_patient_id($data["id"]);
                $this->db->select("if( ref_c.checklist_type = 'stored', c_items.name , ref_c.checklist_name) as 'doc_name'");
                $this->db->from("referral_checklist ref_c");
                $this->db->join("clinic_referral_checklist_items c_items", "c_items.id = ref_c.checklist_id and c_items.active=1", "left");
                $this->db->where(array(
                    "ref_c.active" => 1,
                    "ref_c.attached" => "false",
                    "ref_c.patient_id" => $patient_id
                ));
                $checklist = $this->db->get()->result();
                //log_message("error", "denied check = " . $this->db->last_query());
                //log_message("error", "denied result = " . json_encode($checklist));

                $file_name = "referral_denied.html";
                $replace_stack = array(
                    "###pat_fname###" => $result->fname,
                    "###pat_lname###" => $result->lname,
                    "###pat_dob###" => $result->pat_dob,
                    "###clinic_name###" => $result->clinic_institution_name,
                    "###referral_code###" => $result->referral_code,
                    "###time1###" => $result->referral_received,
                    "###time2###" => date("F jS")
                );
                $fax_number = $result->fax;
                //log_message("error", "not sending fax");
                $response = $this->referral_model->send_status_fax($file_name, $checklist, $replace_stack, $fax_number, "New Referral");


                return $reply;
            } else {
                return "You are not authorized for such Operation";
            }
        } else
            return validation_errors();
    }

    public function confirm_referral_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->join("referral_patient_info pat", "r_pv.patient_id = pat.id and pat.active = 1 and "
                        . "md5(pat.id) = '" . $data["id"] . "'", "left");
                $this->db->where(array(
                    "r_pv.active" => 1
                ));
                $this->db->update("records_patient_visit r_pv", array(
                    "r_pv.visit_confirmed" => "Confirmed"
                ));
                return true;
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function accept_admin_referral_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {

                $referral_id = $this->get_referral_id($data['id']);
                $this->db->where(array(
                    "active" => 1,
                    "id" => $referral_id
                ));
                $this->db->update("clinic_referrals", array("status" => "Referral Triage"));
                //log_message("error", "accept q = " . $this->db->last_query());
                return ($this->db->affected_rows() == 1) ? true : "Referral already Accepted";
            } else
                return "You are not authorized for such Operation";
        } else {
            return validation_errors();
        }
    }

    public function missing_items_details_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $patient_id = $this->get_decrypted_id($data['id'], "referral_patient_info");

                $this->db->select("DATE_FORMAT(miss.create_datetime, '%l %p')  AS last_request_time," .
                        "DATE_FORMAT(miss.create_datetime, '%M %D')  AS last_request_date," .
                        "dr.fax as dr_fax," .
                        "concat('Dr. ', dr.fname, ' ', dr.lname) as dr_name, miss.id");
                $this->db->from("`referral_missing_item_request_info` miss, referral_physician_info dr");
                $this->db->where(array(
                    "miss.`active`" => 1,
                    "dr.`active`" => 1,
                    "md5(miss.patient_id)" => $data['id'],
                    "md5(dr.patient_id)" => $data['id']
                ));
                $this->db->where("miss.requested_to", "dr.id", false);
                $this->db->order_by("miss.create_datetime", "desc");
                $this->db->limit("1");

                $result = $this->db->get()->result();

                //log_message("error", "last 1 = > " . $this->db->last_query());
                $alert_data = null; // data to be returned
                if ($result) {
                    // request has been previously sent for that referral
                    $alert_data = "Missing item request was previously sent to " .
                            $result[0]->dr_name . " at " . $result[0]->last_request_time . " on " .
                            $result[0]->last_request_date . ". " .
                            "Are you sure you would like to send a missing item request again?";
                } else {
                    //first time sending request
                    $this->db->select("concat(dr.fname, ' ', dr.lname) as dr_name");
                    $this->db->from("referral_physician_info dr");
                    $this->db->where(array(
                        "md5(dr.patient_id)" => $data["id"],
                        "dr.active" => 1
                    ));
                    $result = $this->db->get()->result();

                    //log_message("error", "dr_name = " . $this->db->last_query());

                    if (!$result)
                        $dr_name = "ABC";
                    else
                        $dr_name = $result[0]->dr_name;

                    $alert_data = "Are you sure you would like to send a missing item request to " . $dr_name;
                }

                //return result data 
                return array(
                    "result" => "success",
                    "data" => $alert_data
                );
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function request_missing_items_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //send fax to request missing items
                //Send fax in following format, with clinic name, patient name, missing item list, and referral code dynamically added
                $this->db->trans_start();
                $this->db->select("if( ref_c.checklist_type = 'stored', c_items.name , ref_c.checklist_name) as 'doc_name'");
                $this->db->from("referral_checklist ref_c");
                $this->db->join("clinic_referral_checklist_items c_items", "c_items.id = ref_c.checklist_id and c_items.active=1", "left");
                $this->db->where(array(
                    "ref_c.active" => 1,
                    "ref_c.attached" => "false",
                    "md5(ref_c.patient_id)" => $data['id']
                ));
                $this->db->or_group_start()
                        ->where("c_items.clinic_id", $this->session->userdata("user_id"))
                        ->where("ref_c.checklist_type", "typed")
                        ->group_end();
                $checklist = $this->db->get()->result();

                $new_checklist = array();
                foreach ($checklist as $key => $value) {
                    $new_checklist[] = array(
                        "doc_name" => $value->doc_name
                    );
                }
                $checklist = $new_checklist;

                $this->db->select("pat.fname,"
                        . "pat.lname,"
                        . "if(pat.dob, DATE_FORMAT(pat.dob, '(%b %d, %Y)'), '') as pat_dob," .
                        "c_usr.clinic_institution_name," .
                        "c_usr.srfax_number," .
                        "c_ref.referral_code," .
                        "dr.fax, dr.id as dr_id,"
                        . "efax.from as efax_from,"
                        . "date_format(efax.create_datetime, '%M %D') as referral_received,"
                        . "date_format(c_ref.create_datetime, '%M %D') as referral_triaged,"
                        . "c_ref.status");
                $this->db->from("referral_patient_info pat, clinic_user_info c_usr, "
                        . "clinic_referrals c_ref, efax_info efax, referral_physician_info dr");
                $this->db->where(array(
                    "pat.active" => 1,
                    "c_usr.active" => 1,
                    "c_ref.active" => 1,
                    "efax.active" => 1,
                    "dr.active" => 1,
                    "md5(pat.id)" => $data['id'],
                    "md5(dr.patient_id)" => $data['id'],
                    "c_usr.id" => $this->session->userdata("user_id")
                ));
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $this->db->where("c_ref.efax_id", "efax.id", false);
                $info = $this->db->get()->result();

                $file_name = "referral_missing_from_inbox.html";
                $srfax_number = $info[0]->srfax_number;
                //log_message("error", "srfax = " . $srfax_number);
                if (strlen($srfax_number) === 10) {
                    $srfax_number = substr($srfax_number, 0, 3) . "-" .
                            substr($srfax_number, 3, 3) . "-" . substr($srfax_number, 6, 4);
                    //log_message("error", " 10 = srfax = " . $srfax_number);
                } else if (strlen($srfax_number) === 11) {
                    $srfax_number = substr($srfax_number, 0, 1) . "-" . substr($srfax_number, 1, 3) . "-" .
                            substr($srfax_number, 4, 3) . "-" . substr($srfax_number, 7, 4);
                    //log_message("error", " 11 = srfax = " . $srfax_number);
                }
                $pat_dob = $info[0]->pat_dob;
                $replace_stack = array(
                    "###clinic_name###" => $info[0]->clinic_institution_name,
                    "###pat_fname###" => $info[0]->fname,
                    "###pat_lname###" => $info[0]->lname,
                    "###pat_dob###" => $pat_dob,
                    "###fax_number###" => $srfax_number,
                    "###time1###" => $info[0]->referral_triaged,
                    "###time2###" => ""
                );

                $text2 = "<h2>Referral has been triaged and accepted</h2>";
                if ($info[0]->status === "Referral Triage") {
                    $text2 = "<h2>Referral is being triaged</h2>";
                }
                $additional_replace = array(
                    "###text2###" => $text2
                );

                $fax_number = $info[0]->fax;

                $response = $this->send_status_fax2($file_name, $checklist, $replace_stack, $fax_number, "Request Missing Items", $additional_replace);
                //log_message("error", "file sent successfully");
                //store missing item request
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                $result = $this->db->insert("referral_missing_item_request_info", array(
                    "patient_id" => $patient_id,
                    "requested_to" => $info[0]->dr_id
                ));

                //update missing status
                $referral_id = $this->get_referral_id($data["id"]);
                $this->db->where(array(
                    "id" => $referral_id
                ));
                $this->db->update("clinic_referrals", array(
                    "missing_item_status" => "<span class=\"fc-event-dot\" "
                    . "style=\"background-color:#e7e92a\"></span> "
                    . "Missing item requested "
                ));

                $this->db->trans_complete();
                if ($result) {
                    return true;
                } else {
                    return "Operation not completed";
                }
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function send_status_fax($file_name, $checklist, $replace_stack, $fax_number, $reason, $additional_replace = array(), $timeout = 60, $clinic_id = "") {
        //log_message("error", "checklist prepared = " . json_encode($checklist));
//        send_status_fax($file_name, array(), $replace_stack, $fax_number, "Scheduled Referral", $clinic_id)
        //log_message("error", "$file_name, $fax_number");

        $item_template = '<h3 style="margin-bottom: 0em; margin-top: 0em;  font-size: 16px;"> ###item_name###<br>';
        $tmp = "";
        foreach ($checklist as $key => $value) {
            //log_message("error", "val = " . json_encode($value));
            $tmp .= str_replace("###item_name###", ($key + 1) . ". " . $value->doc_name, $item_template);
        }
        $replace_stack["###missing_items###"] = $tmp;
        //log_message("error", "replace stack = " . json_encode($replace_stack));

        $content = "";
        $this->load->helper('file');
        $content = read_file("assets/templates/$file_name");
        foreach ($replace_stack as $key => $value) {
            //log_message("error", "converting $key with $value");
            $content = str_replace($key, $value, $content);
        }

        foreach ($additional_replace as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        $tmp_file_name = $this->generate_random_string(10);
        $dest_file = "assets/fax_assets/" . $tmp_file_name . ".pdf";

        $fp = fopen($dest_file, 'w');
        $postData = array(
            "user-id" => "blockhealth",
            "api-key" => "6FQP7ct7wapUVDyvHph9W6wNGkPbY8SnVZnIczjX5I64erpM",
            "content" => $content,
            "format" => "PDF"
        );
        $url = "https://neutrinoapi.com/html5-render";

        set_time_limit(300);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status != 200) {
            // handle API error...
            //log_message("error", "API Error" . $status);
            return false;
        }


        $fax_content = "Blockhealth Notification Fax";
        $fax_success = $this->send_fax($fax_number, $fax_content, $dest_file, $reason, $clinic_id);
        //log_message("error", "fax code completed" . $fax_success);
        unlink($dest_file);
        return true;
    }

    public function send_status_fax2($file_name, $checklist, $replace_stack, $fax_number, $reason, $additional_replace = array(), $timeout = 60, $clinic_id = "") {
        //log_message("error", "checklist prepared = " . json_encode($checklist));
//        send_status_fax($file_name, array(), $replace_stack, $fax_number, "Scheduled Referral", $clinic_id)
        //log_message("error", "$file_name, $fax_number");

        $item_template = '###item_name###<br/>';
        $tmp = "";
        foreach ($checklist as $key => $value) {
            //log_message("error", "val = " . json_encode($value));
            $tmp .= str_replace("###item_name###", ($key + 1) . ". " . $value["doc_name"], $item_template);
        }
        $replace_stack["###missing_items###"] = $tmp;
        //log_message("error", "replace stack = " . json_encode($replace_stack));

        $content = "";
        $this->load->helper('file');
        $content = read_file("assets/templates/$file_name");
        foreach ($replace_stack as $key => $value) {
            //log_message("error", "converting $key with $value");
            $content = str_replace($key, $value, $content);
        }

        foreach ($additional_replace as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        $tmp_file_name = $this->generate_random_string(10);
        $dest_file = "assets/fax_assets/" . $tmp_file_name . ".pdf";

        $fp = fopen($dest_file, 'w');
        $postData = array(
            "user-id" => "blockhealth",
            "api-key" => "6FQP7ct7wapUVDyvHph9W6wNGkPbY8SnVZnIczjX5I64erpM",
            "content" => $content,
            "format" => "PDF"
        );
        $url = "https://neutrinoapi.com/html5-render";

        set_time_limit(300);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status != 200) {
            // handle API error...
            //log_message("error", "API Error" . $status);
            return false;
        }


        $fax_content = "Blockhealth Notification Fax";
        $fax_success = $this->send_fax($fax_number, $fax_content, $dest_file, $reason, $clinic_id);
        //log_message("error", "fax code completed" . $fax_success);
        unlink($dest_file);
        return true;
    }

    public function accept_physician_referral_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //if physician assigned
                $this->db->select("c_ref.assigned_physician");
                $this->db->from("clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(array(
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "md5(pat.id)" => $data["id"]
                ));
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $tmp_result = $this->db->get()->result();
                $assigned_physician = $tmp_result[0]->assigned_physician;
                if ($assigned_physician == "0") {
                    return "Patient must be assigned before accepting.";
                }

                //can proceed moving to accepted state
                $referral_id = $this->get_referral_id($data['id']);
                $this->db->where(array(
                    "active" => 1,
                    "id" => $referral_id
                ));
                $this->db->update("clinic_referrals", array(
                    "status" => "Accepted",
                    "accepted_datetime" => date("Y-m-d H:i:s")
                ));

                $reply = ($this->db->affected_rows() == 1) ? true : "Referral already Accepted";


                $this->db->select("c_usr.clinic_institution_name, date_format(c_ref.create_datetime, '%M %D') as referral_received, dr.fax, c_ref.referral_code");
                $this->db->from("clinic_user_info c_usr, efax_info efax, clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
                $this->db->where(array(
                    "md5(pat.id)" => $data['id'],
                    "efax.active" => 1,
                    "c_usr.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "dr.active" => 1,
                    "md5(dr.patient_id)" => $data["id"]
                ));

                $this->db->where("efax.to", "c_usr.id", false);
                $this->db->where("efax.id", "c_ref.efax_id", false);
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $result = $this->db->get()->result()[0];



                $file_name = "referral_accepted.html";
                $replace_stack = array(
                    "###clinic_name###" => $result->clinic_institution_name,
                    "###referral_code###" => $result->referral_code,
                    "###time1###" => $result->referral_received,
                    "###time2###" => date("F jS")
                );
                $fax_number = $result->fax;
                //log_message("error", "not sending fax");
//                $response = $this->referral_model->send_status_fax($file_name, array(), $replace_stack, $fax_number, "Accept Referral");

                $this->db->insert("count_accepted_referrals", array(
                    "referral_id" => $referral_id,
                    "login_user_id" => $this->session->userdata("user_id"),
                    "login_role" => $this->session->userdata("login_role")
                ));


                //send first visit request
                $next_visit_info = $this->db->select("c_vt.visit_duration")
                                ->from("clinic_visit_timings c_vt, referral_patient_info pat")
                                ->where(array(
                                    "c_vt.active" => 1,
                                    "pat.active" => 1,
                                    "md5(pat.id)" => $data['id']
                                ))
                                ->where("pat.next_visit", "c_vt.visit_type", false)
                                ->get()->result();
                //log_message("error", "fetching next visit time q = " . $this->db->last_query());
                $new_visit_duration = 30;
                if ($next_visit_info) {
                    $new_visit_duration = $next_visit_info[0]->visit_duration;
                }
                return $this->create_patient_visit($data["id"], "First Visit", $new_visit_duration);
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function complete_referral_model() {
        $this->form_validation->set_rules('id', 'Referral Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->where(
                        array(
                            "active" => 1,
                            "md5(id)" => $data["id"]
                        )
                );
                $this->db->set("completed_datetime", "now()", false);
                $this->db->update("clinic_referrals", array(
                    "status" => "Completed",
                    "completed_datetime" => date("Y-m-d H:i:s")
                ));
                return ($this->db->affected_rows() == 1) ? true : "Referral already Completed";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function get_clinic_physicians_model() {
        $this->db->select(
                "md5(id) as id, " .
                "concat('Dr. ', first_name, ' ', last_name) as physician_name");
        $this->db->from("clinic_physician_info");
        $this->db->where(
                array(
                    "active" => 1,
                    "clinic_id" => $this->session->userdata("user_id")
                )
        );
        $result = $this->db->get()->result();
        return $result;
    }

    public function log_data_points_model() {
        $data = $this->input->post();
        $efax_id = $this->get_decrypted_id($data["efax_id"], "efax_info");
        $this->db->insert("count_data_points", array(
            "login_role" => $this->session->userdata("login_role"),
            "login_user_id" => $this->session->userdata("user_id"),
            "data_points" => $data["data_points"],
            "efax_id" => $efax_id,
            "api" => $data["api"]
        ));
    }

    public function assign_physician_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('target', 'Assign Physician', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //auth and assign physician
                $authorized = $this->check_physician($data["target"]);
                if ($authorized !== false || $data["target"] == "unassign") {
                    $physician_id = $authorized;
                    if ($data["target"] == "unassign") {
                        $physician_id = 0; // means unassign
                    }
                    $referral_id = $this->get_referral_id($data['id']);
                    $this->db->where(
                            array(
                                "active" => 1,
                                "id" => $referral_id
                            )
                    );
                    $this->db->update("clinic_referrals", array(
                        "assigned_physician" => $physician_id
                    ));

                    //disable scheduled visits for this patient

                    $this->db->where(array(
                                "md5(patient_id)" => $data['id']
                            ))
                            ->update("records_patient_visit", array(
                                "active" => 0,
                                "visit_confirmed" => "Inactive"
                    ));
                    $this->db->where(array(
                                "md5(patient_id)" => $data['id']
                            ))
                            ->update("records_patient_visit_reserved", array(
                                "active" => 0,
                                "visit_confirmed" => "Inactive"
                    ));
                    return ($this->db->affected_rows() == 1) ? true : "Referral Not Assigned";
                }
            }
            return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function is_patient_scheduled_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $visit_scheduled = $this->db->select("count(r_pv.id) as visits")
                                ->from("records_patient_visit r_pv")
                                ->where(array(
                                    "md5(r_pv.patient_id)" => $data["id"],
                                    "r_pv.active" => 1
                                ))
                                ->where_in("r_pv.visit_confirmed", array(
                                    "Confirmed",
                                    "Change required",
                                    "Awaiting Confirmation",
                                    "N/A"
                                ))
                                ->get()->result()[0];

                if (intval($visit_scheduled->visits) > 0) {
                    return array(
                        "result" => "success",
                        "is_patient_scheduled" => true
                    );
                } else {
                    return array(
                        "result" => "success",
                        "is_patient_scheduled" => false
                    );
                }
            } else {
                return array(
                    "result" => "error",
                    "msg" => "You are not authorized for such Operation"
                );
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }

    public function set_priority_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('target', 'Priority', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->where(
                        array(
                            "active" => 1,
                            "md5(patient_id)" => $data['id']
                        )
                );
                $this->db->update("referral_clinic_triage", array(
                    "priority" => $data['target']
                ));
                return ($this->db->affected_rows() == 1) ? true : "Failed to set Priority";
            }
            return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    //*********************************
    //  Record Management Functions
    //********************************
    public function add_health_record_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('record_type', 'Select Record', 'required');
        // $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $file_name = "";
            //log_message("error", "uploading" . json_encode($_FILES));
            //log_message("error", json_encode($_FILES['asdqwe']['name']));
            if (!empty($_FILES['asdqwe']['name']) && $_FILES['asdqwe']['name'][0] != "blob") {
                $clinic_id = md5($this->session->userdata("user_id"));
                $patient_id = $data["id"];
                if (!file_exists("./" . files_dir() . "$clinic_id")) {
                    //log_message("error", "creating clinic folder =>" . "./" . files_dir() . "$clinic_id");
                    mkdir("./" . files_dir() . "$clinic_id");
                }
                if (!file_exists("./" . files_dir() . "$clinic_id/" . $patient_id)) {
                    //log_message("error", "creating patient folder =>" . "./" . files_dir() . "$clinic_id/" . $patient_id);
                    mkdir("./" . files_dir() . "$clinic_id/" . $patient_id);
                }
                $target_dir = "./uploads/clinics/$clinic_id/$patient_id/";
//                if (!file_exists($target_dir)) {
//                    mkdir($target_dir);
//                }
                $config = array();
                $config['upload_path'] = $target_dir;
                $config['max_size'] = '10000';
                $config['allowed_types'] = 'pdf';
                $config['overwrite'] = FALSE;
                $this->load->library('upload');
                $files = $_FILES;
                $_FILES['asdqwe']['name'] = $files['asdqwe']['name'][0];
                $_FILES['asdqwe']['type'] = $files['asdqwe']['type'][0];
                $_FILES['asdqwe']['tmp_name'] = $files['asdqwe']['tmp_name'][0];
                $_FILES['asdqwe']['error'] = $files['asdqwe']['error'][0];
                $_FILES['asdqwe']['size'] = $files['asdqwe']['size'][0];
                $file_name = $this->generate_random_string();
                $config['file_name'] = $file_name;
                $this->upload->initialize($config);
                if ($this->upload->do_upload('asdqwe')) {
                    // //log_message("error", "clinical record attachment uploaded");
                } else {
                    return $this->upload->display_errors();
                }
            }
            // authenticate
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                if ($this->session->userdata("login_role") == "clinic_physician")
                    $physician_name = $this->session->userdata("physician_name");
                else if ($this->session->userdata("login_role") == "clinic_admin") {
                    $clinic_id = $this->session->userdata("user_id");
                    $this->db->select("clinic_institution_name");
                    $this->db->from("clinic_user_info");
                    $this->db->where(
                            array(
                                "id" => $clinic_id
                            )
                    );
                    $result = $this->db->get()->result();
                    $physician_name = $result[0]->clinic_institution_name;
                }
                $this->db->insert("records_clinic_notes", array(
                    "patient_id" => $patient_id,
                    "record_type" => $data["record_type"],
                    "description" => htmlspecialchars($data["description"]),
                    "record_file" => $file_name,
                    "physician" => $physician_name
                ));
                return true;
            } else {
                return "Unauthorized access attempt";
            }
        } else
            return validation_errors();
    }

    public function add_admin_note_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('note_type', 'Select Record', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            // authenticate
            $this->db->select("efax.id");
            $this->db->from("efax_info efax, clinic_referrals c_ref, referral_patient_info pat");
            $this->db->where(
                    array(
                        "efax.active" => 1,
                        "c_ref.active" => 1,
                        "pat.active" => 1,
                        "md5(pat.id)" => $data["id"],
                        "efax.to" => $this->session->userdata("user_id")
                    )
            );
            $this->db->where("c_ref.id", "pat.referral_id", false);
            $this->db->where("c_ref.efax_id", "efax.id", false);
            $result = $this->db->get()->result();
            if ($result) {
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                $this->db->insert("records_admin_notes", array(
                    "patient_id" => $patient_id,
                    "note_type" => $data["note_type"],
                    "description" => htmlspecialchars($data["description"])
                ));
                return true;
            } else {
                return "Unauthorized for this Operation";
            }
        } else
            return validation_errors();
    }

    public function add_patient_visit_model() {
        //add patient visit submit from popup in booking and patient details pages
        //log_message("error", "reaching right place");
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $new_visit_duration = 30; // static

        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $patient_id = $this->get_patient_id($data["id"]);
                $referral_id = $this->get_referral_id(md5($patient_id));

                //if selected a slot
                $record_id = $data["record_id"];
                if (isset($data["visit_slot"])) {

                    //log_message("error", "=>" . isset($data["visit_slot_1"]) . "," . isset($data["visit_slot_2"]) . "," . isset($data["visit_slot_3"]));
//                    echo "num = $num <br/>";
                    $num = $data["visit_slot"];
                    $record_data = $this->db->select("*")->from("records_patient_visit_reserved")->where(array(
                                "id" => $record_id
                            ))->get()->result_array();
                    if ($record_data) {
                        $record_data = $record_data[0];

                        //It should only add new visit if one visit is already confirmed by patient
                        $result = $this->db->select("pat.id")
                                        ->from("clinic_referrals c_ref, referral_patient_info pat")
                                        ->where(array(
                                            "pat.id" => $patient_id,
                                            "c_ref.status" => "Accepted",
                                            "c_ref.active" => 1,
                                            "pat.active" => 1
                                        ))->where("c_ref.id", "pat.referral_id", false)
                                        ->get()->result();

                        if ($result) {
                            $this->db->where(array(
                                "patient_id" => $patient_id
                            ))->update("records_patient_visit", array(
                                "active" => 0
                            ));
                            $this->db->where(array(
                                "patient_id" => $patient_id
                            ))->update("records_patient_visit_reserved", array(
                                "active" => 0
                            ));
                        }

                        //add new visit
                        $insert_data = array(
                            "patient_id" => $record_data["patient_id"],
                            "visit_name" => $record_data["visit_name"],
                            "visit_date" => $record_data["visit_date$num"],
                            "visit_time" => $record_data["visit_start_time$num"],
                            "visit_end_time" => $record_data["visit_end_time$num"],
                            "notify_type" => $record_data["notify_type"],
                            "notify_status" => "Booked by staff",
                            "notify_status_icon" => "green",
                            "visit_confirmed" => "N/A"
                        );
                        $inserted = $this->db->insert("records_patient_visit", $insert_data);


                        $appointment_id = $this->db->insert_id();
                        patient_visit_integration("insert", $record_data["patient_id"], $appointment_id);

                        //for oscar clinics - make scheduling entry for integration
                        //update to Follow up if Initial visit scheduled
                        $this->set_next_visit_follow_up($record_data["patient_id"]);

                        // send fax code start
                        //log_message("error", "code for fax send on call is started");
                        $patient_id = $record_data["patient_id"];
                        $clinic_id = $this->session->userdata("user_id");

                        $fax_data = $this->db
                                        ->select("pat.fname, pat.lname, c_usr.clinic_institution_name, "
                                                . "if(pat.dob, DATE_FORMAT(pat.dob, '(%b %d, %Y)'), '') "
                                                . "as pat_dob,"
                                                . "dr.fax, c_usr.email_address")
                                        ->from("clinic_user_info c_usr, referral_patient_info pat, "
                                                . "referral_physician_info dr, clinic_locations c_loc")
                                        ->where(array(
                                            "pat.active" => 1,
                                            "c_usr.active" => 1,
                                            "c_loc.active" => 1,
                                            "pat.id" => $patient_id,
                                            "c_usr.id" => $clinic_id,
                                            "dr.patient_id" => $patient_id,
                                            "c_loc.clinic_id" => $clinic_id
                                        ))
                                        ->where("pat.location_id", "c_loc.id", false)
                                        ->get()->result();

                        $visit_date = $record_data["visit_date$num"];
                        $visit_time = $record_data["visit_start_time$num"];

                        $visit_date = DateTime::createFromFormat("Y-m-d", $visit_date);
                        $visit_date = $visit_date->format("F d, Y");

                        $visit_time = DateTime::createFromFormat("H:i:s", $visit_time);
                        $visit_time = $visit_time->format("H:i");

                        //log_message("error", "fax info q = " . $this->db->last_query());
                        if ($fax_data) {
                            $replace_stack = array(
                                "###clinic_name###" => $fax_data[0]->clinic_institution_name,
                                "###pat_fname###" => $fax_data[0]->fname,
                                "###pat_lname###" => $fax_data[0]->lname,
                                "###pat_dob###" => $fax_data[0]->pat_dob,
                                "###time1###" => "",
                                "###time2###" => "",
                                "###book_date###" => $visit_date,
                                "###book_time###" => $visit_time,
                                "###book_address###" => $fax_data[0]->email_address
                            );

                            $text2 = "<h2>Referral has been booked</h2>";
                            $additional_replace = array(
                                "###text2###" => $text2
                            );
                            $checklist = array();
                            $file_name = "referral_booking.html";

                            $fax_number = $fax_data[0]->fax;
                            $response = $this->referral_model->send_status_fax2($file_name, $checklist, $replace_stack, $fax_number, "Booking Appintment", $additional_replace);
                            //log_message("error", "booking fax sent to " . $fax_number);
                            //log_message("error", "fax sent code completed");
                        } else {
                            //log_message("error", "Issue fetching fax data");
                            //log_message("error", "sql = > " . $this->db->last_query());
                        }
                        //send fax code is sent
                        //change accepted status to "Booked by Staff"
                        $referral_id = $this->get_referral_id(md5($record_data["patient_id"]));
                        $this->db->where(array(
                            "id" => $referral_id
                        ))->update("clinic_referrals", array(
                            "accepted_status" => "Booked by Staff",
                            "accepted_status_icon" => "green",
                            "accepted_status_date" => $record_data["create_datetime"]
                        ));

                        if ($inserted) {
                            //change status to scheduled
                            $this->db->where("id", $referral_id)->update("clinic_referrals", array(
                                "status" => "Scheduled"
                            ));
                            //log_message("error", "changed status with " . $this->db->last_query());

                            return true;
                        } else {
                            return "Failed to add visit record";
                        }
                    } else {
                        return "Failed to add visit after timeout";
                    }
                } else {

                    $visit_start = date_create_from_format('Y-m-d H:i:s', $data["selected_starttime"]);
                    $visit_end = date_create_from_format('Y-m-d H:i:s', $data["selected_endtime"]);
                    $visit_interval = "30"; //30 minutes
                    $patient_data = $this->db->select("*")->from("referral_patient_info")->where(array(
                                "id" => $patient_id
                            ))->get()->result();
                    if ($patient_data) {

                        //It should only add new visit if one visit is already confirmed by patient
                        $result = $this->db->select("pat.id")
                                        ->from("clinic_referrals c_ref, referral_patient_info pat")
                                        ->where(array(
                                            "pat.id" => $patient_id,
                                            "c_ref.status" => "Accepted",
                                            "c_ref.active" => 1,
                                            "pat.active" => 1
                                        ))->where("c_ref.id", "pat.referral_id", false)
                                        ->get()->result();

                        if ($result) {
                            $this->db->where(array(
                                "patient_id" => $patient_id
                            ))->update("records_patient_visit", array(
                                "active" => 0
                            ));
                            $this->db->where(array(
                                "patient_id" => $patient_id
                            ))->update("records_patient_visit_reserved", array(
                                "active" => 0
                            ));
                        }

                        //add new visit
                        $notify_type = ($patient_data[0]->cell_phone != "") ? "call" : "sms";

                        $insert_data = array(
                            "patient_id" => $patient_id,
                            "visit_name" => $data["visit_name"],
                            "visit_date" => $visit_start->format("Y-m-d"),
                            "visit_time" => $visit_start->format("H:i:s"),
                            "visit_end_time" => $visit_end->format("H:i:s"),
                            "notify_type" => $notify_type,
                            "notify_status" => "Booked by staff",
                            "notify_status_icon" => "green",
                            "visit_confirmed" => "N/A"
                        );
                        $inserted = $this->db->insert("records_patient_visit", $insert_data);
                        $appointment_id = $this->db->insert_id();
                        patient_visit_integration("insert", $patient_id, $appointment_id);

                        //update to Follow up if Initial visit scheduled
                        $this->set_next_visit_follow_up($patient_id);


                        //change accepted status to "Booked by Staff"

                        $this->db->where(array(
                            "id" => $referral_id
                        ))->update("clinic_referrals", array(
                            "accepted_status" => "Booked by Staff",
                            "accepted_status_icon" => "green"
                        ));

                        if ($inserted) {
                            //change status to scheduled
                            $this->db->where("id", $referral_id)->update("clinic_referrals", array(
                                "status" => "Scheduled"
                            ));
                            //log_message("error", "changed status with " . $this->db->last_query());
                            return true;
                        } else {
                            return "Failed to add visit record";
                        }
//                    return $this->create_patient_visit($data["id"], $data["visit_name"], $new_visit_duration);
                    } else {
                        return "Patient details not found";
                    }
                }
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function set_next_visit_follow_up($patient_id) {
        //update to Follow up if Initial visit scheduled
        $this->db->where(array(
            "id" => $patient_id,
            "next_visit" => "Initial consult"
        ))->update("referral_patient_info", array(
            "next_visit" => "Follow up"
        ));
        //log_message("error", "changing next_visit q = " . $this->db->last_query());
    }

    public function create_patient_visit($md5_patient_id, $visit_name, $new_visit_duration) {
        //log_message("error", "inside add patient visit auth with name = $visit_name");
        $this->db->trans_start();
        $patient_id = $this->get_patient_id($md5_patient_id);

        //validate notifications if allowed or not
        $this->db->select('admin.id as clinic_id, c_ref.id as referral_id,'
                . "admin.address, pat.email_id, pat.cell_phone, pat.home_phone, pat.work_phone, "
                . "pat.fname, pat.lname, admin.clinic_institution_name, admin.call_address");
        $this->db->from("clinic_referrals c_ref, referral_patient_info pat, efax_info efax, clinic_user_info admin");
        $this->db->where(array(
            "efax.active" => 1,
            "admin.active" => 1,
            "c_ref.active" => 1,
            "pat.active" => 1,
            "pat.id" => $patient_id
        ));
        $this->db->where("pat.referral_id", "c_ref.id", false);
        $this->db->where("efax.to", "admin.id", false);
        $this->db->where("c_ref.efax_id", "efax.id", false);
        $result = $this->db->get()->result();

        //log_message("error", "Add patient visit => " . json_encode($result));
        //log_message("error", "sql for patient info = " . $this->db->last_query());
        if ($result) {
            $msg_data = $result[0];
            $confirm_visit_key = generate_random_string(120);
//                    $weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
            $clinic_id = $this->session->userdata("user_id");
            $response = $this->assign_slots($clinic_id, $patient_id);
            if ($response["result"] === "error") {
                $response = false;
            } else if ($response["result"] === "success") {
                //log_message("error", "Result on success => " . json_encode($response));
                $allocations = $response["data"];
//                    echo "<br/> ****************** <br/>" . "slots assigned = " . json_encode($allocations) . "<br/><br/>";
//                    exit();
                $start_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["start_time"]);
                $end_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["end_time"]);
                $start_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["start_time"]);
                $end_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["end_time"]);
                $start_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["start_time"]);
                $end_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["end_time"]);

                $call_immediately = true;
                $contact_number = "";
                if ($msg_data->work_phone != "") {
                    $contact_number = $msg_data->work_phone;
                } else if ($msg_data->home_phone != "") {
                    $contact_number = $msg_data->home_phone;
                }
                if ($msg_data->cell_phone != "") {
                    $contact_number = $msg_data->cell_phone;
                    $call_immediately = false;
                }

                if ($call_immediately) {
                    $expire_minutes = "5";
                } else {
//                            $expire_minutes = "60"; // uncomment this line
                    $expire_minutes = "10";  // comment this line
                }

                $visit_datetime = array();
                $visit_datetime[] = array(
                    "date" => $start_time1->format("l M jS"),
                    "time" => $start_time1->format("g:ia")
                );
                $visit_datetime[] = array(
                    "date" => $start_time2->format("l M jS"),
                    "time" => $start_time2->format("g:ia")
                );
                $visit_datetime[] = array(
                    "date" => $start_time3->format("l M jS"),
                    "time" => $start_time3->format("g:ia")
                );
                //insert for temp storage for 60 min sms response
                $visit_expire_time = (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT" . $expire_minutes . "M"))->format("Y-m-d H:i:s");
                //log_message("error", "Expire time scheduled after $expire_minutes minutes to => " 
                // . $visit_expire_time);
                $insert_data = array(
                    "patient_id" => $patient_id,
                    "visit_name" => $visit_name,
                    "visit_date1" => $start_time1->format("Y-m-d"),
                    "visit_start_time1" => $start_time1->format("H:i:s"),
                    "visit_end_time1" => $end_time1->format("H:i:s"),
                    "visit_date2" => $start_time2->format("Y-m-d"),
                    "visit_start_time2" => $start_time2->format("H:i:s"),
                    "visit_end_time2" => $end_time2->format("H:i:s"),
                    "visit_date3" => $start_time3->format("Y-m-d"),
                    "visit_start_time3" => $start_time3->format("H:i:s"),
                    "visit_end_time3" => $end_time3->format("H:i:s"),
                    "visit_expire_time" => $visit_expire_time,
                    "notify_type" => ($call_immediately) ? "call" : "sms",
                    "notify_voice" => 1,
                    "notify_sms" => 1,
                    "notify_email" => 1,
                    //                        "reminder_1h" => ($call_immediately) ? null : (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT1H"))->format("Y-m-d H:i:s"),
                    //                        "reminder_24h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("P1D"))->format("Y-m-d H:i:s"),
                    //                        "reminder_48h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("P2D"))->format("Y-m-d H:i:s"),
                    //                        "reminder_72h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("P3D"))->format("Y-m-d H:i:s"),
                    "reminder_1h" => ($call_immediately) ? null : (new DateTime(date("Y-m-d H:i:s")))
                            ->add(new DateInterval("PT20M"))->format("Y-m-d H:i:s"),
                    "reminder_24h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT40M"))->format("Y-m-d H:i:s"),
                    "reminder_48h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT60M"))->format("Y-m-d H:i:s"),
                    "reminder_72h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT80M"))->format("Y-m-d H:i:s"),
                    "confirm_visit_key" => $confirm_visit_key,
                    "notify_status" => ($call_immediately) ? "Call1" : "SMS",
                    "notify_status_icon" => "green",
                    "visit_confirmed" => "N/A"
                );

                //                    echo "call/sms => " . (($call_immediately) ? "call" : "sms");
                //                    echo "date reserved = " . json_encode($insert_data) . "<br/>";

                $this->db->insert("records_patient_visit_reserved", $insert_data);
                //log_message("error", "inserted for new visit = " . json_encode($insert_data));
                $insert_id = $this->db->insert_id();

                if ($call_immediately) {
                    $post_arr = array(
                        'defaultContactFormName' => $msg_data->fname,
                        "patient_lname" => $msg_data->lname,
                        "defaultContactFormName2" => $visit_name,
                        'defaultContactFormName3' => $msg_data->clinic_institution_name,
                        'defaultContactFormName4' => "ddd",
                        'defaultContactFormName5' => "ttt",
                        'defaultContactFormName6' => $contact_number,
                        'address' => $msg_data->call_address,
                        'clinic_id' => $msg_data->clinic_id,
                        'type' => 'first_call',
                        "patient_id" => $patient_id,
                        "notify_voice" => 1,
                        "notify_sms" => 1,
                        "notify_email" => 1,
                        "reserved_id" => $insert_id
                    );


                    //change accepted status to "Call1"
                    $this->db->where(array(
                        "id" => $msg_data->referral_id
                    ))->update("clinic_referrals", array(
                        "accepted_status" => "Call1",
                        "accepted_status_icon" => "green"
                    ));


                    //log_message("error", "data for start call = " . json_encode($post_arr));
                    //                        //log_message("error", "Call should start now");
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_URL, base_url() . "call_view/call");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_arr));
                    $resp = curl_exec($ch);
                    if (curl_errno($ch)) {
                        //log_message("error", "Call error => " . json_encode(curl_error($ch)));
                        return curl_error($ch);
                    }
                    curl_close($ch);
                    //log_message("error", "<br/> call response = " . $resp . "<br/>");
                    //log_message("error", "Call completed " . json_encode($resp));
                } else {
//                            echo "sending sms";
                    $msg = "Hello <patient name>,\n"
                            . "\n"
                            . "This is an automated appointment booking message from <clinic name>. "
                            . "Please select one of the following dates:\n"
                            . "\n"
                            . "<date1> at <time1> - reply with '1'\n"
                            . "\n"
                            . "<date2> at <time2> - reply with '2'\n"
                            . "\n"
                            . "<date3> at <time3> - reply with '3'\n"
                            . "\n"
                            . "If you would like the clinic to contact you directly, please reply with '0'.\n"
                            . "\n"
                            . "Please note - these dates will be reserved for the next 60 minutes.\n"
                            . "\n"
                            . "Thank-you.";

                    $msg = str_replace("<patient name>", $msg_data->fname, $msg);
                    $msg = str_replace("<date1>", $visit_datetime[0]["date"], $msg);
                    $msg = str_replace("<time1>", $visit_datetime[0]["time"], $msg);
                    $msg = str_replace("<date2>", $visit_datetime[1]["date"], $msg);
                    $msg = str_replace("<time2>", $visit_datetime[1]["time"], $msg);
                    $msg = str_replace("<date3>", $visit_datetime[2]["date"], $msg);
                    $msg = str_replace("<time3>", $visit_datetime[2]["time"], $msg);
                    $msg = str_replace("<clinic name>", $msg_data->clinic_institution_name, $msg);

                    $this->send_sms($msg_data->cell_phone, $msg);

                    //change accepted status to "SMS"
                    $this->db->where(array(
                        "id" => $msg_data->referral_id
                    ))->update("clinic_referrals", array(
                        "accepted_status" => "SMS",
                        "accepted_status_icon" => "green"
                    ));
                }
                $response = true;
            }
        } else {
            $response = "Patient information is incorrect";
        }
        $this->db->trans_complete();
        return $response;
    }

    public function get_visit_allocation_for_manual_visit_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //if physician assigned
                $this->db->select("c_ref.assigned_physician, "
                        . "concat('Dr. ', c_dr.first_name, ' ', c_dr.last_name) as physician_name");
                $this->db->from("clinic_referrals c_ref, referral_patient_info pat, "
                        . "clinic_physician_info c_dr");
                $this->db->where(array(
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "md5(pat.id)" => $data["id"]
                ));
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $this->db->where("c_ref.assigned_physician", "c_dr.id", false);
                $tmp_result = $this->db->get()->result();
                if (!$tmp_result || $tmp_result[0]->assigned_physician == "0") {
                    return array(
                        "result" => "error",
                        "message" => "Patient must be assigned before accepting."
                    );
                }
                $assigned_physician = $tmp_result[0]->assigned_physician;
                $physician_name = $tmp_result[0]->physician_name;


                //send first visit request
                $md5_patient_id = $data["id"];
                $visit_name = "Manual Visit";
                $new_visit_duration = 30;
                $patient_id = $this->get_patient_id($md5_patient_id);
                $clinic_id = $this->session->userdata("user_id");
                $response = $this->assign_slots($clinic_id, $patient_id);
                if ($response["result"] === "error") {
                    return array(
                        "result" => "error",
                        "message" => "Not able to assign visit slot."
                    );
                } else if ($response["result"] === "success") {
                    //reserve for 5 min
                    $allocations = $response["data"];
                    //log_message("error", "Popup allocations = " . json_encode($allocations));

                    $patient_data = $this->db->select("*")->from("referral_patient_info")->where(array(
                                "id" => $patient_id
                            ))->get()->result();

                    if ($patient_data) {
                        $patient_data = $patient_data[0];
                        $call_immediately = true;
                        if ($patient_data->work_phone != "") {
                            $contact_number = $patient_data->work_phone;
                        } else if ($patient_data->home_phone != "") {
                            $contact_number = $patient_data->home_phone;
                        }
                        if ($patient_data->cell_phone != "") {
                            $contact_number = $patient_data->cell_phone;
                            $call_immediately = false;
                        }

                        $start_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["start_time"]);
                        $end_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["end_time"]);
                        $start_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["start_time"]);
                        $end_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["end_time"]);
                        $start_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["start_time"]);
                        $end_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["end_time"]);
                        $expire_minutes = "5"; // 5 minutes
                        $this->db->insert("records_patient_visit_reserved", array(
                            "patient_id" => $patient_id,
                            "visit_name" => "",
                            "visit_date1" => $start_time1->format("Y-m-d"),
                            "visit_start_time1" => $start_time1->format("H:i:s"),
                            "visit_end_time1" => $end_time1->format("H:i:s"),
                            "visit_date2" => $start_time2->format("Y-m-d"),
                            "visit_start_time2" => $start_time2->format("H:i:s"),
                            "visit_end_time2" => $end_time2->format("H:i:s"),
                            "visit_date3" => $start_time3->format("Y-m-d"),
                            "visit_start_time3" => $start_time3->format("H:i:s"),
                            "visit_end_time3" => $end_time3->format("H:i:s"),
                            "visit_expire_time" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT" . $expire_minutes . "M"))->format("Y-m-d H:i:s"),
                            "notify_type" => ($call_immediately) ? "call" : "sms",
                            "reminder_1h" => ($call_immediately) ? null : (new DateTime(date("Y-m-d H:i:s")))
                                            ->add(new DateInterval("PT5M"))->format("Y-m-d H:i:s"),
                            "reminder_24h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT10M"))->format("Y-m-d H:i:s"),
                            "reminder_48h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT15M"))->format("Y-m-d H:i:s"),
                            "reminder_72h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT20M"))->format("Y-m-d H:i:s"),
                            "confirm_visit_key" => "",
                            "notify_status" => ($call_immediately) ? "Call1" : "SMS",
                            "notify_status_icon" => "green",
                            "visit_confirmed" => "Booked by Staff",
                            "active" => 0
                        ));

                        //response format for datetime
                        $start_datetime1 = $start_time1->format('l, F jS') . " at " . $start_time1->format('g:iA');
                        $start_datetime2 = $start_time2->format('l, F jS') . " at " . $start_time2->format('g:iA');
                        $start_datetime3 = $start_time3->format('l, F jS') . " at " . $start_time3->format('g:iA');
                        $record_id = $this->db->insert_id();

                        return array(
                            "result" => "success",
                            "data" => array(
                                "record_id" => $record_id,
                                "allocations" => array(
                                    "slot1" => $start_datetime1,
                                    "slot2" => $start_datetime2,
                                    "slot3" => $start_datetime3
                                ),
                                "physician_name" => $physician_name
                            )
                        );
                    } else {
                        return array(
                            "result" => "error",
                            "message" => "Patient details not found"
                        );
                    }
                }
            }
        }
    }

    public function move_from_accepted_to_scheduled($patient_id, $clinic_id = "") {
        //change patient referral status to scheduled
        $this->db->select("referral_id");
        $this->db->from("referral_patient_info");
        $this->db->where(array("active" => 1, "id" => $patient_id));
        $result = $this->db->get()->result();
        $referral_id = $result[0]->referral_id;
        //log_message("error", " STEP Move referral id for update => " . $referral_id);

        $this->db->where(array(
            "id" => $referral_id,
            "active" => 1
        ));
        $this->db->update("clinic_referrals", array(
            "status" => "Scheduled",
            "scheduled_datetime" => date("Y-m-d H:i:s")
        ));
        //log_message("error", "STEP at status send " . $this->db->last_query());
        //send status fax
        $this->db->select("c_usr.clinic_institution_name, date_format(c_ref.create_datetime, '%M %D') as referral_received, dr.fax, c_ref.referral_code");
        $this->db->from("clinic_user_info c_usr, efax_info efax, clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
        $this->db->where(array(
            "pat.id" => $patient_id,
            "efax.active" => 1,
            "c_usr.active" => 1,
            "c_ref.active" => 1,
            "pat.active" => 1,
            "dr.active" => 1,
            "dr.patient_id" => $patient_id
        ));
        $this->db->where("efax.to", "c_usr.id", false);
        $this->db->where("efax.id", "c_ref.efax_id", false);
        $this->db->where("pat.referral_id", "c_ref.id", false);
        $result = $this->db->get()->result()[0];
        //log_message("error", "STEP q for fax = " . $this->db->last_query());

        $file_name = "referral_scheduled.html";
        $replace_stack = array(
            "###clinic_name###" => $result->clinic_institution_name,
            "###referral_code###" => $result->referral_code,
            "###time1###" => $result->referral_received,
            "###time2###" => date("F jS")
        );
        $fax_number = $result->fax;
        //log_message("error", "sending fax");
//        $response = $this->send_status_fax($file_name, array(), $replace_stack, $fax_number, "Scheduled Referral", array(), 60, $clinic_id);
        //log_message("error", "Last query = " . $this->db->last_query());
    }

    public function confirm_visit_key_model() {
        $key = $this->uri->segment(3);
        // echo $key;
        $this->db->where(array(
            "confirm_visit_key" => $key,
            "active" => 1
        ));
        $updated = $this->db->update("records_patient_visit", array(
            "visit_confirmed" => "Confirmed"
        ));
        if ($updated) {
            echo "<h1> Your visit has been successfully confirmed. Thanks</h1>";
        } else {
            echo "<h1> Visit confirmation failed. May be visit confirmed earlier or something has gone wrong";
        }
    }

    public function cancel_patient_visit_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Patient Visit', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->trans_start();
                $patient_visit_id = $this->get_decrypted_id($data["target"], "records_patient_visit");
                //authenticate patient visit with referral
                $this->db->select("pat.cell_phone, pat.email_id, r_cv.notify_sms, r_cv.notify_email");
                $this->db->from("records_patient_visit r_cv, clinic_referrals c_ref, `referral_patient_info` `pat`");
                $this->db->where(array(
                    "c_ref.active" => 1,
                    "r_cv.active" => 1,
                    "pat.active" => 1,
                    "r_cv.id" => $patient_visit_id
                ));
                $this->db->where("r_cv.patient_id", "pat.id", false);
                $result = $this->db->get()->result();
                if ($result) {
                    $this->db->where(array(
                        "id" => $patient_visit_id
                    ));
                    $this->db->update("records_patient_visit", array(
                        "active" => 0
                    ));
                    $this->db->trans_complete();
                    return true;
                } else
                    return "You are not authorized for such Operation";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function update_patient_visit_model() {
//        //log_message("error", "on update patient");
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Patient Visit', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //if selected a slot
                $target_id = $data["target"];
                $record_id = $data["record_id"];
                $patient_id = $this->get_patient_id($data["id"]);
                $referral_id = $this->get_referral_id(md5($patient_id));

                if (isset($data["visit_slot"])) {

//                    //log_message("error", "on update patient visit slot");
//                    echo "num = $num <br/>";
                    $num = $data["visit_slot"];
                    $record_data = $this->db->select("*")
                                    ->from("records_patient_visit_reserved")
                                    ->where(array(
                                        "id" => $record_id,
                                        "active" => "0"
                                    ))->get()->result_array();

//                    //log_message("error", "data to copy from = " . json_encode($record_data));
                    if ($record_data) {
                        $record_data = $record_data[0];
                        //add new visit
                        $update_data = array(
                            "visit_date" => $record_data["visit_date$num"],
                            "visit_time" => $record_data["visit_start_time$num"],
                            "visit_end_time" => $record_data["visit_end_time$num"],
                            "notify_type" => $record_data["notify_type"],
                            "notify_status" => "Booked by staff",
                            "notify_status_icon" => "green",
                            "visit_confirmed" => "N/A"
                        );
                        $updated = $this->db->where("md5(id)", $target_id)
                                ->update("records_patient_visit", $update_data);

//                        //log_message("error", "updated as " . $this->db->last_query());
                        //change accepted status to "Booked by Staff"
                        $referral_id = $this->get_referral_id(md5($record_data["patient_id"]));
                        $this->db->where(array(
                            "id" => $referral_id
                        ))->update("clinic_referrals", array(
                            "accepted_status" => "Booked by Staff",
                            "accepted_status_icon" => "green"
                        ));

                        if ($updated) {
                            return true;
                        } else {
                            return "Failed to update visit record";
                        }
                    } else {
                        return "Failed to update visit after timeout";
                    }
                } else {

                    $visit_date = date_create_from_format('j F Y H:i', $data["visit_date"] . " " . $data["visit_time"]);
                    $visit_interval = "30"; //30 minutes
                    $patient_data = $this->db->select("*")->from("referral_patient_info")->where(array(
                                "id" => $patient_id
                            ))->get()->result();
                    if ($patient_data) {
                        //add new visit
                        $notify_type = ($patient_data[0]->cell_phone != "") ? "call" : "sms";

                        $update_data = array(
                            "patient_id" => $patient_id,
                            "visit_name" => $data["visit_name"],
                            "visit_date" => $visit_date->format("Y-m-d"),
                            "visit_time" => $data["visit_time"],
                            "visit_end_time" => $visit_date->add(new DateInterval("PT" . $visit_interval . "M"))->format("H:i:s"),
                            "notify_type" => $notify_type,
                            "notify_status" => "Booked by staff",
                            "notify_status_icon" => "green",
                            "visit_confirmed" => "N/A"
                        );
                        $inserted = $this->db->where("md5(id)", $target_id)
                                ->update("records_patient_visit", $update_data);

                        //change accepted status to "Booked by Staff"
                        $this->db->where(array(
                            "id" => $referral_id
                        ))->update("clinic_referrals", array(
                            "accepted_status" => "Booked by Staff",
                            "accepted_status_icon" => "green"
                        ));

                        if ($inserted) {
                            return true;
                        } else {
                            return "Failed to update visit record";
                        }
//                    return $this->create_patient_visit($data["id"], $data["visit_name"], $new_visit_duration);
                    } else {
                        return "Patient details not found";
                    }
                }
            }
            return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function ssp_health_records_model() {
        $referral_id = $this->uri->segment(3);
        $table = "health_records_dash";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'record_type', 'dt' => 0),
            array('db' => 'description', 'dt' => 1),
            array('db' => 'create_datetime', 'dt' => 2),
            array('db' => 'id', 'dt' => 3)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null
                        , "clinic_admin=" . $this->session->userdata("user_id") .
                        " and referral_id='" . $referral_id . "'")
        );
    }

    public function ssp_admin_notes_model() {
        $patient_id = $this->uri->segment(3);
        $table = "admin_notes_dash";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'note_type', 'dt' => 0),
            array('db' => 'description', 'dt' => 1),
            array('db' => 'create_datetime', 'dt' => 2),
            array('db' => 'id', 'dt' => 3)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null
                        , "clinic_admin=" . $this->session->userdata("user_id") .
                        " and patient_id='" . $patient_id . "'")
        );
    }

    public function ssp_patient_visits_model() {
        $patient_id = $this->uri->segment(3);
        $table = ($this->session->userdata("user_id") === "7") ?
                "patient_visit_dash" : "view_patient_visit_emr";
        //log_message("error", "patient visit = " . $table);
        $primaryKey = "id";
        $columns = array(
            array('db' => 'visit_name', 'dt' => 0),
            array('db' => 'create_datetime', 'dt' => 1),
            array('db' => 'notify_status', 'dt' => 2),
            array('db' => 'visit_confirmed', 'dt' => 3),
            array('db' => 'notify_status_icon', 'dt' => 4),
            array('db' => 'id', 'dt' => 5)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null
                        , "clinic_admin=" . $this->session->userdata("user_id") .
                        " and patient_id='" . $patient_id . "'")
        );
    }

    public function get_health_record_info_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('target', 'Health Record', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->select("r_cn.record_type, r_cn.description, r_cn.record_file");
                $this->db->from("records_clinic_notes r_cn, clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(array(
                    "md5(pat.id)" => $data["id"],
                    "md5(r_cn.id)" => $data["target"],
                    "pat.active" => 1,
                    "r_cn.active" => 1,
                    "c_ref.active" => 1
                ));
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $result = $this->db->get()->result();
                //log_message("error", "ref health record sql = " . $this->db->last_query());
                if ($result)
                    return $result;
                else
                    return "No Data Found";
            }
            return "Unauthorized Access Attempt";
        } else
            return validation_errors();
    }

    public function get_admin_notes_info_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Admin Note', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->select("r_an.note_type, r_an.description");
                $this->db->from("records_admin_notes r_an, clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(
                        array(
                            "md5(pat.id)" => $data["id"],
                            "md5(r_an.id)" => $data["target"]
                        )
                );
                $this->db->where("r_an.patient_id", "pat.id", false);
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $result = $this->db->get()->result();
                if ($result)
                    return $result;
                else
                    return "No Data Found";
            }
            return "Unauthorized Access Attempt";
        } else
            return validation_errors();
    }

    public function get_patient_visit_info_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Patient Visit', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->select("md5(r_pv.id) as id, r_pv.visit_name, r_pv.visit_date, " .
                        "r_pv.visit_time, r_pv.notify_sms, r_pv.notify_email");
                $this->db->from("records_patient_visit r_pv , clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(
                        array(
                            "md5(pat.id)" => $data["id"],
                            "md5(r_pv.id)" => $data["target"]
                        )
                );
                $this->db->where("r_pv.patient_id", "pat.id", false);
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $result = $this->db->get()->result();
                //log_message("error", "false q = " . $this->db->last_query());
                if ($result)
                    return $result;
                else
                    return "No Data Found";
            }
            return "Unauthorized Access Attempt";
        } else
            return validation_errors();
    }

    public function get_file_model() {
        $uri = $this->uri;
        echo json_encode($uri);
        return;
//        
//        $file = 'my_path/' . $file_name;
//        if (file_exists($file)) { // check the file is existing 
//            header('Content-Type: ' . get_mime_by_extension($file));
//            readfile($file);
//        } else
//            show_404();
    }

    public function update_checklist_item_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Checklist Item', 'required');
        $this->form_validation->set_rules('param', 'Checklist Item Status', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->where(
                        array(
                            "md5(patient_id)" => $data["id"],
                            "md5(id)" => $data["target"],
                            "active" => 1
                        )
                );
                $this->db->update("referral_checklist", array(
                    "attached" => $data["param"]
                        )
                );

                //if all checked, change status to ""

                return true;
            } else {
                return "Unauthorized Access Attempt";
            }
        } else
            return validation_errors();
    }

    //*******************************
    // Private functions
    //*******************************
    private function get_patient_id($md5_id) {
        $this->db->select("id");
        $this->db->from("referral_patient_info");
        $this->db->where(
                array(
                    "active" => 1,
                    "md5(id)" => $md5_id
                )
        );
        $result = $this->db->get()->result();
        if ($result)
            return $result[0]->id;
        else
            return false;
    }

    private function get_referral_id($md5_patient_id) {
        $this->db->select("referral_id");
        $this->db->from("referral_patient_info");
        $this->db->where(array(
            "active" => 1,
            "md5(id)" => $md5_patient_id
                )
        );
        $result = $this->db->get()->result();
        $referral_id = $result[0]->referral_id;
        return $referral_id;
    }

    private function check_physician($md5_id) {
        $this->db->select("id");
        $this->db->from("clinic_physician_info");
        $this->db->where(
                array(
                    "active" => 1,
                    "clinic_id" => $this->session->userdata("user_id"),
                    "md5(id)" => $md5_id
                )
        );
        $result = $this->db->get()->result();
        if ($result)
            return $result[0]->id;
        else
            return false;
    }

    private function check_authentication($md5_id) {
        $this->db->select("pat.id");
        $this->db->from("clinic_referrals c_ref, efax_info efax, referral_patient_info pat");
        $this->db->where(
                array(
                    "c_ref.active" => 1,
                    "efax.active" => 1,
                    "pat.active" => 1,
                    "efax.to" => $this->session->userdata("user_id"),
                    "md5(pat.id)" => $md5_id
                )
        );
        $this->db->where("c_ref.efax_id", "efax.id", false);
        $this->db->where("c_ref.id", "pat.referral_id", false);
        $result = $this->db->get()->result();
//        //log_message("error", "ref auth sql = " . $this->db->last_query());
        return ($result) ? true : false;
    }

    private function generate_random_string($length = 32) {
        $timestamp = time();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $timestamp . "_" . $randomString;
    }

    private function get_decrypted_id($md5_id, $table_name) {
        $this->db->select("id");
        $this->db->from($table_name);
        $this->db->where(array("md5(id)" => $md5_id));
        $result = $this->db->get()->result();
        //log_message("error", "get decrypted sql = " . $this->db->last_query());
        return ($result) ? $result[0]->id : 0;
    }

    public function send_sms($cell_phone_number, $msg) {
        //us country code automation
        $cell_phone_number = "+1" . $cell_phone_number;
//        $ac_sid = "";
//        $auth_token = "";
//        $twilio_number = "+"; //(365) 800-0973

        $ac_sid = get_twilio_sid();
        $auth_token = get_twilio_token();
        $twilio_number = $this->config->item("TWILIO_PHONE_NUMBER");

        $msgarr = array(
            'To' => $cell_phone_number,
            'From' => $twilio_number,
            'Body' => $msg
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/" . $ac_sid .
                "/Messages.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $ac_sid . ":" . $auth_token);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($msgarr));
        $resp = curl_exec($ch);
        if (curl_errno($ch)) {
            return array(
                'status' => 'error',
                'content' => curl_error($ch)
            );
        }
        curl_close($ch);
        //log_message("error", "sms sent to " . $cell_phone_number);
        //log_message("error", json_encode($resp));
        return true;
    }

    private function send_fax($fax_num, $fax_text_content, $file, $reason, $clinic_id = "") {
        if ($clinic_id == "") {
            $clinic_id = $this->session->userdata("user_id");
        }
        if (strlen($fax_num) == 10) {
            $fax_num = "1" . $fax_num;
        }
        //log_message("error", "missing item request to fax = $fax_num");
        $faxnumber = $fax_num; //"16474981226"; 
        $cpsubject = $fax_text_content;
        $cpcomments = $fax_text_content;
//        $files = array($data["txt_form_name"]);

        $this->db->select("id, srfax_number, srfax_email, srfax_pass, srfax_account_num");
        $this->db->from("clinic_user_info");
        $this->db->where(array(
            "active" => 1,
            "id" => $clinic_id
        ));
        $clinic = $this->db->get()->result()[0];
        if ($clinic) {

            $clinic_id = $clinic->id;
            $access_id = $clinic->srfax_account_num;
            $access_pwd = $clinic->srfax_pass;
            $caller_id = $clinic->srfax_number;
            $sender_mail = $clinic->srfax_email;

            $postdata = array(
                'action' => 'Queue_Fax',
                'access_id' => $access_id,
                'access_pwd' => $access_pwd,
                'sCallerID' => $caller_id,
                'sSenderEmail' => $sender_mail,
                'sFaxType' => 'SINGLE',
                'sToFaxNumber' => $faxnumber,
                'sCoverPage' => 'Basic',
                'sCPSubject' => $cpsubject,
                'sCPComments' => $cpcomments,
                'sFileName_1' => "demo.pdf",
                'sFileContent_1' => base64_encode(file_get_contents($file))
                    //            'sFileContent_1' => base64_encode(file_get_contents("uploads/demo.pdf"))
            );

            $curlDefaults = array(
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_URL => 'https://www.srfax.com/SRF_SecWebSvc.php',
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => TRUE,
                //            CURLOPT_SSL_VEFIFYHOST => 2,
                CURLOPT_POSTFIELDS => http_build_query($postdata)
            );
            $ch = curl_init();
            curl_setopt_array($ch, $curlDefaults);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                //log_message("error", "Fax Error – " . curl_error($ch));
                return false;
            } else {
                //log_message("error", "Fax Result:" . json_encode($result));
                add_fax_count($faxnumber, $clinic->srfax_number, $clinic->id, $reason, "Admin");
                return true;
            }
        } else {
            echo "clinic id = " . $clinic_id;
        }
    }

    private function filter_reserved($unfiltered_visits, $next_day) {
//        echo "filtering now <br/>";
        $filtered = array();
        foreach ($unfiltered_visits as $key => $value) {
//            echo "loop ===> " . json_encode($value) . "<br/>";
            if (substr($value->visit_start_time1, 0, 10) >= $next_day) {
                $filtered[] = array(
                    "visit_start_time" => $value->visit_start_time1,
                    "visit_end_time" => $value->visit_end_time1
                );
            }
            if (substr($value->visit_start_time2, 0, 10) >= $next_day) {
                $filtered[] = array(
                    "visit_start_time" => $value->visit_start_time2,
                    "visit_end_time" => $value->visit_end_time2
                );
            }
            if (substr($value->visit_start_time3, 0, 10) >= $next_day) {
                $filtered[] = array(
                    "visit_start_time" => $value->visit_start_time3,
                    "visit_end_time" => $value->visit_end_time3
                );
            }
        }
//        echo "after filtering <br/>";
//        echo json_encode($filtered) . "<br/>";
        return $filtered;
    }

    public function get_patient_visit_calendar_month_view_model() {
        $data = $this->input->post();
        $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
        $clinic_id = $this->session->userdata("user_id");
        $start_date = $data["target"];
        $end_date = $data["param"];
        $cur_date = date("Y-m-d");

        //set start date to current date if current date is more than start date
//        if ($start_date < $cur_date) {
//            $start_date = $cur_date;
//        }
        //return if end date is less than current date
        if ($end_date < $cur_date || $start_date > $end_date) {
            //log_message("error", "error of dates");
            return array(
                "result" => "success",
                "data" => array()
            );
        }
        $start_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $start_date . date(" H:i:s"));
        $end_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $end_date . date(" H:i:s"));

        //get duration based on patient visit time fixed as per next visit settings
        $new_visit_duration = 30; // default

        $next_visit_duration_data = $this->db->select("c_vt.visit_duration")
                        ->from("referral_patient_info pat, clinic_visit_timings c_vt")
                        ->where(array(
                            "c_vt.active" => 1,
                            "pat.active" => 1,
                            "pat.id" => $patient_id,
                            "c_vt.clinic_id" => $clinic_id
                        ))->where("pat.next_visit", "c_vt.visit_type", false)
                        ->get()->result();
        //log_message("error", "next visit check data => " . $this->db->last_query());
        if ($next_visit_duration_data) {
            $new_visit_duration = intval($next_visit_duration_data[0]->visit_duration);
            //get physician assigned to patient
            $assigned = $this->db->select("c_ref.assigned_physician")
                            ->from("referral_patient_info pat, clinic_referrals c_ref")
                            ->where(array(
                                "pat.active" => 1,
                                "c_ref.active" => 1,
                                "pat.id" => $patient_id,
                                "c_ref.assigned_physician <>" => 0
                            ))
                            ->where("pat.referral_id", "c_ref.id", false)
                            ->get()->result();

            if ($assigned) {
                $assigned_physician = $assigned[0]->assigned_physician;
                $cur_date_object = $start_date_object;
                $cur_date = $cur_date_object->format("Y-m-d");

                $visits_booked = $this->db
                                ->select("concat(r_pv.visit_date, ' ', r_pv.visit_time) "
                                        . "as visit_start_time, "
                                        . "concat(r_pv.visit_date, ' ', r_pv.visit_end_time) "
                                        . "as visit_end_time")
                                ->from("records_patient_visit r_pv, referral_patient_info pat, "
                                        . "clinic_referrals c_ref")
                                ->where(array(
                                    "r_pv.active" => 1,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "r_pv.visit_date >= " => $cur_date_object->format('Y-m-d'),
                                    "c_ref.assigned_physician" => $assigned_physician
                                ))
                                ->where("r_pv.patient_id", "pat.id", false)
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->order_by("1")->get()->result();

                //log_message("error", "visits booked = " . json_encode($visits_booked));

                $visits_reserved = $this->db
                                ->select(
                                        "concat(r_pvr.visit_date1, ' ', r_pvr.visit_start_time1) "
                                        . "as visit_start_time1, "
                                        . "concat(r_pvr.visit_date1, ' ', r_pvr.visit_end_time1) "
                                        . "as visit_end_time1,"
                                        . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_start_time2) "
                                        . "as visit_start_time2, "
                                        . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_end_time2) "
                                        . "as visit_end_time2,"
                                        . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_start_time3) "
                                        . "as visit_start_time3, "
                                        . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_end_time3) "
                                        . "as visit_end_time3")
                                ->from("records_patient_visit_reserved r_pvr, referral_patient_info pat, "
                                        . "clinic_referrals c_ref")
                                ->where(array(
                                    "r_pvr.active" => 1,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "c_ref.assigned_physician" => $assigned_physician,
                                    "r_pvr.`visit_expire_time` > " => date("Y-m-d H:i:s")
                                ))->group_start()
                                ->where("r_pvr.visit_date1 >= ", $cur_date)
                                ->or_where("r_pvr.visit_date2 >= ", $cur_date)
                                ->or_where("r_pvr.visit_date3 >= ", $cur_date)
                                ->group_end()
                                ->where("r_pvr.patient_id", "pat.id", false)
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->order_by("1")->get()->result();


                $visits_reserved = $this->filter_reserved($visits_reserved, $cur_date);

                $all_visits = array_merge($visits_booked, $visits_reserved);
                //sort by date
                $all_visits = json_decode(json_encode($all_visits));
                usort($all_visits, array($this, "sort_visits_by_date"));

                $visits_booked = $all_visits;
                $available_visit_slots = array();

                $day = $start_date_object;
                $counter = 0;
                $slots_counter = array();
                //log_message("error", "starting while with day = " . $day->format("Y-m-d"));
                do {
                    //log_message("error", "day = " . $day->format("Y-m-d"));
                    $slots_counter[$day->format("Y-m-d")] = 0;
                    if ($day->format("Y-m-d") > date("Y-m-d")) {

                        $scheduling_day = $this->check_day_availability($day, $assigned_physician);

                        if ($scheduling_day["available"]) {
//                    echo "is available <br/>";
                            $day_start_time = $scheduling_day["day_start_time"];
                            $day_end_time = $scheduling_day["day_end_time"];
                            $blocks = $scheduling_day["blocks"];

                            //log_message("error", "blocks = " . json_encode($blocks) . " will be added to visits");
                            $processed_keys = 0;
                            $time1 = $scheduling_day["day"] . " " . $day_start_time;

                            $visits_booked_for_day = $this->get_visit_booked_for_day($day, $visits_booked, $blocks);

                            //log_message("error", "visits booked = " . json_encode($visits_booked_for_day));

                            for ($key = 0; $key < sizeof($visits_booked_for_day); $key++) {
                                //log_message("error", "inside for loop <br/> key = " . $key . " size = " .
                                //  sizeof($visits_booked_for_day));

                                $processed_keys = $key;
                                $visit_start_time = null;
                                $visit_end_time = null;
                                //log_message("error", "end = " . json_encode(end($visits_booked_for_day)));
                                if (is_object($visits_booked_for_day[$key])) {
                                    $visit_start_time = $visits_booked_for_day[$key]->visit_start_time;
                                    $visit_end_time = $visits_booked_for_day[$key]->visit_start_time;
                                } else {
                                    $visit_start_time = $visits_booked_for_day[$key]["visit_start_time"];
                                    $visit_end_time = $visits_booked_for_day[$key]["visit_end_time"];
                                }
                                if (is_object(end($visits_booked_for_day))) {
                                    $last_visit_end_time = end($visits_booked_for_day)->visit_end_time;
                                } else {
                                    $last_visit_end_time = end($visits_booked_for_day)["visit_end_time"];
                                }

                                $time2 = $visit_start_time;

//                            echo "################ check between " . $time1 . " to " . $time2 . " <br/>";
                                $counts = $this->count_time_slot_available($time1, $time2, $new_visit_duration);
                                $slots_counter[$day->format("Y-m-d")] += $counts;
                                $time1 = $visit_end_time;
                                //log_message("error", "day " . $day->format("Y-m-d") . " => " . " added $counts "
                                //  . "between $time1 and $time2");
                            }
//                        echo "visits_booked_for_day has no visits <br/>";
                            $time2 = $scheduling_day["day"] . " " . $day_end_time;

                            $counts = $this->count_time_slot_available($time1, $time2, $new_visit_duration);
                            $slots_counter[$day->format("Y-m-d")] += $counts;
                            //log_message("error", "day " . $day->format("Y-m-d") . " => " . " added $counts "
                            //  . "between $time1 and $time2");
                        } else {
                            //log_message("error", "day is not available <br/>");
                        }
                    }
                    $day = $day->modify('+1 day');
                    //log_message("error", "moving to " . $day->format("Y-m-d") . "<br/>");
                    $counter++;

                    //log_message("error", "day = " . json_encode($day) . " and " . json_encode($end_date_object));
                } while ($day <= $end_date_object && $counter < 100);


//            echo "<br/> ============================================================= <br/>";
                return array(
                    "result" => "success",
                    "data" => $slots_counter
                );
            } else {
                //log_message("error", "Assign physician to patient before scheduling");
                return array(
                    "result" => "error",
                    "message" => "Assign physician to patient before scheduling"
                );
            }
        } else {
            //log_message("error", "Next visit data not found");
            return array(
                "result" => "error",
                "message" => "Next visit data not found"
            );
        }
    }

    public function get_patient_visit_calendar_week_view_model() {
        $data = $this->input->post();
        $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
        $clinic_id = $this->session->userdata("user_id");
        $start_date = $data["target"];
        $end_date = $data["param"];
        $cur_date = date("Y-m-d");

        //set start date to current date if current date is more than start date
//        if ($start_date < $cur_date) {
//            $start_date = $cur_date;
//        }
        //return if end date is less than current date
        if ($end_date < $cur_date || $start_date > $end_date) {
            //log_message("error", "error of dates with $start_date and $end_date");
            return array(
                "result" => "success",
                "data" => array()
            );
        }
        $start_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $start_date . date(" H:i:s"));
        $end_date_object = DateTime::createFromFormat('Y-m-d H:i:s', $end_date . date(" H:i:s"));

        //get duration based on patient visit time fixed as per next visit settings
        $new_visit_duration = 30; // default

        $next_visit_duration_data = $this->db->select("c_vt.visit_duration")
                        ->from("referral_patient_info pat, clinic_visit_timings c_vt")
                        ->where(array(
                            "c_vt.active" => 1,
                            "pat.active" => 1,
                            "pat.id" => $patient_id,
                            "c_vt.clinic_id" => $clinic_id
                        ))->where("pat.next_visit", "c_vt.visit_type", false)
                        ->get()->result();
        //log_message("error", "next visit check data => " . $this->db->last_query());
        if ($next_visit_duration_data) {
            $new_visit_duration = intval($next_visit_duration_data[0]->visit_duration);
            //get physician assigned to patient
            $assigned = $this->db->select("c_ref.assigned_physician")
                            ->from("referral_patient_info pat, clinic_referrals c_ref")
                            ->where(array(
                                "pat.active" => 1,
                                "c_ref.active" => 1,
                                "pat.id" => $patient_id,
                                "c_ref.assigned_physician <>" => 0
                            ))
                            ->where("pat.referral_id", "c_ref.id", false)
                            ->get()->result();

            if ($assigned) {
                $assigned_physician = $assigned[0]->assigned_physician;
                $cur_date_object = $start_date_object;
                $cur_date = $cur_date_object->format("Y-m-d");

                $visits_booked = $this->db
                                ->select("concat(r_pv.visit_date, ' ', r_pv.visit_time) "
                                        . "as visit_start_time, "
                                        . "concat(r_pv.visit_date, ' ', r_pv.visit_end_time) "
                                        . "as visit_end_time")
                                ->from("records_patient_visit r_pv, referral_patient_info pat, "
                                        . "clinic_referrals c_ref")
                                ->where(array(
                                    "r_pv.active" => 1,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "r_pv.visit_date >= " => $cur_date_object->format('Y-m-d'),
                                    "c_ref.assigned_physician" => $assigned_physician
                                ))
                                ->where("r_pv.patient_id", "pat.id", false)
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->order_by("1")->get()->result();

                //log_message("error", "visits booked = " . json_encode($visits_booked));

                $visits_reserved = $this->db
                                ->select(
                                        "concat(r_pvr.visit_date1, ' ', r_pvr.visit_start_time1) "
                                        . "as visit_start_time1, "
                                        . "concat(r_pvr.visit_date1, ' ', r_pvr.visit_end_time1) "
                                        . "as visit_end_time1,"
                                        . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_start_time2) "
                                        . "as visit_start_time2, "
                                        . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_end_time2) "
                                        . "as visit_end_time2,"
                                        . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_start_time3) "
                                        . "as visit_start_time3, "
                                        . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_end_time3) "
                                        . "as visit_end_time3")
                                ->from("records_patient_visit_reserved r_pvr, referral_patient_info pat, "
                                        . "clinic_referrals c_ref")
                                ->where(array(
                                    "r_pvr.active" => 1,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "c_ref.assigned_physician" => $assigned_physician,
                                    "r_pvr.`visit_expire_time` > " => date("Y-m-d H:i:s")
                                ))->group_start()
                                ->where("r_pvr.visit_date1 >= ", $cur_date)
                                ->or_where("r_pvr.visit_date2 >= ", $cur_date)
                                ->or_where("r_pvr.visit_date3 >= ", $cur_date)
                                ->group_end()
                                ->where("r_pvr.patient_id", "pat.id", false)
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->order_by("1")->get()->result();


                $visits_reserved = $this->filter_reserved($visits_reserved, $cur_date);

                $all_visits = array_merge($visits_booked, $visits_reserved);
                //sort by date
                $all_visits = json_decode(json_encode($all_visits));
                usort($all_visits, array($this, "sort_visits_by_date"));

                $visits_booked = $all_visits;
                $available_visit_slots = array();

                $day = $start_date_object;
                $counter = 0;
                $slots_timings = array();
                //log_message("error", "starting while with day = " . $day->format("Y-m-d"));
                //log_message("error", "visit duration = " . $new_visit_duration);

                do {
                    //log_message("error", "day = " . $day->format("Y-m-d"));
                    if ($day->format("Y-m-d") > date("Y-m-d")) {

                        $scheduling_day = $this->check_day_availability($day, $assigned_physician);

                        if ($scheduling_day["available"]) {
//                    echo "is available <br/>";
                            $day_start_time = $scheduling_day["day_start_time"];
                            $day_end_time = $scheduling_day["day_end_time"];
                            $blocks = $scheduling_day["blocks"];

//                    echo "day times = $day_start_time and $day_end_time <br/>";

                            $processed_keys = 0;
                            $time1 = $scheduling_day["day"] . " " . $day_start_time;
                            //log_message("error", "visits booked = " . json_encode($visits_booked));
                            //log_message("error", "blocks = " . json_encode($blocks));
                            $visits_booked_for_day = $this->get_visit_booked_for_day($day, $visits_booked, $blocks);
                            //log_message("error", "just before for loop : visit booked for day = " . json_encode($visits_booked_for_day));
                            for ($key = 0; $key < sizeof($visits_booked_for_day); $key++) {
                                //log_message("error", "inside for loop <br/>");
                                $processed_keys = $key;
                                $week_visit_start_time = $visits_booked_for_day[$key]["visit_start_time"];
                                $week_visit_end_time = $visits_booked_for_day[$key]["visit_end_time"];
                                //log_message("error", "processing key = " .
                                // json_encode($visits_booked_for_day[$key]));

                                if (is_object(end($visits_booked_for_day))) {
                                    $last_visit_end_time = end($visits_booked_for_day)->visit_end_time;
                                } else {
                                    $last_visit_end_time = end($visits_booked_for_day)["visit_end_time"];
                                }
                                //log_message("error", "set time222222222222 => " . $week_visit_start_time);
                                $time2 = $week_visit_start_time;
                                if ($time1 >= $time2) {
                                    //log_message("error", "EROOOOOOOOOOOOOOOOOO => time 1 >= time 2 = $time1");
//                                    continue;
                                }

                                //log_message("error", "################ check between " . $time1 . " to " . $time2 . " <br/>");
                                $timings = $this->get_time_slot_available($time1, $time2, $new_visit_duration);
                                foreach ($timings as $key1 => $timing) {
                                    $slots_timings[] = $timing;
                                }
                                //log_message("error", "day " . $day->format("Y-m-d") . " => " . " added "
                                // . "between $time1 and $time2 => " . json_encode($timings));
                                //log_message("error", "set time11111111111 => " . $week_visit_end_time);
                                $time1 = $week_visit_end_time;
                            }
//                        echo "visits_booked_for_day has no visits <br/>";
                            $time2 = $scheduling_day["day"] . " " . $day_end_time;

                            $timings = $this->get_time_slot_available($time1, $time2, $new_visit_duration);
                            foreach ($timings as $key2 => $timing) {
                                $slots_timings[] = $timing;
                            }
                            //log_message("error", "day " . $day->format("Y-m-d") . " => " . " added  "
                            // . "between $time1 and $time2");
                        } else {
                            //log_message("error", "day is not available <br/>");
                        }
                    }
                    $day = $day->modify('+1 day');
                    //log_message("error", "moving to " . $day->format("Y-m-d") . "<br/>");
                    $counter++;

                    //log_message("error", "day = " . json_encode($day) . " and " . json_encode($end_date_object));
                } while ($day < $end_date_object && $counter < 100);


//            echo "<br/> ============================================================= <br/>";
                return array(
                    "result" => "success",
                    "data" => $slots_timings
                );
            } else {
                //log_message("error", "Assign physician to patient before scheduling");
                return array(
                    "result" => "error",
                    "message" => "Assign physician to patient before scheduling"
                );
            }
        } else {
            //log_message("error", "Next visit data not found");
            return array(
                "result" => "error",
                "message" => "Next visit data not found"
            );
        }
    }

    public function assign_slots($clinic_id, $patient_id) {

        //get duration based on patient visit time fixed as per next visit settings
        $new_visit_duration = 30; // default

        $next_visit_duration_data = $this->db->select("c_vt.visit_duration")
                        ->from("referral_patient_info pat, clinic_visit_timings c_vt")
                        ->where(array(
                            "c_vt.active" => 1,
                            "pat.active" => 1,
                            "pat.id" => $patient_id,
                            "c_vt.clinic_id" => $clinic_id
                        ))->where("pat.next_visit", "c_vt.visit_type", false)
                        ->get()->result();
        //log_message("error", "next visit check data => " . $this->db->last_query());
        if ($next_visit_duration_data) {
            $new_visit_duration = intval($next_visit_duration_data[0]->visit_duration);
            //get physician assigned to patient
            $assigned = $this->db->select("c_ref.assigned_physician")
                            ->from("referral_patient_info pat, clinic_referrals c_ref")
                            ->where(array(
                                "pat.active" => 1,
                                "c_ref.active" => 1,
                                "pat.id" => $patient_id,
                                "c_ref.assigned_physician <>" => 0
                            ))
                            ->where("pat.referral_id", "c_ref.id", false)
                            ->get()->result();

            if ($assigned) {
                $assigned_physician = $assigned[0]->assigned_physician;

                $next_day = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+1 day")));

                $visits_booked = $this->db
                                ->select("concat(r_pv.visit_date, ' ', r_pv.visit_time) "
                                        . "as visit_start_time, "
                                        . "concat(r_pv.visit_date, ' ', r_pv.visit_end_time) "
                                        . "as visit_end_time")
                                ->from("records_patient_visit r_pv, referral_patient_info pat, "
                                        . "clinic_referrals c_ref")
                                ->where(array(
                                    "r_pv.active" => 1,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "r_pv.visit_date >= " => $next_day->format('Y-m-d'),
                                    "c_ref.assigned_physician" => $assigned_physician
                                ))
                                ->where("r_pv.patient_id", "pat.id", false)
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->order_by("1")->get()->result();

                //log_message("error", "visits booked = " . json_encode($visits_booked));
                //log_message("error", "visits booked = " . $this->db->last_query());
//        echo "visits booked = " . json_encode($visits_booked) . "<br/>";
//        echo "visits booked = " . $this->db->last_query() . "<br/>";

                $visits_reserved = $this->db
                                ->select(
                                        "concat(r_pvr.visit_date1, ' ', r_pvr.visit_start_time1) "
                                        . "as visit_start_time1, "
                                        . "concat(r_pvr.visit_date1, ' ', r_pvr.visit_end_time1) "
                                        . "as visit_end_time1,"
                                        . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_start_time2) "
                                        . "as visit_start_time2, "
                                        . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_end_time2) "
                                        . "as visit_end_time2,"
                                        . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_start_time3) "
                                        . "as visit_start_time3, "
                                        . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_end_time3) "
                                        . "as visit_end_time3")
                                ->from("records_patient_visit_reserved r_pvr, referral_patient_info pat, "
                                        . "clinic_referrals c_ref")
                                ->where(array(
                                    "r_pvr.active" => 1,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "c_ref.assigned_physician" => $assigned_physician,
                                    "r_pvr.`visit_expire_time` > " => date("Y-m-d H:i:s")
                                ))->group_start()
                                ->where("r_pvr.visit_date1 >= ", $next_day->format('Y-m-d'))
                                ->or_where("r_pvr.visit_date2 >= ", $next_day->format('Y-m-d'))
                                ->or_where("r_pvr.visit_date3 >= ", $next_day->format('Y-m-d'))
                                ->group_end()
                                ->where("r_pvr.patient_id", "pat.id", false)
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->order_by("1")->get()->result();


                //log_message("error", "visits reserved = " . json_encode($visits_reserved));
                //log_message("error", "visits reserved = " . $this->db->last_query());

                $visits_reserved = $this->filter_reserved($visits_reserved, $next_day->format('Y-m-d'));
//        echo "visits booked = " . json_encode($visits_booked) . "<br/>";
//        echo "visits reserved = " . json_encode($visits_reserved) . "<br/>";

                $all_visits = array_merge($visits_booked, $visits_reserved);
                //sort by date
                $all_visits = json_decode(json_encode($all_visits));
                usort($all_visits, array($this, "sort_visits_by_date"));

//        echo "<br/><br/>all visits = " . json_encode($all_visits) . "<br/><br/>";
//        echo $this->db->last_query() . "<br/><br/>";

                $visits_booked = $all_visits;
                $available_visit_slots = array();

                $day = $next_day;
                $counter = 0;
                do {
                    //for each day
//                echo "*** day = " . json_encode($day) . "<br/>";
                    $scheduling_day = $this->check_day_availability($day, $assigned_physician);
//                echo " [][][][][][] => checking availablility for day for pv to be created = " . json_encode($day->format("Y-m-d")) . "<br/>";
                    $day_assigned = false;
//                echo "availability checked fine";

                    if ($scheduling_day["available"]) {
//                    echo "is available <br/>";
                        $day_start_time = $scheduling_day["day_start_time"];
                        $day_end_time = $scheduling_day["day_end_time"];
                        $blocks = $scheduling_day["blocks"];

//                    echo "day times = $day_start_time and $day_end_time <br/>";

                        $processed_keys = 0;
                        $time1 = $scheduling_day["day"] . " " . $day_start_time;

                        $visits_booked_for_day = $this->get_visit_booked_for_day($day, $visits_booked, $blocks);
//                    echo "visits_booked_for_day = " . json_encode($visits_booked_for_day) . "<br/>";
                        if (sizeof($visits_booked_for_day) != 0) {
//                        echo "visits_booked_for_day has visits <br/>";

                            for ($key = 0; $key < sizeof($visits_booked_for_day) && !$day_assigned; $key++) {
//                            echo "inside for loop <br/>";
                                $processed_keys = $key;
                                $visit_start_time = null;
                                $visit_end_time = null;
                                if (is_object($visits_booked_for_day[$key])) {
                                    $visit_start_time = $visits_booked_for_day[$key]->visit_start_time;
                                    $visit_end_time = $visits_booked_for_day[$key]->visit_start_time;
                                } else {
                                    $visit_start_time = $visits_booked_for_day[$key]["visit_start_time"];
                                    $visit_end_time = $visits_booked_for_day[$key]["visit_end_time"];
                                }
                                if (is_object(end($visits_booked_for_day))) {
                                    $last_visit_end_time = end($visits_booked_for_day)->visit_end_time;
                                } else {
                                    $last_visit_end_time = end($visits_booked_for_day)["visit_end_time"];
                                }

                                $time2 = $visit_start_time;

//                            echo "################ check between " . $time1 . " to " . $time2 . " <br/>";
                                $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
//                            echo "1. response from slot = " . json_encode($slot_response) . "<br/>";
                                if ($slot_response["available"]) {
                                    $new_visit = array(
                                        "start_time" => $slot_response["start_time"],
                                        "end_time" => $slot_response["end_time"]
                                    );
                                    $available_visit_slots[] = $new_visit;
//                                echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                                    $day_assigned = true;
                                } else {
                                    //check for next visit
//                                echo "setting time1 to visit end time <br/>";
                                    $time1 = $visit_end_time;
                                }
                            }
                            //check for day start time to visit 1
                            if (!$day_assigned) {
                                $time1 = $last_visit_end_time;
                                $time2 = $scheduling_day["day"] . " " . $day_end_time;
//                            echo "at end of day <br/>";
//                            echo "################ check between " . $time1 . " to " . $time2 . " <br/>";
                                $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
//                            echo "2. response from slot = " . json_encode($slot_response) . "<br/>";
                                if ($slot_response["available"]) {
                                    $new_visit = array(
                                        "start_time" => $slot_response["start_time"],
                                        "end_time" => $slot_response["end_time"]
                                    );
                                    $available_visit_slots[] = $new_visit;
//                                echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                                    $day_assigned = true;
                                }
                            }

//                    $time2 = 
//                        echo "should check for visit slot <br/>";
                        } else {
//                        echo "visits_booked_for_day has no visits <br/>";
                            $time2 = $scheduling_day["day"] . " " . $day_end_time;

                            $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
//                        echo "response from slot = " . json_encode($slot_response) . "<br/>";
//                        echo "2. response from slot = " . json_encode($slot_response) . "<br/>";
                            if ($slot_response["available"]) {
                                $new_visit = array(
                                    "start_time" => $slot_response["start_time"],
                                    "end_time" => $slot_response["end_time"]
                                );
                                $available_visit_slots[] = $new_visit;
//                            echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                                $day_assigned = true;
                            }
                        }
                    } else {
//                    echo "is not available <br/>";
                    }
                    $day = $day->modify('+1 day');
//                echo "moving to " . $day->format("Y-m-d") . "<br/>";
                    $counter++;
                } while (sizeof($available_visit_slots) < 3 && $counter < 100);


//            echo "<br/> ============================================================= <br/>";
                return array(
                    "result" => "success",
                    "data" => $available_visit_slots
                );
            } else {
                //log_message("error", "Assign physician to patient before scheduling");
                return array(
                    "result" => "error",
                    "message" => "Assign physician to patient before scheduling"
                );
            }
        } else {
            //log_message("error", "Next visit data not found");
            return array(
                "result" => "error",
                "message" => "Next visit data not found"
            );
        }
    }

    private function sort_visits_by_date($a, $b) {
        return ($a->visit_start_time > $b->visit_start_time);
    }

    private function time_slot_available($time1, $time2, $new_visit_duration) {
        //echo "### called time_slot_available" . "<br/>";
        //echo json_encode($time1) . "<br/>";
        //echo json_encode($time2) . "<br/>";

        $datetime1 = DateTime::createFromFormat('Y-m-d H:i:s', $time1);
        $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $time2);


        $gap = $datetime1->diff($datetime2);
        //echo "gap = " . json_encode($gap) . "<br/>";
        $gap_in_minutes = ($gap->h * 60) + $gap->i;

        if ($gap_in_minutes > $new_visit_duration) {
            $response = array(
                "available" => true,
                "start_time" => $datetime1->format("Y-m-d H:i:s"),
                "end_time" => $datetime1->add(new DateInterval("PT" . $new_visit_duration . "M"))->format("Y-m-d H:i:s")
            );
        } else {
            $response = array(
                "available" => false
            );
        }
        return $response;
    }

    private function get_time_slot_available($time1, $time2, $new_visit_duration) {
        if ($time1 > $time2) {
            //log_message("error", "EROOOOOOOOOOOOOOOOOOOOOOR => $time1, $time2");
            return array();
        }
        $datetime1 = DateTime::createFromFormat('Y-m-d H:i:s', $time1);
        $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $time2);
        //log_message("error", "for weekloop start at " . $datetime1->format("Y-m-d H:i:s"));
        //log_message("error", "for weekloop end at " . $datetime2->format("Y-m-d H:i:s"));

        $tmp1 = DateTime::createFromFormat('Y-m-d H:i:s', $datetime1->format("Y-m-d H:i:s"));
        $tmp2 = DateTime::createFromFormat('Y-m-d H:i:s', $datetime1->format("Y-m-d H:i:s"));
        $tmp2->add(new DateInterval("PT{$new_visit_duration}M"));
        //log_message("error", "tmp end is set to " . $tmp2->format("Y-m-d H:i:s"));

        $response = array();
        while ($tmp2->format("Y-m-d H:i:s") <= $datetime2->format("Y-m-d H:i:s")) {
            $response[] = array(
                "start_time" => $tmp1->format("Y-m-d H:i:s"),
                "end_time" => $tmp2->format("Y-m-d H:i:s")
            );
            //log_message("error", "before tmp1 = " . $tmp1->format("Y-m-d H:i:s") . " and "
            // . "tmp2 = " . $tmp2->format("Y-m-d H:i:s"));
            $tmp1 = DateTime::createFromFormat('Y-m-d H:i:s', $tmp2->format("Y-m-d H:i:s"));
            $tmp2 = $tmp2->add(new DateInterval("PT{$new_visit_duration}M"));
            //log_message("error", "after tmp1 = " . $tmp1->format("Y-m-d H:i:s") . " and "
            //. "tmp2 = " . $tmp2->format("Y-m-d H:i:s"));
            //log_message("error", "added something");
        }
        //log_message("error", "time slots for this block => " . json_encode($response));
        return $response;
    }

    private function count_time_slot_available($time1, $time2, $new_visit_duration) {
        //echo "### called time_slot_available" . "<br/>";
        //echo json_encode($time1) . "<br/>";
        //echo json_encode($time2) . "<br/>";

        $datetime1 = DateTime::createFromFormat('Y-m-d H:i:s', $time1);
        $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $time2);


        $gap = $datetime1->diff($datetime2);
        //echo "gap = " . json_encode($gap) . "<br/>";
        $gap_in_minutes = ($gap->h * 60) + $gap->i;

        return floor($gap_in_minutes / $new_visit_duration);
    }

    private function day_of($visit) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $visit->visit_start_time)->format("Y-m-d");
        return $date;
    }

    private function get_visit_booked_for_day($day, $visits_booked, $blocks) {
//        echo json_encode($visits_booked) . "<br/>" . json_encode($day);
        //echo "### called get_visit_booked_for_day <br/>";
        $visits_booked_for_day = array();
//        echo "<br/>CHECK HERE<br/>";
        foreach ($visits_booked as $key => $value) {
//            echo json_encode($value);
            if (isset($value->visit_start_time)) {
                $visit_day = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time)->format("Y-m-d");
                if ($visit_day === $day->format("Y-m-d")) {
                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $value->visit_start_time,
                        "visit_end_time" => $value->visit_end_time
                    );
                }
            } else if (isset($value->visit_start_time1)) {
                // reserved visit
                $visit_start_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time1);
                if ($visit_start_time->format("Y-m-d") === $day->format("Y-m-d")) {
                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $value->visit_start_time1,
                        "visit_end_time" => $value->visit_end_time1
                    );

                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $value->visit_start_time2,
                        "visit_end_time" => $value->visit_end_time2
                    );

                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $value->visit_start_time3,
                        "visit_end_time" => $value->visit_end_time3
                    );
                }
            }
        }
//        //log_message("error", "before visits = " . json_encode($visits_booked_for_day));
//        $tmp = $visits_booked_for_day;
//        foreach ($tmp as $key => $value) {
//            
//        }
        //log_message("error", "before visits 2 = " . json_encode($visits_booked_for_day));
        foreach ($blocks as $key => $block) {
            $visits_booked_for_day[] = array(
                "visit_start_time" => $block["day"] . " " . $block["start_time"],
                "visit_end_time" => $block["day"] . " " . $block["end_time"]
            );
        }

        //log_message("error", "after blocks added = " . json_encode($visits_booked_for_day));
        //sort them as some new blocks may be added
        for ($i = 0; $i < sizeof($visits_booked_for_day); $i++) {
            for ($j = $i + 1; $j < sizeof($visits_booked_for_day); $j++) {
                //log_message("error", "sorting = " . json_encode($visits_booked_for_day[$i]) . "," . is_object($visits_booked_for_day[$i]));
                if ($visits_booked_for_day[$i]["visit_start_time"] >
                        $visits_booked_for_day[$j]["visit_start_time"]) {
                    $tmp = $visits_booked_for_day[$i];
                    $visits_booked_for_day[$i] = $visits_booked_for_day[$j];
                    $visits_booked_for_day[$j] = $tmp;
                }
            }
        }

        //log_message("error", "visits booked for day ( includes blocks ) = " .
        // json_encode($visits_booked_for_day));
//        echo json_encode($visits_booked_for_day) . "<br/>";
        return $visits_booked_for_day;
    }

    private function check_day_availability($day, $assigned_physician) {
//        echo "checking day availability. here now<br/>";
        if ($this->check_for_specific_leaves($day, $assigned_physician)) {
//            echo "checking availability of day ".json_encode($day) . " <br/>";
//            echo "dr = " . $assigned_physician;
            $availability_response = $this->check_for_weekend_days($day, $assigned_physician);
//            echo json_encode($availability_response) . "<br/>";
//            echo "called wekend function." . "<br/>";
            if (isset($availability_response["available"]) && $availability_response["available"]) {
                return $availability_response;
            }
        } else {
            //log_message("error", "jumped for specific day of for");
            return array(
                "available" => false
            );
        }
    }

    private function check_for_weekend_days($day, $assigned_physician) {
//        echo "hi <br/>";
//        return "hola";
        //convert day to weekday name
//        echo "### called check_for_weekend_days <br/>";
//        echo "day = " . json_encode($day) . "<br/>";

        $weekday_name = strtolower($day->format('D'));
        $day = strtolower($day->format('Y-m-d'));
        $data = $this->db->select("$weekday_name as available, "
                                . "{$weekday_name}_start_time as start_time, "
                                . "{$weekday_name}_end_time as end_time")
                        ->from("schedule_visit_settings")->where(array(
                    "clinic_physician_id" => $assigned_physician, //convert to session then
                    "active" => "yes"
                ))->get()->result();

//        echo json_encode($day) . "<br/>";
//        //log_message("error", "check_for_weekend_days = " . $this->db->last_query());
//
//
        if ($data) {
            if ($data[0]->available === "yes") {
                //assign day start and end time as per specified in weekday
                $day_start_time = $data[0]->start_time;
                $day_end_time = $data[0]->end_time;
                //respond with time slot configuration for that day
                //find weekly blocks for this day
                $weekly_blocks = array();


                $day_blocks = $this->db->select("start_time, end_time, type")
                                ->from("clinic_physician_day_blocks")
                                ->where(array(
                                    "for_date" => $day,
                                    "clinic_physician_id" => $assigned_physician,
                                    "active" => 1
                                ))->get()->result();

                if (!$day_blocks) {
                    $weekly_blocks = $this->db->select("start_time, end_time")
                                    ->from("clinic_physician_weekly_blocks")
                                    ->where(array(
                                        "for_weekday" => $weekday_name,
                                        "clinic_physician_id" => $assigned_physician,
                                        "active" => 1
                                    ))->get()->result();
                }

                $blocks_filtered = array();
                foreach ($day_blocks as $key => $day_block) {
                    if ($day_block->type === "daytime") {
                        $day_start_time = $day_block->start_time;
                        $day_end_time = $day_block->end_time;
                    }
                    if ($day_block->type === "timeblock") {
                        $blocks_filtered[] = array(
                            "start_time" => $day_block->start_time,
                            "end_time" => $day_block->end_time,
                            "day" => $day
                        );
                    }
                }

                if (!$day_blocks) {
                    foreach ($weekly_blocks as $key => $weekly_block) {
                        $blocks_filtered[] = array(
                            "start_time" => $weekly_block->start_time,
                            "end_time" => $weekly_block->end_time,
                            "day" => $day
                        );
                    }
                }

                $col_start_time = array_column($blocks_filtered, 'start_time');
                array_multisort($col_start_time, SORT_ASC, $blocks_filtered);

                //find day blocks for this day
                $response = array(
                    "day" => $day,
                    "available" => true,
                    "day_start_time" => $day_start_time,
                    "day_end_time" => $day_end_time,
                    "blocks" => $blocks_filtered
                );
//                //log_message("error", "according to q data = " . json_encode($response));
            } else {
                $response = array(
                    "available" => false
                );
            }
        } else {
            $response = array(
                "available" => false
            );
        }
//        echo "response = " . json_encode($response) . "<br/>";
        return $response;
    }

    private function check_for_specific_leaves($day, $assigned_physician) {
        $day = strtolower($day->format('Y-m-d'));
        $specific_day_leave = $this->db->select("id")
                        ->from("clinic_physician_day_blocks")
                        ->where(array(
                            "clinic_physician_id" => $assigned_physician,
                            "active" => 1,
                            "type" => "dayblock",
                            "for_date" => $day
                        ))->get()->result();

        if ($specific_day_leave) {
            //this day is assigned, so not allowed slot for this day
            return false;
        } else {
            //this day can be used to find time slots.
            return true;
        }
    }

}
