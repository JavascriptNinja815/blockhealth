<?phpclass Scheduled_model extends CI_Model {    public function ssp_scheduled_model() {        $table = "scheduled_dash";        $primaryKey = "id";        $columns = array(            array('db' => 'patient_name', 'dt' => 0),            array('db' => 'priority', 'dt' => 1),            array('db' => 'referral_reason', 'dt' => 2),            array('db' => 'next_visit', 'dt' => 3),            array('db' => 'id', 'dt' => 4)        );        $sql_details = array(            'user' => $this->db->username,            'pass' => $this->db->password,            'db' => $this->db->database,            'host' => $this->db->hostname        );        $where = "1";        if ($this->session->userdata("login_role") == "clinic_admin")            $where = "clinic_admin=" . $this->session->userdata("user_id");        else if ($this->session->userdata("login_role") == "clinic_physician")            $where = "physician_id=" . $this->session->userdata("physician_id");        require('ssp.class.php');        return json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where));    }    public function get_referral_dash_info_model() {        $this->form_validation->set_rules('id', 'Patient ID', 'required');        if ($this->form_validation->run()) {            $data = $this->input->post();            $dynamic_status = "";            $this->db->select("count(r_pv.visit_confirmed) as visits_unconfirmed");            $this->db->from("clinic_referrals c_ref, records_patient_visit r_pv, referral_patient_info pat");            $this->db->where(array(                "md5(pat.id)" => $data["id"],                "c_ref.active" => 1,                "r_pv.active" => 1,                "pat.active" => 1,            ));            $this->db->where_in("r_pv.visit_confirmed", array("Awaiting Confirmation", "Change required", "N/A"));            $this->db->where("pat.referral_id", "c_ref.id", false);            $this->db->where("r_pv.patient_id", "pat.id", false);            $result = $this->db->get()->result();            $dynamic_status = (intval($result[0]->visits_unconfirmed) > 0)?"Scheduled":"Confirmed";            log_message("error", "confirm => " . $this->db->last_query());                        $this->db->select(                    "`pat`.`fname` as pat_fname," .                    "`pat`.`lname` as pat_lname," .                    "`pat`.`dob` as pat_dob," .                    "`pat`.`cell_phone` as pat_cell_phone," .                    "`pat`.`home_phone` as pat_home_phone," .                    "`pat`.`work_phone` as pat_work_phone," .                    "`pat`.`email_id` as pat_email_id," .                    "`pat`.`ohip` as pat_ohip," .                    "`pat`.`address` as pat_address," .                    "`pat`.`gender` as pat_gender," .                    "if(`pat`.`dob`, DATE_FORMAT(`pat`.`dob`, ' (%b %d, %Y)'), '') AS pat_dob," .                    "`dr`.`fname` as dr_fname," .                    "`dr`.`lname` as dr_lname," .                    "`dr`.`email` as dr_email_id," .                    "`dr`.`phone` as dr_phone_number," .                    "`dr`.`fax` as dr_fax," .                    "`dr`.`address` as dr_address," .                    "`dr`.`billing_num` as dr_billing_num," .                    "`c_ref`.`referral_reason`," .                    "if(isnull(c_dr.id),'empty', md5(c_dr.id)) as assigned_physician," .                    "r_tri.priority as priority," .                    "'$dynamic_status' as dynamic_status,".                    "if(isnull(c_dr.id), 'Not Assigned', concat(c_dr.first_name, ' ', c_dr.last_name)) as assigned_physician_name");            $this->db->from("`efax_info` `efax`, referral_patient_info pat, referral_physician_info dr, referral_clinic_triage r_tri, `clinic_referrals` `c_ref` left join clinic_physician_info c_dr on ( c_dr.active = 1 and c_dr.id = `c_ref`.assigned_physician)");            $this->db->where(                    array(                        "efax.active" => 1,                        "dr.active" => 1,                        "r_tri.active" => 1,                        "pat.active" => 1,                        "c_ref.active" => 1,                        "md5(pat.id)" => $data["id"],                        "efax.to" => $this->session->userdata("user_id")                    )            );            $this->db->where("c_ref.efax_id", "efax.id", false);            $this->db->where("r_tri.patient_id", "pat.id", false);            $this->db->where("dr.patient_id", "pat.id", false);            $this->db->where("pat.referral_id", "`c_ref`.id", false);            $result = $this->db->get()->result();//            log_message("error", "working sql = " . $this->db->last_query());            $this->db->select("md5(chk.id) as id," .                    "chk.checklist_id, " .                    "chk.attached, " .                    "case " .                    "when (chk.checklist_id <> 0) then itm.name " .                    "else chk.checklist_name " .                    "end " .                    "as checklist_name");            $this->db->from("referral_checklist chk");            $this->db->join("clinic_referral_checklist_items itm", "chk.checklist_id = itm.id and itm.active=1", "left");            $this->db->where(array(                "md5(chk.patient_id)" => $data["id"],                "chk.active" => 1            ));            $this->db->or_group_start()                ->where("itm.clinic_id", $this->session->userdata("user_id"))                ->where("chk.checklist_type", "typed")                ->group_end();            $result2 = $this->db->get()->result();            log_message("error", "sql = " . $this->db->last_query());            //prepare clinical triage data ( diseases, tests, etc)            // get clinic triage id            $triage_data = null;            $this->db->select("r_tri.id");            $this->db->from("referral_clinic_triage r_tri");            $this->db->join("referral_patient_info pat", "r_tri.patient_id = pat.id", "left");            $this->db->where(array(                "r_tri.active" => 1,                "pat.active" => 1,                "md5(pat.id)" => $data["id"]            ));            $tmp_result = $this->db->get()->result();            if ($tmp_result) {                $triage_id = $tmp_result[0]->id;                //get details of diseases and other data                $this->db->select("disease");                $this->db->from("referral_clinic_triage_disease_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $diseases = $this->db->get()->result();                $this->db->select("drug");                $this->db->from("referral_clinic_triage_drugs_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $drugs = $this->db->get()->result();                $this->db->select("symptom");                $this->db->from("referral_clinic_triage_symptom_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $symptoms = $this->db->get()->result();                $this->db->select("test");                $this->db->from("referral_clinic_triage_tests_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $tests = $this->db->get()->result();                $this->db->select("device");                $this->db->from("referral_clinic_triage_devices_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $devices = $this->db->get()->result();                $triage_data = array(                    "diseases" => $diseases,                    "drugs" => $drugs,                    "symptoms" => $symptoms,                    "tests" => $tests,                    "devices" => $devices                );            }            return array(                "dash_info" => $result,                "checklist_info" => $result2,                "triage_info" => $triage_data            );        } else            return validation_errors();    }    public function send_report_model() {        $this->form_validation->set_rules('id', 'Referral Id', 'required');        $this->form_validation->set_rules('record_type', 'Record Type', 'required');        if ($this->form_validation->run()) {            $data = $this->input->post();            if ($data["record_type"] == "Diagnosis and Reason for Referral") {                $this->form_validation->set_rules('diagnosis', 'Diagnosis', 'required');                $this->form_validation->set_rules('remarks', 'Remarks', 'required');                if ($this->form_validation->run()) {                    if ($this->check_authentication($data["id"])) {                        $this->db->where(                                array(                                    "md5(id)" => $data["id"],                                    "active" => 1                                )                        );                        $this->db->update("clinic_referrals", array(                            "record_type" => $data["record_type"],                            "diagnosis_report" => $data["diagnosis"],                            "remarks" => $data["remarks"],                            "status" => "Completed"                                )                        );                        log_message("error", "last sql = " . $this->db->last_query());                        return true;                    }                } else                    return validation_errors();            }            else {                $this->form_validation->set_rules('record_description', 'Record Description', 'required');                // $this->form_validation->set_rules('asdqwe[]', 'Record File', 'required');                // log_message("error", "validating");                if ($this->form_validation->run()) {                    // log_message("error", "validated");                    if ($this->check_authentication($data["id"])) {                        // log_message("error", "files = " . json_encode($_FILES));                        if (!empty($_FILES['asdqwe']['name'])) {                            $files = $_FILES;                            $_FILES['asdqwe']['name'] = $files['asdqwe']['name'][0];                            $_FILES['asdqwe']['type'] = $files['asdqwe']['type'][0];                            $_FILES['asdqwe']['tmp_name'] = $files['asdqwe']['tmp_name'][0];                            $_FILES['asdqwe']['error'] = $files['asdqwe']['error'][0];                            $_FILES['asdqwe']['size'] = $files['asdqwe']['size'][0];                            $target_dir = "./uploads/health_records/";                            $config = array();                            $config['upload_path'] = $target_dir;                            $config['max_size'] = '10000';                            $config['allowed_types'] = 'pdf';                            $config['overwrite'] = FALSE;                            $this->load->library('upload');                            $file_name = $this->generate_random_string();                            $config['file_name'] = $file_name;                            $this->upload->initialize($config);                            // log_message("error", "okay till here");                            // log_message("error", "file = " . json_encode($_FILES['asdqwe']));                            if ($this->upload->do_upload('asdqwe')) {                                //change status to completed                                $this->db->where(                                        array(                                            "md5(id)" => $data["id"],                                            "active" => 1                                        )                                );                                $this->db->update("clinic_referrals", array(                                    "status" => "Completed"                                        )                                );                                //insert as clinical record                                $referral_id = $this->get_decrypted_id($data["id"], "clinic_referrals");                                $this->db->insert("records_clinic_notes", array(                                    "referral_id" => $referral_id,                                    "record_type" => $data["record_type"],                                    "description" => $data["record_description"],                                    "record_file" => $file_name,                                    "physician" => $this->session->userdata("physician_name")                                        )                                );                                // log_message("error", "last sql2 = " . $this->db->last_query());                                return true;                            } else {                                return $this->upload->display_errors();                            }                        }                    }                } else                    return validation_errors();            }        } else            return validation_errors();    }    private function check_authentication($md5_id) {        $this->db->select("c_ref.id");        $this->db->from("clinic_referrals c_ref, efax_info efax");        $this->db->where(                array(                    "c_ref.active" => 1,                    "efax.active" => 1,                    "efax.to" => $this->session->userdata("user_id"),                    "md5(c_ref.id)" => $md5_id                )        );        $this->db->where("c_ref.efax_id", "efax.id", false);        $result = $this->db->get()->result();        // log_message("error", "ref auth sql = " . $this->db->last_query());        return ($result) ? true : false;    }    private function generate_random_string($length = 32) {        $timestamp = time();        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';        $charactersLength = strlen($characters);        $randomString = '';        for ($i = 0; $i < $length; $i++) {            $randomString .= $characters[rand(0, $charactersLength - 1)];        }        return $timestamp . "_" . $randomString;    }    private function get_decrypted_id($md5_id, $table_name) {        $this->db->select("id");        $this->db->from($table_name);        $this->db->where(array("md5(id)" => $md5_id));        $result = $this->db->get()->result();        // log_message("error", "get decrypted sql = " . $this->db->last_query());        return ($result) ? $result[0]->id : 0;    }}