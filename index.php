<?php
include 'header.inc';
require 'WSApi.php';
require 'WSApi_Record.php';

/***** CONFIGURATION *****/
$domain = 'php-assignment-6.ws';
$apiKey = '767502fb-b1bb-4f47-bbb1-988677e07cc9';
$secret = 'd88c81f7ceac72ae1634d99508efb3d692229a20';
/***** ************* *****/

$wsApi = new WSApi($domain, $apiKey, $secret);

$referer = $_SERVER['HTTP_REFERER'] ?? '?';
$action = $_GET['a'] ?? 'list';

if(isset($_POST) && !empty($_POST)) {
    if($action == 'add') {
        $new = $wsApi->postRecord(new WSApi\Record(filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING)));
        
        $type = $_GET['type'] ?? NULL;
        if($type !== NULL || array_key_exists($type, $wsApi::TYPES))
            header('Location: ?a=list&type='.$type);
    }
}

switch($action) {
    case 'add':
        $type = $_GET['type'] ?? NULL;
        if($type === NULL || !array_key_exists($type, $wsApi::TYPES))
            header('Location: ?a=add&type=A');
        
        require('form.inc');
    break;

    case 'delete':
        if(isset($_GET['id']))
            $del = $wsApi->deleteRecord($_GET['id']);
        
        header('Location: '.$referer);
    break;

    case 'list':
    default:
        $type = $_GET['type'] ?? NULL;
        if($type === NULL || !array_key_exists($type, $wsApi::TYPES))
            header('Location: ?a=list&type=A');
                
        $records = $wsApi->getRecords($type);
        
        echo "<nav>";
        echo "<ul>";
        foreach($wsApi::TYPES as $recordType => $recordValues)
            echo "<li><a href='?a=list&type={$recordType}' class='".($recordType == $type ? "active" : "")."'>{$recordType}</a></li>";
        echo "</ul>";
        echo "</nav>";
        
        if(count($records) !== 0) {
            echo "<table border='1'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col'>Typ</th>";
            foreach($wsApi::TYPES[$type] as $col => $value)
                echo "<th scope='col'>{$value}</th>";
            echo "<th scope='col'>Možnosti</th>";
            echo "</tr>";
            echo "</thead>";
            foreach($records as $record) {
                echo "<tr>";
                echo "<td>".$record->type."</td>";
                echo "<td>".($record->name == '@' ? '' : $record->name.'.').$domain."</td>";
                echo "<td>".$record->content."</td>";
                if($record->type == 'MX')
                    echo "<td>".$record->prio."</td>";
                if($record->type == 'SRV') {
                    echo "<td>".$record->port."</td>";
                    echo "<td>".$record->weight."</td>";
                    echo "<td>".$record->prio."</td>";
                }
                echo "<td>".$record->ttl."</td>";
                echo "<td>".$record->note."</td>";
                echo "<td><!--<a href='?a=edit&id=".$record->id."'>Upraviť</a>--><a href='?a=delete&id=".$record->id."' class='button small'>Zmazať</a></td>";
                echo "</tr>";
            }                
            echo "</table>";
        } else {
            echo "<p class='notice'>Žiadne {$type} záznamy neboli nájdené.</p>";
        }
        
        echo "<a href='?a=add&type={$type}' class='button'>Pridať nový {$type} záznam</a>";
    break;
}