<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class TestController extends Controller
{
  public function arraySum()
  {
    // creating an associative array
    $stationary = array(
      'Pencils' => 10,
      'Sharpners' => 5,
      'Erasers' => 12,
      'Rulers' => 6,
    );
    // using the array_sum function
    $sum = array_sum($stationary);
    // printing the total quantity
    print_r("Total Quantity: {$sum}.");
    // output: Total Quantity: 33.

    $array = [
      ['tax' => '1', 'val' => 10],
      ['tax' => '1', 'val' => 20],
      ['tax' => '2', 'val' => 10]
    ];

    $result = array_reduce($array, function ($carry, $item) {
      if (!isset($carry[$item['tax']])) {
        $carry[$item['tax']] = ['tax' => $item['tax'], 'val' => $item['val']];
      } else {
        $carry[$item['tax']]['val'] += $item['val'];
      }
      return $carry;
    });
    print_r($result);
  }

  public function arraySumProduct()
  {
    $products = [
      ['orderno' => 'o001', 'product_code' => 'p1', 'name' => 'product 001', 'qty' => '1'],
      ['orderno' => 'o002', 'product_code' => 'p4', 'name' => 'product 004', 'qty' => '5'],
      ['orderno' => 'o003', 'product_code' => 'p1', 'name' => 'product 001', 'qty' => '6'],
      ['orderno' => 'o004', 'product_code' => 'p2', 'name' => 'product 002', 'qty' => '3'],
      ['orderno' => 'o005', 'product_code' => 'p2', 'name' => 'product 002', 'qty' => '7'],
      ['orderno' => 'o006', 'product_code' => 'p1', 'name' => 'product 001', 'qty' => '4'],
      ['orderno' => 'o007', 'product_code' => 'p4', 'name' => 'product 004', 'qty' => '3'],
      ['orderno' => 'o008', 'product_code' => 'p5', 'name' => 'product 005', 'qty' => '3'],
    ];

    $newProduct = [];
    foreach ($products as $product) {
      $index = array_search($product['product_code'], array_column($newProduct, 'product_code'));

      if ($index !== false) {
        $newProduct[$index]['qty'] += $product['qty'];
      } else {
        $newProduct[] = [
          'product_code' => $product['product_code'],
          'name' => $product['name'],
          'qty' => $product['qty'],
        ];
      }
    }

    // Custom comparison function to sort based on "product_code"
    usort($newProduct, function ($a, $b) {
      return $a['product_code'] <=> $b['product_code'];
    });
    
    return $newProduct;
  }

  public function arrayMultidimensional()
  {
    // creating a multidimensional array
    $array = [
      ['batch' => '1708A', 'lectures' => 10, 'teacher_id' => 1, 'teacher_uuid' => '001'],
      ['batch' => '1709B', 'lectures' => 12, 'teacher_id' => 2, 'teacher_uuid' => '002'],
      ['batch' => '1709A', 'lectures' => 8, 'teacher_id' => 1, 'teacher_uuid' => '001'],
      ['batch' => '1710M', 'lectures' => 15, 'teacher_id' => 2, 'teacher_uuid' => '002'],
      ['batch' => '1704A', 'lectures' => 5, 'teacher_id' => 1, 'teacher_uuid' => '001'],
    ];
    // creating an empty array
    $myArray = array();
    // iterating through the array passed as argument
    foreach ($array as $item) {
      // getting the groupBy value
      $key = $item['teacher_id'];
      // checking if the value exists in the new array
      if (!array_key_exists($key, $myArray)) {
        $myArray[$key] = array(
          'teacher_id' => $item['teacher_id'],
          'teacher_uuid' => $item['teacher_uuid'],
          'lectures' => $item['lectures'],
        );
      } else {
        $myArray[$key]['lectures'] = $myArray[$key]['lectures'] + $item['lectures'];
      }
    }
    // output:  Array ( [1] => Array ( [teacher_id] => 1 [lectures] => 23 ) [2] => Array ( [teacher_id] => 2 [lectures] => 27 ) )
    dd($myArray);
  }

  public function arraySum2()
  {
    $namelist = array();

    $namelist[] = ['data' => 'name1', 'user' => 'user1@example.com'];
    $namelist[] = ['data' => 'name2', 'user' => 'user2@example.com'];
    $namelist[] = ['data' => 'name3', 'user' => 'user1@example.com'];
    $namelist[] = ['data' => 'name4', 'user' => 'user2@example.com'];
    $namelist[] = ['data' => 'name5', 'user' => 'user3@example.com'];

    $namelist = collect($namelist)->groupBy('user');
    dd($namelist);
  }
}
