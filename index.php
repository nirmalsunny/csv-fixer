<?php

$file = '/path/to/your/json/file';
$dodgy_coloumn = "ProductName"; //name of the coloumn that needs a fix
$json = file_get_contents($file);
//$json = ""; //or, paste the json value here

$array = json_decode($json, true);

$array_keys = array_keys($array[0]); //array keys wihtout '__parsed_extra' //TODO: remove if exist

$dodgy_coloumn_index = array_search($dodgy_coloumn, $array_keys);
$index = 0;

foreach ($array as $keys) {
    if (array_key_exists('__parsed_extra', $keys)) {
        if (count($keys['__parsed_extra']) > 0) {
            $temp = $array[$index][$dodgy_coloumn]; //copy the broken value to temp
            for ($i = $dodgy_coloumn_index + 1; $i <= ($dodgy_coloumn_index + count($keys['__parsed_extra'])); $i++) {
                $temp .= ',' . $array[$index][$array_keys[$i]]; // join all broken values to temp
            }
            $array[$index][$dodgy_coloumn_index] = $temp;
            //now, the mismatched coloumns need fix
            for ($i = $dodgy_coloumn_index + 1; $i < count($array_keys); $i++) {
                if ($i + count($keys['__parsed_extra']) <= (count($array_keys) - count($keys['__parsed_extra']))) //if its within the index
                    $array[$index][$array_keys[$i]] = $array[$index][$array_keys[$i + count($keys['__parsed_extra'])]];
                else // that means we need to take the value from parsed extra
                    $array[$index][$array_keys[$i]] = $array[$index]['__parsed_extra'][abs($i + count($keys['__parsed_extra']) - count($array_keys))];
            }
            unset($array[$index]['__parsed_extra']); //remove '__parsed_extra'
        }
    }
    $index++;
}

echo json_encode($array);
