<?php

$file = './products-min.json';
$dodgy_coloumn = "ProductName";
$json = file_get_contents($file);

$array = json_decode($json, true);

//var_dump($array[3]['__parsed_extra']);

$dodgy = [];
$new_array = $array;
$array_keys = array_keys($array[0]); //array keys wihtout '__parsed_extra' //remove if exist

//print_r($array_keys);

$dodgy_coloumn_index = array_search($dodgy_coloumn, $array_keys); //print_r($dodgy_coloumn);
$index = 0;
foreach ($array as $keys) {
    if (array_key_exists('__parsed_extra', $keys)) {
        $dodgy[] = $keys;
        if (count($keys['__parsed_extra']) > 0) {
            $temp = $array[$index][$dodgy_coloumn]; //copy the broken value to temp
            for ($i = $dodgy_coloumn_index + 1; $i <= ($dodgy_coloumn_index + count($keys['__parsed_extra'])); $i++) {
                $temp .= ',' . $array[$index][$array_keys[$i]]; // join all broken values to temp
            }
            $array[$index][$dodgy_coloumn_index] = $temp;
            //now, the mismatched coloumns need fix
            //echo $index . ': ' . $array[$index][$dodgy_coloumn_index] . PHP_EOL; // dodgy coloumn is fixed
            for ($i = $dodgy_coloumn_index + 1; $i < count($array_keys); $i++) {
                if ($i + count($keys['__parsed_extra']) <= (count($array_keys) - count($keys['__parsed_extra']))) //if its within the index
                    $array[$index][$array_keys[$i]] = $array[$index][$array_keys[$i + count($keys['__parsed_extra'])]];
                else // that means we need to take the value from parsed extra
                    $array[$index][$array_keys[$i]] = $array[$index]['__parsed_extra'][abs($i + count($keys['__parsed_extra']) - count($array_keys))];
                    //echo $index . ': ' . $array[$index][$array_keys[$i]] . ' = ' .  $array[$index]['__parsed_extra'][abs($i + count($keys['__parsed_extra']) - count($array_keys))] . PHP_EOL;
                    //echo abs($i + count($keys['__parsed_extra']) - count($array_keys)) . PHP_EOL;
            }
            unset($array[$index]['__parsed_extra']); //remove '__parsed_extra'
        }
    }
    $index++;
}

echo json_encode($array);