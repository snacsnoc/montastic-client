<?php

/**
 * Montastic
 * 
 * @author    Easton Elliott <easton@geekness.eu> 
 * @license   MIT
 * @version   1.0
 */
class Montastic {

    /**
     * @var string Montastic username
     */
    private $username;

    /**
     * @var string Montastic password 
     */
    private $password;

    /**
     * Set the username and password
     * 
     * @param string $username Username
     * @param string $password Password
     * @throws Exception 
     */
    public function __construct($username, $password) {
        if ($username && $password) {
            $this->username = $username;
            $this->password = $password;
        } else {
            throw new Exception('Invalid username and/or password');
        }
    }

    /**
     * Get all the checkpoints
     * 
     * @return SimpleXMLElement SimpleXMLElement object
     */
    public function getAllCheckpoints() {
        $checkpoints = $this->sendRequest("https://www.montastic.com/checkpoints/index");
        return simplexml_load_string($checkpoints);
    }

    /**
     * Get a particular checkpoint
     * 
     * @param int $checkpoint_id Checkpoint ID
     * @return SimpleXMLElement SimpleXMLElement object 
     */
    public function getCheckpoint($checkpoint_id) {
        if (filter_var($checkpoint_id, FILTER_VALIDATE_INT)) {
            $checkpoint_info = $this->sendRequest("https://www.montastic.com/checkpoints/show/$checkpoint_id");
            return simplexml_load_string($checkpoint_info);
        } else {
            throw new InvalidArgumentException('Invalid checkpoint ID');
        }
    }

    /**
     * Delete a particular checkpoint
     * 
     * @param int $checkpoint_id Checkpoint ID
     * @return boolean 
     */
    public function deleteCheckpoint($checkpoint_id) {
        if (filter_var($checkpoint_id, FILTER_VALIDATE_INT)) {
            $delete_checkpoint = $this->sendRequest("https://www.montastic.com/checkpoints/destroy/$checkpoint_id");

            //If the return result is an empty string, then the deletion has succeeded
            if ($delete_checkpoint == ' ') {
                return true;
            } else {
                return false;
            }
        } else {
            throw new InvalidArgumentException('Invalid checkpoint ID');
        }
    }

    /**
     * Update a checkpoint's data
     * 
     * @param int $checkpoint_id Checkpoint ID
     * @param string $field_name Checkpoint field name. Valid fields: notes, name, url
     * @param string $new_data  New field data
     * @return boolean 
     */
    public function updateCheckpoint($checkpoint_id, $field_name, $new_data) {
        if (filter_var($checkpoint_id, FILTER_VALIDATE_INT)) {
            $update_checkpoint = $this->sendRequest("https://www.montastic.com/checkpoints/update/$checkpoint_id", "<checkpoint><$field_name>$new_data</$field_name></checkpoint>");

            //Check if the update was successful 
            if ($update_checkpoint == ' ') {
                return true;
            } else {
                return false;
            }
        } else {
            throw new InvalidArgumentException('Invalid checkpoint ID');
        }
    }

    /**
     * Create a checkpoint to monitor
     * 
     * @param string $url New URL to monitor
     * @return boolean 
     */
    public function createCheckpoint($url) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $create_checkpoint = $this->sendRequest("https://www.montastic.com/checkpoints/create/", "<checkpoint><url>$url</url></checkpoint>");

            //The API returns null on a successful or a failed attempt
            if ($create_checkpoint == null) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new InvalidArgumentException('Invalid URL');
        }
    }

    /**
     * Send a HTTP request to Montastic's API
     * @param string $url Montastic API URL
     * @param string $post_data Optionally send POST data
     * @return string $result Result data
     * @throws Exception Exception thrown if login credentials are invalid
     */
    private function sendRequest($url, $post_data = null) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, "$this->username:$this->password");

        if ($post_data !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-type: application/xml'));
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        switch ($httpcode) {
            case 200:
                return $result;
                break;
            case 401:
                throw new Exception('Unable to login to Montastic');
                break;
        }
    }

}