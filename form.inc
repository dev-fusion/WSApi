<?php 
$inputs = $wsApi::TYPES[$type];

echo "<form method='POST'>";
echo "<input type='hidden' name='type' value='{$type}'>";
echo "<table>";
foreach($inputs as $input => $label) {
    echo "<tr>";
    echo "<td><label for='{$input}'>{$label}:</label></td>";
    echo "<td><input type='".($input == 'ttl' || $input == 'weight' || $input == 'port' || $input == 'prio' ? 'number' : 'text')."' name='{$input}' value='".($input == 'ttl' ? 600 : '')."' ".($input != 'ttl' && $input != 'note'  ? 'required' : '')." ".($type == 'ANAME' && $input == 'name'  ? 'disabled' : '').">".($input == 'name' ? '.'.$domain : '')."</td>";
    echo "</tr>";
}
echo "<tr>";
echo "<td colspan='2'><button type='submit'>Submit</button><button type='button' onclick='window.location=\"{$referer}\"'>Späť</button></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
    