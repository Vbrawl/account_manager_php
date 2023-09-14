<?php



namespace ACCOUNT_MANAGER {

    class Account {

        private ?int $id;
        private string $username;
        private string $password;
        private ?string $birthdate;
        private ?string $email;
        private ?string $mobile;
        private string $privacy_status;
        private Database $db;

        function __construct(Database $db, string $username, string $password, ?string $birthdate = null, ?string $email = null, ?string $mobile = null, string $privacy_status = 'private', ?int $id = null) {
            $this->db = $db;
            $this->id = $id;
            $this->username = $username;
            $this->password = $password;
            $this->birthdate = $birthdate;
            $this->email = $email;
            $this->mobile = $mobile;
            $this->privacy_status = $privacy_status;
        }

        function serialize() {
            return array(
                'id' => $this->id,
                'username' => $this->username,
                'password' => $this->password,
                'birthdate' => $this->birthdate,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'privacy_status' => $this->privacy_status
            );
        }

        function deserialize(Database $db, $data) {
            $this->db = $db;
            $this->id = $data['id'];
            $this->username = $data['username'];
            $this->password = $data['password'];
            $this->birthdate = $data['birthdate'];
            $this->email = $data['email'];
            $this->mobile = $data['mobile'];
            $this->privacy_status = $data['privacy_status'];
        }

        function add() : ?int {
            $id = $this->db->register_account($this->username, $this->password, $this->birthdate, $this->email, $this->mobile, $this->privacy_status);
            if($id) $this->id = $id;
            return $id;
        }

        function delete() : bool {
            return $this->db->delete_account($this->id, $this->password);
        }

        function get_permissions(string $permission_name = '%') : ?PermissionResults {
            return $this->db->get_account_permissions($this->id, $permission_name);
        }

        function set_permission(string $permission_name, int $value) : bool {
            return $this->db->set_account_permission($this->id, $permission_name, $value);
        }

        function get_id() : ?int {
            return $this->id;
        }

        function get_username() : string {
            return $this->username;
        }

        function get_password() : string {
            return $this->password;
        }

        function get_birthdate() : ?string {
            return $this->birthdate;
        }

        function get_email() : ?string {
            return $this->email;
        }

        function get_mobile() : ?string {
            return $this->mobile;
        }

        function get_privacy_status() : string {
            return $this->privacy_status;
        }

        function get_db() : Database {
            return $this->db;
        }

    }


    class AccountResults {

        private \DATABASE_ADAPTER\RESULTAdapter $results;
        private Database $db;

        function __construct(Database $db, \DATABASE_ADAPTER\RESULTAdapter $results) {
            $this->db = $db;
            $this->results = $results;
        }

        function getAccount() : ?Account {
            $row = $this->results->getRowA();
            if($row)
                return new Account($this->db, $row["username"], $row["password"], $row["birthdate"], $row["email"], $row["mobile"], $row["privacy_status"], $row["id"]);
            return null;
        }
    }

}
