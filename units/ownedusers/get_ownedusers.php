<?php
global $CFG;
// Gets all the owned users of a particular user, as specified in $parameter[0],
// and return it in a data structure with the idents of all the users
    
$ident = (int) $parameter[0];
/*
        if (!isset($_SESSION['friends_cache'][$ident]) || (time() - $_SESSION['friends_cache'][$ident]->created > 120)) {
            $_SESSION['friends_cache'][$ident]->created = time();
            $_SESSION['friends_cache'][$ident]->data =  get_records_sql('SELECT f.friend AS user_id,u.name FROM '.$CFG->prefix.'friends f
                               JOIN '.$CFG->prefix.'users u ON u.ident = f.friend
                               WHERE f.owner = ?',array($ident));
        }
        $run_result = $_SESSION['friends_cache'][$ident]->data;*/

$run_result = get_records_sql('SELECT ident,name FROM '.$CFG->prefix.'users
                               WHERE owner = ? AND user_type = ?',array($ident,'person'));

                
?>