<?php




namespace ACCOUNT_MANAGER {


    class Permission {

        private $db = null;
        private $account_id = null;
        private $name = '';
        private $value = 0;


        function __construct($db, $account_id, $name, $value) {
            $this->db = $db;
            $this->account_id = $account_id;
            $this->name = $name;
            $this->value = $value;
        }

        function set_value($new_value = null) {
            if($new_value == null) $new_value = $this->value;
            $updated = $this->db->set_account_permission($this->account_id, $this->name, $new_value);
            if($updated) $this->value = $new_value;
        }


        function get_db() {
            return $this->db;
        }

        function get_account_id() {
            return $this->account_id;
        }

        function get_name() {
            return $this->name;
        }

        function get_value() {
            return $this->value;
        }
    }


    class PermissionResults {

        private $db = null;
        private $results = null;

        function __construct($db, $results) {
            $this->db = $db;
            $this->results = $results;
        }

        function getPermission() {
            $row = $this->results->getRowA();
            if($row) return new Permission($this->db, $row['accounts_id'], $row['name'], $row['value']);
        }
    }




}