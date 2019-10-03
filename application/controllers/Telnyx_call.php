<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Telnyx_call extends CI_Controller {

    public function get_data() {
        log_message("error", "telnyx webhook triggerd");
        log_message("error", "ends here");
        $json = file_get_contents('php://input');
        log_message("error", "1 = " . $json);
//        
        $json = json_encode($_REQUEST);
        log_message("error", "1 = " . $json);
        
        $json = json_encode($this->input->get());
        log_message("error", "1 = " . $json);
//        
//        $action = json_decode($json, true);
//        
//        
//        log_message("error", "telnyx webhook triggerd");
//
//        $paydata = $action['data'];
//        $payload = $paydata['payload'];
//        $event_type = $paydata['event_type'];
////file_put_contents('demo2.txt', print_r($payload,true) ); 
//        
//        
//        if (isset($payload['call_control_id']) && !empty($payload['call_control_id'])) {
//            $call_control_id = $payload['call_control_id'];
//        } else {
//            $datalPAyload = selectCallID($payload['call_leg_id']);
//            $call_control_id = $datalPAyload['call_control_id'];
//        }
//        $selectData = selectOne('step_one', $call_control_id);
//        $status_update = selectOne('status_update', $call_control_id);
//        $recording_saved = selectOne('recording_saved', $call_control_id);
//
//
//        if ($event_type == 'call_initiated' && $payload['direction'] == 'incoming') {
//            $url = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/answer';
//            $data1 = getcallType($url, $call_control_id);
//        } elseif ($event_type == 'call_answered' && base64_decode($payload['client_state']) == "NewCall") {
//            $text = 'Hello. Thank you for calling Premier Health.';
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
//            $encodedString = base64_encode('welcome_first');
//            $dataarray = array(
//                'payload' => $text,
//                'voice' => 'female',
//                'language' => 'en-US',
//                'payload_type' => 'ssml',
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//
//            $data = curlPostData($urlNew, $call_control_id, $dataarray);
//        } elseif ($event_type == 'speak_ended' && base64_decode($payload['client_state']) == 'welcome_first') {
//            // $urlNew = 'https://api.telnyx.com/v2/calls/'.$call_control_id.'/actions/speak';
//            $inserted = $this->db->insert("ivr_responses", array(
//                "call_control_id" => $call_control_id,
//                "call_leg_id" => $payload['call_leg_id']
//            ));
//
//
//
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
//            $text = 'If you are calling to book an appointment with Premier Health, please press 1.
//                     If you are calling from a clinic or pharmacy, please press 2.
//                    If you are calling for some other reason, please press 3.
//                ';
//            $encodedString = base64_encode('MainMenu');
//            $dataarray = array(
//                'payload' => $text,
//                'voice' => 'female',
//                'language' => 'en-US',
//                'payload_type' => 'ssml',
//                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
//                'terminating_digit' => '#',
//                'timeout_millis' => '5000',
//                'inter_digit_timeout_millis' => '2000',
//                'valid_digits' => '123',
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//
//            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
//        } elseif ($event_type == 'dtmf' && base64_decode($payload['client_state']) == "MainMenu") {
//            //pressed button 1/2/3
//
//            $digits = $payload['digit'];
//            $update = updateData('step_one', $digits, $call_control_id);
//            if ($digits == 1) {
//                $text = "Great, I can help you with that";
//            } elseif ($digits == 2) {
//                $text = "Please record a message after the beep, and the clinic staff will be in touch as soon as possible";
//            } elseif ($digits == 3) {
//                $text = "Please record a message after the beep, and the clinic staff will be in touch as soon as possible";
//            } else {
//                $text = "I’m sorry, I didn’t catch that.";
//            }
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
//            $encodedString = base64_encode('user_response_get');
//            $dataarray = array(
//                'payload' => $text,
//                'voice' => 'female',
//                'language' => 'en-US',
//                'payload_type' => 'ssml',
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//
//            $data = curlPostData($urlNew, $call_control_id, $dataarray);
//        } elseif ($event_type == 'speak_ended' &&
//                base64_decode($payload['client_state']) == "user_response_get") {
//            if ($selectData['step_one'] == 1) {
//                $text = 'First, I’m going to need to ask you a few questions.
//            
//                Please say your first and last name after the beep ?';
//                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
//                $encodedString = base64_encode('user_name_say');
//                $dataarray = array(
//                    'payload' => $text,
//                    'voice' => 'female',
//                    'language' => 'en-US',
//                    'payload_type' => 'ssml',
//                    'command_id' => rand(),
//                    'client_state' => $encodedString
//                );
//
//                $data = curlPostData($urlNew, $call_control_id, $dataarray);
//            } elseif ($selectData['step_one'] == 2) {
//                $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
//                $datarecord = array(
//                    'format' => 'mp3',
//                    'channels' => 'single',
//                    'play_beep' => 'true',
//                    'client_state' => base64_encode('voicemailoption1'),
//                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
//                );
//                $data = curlPostData($url_new, $call_control_id, $datarecord);
//            } elseif ($selectData['step_one'] == 3) {
//                $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
//                $datarecord = array(
//                    'format' => 'mp3',
//                    'channels' => 'single',
//                    'play_beep' => 'true',
//                    'client_state' => base64_encode('voicemailoption2'),
//                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
//                );
//                $data = curlPostData($url_new, $call_control_id, $datarecord);
//            } else {
//                
//            }
//        } elseif ($event_type == 'speak_ended' && base64_decode($payload['client_state']) == "user_name_say" && $status_update['status_update'] == '0') {
//            $update = updateData('status_update', '1', $call_control_id);
//            $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
//            $datarecord = array(
//                'format' => 'mp3',
//                'channels' => 'single',
//                'play_beep' => 'true',
//                'client_state' => base64_encode('nametracnscription'),
//                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
//            );
//            $data = curlPostData($url_new, $call_control_id, $datarecord);
//
//
//            if (isset($data)) {
//                sleep(4);
//                $url_stop = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_stop';
//                $datastop = array(
//                    'client_state' => base64_encode('name_recording_stop'),
//                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49012'
//                );
//                $data1 = curlPostData($url_stop, $call_control_id, $datastop);
//            }
//        } elseif ($event_type == 'recording_saved' && base64_decode($payload['client_state']) == "name_recording_stop" && $recording_saved['recording_saved'] == '0') {
////            require 'functions.php';
//
//            $datalPAyload = selectCallID($payload['call_leg_id']);
//            $call_control_id = $datalPAyload['call_control_id'];
//            $update = updateData('recording_saved', '1', $call_control_id);
//            //$run = getTranscription($payload['recording_urls']['mp3']);
//            //$nameget = explode(' ', $run);
//            //this need to be uncommented
//            log_message("error", "mp3 file recorded = " . $payload['recording_urls']['mp3']);
////            file_put_contents('recording_url.txt', $payload['recording_urls']['mp3']);
//            //file_put_contents('recording_url_trans.txt', $run);
//
//
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
//            $encodedString = base64_encode('name_confirmation');
//            // $update1 =  updateData('first_name',$run,$call_control_id,$conn);
//            $dataarray = array(
//                'payload' => "Thank you ",
//                'voice' => 'female',
//                'language' => 'en-US',
//                'payload_type' => 'ssml',
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//
//            $data = curlPostData($urlNew, $call_control_id, $dataarray);
//        } elseif ($event_type == 'speak_ended' && base64_decode($payload['client_state']) == "name_confirmation") {
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
//            $text = 'Please enter your 10 digit health card number, followed by the pound key. If you don’t have a health card number, please press 0.';
//            $encodedString = base64_encode('UserCard');
//            $dataarray = array(
//                'payload' => $text,
//                'voice' => 'female',
//                'language' => 'en-US',
//                'payload_type' => 'ssml',
//                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
//                'terminating_digit' => '#',
//                'inter_digit_timeout_millis' => '5000',
//                'minimum_digits' => '1',
//                'maximum_digits' => '10',
//                'valid_digits' => '0123456789',
//                'terminating_digit' => '#',
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
//        } elseif ($event_type == 'dtmf' && base64_decode($payload['client_state']) == "name_recording_stop") {
//            $url_stop = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_stop';
//            $datastop = array(
//                'client_state' => base64_encode('name_recording_stop'),
//                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49012'
//            );
//            $data1 = curlPostData($url_stop, $call_control_id, $datastop);
//        } elseif ($event_type == 'gather_ended' && base64_decode($payload['client_state']) == "UserCard") {
//            $digits = $payload['digits'];
//            $len = strlen($digits);
//
//            if ($len >= 10 || $digits == '0') {
//                $update = updateData('health_card', $digits, $call_control_id);
//                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
//                $encodedString = base64_encode('thankyou_message_after_card');
//                $dataarray = array(
//                    'payload' => 'Thank you',
//                    'voice' => 'female',
//                    'language' => 'en-US',
//                    'payload_type' => 'ssml',
//                    'command_id' => rand(),
//                    'client_state' => $encodedString
//                );
//
//                $data = curlPostData($urlNew, $call_control_id, $dataarray);
//            } elseif ($digits != '0' && $len < 10) {
//                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
//                $text = 'I’m sorry, I didn’t catch that. Please enter your 10 digit health card number, followed by the pound key. ';
//                $encodedString = base64_encode('UserCard');
//                $dataarray = array(
//                    'payload' => $text,
//                    'voice' => 'female',
//                    'language' => 'en-US',
//                    'payload_type' => 'ssml',
//                    'invalid_payload' => 'I’m sorry, I didn’t catch that.',
//                    'terminating_digit' => '#',
//                    'timeout_millis' => '5000',
//                    'inter_digit_timeout_millis' => '5000',
//                    'minimum_digits' => '1',
//                    'maximum_digits' => '10',
//                    'valid_digits' => '0123456789',
//                    'terminating_digit' => '#',
//                    'command_id' => rand(),
//                    'client_state' => $encodedString
//                );
//                $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
//            }
//        } elseif ($event_type == 'speak_ended' && base64_decode($payload['client_state']) == "thankyou_message_after_card") {
//
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
//            $text = 'Please enter your 10 digit phone number, including the area code, followed by the pound key';
//            $encodedString = base64_encode('UserPhone');
//            $dataarray = array(
//                'payload' => $text,
//                'voice' => 'female',
//                'language' => 'en-US',
//                'payload_type' => 'ssml',
//                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
//                'terminating_digit' => '#',
//                'timeout_millis' => '5000',
//                'inter_digit_timeout_millis' => '5000',
//                'minimum_digits' => '1',
//                'maximum_digits' => '13',
//                'valid_digits' => '0123456789',
//                'terminating_digit' => '#',
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
//        } elseif ($event_type == 'gather_ended' && base64_decode($payload['client_state']) == "UserPhone") {
//            $digits = $payload['digits'];
//            $len = strlen($digits);
//            log_message("error", "payload = " . json_encode($payload));
////            file_put_contents('payloadnext.txt', print_r($payload, true));
//            if ($len >= 10 || $digits == '0') {
//                $update = updateData('user_number', $digits, $call_control_id);
//                $text = 'Thank you. The clinic staff has been notified of your appointment request, and will be in touch as soon as possible.
//    Have a great day';
//                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
//                $encodedString = base64_encode('user_final_callback');
//                $dataarray = array(
//                    'payload' => $text,
//                    'voice' => 'female',
//                    'language' => 'en-US',
//                    'payload_type' => 'ssml',
//                    'command_id' => rand(),
//                    'client_state' => $encodedString
//                );
//
//                $data = curlPostData($urlNew, $call_control_id, $dataarray);
//            } elseif ($digits != '0' && $len < 10) {
//                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
//                $text = 'I’m sorry, I didn’t catch that. Please enter your 10 digit phone number, including the area code, followed by the pound key';
//                $encodedString = base64_encode('UserPhone');
//                $dataarray = array(
//                    'payload' => $text,
//                    'voice' => 'female',
//                    'language' => 'en-US',
//                    'payload_type' => 'ssml',
//                    'invalid_payload' => 'I’m sorry, I didn’t catch that.',
//                    'terminating_digit' => '#',
//                    'timeout_millis' => '5000',
//                    'inter_digit_timeout_millis' => '5000',
//                    'minimum_digits' => '1',
//                    'maximum_digits' => '13',
//                    'valid_digits' => '0123456789',
//                    'terminating_digit' => '#',
//                    'command_id' => rand(),
//                    'client_state' => $encodedString
//                );
//                $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
//            }
//        } elseif ($event_type == 'speak_ended' && base64_decode($payload['client_state']) == "user_final_callback") {
//
//            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/hangup';
//            $encodedString = base64_encode('call_end_command');
//            $dataarray = array(
//                'command_id' => rand(),
//                'client_state' => $encodedString
//            );
//
//            $data = curlPostData($urlNew, $call_control_id, $dataarray);
//        } else {
//            
//        }
    }

}