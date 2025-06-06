<?php
require_once (realpath(dirname(__FILE__).'/../').'/boot.php');
global $naDebugAll;
$debug_naWebOS_traceroute_data_gathering = $dbgDG = true; //$naDebugAll;
global $dbgDG;
global $naWebOS;

$fn = realpath(dirname(__FILE__)).'/naWebOS_traceroute_data_gathering.css';
$fnWeb = $naWebOS->adjustPath($fn);
echo '<!DOCTYPE html>';
echo '<head>';
echo '<link type="text/css" rel="StyleSheet" href="'.$fnWeb.'?m='.$naWebOS->fileDateTimeStamp($fn).'"   >'.PHP_EOL;
echo '</head>';
echo '<body>';

naWebOS_gather_traceroute_data();

function naWebOS_gather_traceroute_data () {
    naWebOS_gather_desktop_OS_info();
    naWebOS_gather_traceroute_version();
    naWebOS_gather_traceroute_data_for_VPN_outgoing_connections ('Google.com');
    //naWebOS_gather_traceroute_data_for_VPN_outgoing_connections ('HotMail.com');
}

function naWebOS_gather_desktop_OS_info() {
    $xec = 'hostnamectl';
    exec ($xec, $output, $result_code);
    $di = [
        'mainPreClassName' => 'naWebOS_desktopos_info',
        'execString' => $xec,
        'output' => $output,
        'result_code' => $result_code
    ];
    $cmd = [
        'di' => $di,
        '{"HTML_className":"naWebOS-debug-outer-DIV"}' => 'naWebOS-debug-outer-DIV',
        '{"HTML_className":"naWebOS-field-name"}' => 'naWebOS-field-name',
        '{"HTML_className":"naWebOS-field-value"}' => 'naWebOS-field-value',
        '{"HTML_className":"naWebOS-debug-lineRemaining"}' => 'naWebOS-debug-lineRemaining'
    ];
    naWebOS_output_debug_info ($cmd);

    /*
    $regEx = '/\s+\([a-z][A-Z[0-0]\):\s([a-z][A-Z[0-0]\)./)';
    $preg_match_result_code = preg_match ($regEx, $ouput, $matches, PREG_OFFSET_CAPTURE);
    $di = [
        '$regEx' => $regEx,
        'mode' => 'PREG_OFFSET_CAPTURE',
        '$matches' => $matches,
        '$preg_match_result_code' => $preg_match_result_code
    ];
    naWebOS_output_debug_info ($di);
    */
}

function naWebOS_gather_traceroute_version () {
    $xec = 'traceroute --v';
    exec ($xec, $output, $result_code);
    $di = [
        'mainPreClassName' => 'naWebOS_traceroute_version',
        'execString' => $xec,
        'output' => $output,
        'result_code' => $result_code
    ];
    $cmd = [
        'di' => $di,
        '{"HTML_className":"naWebOS-debug-outer-DIV"}' => 'naWebOS-debug-outer-DIV',
        '{"HTML_className":"naWebOS-field-name"}' => 'naWebOS-field-name',
        '{"HTML_className":"naWebOS-field-value"}' => 'naWebOS-field-value',
        '{"HTML_className":"naWebOS-debug-lineRemaining"}' => 'naWebOS-debug-lineRemaining'
    ];
    naWebOS_output_debug_info ($cmd);

    $expectedOutputs = [
        0    => [
            'traceroute (GNU inetutils) 2.5',
            'Copyright (C) 2023 Free Software Foundation, Inc.',
            'License GPLv3+: GNU GPL version 3 or later <https://gnu.org/licenses/gpl.html>.',
            'This is free software: you are free to change and redistribute it.',
            'There is NO WARRANTY, to the extent permitted by law.',
            '',
            'Written by Elian Gidoni.'
        ]
    ];

    $cr = false;
    foreach ($expectedOutputs as $k => $v) {
        if ($result_code === $k && $v == $output[0][$k]) $cr = true;
    }
    return $cr;
}

function naWebOS_gather_traceroute_data_for_VPN_outgoing_connections ($target='Google.com') {
    $xec = 'traceroute '.$target;
    exec ($xec, $output, $result_code);
    $di = [
        'execString' => $xec,
        'output' => $output,
        'result_code' => $result_code
    ];
    $cmd = [
        'di' => $di,
        '{"HTML_className":"naWebOS-debug-outer-DIV"}' => 'naWebOS-debug-outer-DIV',
        '{"HTML_className":"naWebOS-field-name"}' => 'naWebOS-field-name',
        '{"HTML_className":"naWebOS-field-value"}' => 'naWebOS-field-value',
        '{"HTML_className":"naWebOS-debug-lineRemaining"}' => 'naWebOS-debug-lineRemaining'
    ];
    naWebOS_output_debug_info ($cmd);

    foreach ($output as $idx => $line) {
        if ($idx===0) {
            $regEx_targetIP = '/(.*\))/';
            $preg_match_result_code = preg_match ($regEx_targetIP, $line, $matches, PREG_OFFSET_CAPTURE);
            $di = [
                '$idx' => $idx,
                '$line' => $line,
                '$regEx_targetIP' => $regEx_targetIP,
                'mode' => 'PREG_OFFSET_CAPTURE',
                '$matches' => $matches,
                '$preg_match_result_code' => $preg_match_result_code
            ];
            $cmd['di']= $di;
            naWebOS_output_debug_info ($cmd);
        }

        if ($idx > 0) {
            $line1a = $line;
            $regEx_lineDecompiledRXdata = '/\s+([\-\w\d]+)\s+(([\-\w\d]+\.[\-\w\d]+\.[\-\w\d]+\.[\-\w\d]+)\s+(\([\-\w\d]+\.[\-\w\d]+\.[\-\w\d]+\.[\-\w\d]+\))\s+(\d+\.\d+\sms))\s+/';
            $preg_match_result_code = preg_match ($regEx_lineDecompiledRXdata, $line, $matches, PREG_OFFSET_CAPTURE);
            $di = [
                '$idx' => $idx,
                '$line' => $line,
                '$regEx_lineDecompiledRXdata' => $regEx_lineDecompiledRXdata,
                'mode' => 'PREG_OFFSET_CAPTURE',
                '$matches' => $matches,
                '$preg_match_result_code' => $preg_match_result_code
            ];
            $line1a = str_replace ($matches[0][0],'',$line1a);
            $cmd['di']= $di;
            $cmd['{"value:linesRemaining"}'] = $line1a;
            naWebOS_output_debug_info ($cmd);

        }
    }
}

function naWebOS_gather_traceroute_data_for_regular_outgoing_connections () {

}

function naWebOS_calculate_VPN_traceroute_averages_phase001 () {

}

function naWebOS_put_VPN_traceroute_activity_averages_into_DB_couchdb () {

}

function naWebOS_get_new_couchdb_IDs_for_VPN_traceroute_averages () {

}

?>
</body>
</html>
