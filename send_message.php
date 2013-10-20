<?php

require('textio.php');

$message = "RUBUS (41411) has moved to this new phone number (848-999-3001). Sorry for the downtime. Tell your friends.";
send_message('17327252998', $message);

$handle = fopen("users", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $numba = trim($line);

        print $numba."\n";
    }
}
