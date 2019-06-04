<?php
function adminer_object() {
    class AdminerSoftware extends Adminer {
        function login($login, $password) {
            global $jush;
            if ($jush == "sqlite")
                return ($login === 'hapuk') && ($password === 'jordan');
            return true;
        }
        function databases($flush = true) {
            if (isset($_GET['sqlite']))
                return ["../db/sqlite.db"];
            return get_databases($flush);
        }
    }
    return new AdminerSoftware;
}
include "./adminer-4.2.5.php";