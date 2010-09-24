<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link type="text/css" href="/css/custom-theme/jquery-ui-1.7.1.custom.css" rel="stylesheet" />	
<link type="text/css" href="/css/admin.css" rel="stylesheet" />	
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript">
    $(function() {
        $(".datePicker").datepicker();
    });
</script>
</head>

<body>
<div id="adminCont">
    <div id="contentCont">
<?php
function display_upload_form()
{
echo <<<DISPLAY_UPLOAD_FORM

        	<h1>Add Newsletter <a href="viewNewsletters.php">View Newsletters</a></h1>
            <form method="post" action="{$_SERVER['PHP_SELF']}" enctype="multipart/form-data">
        
            <label for="inpFile">Select Newsletter PDF file: <input id="inpFile" type="file" size="30" name="submittedFile" tabindex="1" /></label>
            
        
            <label for="inpTitle">Newsletter Title: <input id="inpTitle" name="pubTitle" size="40" type="text" /></label>
        
            <label for="inpPubDate">Newsletter Date: <input id="inpPubDate" class="datePicker" name="pubDate" size="10" maxlength="10" type="text" /></label>
        
            <label for="inpDesc">Description: <textarea id="inpDesc" name="desc" rows="8" cols="40"></textarea></label>    
            
            <div style="display: none;" class="demo-description">
            
            <p>The datepicker is tied to a standard form input field.  Focus on the input (click, or use the tab key) to open an interactive calendar in a small overlay.  Choose a date, click elsewhere on the page (blur the input), or hit the Esc key to close. If a date is chosen, feedback is shown as the input's value.</p>
            
            </div><!-- End demo-description -->			
        
            <p>
                <input type="hidden" name="execute" value="1" />
                <input type="hidden" name="pubType" value="1" />
                
                <input type="submit" value="Upload" tabindex="2" />
            </p>
        
            </form>

DISPLAY_UPLOAD_FORM;
}

// File Upload ****************************************************************

function execute_upload()
{
	//require_once( "db_connect.php" );
	
    // root path
    $path = $_SERVER['DOCUMENT_ROOT'];

    // upload directory. path will originate from root.
    $dirname = '/charter/ncta/userimg';

    // permission settings for newly created folders
    $chmod = 0755;

    // create file vars to make things easier to read.
    $filename = $_FILES['submittedFile']['name'];
    $filesize = $_FILES['submittedFile']['size'];
    $filetype = $_FILES['submittedFile']['type'];
    $file_tmp = $_FILES['submittedFile']['tmp_name'];
    $file_err = $_FILES['submittedFile']['error'];
    $file_ext = strrchr($filename, '.');

    // check if user actually put something in the file input field.
    if (($file_err == 0) && ($filesize != 0))
    {
        // Check extension.
        if (!$file_ext)
        {
            unlink($file_tmp);
            die('File must have an extension.');
        }

        // extra check to prevent file attacks.
        if (is_uploaded_file($file_tmp))
        {
            /*
            * check if the directory exists
            * if it doesnt exist, make the directory
            */
            $dir = $path . $dirname;

            if (!is_dir($dir))
            {
                
				print $dir;
				$dir = explode('/', $dirname);

                foreach ($dir as $sub_dir)
                {
                    $path .= '/' . $sub_dir;
                    if (!is_dir($path))
                    {
                        if (!mkdir($path, $chmod))
                        {
                            unlink($file_tmp);
                            die('<strong>Error:</strong> Directory does not exist and was unable to be created.');
                        }
                    }
                }
            }

            /*
            * copy the file from the temporary upload directory
            * to its final detination.
            */
            if (@move_uploaded_file($file_tmp, $dir . '/' . $filename))
            {
                // success!

				//write table in db
				//print_r($_POST);
                
                //format pub date for mysql
                // Main Date String
                $dateString = $_POST[pubDate];
                
                // Explode contents to an aray
                $dateArr = explode('/',$dateString);
                
                // Grab some details
                $month = $dateArr[0];
                $day = $dateArr[1];
                $year = $dateArr[2];
                
                // Echo out the contents
                //echo 'DAY=>',$day,' MONTH=>',$month,' YEAR=>',$year;
                
                // Correctly format the date in month/day/year
                $correctFomattedDateString = sprintf('%d/%d/%d',$dateArr[0] ,$dateArr[1],$dateArr[2]);
                
                // Use strtotime to convert it to timestamp
                $formattedPubDate = strtotime($correctFomattedDateString);
                
                // Lets use date function to display it in any means necessary
                //echo 'TIMESTAMP => ', date('l dS \of F Y h:i:s A',$formattedPubDate);
                /*
				$sql = "INSERT INTO `sally_www`.`pr_files` (
				`id` ,
				`type` ,
				`title` ,
				`desc` ,
				`file_path` ,
				`pub_date` ,
				`datestamp`
				)
				VALUES (
					NULL ,
					'$_POST[pubType]',
					'$_POST[pubTitle]',
					'$_POST[desc]',
					'$dirname/$filename',
					FROM_UNIXTIME($formattedPubDate),
					NOW( )
				)";
				$result = mysql_query($sql, $link) or die(mysql_error());
				*/
                //<p><strong>View File:</strong> <a href=\"$dirname/$filename\">$filename</a>" . print_r($_POST) . "</p>
				

                echo "
                <h1>Success!</h1>
                <p><strong>View File:</strong> <a href=\"$dirname/$filename\">$filename</a></p>
                ";
				
				if ($handle = opendir($dir)) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							echo "<div><a href=\"$dirname/$file\" target=\"_blank\" >$file</a></div>\n";
						}
					}
					closedir($handle);
				}
				
            }
            else
            {
                // error moving file. check file permissions.
                unlink($file_tmp);
                echo '<strong>Error:</strong> Unable to move file to designated directory.';
            }
        }
        else
        {
            // file seems suspicious... delete file and error out.
            unlink($file_tmp);
            echo '<strong>Error:</strong> File does not appear to be a valid upload. Could be a file attack.';
        }
    }
    else
    {
        // Kill temp file, if any, and display error.
        if ($file_tmp != '')
        {
            unlink($file_tmp);
        }

        switch ($file_err)
        {
            case '0':
                echo 'That is not a valid file. 0 byte length.';
                break;

            case '1':
                echo 'This file, at ' . $filesize . ' bytes, exceeds the maximum allowed file size as set in <em>php.ini</em>. '.
                'Please contact your system admin.';
                break;

            case '2':
                echo 'This file exceeds the maximum file size specified in your HTML form.';
                break;

            case '3':
                echo 'File was only partially uploaded. This could be the result of your connection '.
                'being dropped in the middle of the upload.';

            case '4':
                echo 'You did not upload anything... Please go back and select a file to upload.';
                break;
        }
    }
}

// Logic Code *****************************************************************

if (isset($_POST['execute']))
{
    execute_upload();
}
else
{
    display_upload_form();
}


?>
	</div>
</div>
</body>
</html>