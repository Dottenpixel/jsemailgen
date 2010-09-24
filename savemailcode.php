<?php

$filename = "emaildat/$_POST[fn].dat";
$somecontent = $_POST["_c"];

// Let's make sure the file exists and is writable first.
if ($handle = fopen($filename, 'w')) {

    // In our example we're opening $filename in append mode.
    // The file pointer is at the bottom of the file hence
    // that's where $somecontent will go when we fwrite() it.

    // Write $somecontent to our opened file.
    if (fwrite($handle, $somecontent) === FALSE) {
        echo "Cannot write to file ($filename)";
        exit;
    }

    echo "Success, wrote ($somecontent) to file ($filename)";

    fclose($handle);

} else {
    echo "The file $filename is not writable";
    echo "Cannot open file ($filename)";
}

?>