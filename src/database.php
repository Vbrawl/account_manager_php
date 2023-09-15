<?php


namespace ACCOUNT_MANAGER {

    class Database {

        private \DATABASE_ADAPTER\DBAdapter $db;

        function __construct(\DATABASE_ADAPTER\DBAdapter $dbadapter) {

            $this->db = $dbadapter;

        }

        function get_dbadapter() : \DATABASE_ADAPTER\DBAdapter {
            return $this->db;
        }

        function init_db() : void {

            if(!$this->db->isConnected()) $this->db->connect();

            $this->db->exec('CREATE TABLE IF NOT EXISTS accounts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                birthdate TEXT,
                email TEXT UNIQUE,
                mobile TEXT UNIQUE,
                privacy_status TEXT NOT NULL DEFAULT "private",
                creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                deletion_date DATETIME DEFAULT NULL
            );');

            $this->db->exec('CREATE TABLE IF NOT EXISTS permissions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                accounts_id INTEGER,
                name TEXT NOT NULL,
                value INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY(accounts_id) REFERENCES accounts(id)
            );');

        }

        function connect() : void {
            $this->db->connect();
        }

        function close() : void {
            $this->db->close();
        }

        function register_account(string $username, string $password, ?string $birthdate = null, ?string $email = null, ?string $mobile = null, string $privacy_status = 'private') : ?int {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->execPrepared('INSERT INTO `accounts` (`username`, `password`, `birthdate`, `email`, `mobile`, `privacy_status`) VALUES (:username, :password, :birthdate, :email, :mobile, :privacy_status);', array(':username' => $username, ':password' => $password, ':birthdate' => $birthdate, ':email' => $email, ':mobile' => $mobile, ':privacy_status' => $privacy_status));
            if($res)
                return $this->db->lastInsertRowID();
        }

        function login_account(string $username_or_email, string $password) : ?Account {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->queryPrepared('SELECT * FROM `accounts` WHERE (`username` = :uoe OR `email` = :uoe) AND `password` = :password AND (deletion_date IS NULL OR deletion_date > datetime("now"));', array(':uoe' => $username_or_email, ':password' => $password));
            if($res) {
                $account = (new AccountResults($this, $res))->getAccount();
                if($account !== null)
                    $this->db->execPrepared('UPDATE `accounts` SET deletion_date = NULL WHERE id=:id', array(':id' => $account->get_id()));
                return $account;
            }
        }

        function get_account(int $id) {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->queryPrepared('SELECT * FROM `accounts` WHERE id=:id', array(':id' => $id));
            if($res) {
                return (new AccountResults($this, $res))->getAccount();
            }
        }

        function delete_account(int $id, string $password, string $offset_days = '+30 days') : bool {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->execPrepared('UPDATE `accounts` SET `delete_on` = DATE("now", :offdays) WHERE `id` = :id AND `password` = :password;', array(':id' => $id, ':password' => $password, ':offdays' => $offset_days));
            return $res;
        }

        function get_account_permissions(int $account_id, string $permission_name = '%') : ?PermissionResults {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->queryPrepared('SELECT * FROM `permissions` WHERE `accounts_id` = :accID AND `name` LIKE :permNAME;', array(':accID' => $account_id, ':permNAME' => $permission_name));
            if($res) {
                return new PermissionResults($this, $res);
            }
        }

        function set_account_permission(int $account_id, string $permission_name, int $permission_value) : bool {
            if(!$this->db->isConnected()) $this->db->connect();
            $res = $this->db->upsert('INSERT INTO `permissions` (`accounts_id`, `name`, `value`) VALUES (:accID, :permName, :permValue);', 'UPDATE `permissions` SET `value` = :permValue WHERE accounts_id = :accID AND `name` = :permName;', array(':accID' => $account_id, ':permName' => $permission_name, ':permValue' => $permission_value));
            return $res;
        }

    }


}
