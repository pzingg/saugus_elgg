<?php
global $db;

/// Check if the main tables have been installed yet or not.
if (! $tables = $db->Metatables() ) {    // No tables yet at all.
    $maintables = false;
} else {
    $maintables = false;
    $datalists = false;
    foreach ($tables as $table) {
        if (preg_match("/^{$CFG->prefix}users$/", $table)) {
            $maintables = true;
        }
        if (preg_match("/^{$CFG->prefix}datalists$/", $table)) {
            $datalists = true;
        }
    }
}

if (!$maintables) {
    if (file_exists($CFG->dirroot . "lib/db/$CFG->dbtype.sql")) {
        $db->debug = true;
        if (modify_database($CFG->dirroot . "lib/db/$CFG->dbtype.sql")) {
            include_once($CFG->dirroot . "version.php");
            set_config('version', $version);
            $db->debug = false;
            notify($strdatabasesuccess, "green");
        } else {
            $db->debug = false;
            error("Error: Main databases NOT set up successfully");
        }
    } else {
        error("Error: Your database ($CFG->dbtype) is not yet fully supported by Elgg.  See the lib/db directory.");
    }
    print_continue("index.php");
    die;
}

if (run("users:flags:get", array("admin",$_SESSION['userid']))) {
    
    if (empty($CFG->version)) {
        $CFG->version = 1;
    }

    if (!$datalists) {
        $CFG->version = -1;
    }

    /// Upgrades
    include_once($CFG->dirroot . "version.php");              # defines $version
    include_once($CFG->dirroot . "lib/db/$CFG->dbtype.php");  # defines upgrades

    if ($CFG->version) {
        if ($version > $CFG->version) {  // upgrade

            $a->oldversion = "$CFG->release ($CFG->version)";
            $a->newversion = "$release ($version)";

            if (empty($_GET['confirmupgrade'])) {
                notice_yesno(gettext('Need to upgrade database'), $CFG->wwwroot . '?confirmupgrade=yes', '');
                exit;

            } else {
                $db->debug=true;
                if (main_upgrade($CFG->version)) {
                    $db->debug=false;
                    if (set_config("version", $version)) {
                        notify($strdatabasesuccess, "green");
                        print_continue("index.php");
                        exit;
                    } else {
                        notify("Upgrade failed!  (Could not update version in config table)");
                    }
                } else {
                    $db->debug=false;
                    notify("Upgrade failed!  See /version.php");
                }
            }
        } else if ($version < $CFG->version) {
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
        }

    } else {
        if (set_config("version", $version)) {
            print_header("Elgg $release ($version)");
            print_continue("index.php");
            die;
        } else {
            $db->debug=true;
            if (main_upgrade(0)) {
                print_continue("index.php");
            } else {
                error("A problem occurred inserting current version into databases");
            }
            $db->debug=false;
        }
    }

}
?>