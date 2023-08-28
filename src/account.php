<?php



namespace ACCOUNT_MANAGER {

    class Account {

        private $id = null;
        private $username = null;
        private $password = null;
        private $birthdate = null;
        private $email = null;
        private $mobile = null;
        private $privacy_status = 'private';
        private $db = null;

        function __construct($db, $username, $password, $birthdate = null, $email = null, $mobile = null, $privacy_status = 'private', $id = null) {
            $this->db = $db;
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->birthdate = $birthdate;
            $this->email = $email;
            $this->mobile = $mobile;
            $this->privacy_status = $privacy_status;
        }

        function add() {
            $id = $this->db->add_account($this->username, $this->password, $this->birthdate, $this->email, $this->mobile, $this->privacy_status);
            if($id) $this->id = $id;
            return $id;
        }

        function delete() {
            return $this->db->delete_account($this->id, $this->password);
        }

        function get_permissions($permission_name = '%') {
            return $this->db->get_account_permissions($this->id, $permission_name);
        }

        function set_permission($permission_name, $value) {
            return $this->db->set_account_permission($this->id, $permission_name, $value);
        }

        function get_id() {
            return $this->id;
        }

        function get_username() {
            return $this->username;
        }

        function get_password() {
            return $this->password;
        }

        function get_birthdate() {
            return $this->birthdate;
        }

        function get_email() {
            return $this->email;
        }

        function get_mobile() {
            return $this->mobile;
        }

        function get_privacy_status() {
            return $this->privacy_status;
        }

        function get_db() {
            return $this->db;
        }

    }


    class AccountResults {

        private $results = null;
        private $db = null;

        function __construct($db, $results) {
            $this->db = $db;
            $this->results = $results;
        }

        function getAccount() {
            $row = $this->results->getRowA();
            return new Account($this->db, $row["username"], $row["password"], $row["birthdate"], $row["email"], $row["mobile"], $row["privacy_status"], $row["id"]);
        }
    }

}
