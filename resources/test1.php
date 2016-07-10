<?php$ip = $_SERVER["SERVER_ADDR"];$par = $_GET["par"];$redis = new Redis();if ($ip == "127.0.0.1") {    $redis->connect("192.168.253.253", 6379);} else {    $redis->connect($ip, 6379);}$arList = $redis->keys("*");if ($par == "filePublish") {    //$pv = current($redis->hmGet('Send_File', Array("Present_Value")));    $pv = $redis->hGet('Send_File', "Present_Value");    $key = $_GET["key"];    $value = $_GET["value"];    if ($pv == "1") {        $redis->publish($key, $value);        echo $pv;    } else {        echo $pv;    }}if ($par == "devPublish") {    $key = $_GET["key"];    $value = $_GET["value"];    echo $redis->publish($key, $value);}if ($par == "delFile") {    $fn = $_GET['fileName'];    echo file_put_contents($fn, "");    //echo unlink($fn);}if ($par == "clear") {    if (file_exists("/var/run/bac_client.pid")) {        $myfile = fopen("/var/run/bac_client.pid", "r");        $jc = fgets($myfile);        $test = "kill " . $jc;        exec($test, $array);    }    if (file_exists("/var/run/bip_client.pid")) {        $myfile1 = fopen("/var/run/bip_client.pid", "r");        $jc1 = fgets($myfile1);        $test1 = "kill " . $jc1;        exec($test1, $array1);    }    echo $redis->delete($arList);    //$redis->save();}if ($par == 'devRename') {    $devname = $_GET['devname'];    $keys = $redis->keys("1100*");}if ($par == "getKeys") {    $devname = $_GET['devname'];    $keys = $redis->keys($devname . "*");    $arr = [];    foreach ($keys as $key => $value) {        $types = $redis->hGetAll($value);        $types['key'] = $value;        //$arr=$types;        array_push($arr, $types);    }    echo json_encode($arr);    // echo xmlrpc_encode($arr);}if ($par == "file_exists") {    $fn = $_GET['filename'];    echo file_exists($fn);    /*if(file_exists($fn)){        echo "true";    }else{        echo "false";    }*/}if ($par == "backup") {    $fn = $_GET['filename'];    $file = fopen($fn, "r");    header("Content-Type: application/octet-stream");    header("Accept-Ranges: bytes");    header("Accept-Length: " . filesize($fn));    header("Content-Disposition: attachment; filename=" . $file);    echo fread($file, filesize($fn));    fclose($file);}if ($par == "getAlarm") {    $nodeName = $_GET["nodename"];    $type = "Alarm";    echo $redis->hGet($nodeName, $type);}if ($par == "addAlarm") {    $nodeName = $_GET["nodename"];//$high_limit=$_POST["high_limit"];    //$low_limit=$_POST["low_limit"];    $delay_time = $_POST["delay_time"];    //$deadband=$_POST["deadband"];    $notification_class = $_POST["notification_class"];    //$limit=$_POST["limit"];    $limit = isset($_POST["limit"]) ? "\"limit\":" . $_POST["limit"] . "," : "";    $event_enable = $_POST["event_enable"];    $high_limit = isset($_POST["high_limit"]) ? "\"high_limit\":" . $_POST["high_limit"] . "," : "";    $low_limit = isset($_POST["low_limit"]) ? "\"low_limit\":" . $_POST["low_limit"] . "," : "";    $deadband = isset($_POST["deadband"]) ? "\"deadband\":" . $_POST["deadband"] . "," : "";    $event_type = isset($_POST["event_type"]) ? "\"event_type\":" . $_POST["event_type"] . "," : "";    $alarm_value = isset($_POST["alarm_value"]) ? "\"alarm_value\":" . $_POST["alarm_value"] . "," : "";    $type = "Alarm";    $value = "{\"Set_Alarm\":[{" .//"\"high_limit\":".$high_limit.",".//"\"low_limit\":".$low_limit.",".//"\"deadband\":".$deadband.",".        $high_limit . $low_limit . $deadband .        "\"delay_time\":" . $delay_time . "," .        "\"notification_class\":" . $notification_class . "," .        $event_type . $alarm_value . $limit .        "\"event_enable\":" . $event_enable . "}]}";    $redis->hSet($nodeName, $type, $value);    echo $value;}if ($par == "getAllScheduleNamesOuter") {    $devname = $_GET['devname'];    $schduleType = array("601", "602", "603", "604", "605", "606", "607", "608", "609", "610");    $newArray = Array();    foreach ($arList as $key => $value) {        if (strlen($value) == 7) {            if (in_array(substr($value, 4, 3), $schduleType) & substr($value, 0, 4) != $devname) {                array_push($newArray, $value);            }        }    }    //$newArray=array_fill_keys($newArray,"name");    //$newArray=array_flip($newArray);    echo json_encode($newArray);}if ($par == "ScheduleConfig") {    $nodeName = $_GET["nodename"];    $Object_Name = $_GET["Object_Name"];    if ($redis->hGet($nodeName, "Object_Name") != $Object_Name) {        $redis->hSet($nodeName, "Object_Name", $Object_Name);        if (isset($_GET["ispublish"])) {            $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Object_Name" . "\r\n" . $Object_Name);        }    }    $Present_Value = $_GET["Present_Value"];    if ($redis->hGet($nodeName, "Present_Value") != $Present_Value) {        $redis->hSet($nodeName, "Present_Value", $Present_Value);        if (isset($_GET["ispublish"])) {            $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Present_Value" . "\r\n" . $Present_Value);        }    }    $Description = $_GET["Description"];    if ($redis->hGet($nodeName, "Description") != $Description) {        $redis->hSet($nodeName, "Description", $Description);        if (isset($_GET["ispublish"])) {            $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Description" . "\r\n" . $Description);        }    }    $Priority_For_Writing = $_GET["Priority_For_Writing"];    if ($redis->hGet($nodeName, "Priority_For_Writing") != $Priority_For_Writing) {        $redis->hSet($nodeName, "Priority_For_Writing", $Priority_For_Writing);        if (isset($_GET["ispublish"])) {            $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Priority_For_Writing" . "\r\n" . $Priority_For_Writing);        }    }    if (isset($_GET["after"])) {        $after = $_GET["after"];        $value = trimall('{"dateRange":	{"startDate":{' . dateToJson($after) . '},"endDate":{"year":255,"month":255,"day_of_month":255,"day_of_week":255}}}');        echo $value;        if ($redis->hGet($nodeName, "Effective_Period") != $value) {            $redis->hSet($nodeName, "Effective_Period", $value);            if (isset($_GET["ispublish"])) {                $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Effective_Period" . "\r\n" . $value);            }        }    }    if (isset($_GET["front"])) {        $front = $_GET["front"];        $value = trimall('{"dateRange":	{"startDate":{"year":255,"month":255,"day_of_month":255,"day_of_week":255},"endDate":{' . dateToJson($front) . '}}}');        echo $value;        if ($redis->hGet($nodeName, "Effective_Period") != $value) {            $redis->hSet($nodeName, "Effective_Period", $value);            if (isset($_GET["ispublish"])) {                $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Effective_Period" . "\r\n" . $value);            }        }    }    if (isset($_GET["fromstart"])) {        $fromstart = $_GET["fromstart"];        $fromend = $_GET["fromend"];        $value = trimall('{"dateRange":{"startDate":{' . dateToJson($fromstart) . '},"endDate":{' . dateToJson($fromend) . '}}}');        echo $value;        if ($redis->hGet($nodeName, "Effective_Period") != $value) {            $redis->hSet($nodeName, "Effective_Period", $value);            if (isset($_GET["ispublish"])) {                $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . "Effective_Period" . "\r\n" . $value);            }        }    }}if ($par == "getnullschedule") {    $nodeName = $_GET["nodename"];    $count = array("601", "602", "603", "604", "605", "606", "607", "608", "609", "610");    foreach ($count as $key => $value) {        $is = $redis->exists($nodeName . $value);        if (!$is) {            echo $nodeName . $value;            return;        }    }    echo "null";}//http://127.0.0.1/svgxml/resources/test1.php?par=getvalue&nodename=1100&type=Object_Nameif ($par == "setRenameValue") {    echo print_r($_POST);    echo xmlrpc_encode($_POST);    foreach ($_POST as $type => $value) {        echo $type . $value;    }    //$nodeName = $_GET["nodename"];    //$type = $_GET["type"];    ////echo "{type:'".$type."',value:'"."12313"."'}";    //echo $redis->hGet($nodeName, $type);}if ($par == "changevaluenopublish") {    $nodeName = $_GET["nodename"];    $type = $_GET["type"];    //$value=$_GET["value"];    if (isset($_GET["value"])) {        $value = $_GET["value"];    }    if (isset($_POST["value"])) {        $value = $_POST["value"];    }    //echo "{type:'".$type."',value:'"."12313"."'}";    echo $redis->hSet($nodeName, $type, $value);}if ($par == "changevalue") {    $nodeName = $_GET["nodename"];    $type = $_GET["type"];    if (isset($_GET["value"])) {        $value = $_GET["value"];    }    if (isset($_POST["value"])) {        $value = $_POST["value"];    }    //echo "{type:'".$type."',value:'"."12313"."'}";    echo $redis->hSet($nodeName, $type, $value);    $redis->publish(substr($nodeName, 0, 4) . ".8.*", $nodeName . "\r\n" . $type . "\r\n" . $value);}if ($par == "schedule") {    $str = "";    echo "[";    $nodeName = $_GET["nodename"];    foreach ($arList as $key => $value) {        if (strlen($value) == 7) {            $devName = substr($value, 0, 4);            if (strcmp($devName, $nodeName) == 0) {                //$dev= $redis->sIsMember($value, "Position");                $dev = $redis->hGet($value, 'Position');                if ($dev) {                    $Object_Name = $redis->hGet($value, 'Object_Name');                    $str .= '{allowDrop: false, allowDrag: false,leaf: true, text :"' . $value . '",value:"' . $value . '"},';                }            }        }    }    echo substr($str, 0, strlen($str) - 1);    echo "]";}if ($par == "node") {    $nodeName = $_GET["nodename"];    $sortarr = Array("Object_Identifier", "Object_Name", "Description", "Priority_Array", "Status_Flags", "Max_Pres_Value", "Min_pres_Value", "High_Limit", "Limit_Enable", "COV_Increment", "Event_Enable");    $arList = $redis->hKeys($nodeName);    $arr1 = array_intersect($sortarr, $arList);    $arr2 = array_diff($arList, $sortarr);    $arr3 = array_merge($arr1, $arr2);    $parameters = Array("Object_Name", "Description", "Present_Value", "Max_Pres_Value", "Min_pres_Value", "High_Limit", "Low_Limit", "COV_Increment", "Device_Type", "Offset", "Inactive_Text", "Active_Text");    $event = Array("Event_State", "Event_Enable");    $alarm = Array("Alarm_Enable", "Limit_Enable", "Time_Delay", "Acked_Transitions");    if (isset($_GET["type"])) {        $type = $_GET["type"];        if ($type == "parameters") {            $arr3 = array_intersect($arr3, $parameters);            //echo print_r($arr3);            //"Inactive_Text","Active_Text"            if (isBIBOBV($nodeName)) {                if (!in_array("Inactive_Text", $arr3)) {                    array_push($arr3, "Inactive_Text");                }                if (!in_array("Active_Text", $arr3)) {                    array_push($arr3, "Active_Text");                }            }        } else if ($type == "event") {            $arr3 = array_intersect($arr3, $event);        } else if ($type == "alarm") {            $arr3 = array_intersect($arr3, $alarm);        } else if ($type == "other") {            $arr3 = array_diff($arr3, $parameters);            //echo print_r($arr3);            //echo "<br>";            $arr3 = array_diff($arr3, $event);            $arr3 = array_diff($arr3, $alarm);        }    }    $str = "";    echo "[";    foreach ($arr3 as $key) {        $value = $redis->hGet($nodeName, $key);        $str .= "{type:'" . $key . "',value:'" . $value . "'},";    }    echo substr($str, 0, strlen($str) - 1);    echo "]";}function isBIBOBV($nodeName){    $four = substr($nodeName,4,1);    if($four=="3"||$four=="4"||$four=="5"){        return true;    }    else{        return false;    }}if ($par == "getbackupfiles") {    $dir = "devsinfo";    $scanned_directory = array_diff(scandir($dir), array('..', '.'));    $str = "";    echo "[";    foreach ($scanned_directory as $key => $value) {        //$time =fileatime($dir."/".$value);        date_default_timezone_set("UTC");        $time = date("Y-m-d H:i:s", filectime($dir . "/" . $value));        $size = filesize($dir . "/" . $value);        $filetype = filetype($dir . "/" . $value);        $str .= "{name:'" . $value . "',lasttime:'" . $time . "',size:'" . $size . "',filetype:'" . $filetype . "'},";        //echo filectime($dir."/".$value)."   ";        //if(strlen($value)==4||$value=='local'){        //$str.= "'".$value."',  ".$key."  ";        //}    }    echo substr($str, 0, strlen($str) - 1);    echo "]";}if ($par == "nodes") {    echo "[";    $str = "";    foreach ($arList as $value) {        $value = "$value";        $Object_Name = $redis->hGet($value, 'Object_Name');        if (strlen($value) == 7) {            $str .= '{leaf: true, text :"' . $Object_Name . '",value:"' . $value . '"},';        }    };    echo substr($str, 0, strlen($str) - 1);    echo "]";}if ($par == "getreferencesdev") {    $nodeName = substr($_GET["nodename"], 0, 4);    $newArry = array();    foreach ($arList as $value) {        $value = "$value";        $sfive = substr($value, 4, 1);        //echo $sfive.'<br>';        //if(strlen($value)==7&substr($value,0,4)==$nodeName){        if (strlen($value) == 7) {            if ($sfive == 4 || $sfive == 5) {                array_push($newArry, $value);            }        }    };    echo json_encode($newArry);}if ($par == "getDevInfoFileNames") {    $directory = 'devsinfo';    $newArry = Array();    $scanned_directory = array_diff(scandir($directory), array('..', '.'));    foreach ($scanned_directory as $key => $value) {        array_push($newArry, $value);    }    echo json_encode($newArry);}if ($par == "getDevFileNames") {    $directory = "../../../../";    $newArry = Array();    $scanned_directory = array_diff(scandir($directory), array('..', '.'));    foreach ($scanned_directory as $key => $value) {        if (strlen($value) == 4 & is_numeric($value)) {            array_push($newArry, $value);        }    }    echo json_encode($newArry);}if ($par == "dev") {    echo "[";    $str = "";    foreach ($arList as $value) {        $value = "$value";        if (strlen($value) == 7) {            $str .= $value . ',';        }    };    echo substr($str, 0, strlen($str) - 1);    echo "]";}function dateToJson($riqi){    echo "<br>";    echo $riqi;    echo "<br>";    $riqiarr = explode("-", $riqi);    $ri = current($riqiarr);    $yue = next($riqiarr);    $nian = next($riqiarr);    $zhou = date("W", mktime(0, 0, 0, $yue, $ri, $nian));    $jsstr = '"year":	' . $nian . ',	"month":	' . $yue . ',	"day_of_month":	' . $ri . ',	"day_of_week":	' . $zhou;    return $jsstr;}function trimall($str){    $qian = array(" ", "　", "\t", "\n", "\r");    return str_replace($qian, '', $str);}//$fn=$_POST['fileName'];//$rw=$_POST['rw'];if ($par == "uploadfiles") {    echo move_uploaded_file($_FILES["file"]["tmp_name"], "devsinfo/" . $_FILES["file"]["name"]);}if ($par == 'updateProgram') {    //echo move_uploaded_file($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);    $error = $_FILES['file1']['error'];    if ($error != 0) {        echo "Error File" . $error;    }    $isMove = move_uploaded_file($_FILES["file1"]["tmp_name"], $_FILES["file1"]["name"]);    if ($isMove != 1) {        echo "Error Move";    }    $isTar = popen("tar -xzvf " . $_FILES['file1']['name'] . " 2>&1 -C ../../", 'r');    if (!$isTar) {        echo "Error Tar";    }    echo $error . $isMove . $isTar;    listDir("/mnt/nandflash");}if ($par == "openPermission") {    echo listDir("/mnt/nandflash");}function listDir($dir){    if (is_dir($dir)) {        if ($dh = opendir($dir)) {            while (($file = readdir($dh)) !== false) {                if ((is_dir($dir . "/" . $file)) && $file != "." && $file != "..") {                    // 				echo "<b><font color='red'>文件名：</font></b>",$dir.$file,"<br><hr>";                    listDir($dir . "/" . $file . "/");                } else {                    if ($file != "." && $file != "..") {                        chmod($dir . '/' . $file, 0777);//echo "<br>";                        //       				echo $file."<br>";                    }                }            }            closedir($dh);        }    }}//开始运行?>