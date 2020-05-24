<?php


$regexTemplate = [
    "@utf8date@"=>"[A-Z][a-z]{2}[\s][A-Z][a-z]{2}[\s][\d]{2}[\s][\d]{2}[:][\d]{2}[:][\d]{2}[\s][\d]{4}",

    "@ip@" => "[\d]+[.][\d]+[.][\d]+[.][\d:]+",
    "@email@" => "[\w.]+[@][a-z]+[.].[a-z]{0,3}",
    "@date@" => "[\d]+[-\/.][\d\w]+[-\/.][0-9]+",
    "@time@" => "[\d]{2}[:][\d]{2}",
    "@mac@" => "[0-9a-zA-Z]{2}[-:][0-9a-zA-Z]{2}[-:][0-9a-zA-Z]{2}[-:][0-9a-zA-Z]{2}[-:][0-9a-zA-Z]{2}[-:][0-9a-zA-Z]{2}",
    "@space@"=>"[\s]+",
    "@url@"=>"[w]{3}[.][\w]+[.][a-z]{1,3}"
];

function checkTempalte($str)
{
    global $regexTemplate;
    foreach ($regexTemplate as $key => $val) {
        preg_match_all("/$val/", $str, $output);
        foreach ($output as $val) {
            $str = str_replace($val, $key, $str);
        }
    }

    return ($str);
}

function compareString($str1, $str2)//сравнивает первую со второй
{
    $lengthStr1 = strlen($str1);
    $lengthStr2 = strlen($str2);
    $maxCompare = "";

    for ($i = 0; $i < max($lengthStr1, $lengthStr2); $i++) {
        $letter1 = $str1[$i];
        $letter2 = $str2[$i];
        if ($letter1 == $letter2) {
            $maxCompare .= $letter1;
        } else {
            return $maxCompare;
        }
    }
}

function correctRegular($reg)
{
    $lengthReg = strlen($reg);
    if ($reg[$lengthReg - 1] == "[") {
        return substr($reg, 0, $lengthReg - 1);
    }
    return $reg;

}

function getRangeTemplate($str)
{
    global $regexTemplate;
    $range = [];
    foreach ($regexTemplate as $key => $val) {
        $count = substr_count($str, $key);

        $prevPos = 0;
        for ($i = 0; $i < $count; $i++) {
            $pos = strpos($str, $key, $prevPos);
            $prevPos = $pos + strlen($key);
            for ($j = $pos; $j < $prevPos; $j++) {
                $range[] = $j;
            }
        }
    }
    return $range;
}

function splitExpression($expression)
{
    echo $expression . "<br>";
    $lengthStr = strlen($expression);
    $finalRow = "";
    $arrExpression = [];
    for ($i = 0; $i < $lengthStr; $i++) {
        $letter = $expression[$i];


        if ($letter == "@") {
            $i++;
            $row = $letter;
            do {
                $nextSymbol = $expression[$i];
                $row .= $nextSymbol;
                $i++;
            } while ($nextSymbol != "@");

            $finalRow .= $row;

            $arrExpression[] = $finalRow . ".*";
            $i--;
        }
        else{
            $finalRow .=$letter;
        }
    }
    echo "<pre>";
    print_r ($arrExpression);

}


function getReg($str)
{
    global $regexTemplate;
    $str = checkTempalte($str);
    $range = getRangeTemplate($str);

    $expression = "";
    $length = strlen($str);
    $prevLetter = "";
    for ($i = 0; $i < $length; $i++) {
        $letter = $str[$i];

        if (in_array($i, $range)) {
            $expression .= $str[$i];
            $prevLetter = "";
            continue;
        }


        if (preg_match('/[^A-Za-z\d\s]/', $letter)) {
            if (!preg_match('/[^A-Za-z\d\s]/', $prevLetter)) {
                $expression .= '[^A-Za-z\d]';
            } else {
                if ($expression[strlen($expression)] == "]") $expression .= "+";

            }
        } elseif (preg_match('/[A-Za-z]/', $letter)) {
            if (!preg_match('/[A-Za-z]/', $prevLetter)) {
                $expression .= '[A-Za-z]';
            } else {
                if ($expression[strlen($expression) - 1] == "]") $expression .= "+";

            }
        } elseif (preg_match('/\d/', $letter)) {
            if (!preg_match('/\d/', $prevLetter)) {
                $expression .= '[\d]';
            } else {
                if ($expression[strlen($expression) - 1] == "]") $expression .= "+";


            }
        } elseif (preg_match('/\s/', $letter)) {
            if (!preg_match('/\s/', $prevLetter)) {
                $expression .= '[\s]';
            }else {
                if ($expression[strlen($expression) - 1] == "]") $expression .= "+";


            }
        }
        $prevLetter = $letter;


    }

    return $expression;

}

function test()
{
    $arrayTestStr =
        ["[Thu Nov 01 21:54:03 2012] [error] [client 1.2.3.4] File does not exist: /usr/local/apache2/htdocs/default/cpc",
            "[Thu Nov 01 21:56:32 2012] [error] (146)Connection refused: proxy: AJP: attempt to connect to 1.2.3.4:8080 (dev1) failed",
            "[Thu Nov 01 21:56:32 2012] [error] ap_proxy_connect_backend disabling worker for (dev1)",
            "[Thu Nov 01 21:56:32 2012] [error] proxy: AJP: failed to make connection to backend: dev1",
            "[Thu Nov 01 21:56:35 2012] [error] (146)Connection refused: proxy: AJP: attempt to connect to 1.2.3.4:8012 (dev1) failed",
            "[Thu Nov 01 21:56:35 2012] [error] ap_proxy_connect_backend disabling worker for (dev1)",
            "[Thu Nov 01 21:56:35 2012] [error] proxy: AJP: failed to make connection to backend: dev1"];
    echo "<pre>";
    print_r($arrayTestStr);

    $regexArray = [];
    foreach ($arrayTestStr as $str) {
        $regexArray[] = getReg($str);

    }

    echo "<pre>";
    print_r($regexArray);
    $firstTest = $regexArray[0];
    $fRegexArray = [];
    foreach ($regexArray as $key => $str) {
        for ($i = $key; $i < count($regexArray); $i++) {
            $res = correctRegular(compareString($regexArray[$i], $str));
            if (!in_array($res, $fRegexArray) && $res != "") {

                $fRegexArray[] = $res;
            }
        }
    }
    echo "<pre>";
    print_r($fRegexArray);
}

//foreach($fRegexArray as $str){
//
//}
//
//preg_match_all("/".$fRegexArray[0]."/",$mainText,$output);
//print_r($output);
$action = $_POST['a'];

if (isset($action)) {
    switch ($action) {
        case "generateRegular":
            $text = explode("\n", $_POST['text']);
            $regexArray = [];
            foreach ($text as $str) {
                $regexArray[] = getReg($str);

            }
            $firstTest = $regexArray[0];
            $fRegexArray = [];
            foreach ($regexArray as $key => $str) {
                for ($i = $key; $i < count($regexArray); $i++) {
                    $res = correctRegular(compareString($regexArray[$i], $str));
                    if (!in_array($res, $fRegexArray) && $res != "") {

                        $fRegexArray[] = $res;
                    }
                }
            }
            echo json_encode($fRegexArray);
//            echo json_encode($regexArray);
            break;
        case 'getMatches':
            $text = $_POST['text'];

            $expression = $_POST['reg'];

            foreach ($regexTemplate as $key=>$val){
                $expression = str_replace($key, $val, $expression);
            }

            if ($text != "" && $expression != "") {
                preg_match_all($expression, $text, $outPut);
                echo json_encode($outPut);
            }

            break;
    }

}
//splitExpression("@email@[\s]@email@[\s]@ip@[\s]+@email@[\s][A-Za-z]+");