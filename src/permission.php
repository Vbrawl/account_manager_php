<?php




namespace ACCOUNT_MANAGER {


    class Permission {

        private Database $db;
        private int $account_id;
        private string $name;
        private int $value;


        function __construct(Database $db, int $account_id, string $name, int $value) {
            $this->db = $db;
            $this->account_id = $account_id;
            $this->name = $name;
            $this->value = $value;
        }

        function set_value(?int $new_value = null) : void {
            if($new_value == null) $new_value = $this->value;
            $updated = $this->db->set_account_permission($this->account_id, $this->name, $new_value);
            if($updated) $this->value = $new_value;
        }


        function get_db() : Database {
            return $this->db;
        }

        function get_account_id() : int {
            return $this->account_id;
        }

        function get_name() : string {
            return $this->name;
        }

        function get_value() : int {
            return $this->value;
        }
    }


    class PermissionResults {

        private Database $db;
        private \DATABASE_ADAPTER\RESULTAdapter $results;

        function __construct(Database $db, \DATABASE_ADAPTER\RESULTAdapter $results) {
            $this->db = $db;
            $this->results = $results;
        }

        function getPermission() : ?Permission {
            $row = $this->results->getRowA();
            if($row) return new Permission($this->db, $row['accounts_id'], $row['name'], $row['value']);
        }
    }




}