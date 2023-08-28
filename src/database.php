<?php


namespace ACCOUNT_MANAGER {

    class Database {

        private $db = null;

        function __construct($dbadapter) {

            $this->db = $dbadapter;

        }

        function get_dbadapter() {
            return $this->db;
        }

        function init_db() {

            if(!$this->db->isConnected()) $this->db->connect();

            $this->db->exec('CREATE TABLE IF NOT EXISTS accounts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                birthdate TEXT,
                email TEXT UNIQUE,
                mobile TEXT UNIQUE,
                privacy_status TEXT NOT NULL DEFAULT "private",
                delete_on TEXT DEFAULT NULL
            );');

            $this->db->exec('CREATE TABLE IF NOT EXISTS permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                accounts_id INTEGER,
                name TEXT NOT NULL,
                value INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY(accounts_id) REFERENCES accounts(id)
            );');

        }

        function connect() {
            $this->db->connect();
        }

        function close() {
            $this->db->close();
        }

        function register_account($username, $password, $birthdate = null, $email = null, $mobile = null, $privacy_status = 'private') {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->execPrepared('INSERT INTO `accounts` (`username`, `password`, `birthdate`, `email`, `mobile`, `privacy_status`) VALUES (:username, :password, :birthdate, :email, :mobile, :privacy_status);', array(':username' => $username, ':password' => $password, ':birthdate' => $birthdate, ':email' => $email, ':mobile' => $mobile, ':privacy_status' => $privacy_status));
            return $res;
        }

        function login_account($username_or_email, $password) {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->queryPrepared('SELECT * FROM `accounts` WHERE (`username` = :uoe OR `email` = :uoe) AND `password` = :password;', array(':uoe' => $username_or_email, ':password' => $password));
            if($res) {
                return (new AccountResults($this, $res))->getAccount();
            }
        }

        function delete_account($id, $password, $offset_days = '+30 days') {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->execPrepared('UPDATE `accounts` SET `delete_on` = DATE("now", :offdays) WHERE `id` = :id AND `password` = :password;', array(':id' => $id, ':password' => $password, ':offdays' => $offset_days));
            return $res;
        }

        function get_account_permissions($account_id, $permission_name = '%') {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->queryPrepared('SELECT * FROM `permissions` WHERE `accounts_id` = :accID AND `name` LIKE :permNAME;', array(':accID' => $account_id, ':permNAME' => $permission_name));
            if($res) {
                return new PermissionResults($this, $res);
            }
        }

        function set_account_permission($account_id, $permission_name, $permission_value) {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->upsert('INSERT INTO `permissions` (`accounts_id`, `name`, `value`) VALUES (:accID, :permName, :permValue);', 'UPDATE `permissions` SET `value` = :permValue WHERE accounts_id = :accID AND `name` = :permName;', array(':accID' => $account_id, ':permName' => $permission_name, ':permValue' => $permission_value));
            return $res;
        }

    }


}
