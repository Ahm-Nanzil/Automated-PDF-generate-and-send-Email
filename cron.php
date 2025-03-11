<?php
### protect script from being run by the browser
if (php_sapi_name() !='cli') { exit; }

### includes
(@require_once __DIR__ . '/vendor/autoload.php') or lp('Require autoload failed', 1);
(@require_once __DIR__ . '/input/config.php') or lp('Require config failed', 1);

### are we set to go?
if(!$conf['enabled']) { exit; }

### set timezone
date_default_timezone_set($conf['timezone']);

### define program files
$input  = __DIR__ . '/input/list.csv';
$report = __DIR__ . '/system/log/report.log';
$db     = __DIR__ . '/system/db/state.db';

## define template files
$startTmplMail   = __DIR__ . '/templates/system/start_mail.html';
$reportTmplPdf   = __DIR__ . '/templates/system/report_pdf.html';
$reportTmplMail  = __DIR__ . '/templates/system/report_mail.html';
$invoiceTmplPdf  = __DIR__ . '/templates/invoices/pdf_'.$conf['country'].'.html';
$invoiceTmplMail = __DIR__ . '/templates/invoices/mail_'.$conf['country'].'.html';

### check files
if(!is_file($input)) { lp('No input file available', 1); }
$hash = md5_file($input);
if(!is_file($report) || !is_file($db)) { if(!initSsm($hash, $db, $report)) { lp('Could not initialise', 1); }}

### check current state
$state = getState($db);
if($hash !== $state['hash']) {
    if(!initSsm($hash, $db, $report)) { lp('Could not re-initialise', 1); }
    $state = getState($db);
}
if(isset($state['total']) && ($state['total'] === $state['last'])) {  lp('All work is done', 1); }

### get csv input data
try {
    $csv = new SplFileObject($input);
    $csv->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
    $csv->seek(0);
    $header = $csv->current();
    $csv->seek(PHP_INT_MAX);
    $total = $csv->key()-1;
    if($conf['debug']) { echo "total lines: $total\n"; }
} catch (RuntimeException $e) {
    lp($e->getMessage(), 1);
}

### prepare templates
if (false === ($invoicePdfHtml = @file_get_contents($invoiceTmplPdf))) lp('No invoice template file available', 1);
if (false === ($invoiceMailHtml = @file_get_contents($invoiceTmplMail))) lp('No mail template file available', 1);
$business = $conf['business'][$conf['country']];
$business['date'] = date('d-m-Y');
$business['confCountry'] = $conf['country'];
preg_match_all('/{{([0-9A-Za-z_\s,\.-\/]+)}}/', $invoicePdfHtml, $invoiceMatches);

### prepare batch logic
$last = $state['last'];
$left = $total - $last;
if($conf['max_per_run'] > 20) { $conf['max_per_run'] = 20; }
$max  = ($left < $conf['max_per_run']) ? ($last+$left)-1 : ($last+$conf['max_per_run'])-1;
if($conf['debug']) { echo "batch start at line $last end at line $max\n"; }
$errors = [];

### prepare e-mail transport settings
if(!$conf['smtp_account']) {
    $mailTransport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');
} else {
    $smtp = $conf['smtp_settings'];
    $mailHost = empty($smtp['host']) ? 'localhost' : $smtp['host'];
    $mailPort = (empty($smtp['port']) || !is_numeric($smtp['port'])) ? '25' : $smtp['port'];
    $mailTransport = new Swift_SmtpTransport($mailHost, $mailPort);
    if (!empty($smtp['username']) && !empty($smtp['password'])) {
        $mailTransport->setUsername($smtp['username']);
        $mailTransport->setPassword($smtp['password']);
    }
    if (!empty($smtp['encryption']) && in_array($smtp['encryption'], ['tls', 'ssl', 'TLS', 'SSL'])) {
        $mailTransport->setEncryption($smtp['encryption']);
    }
}

### notify start on first run
if($state['num_run'] == 0) {
    if (false === ($startMail = file_get_contents($startTmplMail))) lp('No report pdf template file available', 1);
    preg_match_all('/{{([0-9A-Za-z_\s,\.-\/]+)}}/', $startMail, $startMatches);
    $startArray = ['total' => $total,'max_per_run' => $conf['max_per_run']];
    foreach($startMatches[1] as $match) {
        $startReplace = isset($startArray[$match]) ? $startArray[$match] : ( isset($business[$match]) ? $business[$match] : '');
        $startMail = str_replace('{{'.$match.'}}', $startReplace, $startMail);
    }
    try {
        $startMessage = new Swift_Message("Batch started for ".$business['myWebsite']." at ".date('Y-m-d H:i:s'));
        if($conf['debug']) {
            $startMessage->setFrom([$conf['debug_mail_from'] => $business['myCompany']])->setTo([$conf['debug_mail_to'] => "The SS Machine (debug)"]);
        } else {
            $startMessage->setFrom([$business['myEmail'] => $business['myCompany']])->setTo([$conf['report_mail'] => "The SS Machine"]);
        }
        $startMessage->setBody(strip_tags($startMail))->addPart($startMail, 'text/html');
        $startMailer = new Swift_Mailer($mailTransport);
        $startMailer->send($startMessage);
    } catch (\Swift_TransportException $e) {
        lp($e->getMessage(), 1);
    }
}

### start benchmark
$batchTimeStart = microtime(true);

### main loop
for ($x=$last; $x<=$max; $x++) {
    $csv->seek($x);
    $record = array_combine($header, $csv->fgetcsv());

    ## report on missing required fields
    foreach($conf['required_fields'] as $field) {
        if (empty($record[$field])) { $errors[$x+2]['required'][] = $field; }
    }
    if(isset($errors[$x+2]['required'])) {
        if($conf['debug']) { echo ($x+2)." requirement(s) failed\n"; }
        $state['last']++;
        continue;
    }

    ## report on invalid email
    $emailArray = [$record['email1'], $business['invoiceBcc'], $business['myEmail']];
    foreach($emailArray as $email) {
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[$x+2]['email'][] = $email;
        }
    }
    if(isset($errors[$x+2]['email'])) {
        if($conf['debug']) { echo ($x+2)." email(s) failed\n"; }
        $state['last']++;
        continue;
    }

    ## create record templates
    $invoiceHtml = $invoicePdfHtml;
    $invoiceMail = $invoiceMailHtml;
    foreach($invoiceMatches[1] as $match) {
        $invoiceReplace = isset($record[$match]) ? $record[$match] : ( isset($business[$match]) ? $business[$match] : '');
        $invoiceHtml = str_replace('{{'.$match.'}}', $invoiceReplace, $invoiceHtml);
        $invoiceMail = str_replace('{{'.$match.'}}', $invoiceReplace, $invoiceMail);
    }

    ## create customer invoice PDF:
    $invoiceName = $business['invoiceTranslation']."_".$record['invoiceNumber'].'.pdf';
    try {
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($invoiceHtml );
        if($conf['debug']) { $mpdf->Output('output/'.$invoiceName,'F'); }
        $invoicePDF = $mpdf->Output('', 'S');
    } catch (\Mpdf\MpdfException $e) {
        if($conf['debug']) { echo ($x+2)." pdf failed\n"; }
        $errors[$x+2]['pdf'] = $e->getMessage();
        lp($e->getMessage());
        $state['last']++;
        continue;
    }

    ## create customer e-mail
    try {
        $invoiceAttachment = new Swift_Attachment($invoicePDF, $invoiceName, 'application/pdf');
        $invoiceMessage = new Swift_Message(ucfirst($business['invoiceTranslation'])." ".$record['invoiceNumber']);
        if($conf['debug']) {
            $invoiceMessage->setFrom([$conf['debug_mail_from'] => $business['myCompany']])->setTo([$conf['debug_mail_to'] => "The SS Machine (debug)"]);
        } else {
            $invoiceMessage->setFrom([$business['myEmail'] => $business['myCompany']])->setTo([$record['email1'] => $record['contact']]);
            if(isset($business['invoiceBcc']) && !empty($business['invoiceBcc'])) {
                $invoiceMessage->setBcc([$business['invoiceBcc'] => $business['myCompany']]);
            }
        }
        $invoiceMessage->setBody(strip_tags($invoiceMail))
            ->addPart($invoiceMail, 'text/html')
            ->attach($invoiceAttachment);
        $invoiceMailer = new Swift_Mailer($mailTransport);
        $invoiceMailer->send($invoiceMessage);
    } catch (\Swift_TransportException $e) {
        if($conf['debug']) { echo ($x+2)." mail failed\n"; }
        $errors[$x+2]['mail'] = $e->getMessage();
        lp($e->getMessage());
        $state['last']++;
        continue;
    }

    ## update last record and sleep
    $state['last']++;
    usleep(rand(500000, 2500000));
}

### end benchmark
$batchDuration = microtime(true) - $batchTimeStart;

### cleanup csv handle
unset($csv); // close csv

### update state in db
if (file_put_contents($db, json_encode([
        'hash'          => $hash,
        'time_start'    => $state['time_start'],
        'num_run'       => (int)$state['num_run']+1,
        'avg_run_time'  => round((($state['avg_run_time'] * $state['num_run']) + $batchDuration) / ((int)$state['num_run']+1), 3),
        'last'          => $state['last'],
        'total'         => $total
    ]), LOCK_EX)===false) lp('Progress update failed', 1);

### update report errors
if(!empty($errors)) {
    if (false === ($lastReport = file_get_contents($report))) lp('Could not read report log file', 1);
    $lastReportArr = !empty($lastReport) ? json_decode($lastReport) : [];
    array_push($lastReportArr, $errors);
    if (file_put_contents($report, json_encode($lastReportArr))===false) lp('Report update failed', 1);
}

### send report on last run
if($state['last'] == $total) {

    ## create report record templates
    $now = time();
    $lastState = getState($db);
    $lastState['time_total']    = secondsToTime($now-$lastState['time_start']);
    $lastState['time_start']    = date('Y-m-d H:i:s', $lastState['time_start']);
    $lastState['time_end']      = date('Y-m-d H:i:s', $now);
    $lastState['num_true']      = date('Y-m-d H:i:s', $now);
    $lastState['num_runs']      = (int)$lastState['num_run']+1;
    $lastState['error_count']   = 0;
    $lastState['report_errors'] = "";
    $lastState['max_per_run']   = $conf['max_per_run'];
    if (false === ($reportPerRun = file_get_contents($report))) lp('Could not read end report log file', 1);
    if(!empty($reportPerRun)) {
        foreach(json_decode($reportPerRun, true) as $errorsPerRun) {
            $lastState['error_count'] = $lastState['error_count']+count($errorsPerRun);
            foreach($errorsPerRun as $lineNumber => $errorArray) {
                $errorType = key($errorArray);
                $lastState['report_errors'] .= "<tr><td>".((int)$lineNumber)."</td><td>$errorType</td><td>".implode(';', $errorArray[$errorType])."</td></tr>";
            }
        }
    }
    $lastState['success_total'] = $total - $lastState['error_count'];
    if(empty($lastState['report_errors'])) { $lastState['report_errors'] = "<tr><td colspan='3'>No errors found</td></tr>"; }
    if (false === ($reportHtml = file_get_contents($reportTmplPdf))) lp('No report pdf template file available', 1);
    if (false === ($reportMail = file_get_contents($reportTmplMail))) lp('No report mail template file available', 1);
    preg_match_all('/{{([0-9A-Za-z_\s,\.-\/]+)}}/', $reportHtml, $reportMatches);
    foreach($reportMatches[1] as $match) {
        $replace = isset($lastState[$match]) ? $lastState[$match] : ( isset($business[$match]) ? $business[$match] : '');
        $reportHtml = str_replace('{{'.$match.'}}', $replace, $reportHtml);
        $reportMail = str_replace('{{'.$match.'}}', $replace, $reportMail);
    }

    ## create report PDF
    $reportName = 'final_report_'.date('YmdHis').'.pdf';
    try {
        $mpdf = new \Mpdf\Mpdf(['setAutoBottomMargin' => 'stretch']);
        $mpdf->WriteHTML($reportHtml);
        if($conf['debug']) { $mpdf->Output('output/'.$reportName,'F'); }
        $reportPDF = $mpdf->Output('', 'S');
    } catch (\Mpdf\MpdfException $e) {
        lp($e->getMessage(), 1);
    }

    ## create report e-mail
    try {
        $reportAttachment = new Swift_Attachment($reportPDF, $reportName, 'application/pdf');
        $reportMessage = new Swift_Message("Batch finished for ".$business['myWebsite']." at ".date('Y-m-d H:i:s'));
        if($conf['debug']) {
            $reportMessage->setFrom([$conf['debug_mail_from'] => $business['myCompany']])->setTo([$conf['debug_mail_to'] => "The SS Machine (debug)"]);
        } else {
            $reportMessage->setFrom([$business['myEmail'] => $business['myCompany']])->setTo([$conf['report_mail'] => "The SS Machine"]);
        }
        $reportMessage->setBody(strip_tags($reportMail))
            ->addPart($reportMail, 'text/html')
            ->attach($reportAttachment);
        $reportMailer = new Swift_Mailer($mailTransport);
        $reportMailer->send($reportMessage);
    } catch (\Swift_TransportException $e) {
        lp($e->getMessage(), 1);
    }
}


/*
 * desc: Initialise required database and report files
 * name: initSsm
 * @param: $hash - input file hash, $db - database file path, $report - report file path
 * @return: bool
 */
function initSsm($hash, $db, $report) {
    @unlink($db);
    $initDb = json_encode(['hash' => $hash,'time_start' => time(),'num_run' => 0,'avg_run_time' => 0,'last' => 0]);
    if(!file_put_contents($db, $initDb)) { lp('Could not initialise state db file', 1); }
    @unlink($report);
    if(!touch($report)) { lp('Could not initialise report log file', 1); }
    return true;
}

/*
 * desc: get the current state from flat database file
 * name: getState
 * @param: $db file
 * @return: array $state
 *
 */
function getState($db) {
    if (false === ($stateRaw = @file_get_contents($db))) lp('No state db file available', 1);
    return json_decode($stateRaw, true);
}

/*
 * desc: Log to file and keep processing or die
 * name: lp
 * @param: $message, $die
 * @return: die or true
 */
function lp($message, $die = false) {
    $log = __DIR__ . '/system/log/error.log';
    if(!is_file($log)) { touch($log); }
    $message = sprintf( '[%s] %s', date('Y-m-d H:i:s'), $message);
    file_put_contents($log, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
    if($die) { die($message); }
    return true;
}

/*
 * desc: Format timestamps to human readable output
 * name: secondsToTime
 * @param: timestamp
 * @return: human readable time
 */
function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}
?>
