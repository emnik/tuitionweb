<?php

// This is a sample PHP file for testing php-cs-fixer

class SampleClass
{
    public function sayHello($name)
    {
        echo 'Hello, '.$name.'!';
    }

    public function addNumbers($a, $b)
    {
        return $a + $b;
    }

    public function printArray($array)
    {
        foreach ($array as $value) {
            echo $value;
        }
    }
}

$sample = new SampleClass();
$sample->sayHello('World');
$result = $sample->addNumbers(5, 10);
echo 'The result is: '.$result;

// An array to test the printArray method
$array = [1, 2, 3, 4, 5];
$sample->printArray($array);
