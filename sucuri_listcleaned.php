<?php

@set_time_limit(0);
@ini_set("max_execution_time",0);
@set_time_limit(0);
@ignore_user_abort(TRUE);


/* If running via terminal. */
if(!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['SHELL']))
{
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

if(!isset($_GET['srun']))
{
    @unlink("sucuri-cleanup.php");
    @unlink("sucuri-version-check.php");
    @unlink("sucuri-wpdb-clean.php");
    @unlink("sucuri_listcleaned.php");
    @unlink("sucuri-db-cleanup.php");
    @unlink("sucuri_db_clean.php");
    @unlink("sucuri-filemanager.php");
    @unlink(__FILE__);
}

$toorder = array();

function scanallfiles($dir)
{
    global $toorder;
    $dh = opendir($dir);
    if(!$dh)
    {
        return(0);
    }

    if($dir == "./")
    {
        $dir = ".";
    }

    while (($myfile = readdir($dh)) !== false)
    {
        if($myfile == "." || $myfile == "..")
        {
            continue;
        }

        if(strpos($myfile, "sucuribackup.") !== FALSE)
        {
            $mydate = explode("_sucuribackup.", $myfile);
            $newpos = strpos($myfile, "_sucuribackup");
            $newfile = substr($myfile, 0, $newpos);

            $path = str_replace("/.sucuriquarantine", "", "$dir/$newfile");
            if (isset($_GET['order']))
            {
                array_push($toorder, array($mydate[1], "$dir/$newfile"));
            }
            else if (isset($_GET['date']))
            {
                echo "File fixed (malware removed): $path [" . @date('Y-m-d H:i:s', $mydate[1]) . "]\n";
            }
            else
            {
                echo "File fixed (malware removed): $path\n";
            }
            continue;
        }


        if(is_dir($dir."/".$myfile))
        {
            scanallfiles($dir."/".$myfile);
        }
       
    }
    closedir($dh);
}


echo "<pre>\n";


/* Scanning all files. */
$dir = "./";
if(isset($_GET['up']))
{
    $dir = "../";
}
if(isset($_GET['upup']))
{
    $dir = "../../";
}
if(isset($_GET['upupup']))
{
    $dir = "../../../";
}
scanallfiles($dir);

if(isset($_GET['order']))
{
    rsort($toorder);
    foreach ($toorder as $key => $value)
    {
        $path = str_replace("/.sucuriquarantine", "", $value[1]);
        echo "File fixed (malware removed): $path [" . @date('Y-m-d H:i:s', $value[0]) . "]\n";
    }
}

echo "</pre>\n"
?>
