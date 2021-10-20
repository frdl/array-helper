<?php

namespace Wehowski\Helpers;

use Generator;

class ArrayHelper
{

 protected	$arr = null;
 protected	$item = null;
 protected	$index = null;

 public function __construct(array $arr = null){
	 if(null===$arr){
		 $arr=[];
	 }
	 $this->arr=$arr;
	 $this->index=count($this->arr)-1;
 }
 public function __call($name, $params){
	 if(function_exists('array_'.$name)){
		 array_unshift($params, $this->arr);
		 return call_user_func_array('array_'.$name, $params);
	 }
 }	
	
 public static function __callStatic($name, $params){
	 $input = (count($params)>0 && is_array($params[0]))
		       ? array_shift($params)
		       : [];

	return call_user_func_array([new self($input), $name], $params);
 }
	
/*
Example:
<?php
$a = [
    295 => "Hello",
    58 => "world",
];

$a = arrayInsert($a, 1, [123 => "little"]);

Output:
Array
(
    [295] => Hello
    [123] => little
    [58] => world
)
?>
 Despite PHP's amazing assortment of array functions and juggling maneuvers, I found myself needing a way to get the FULL array key mapping to a specific value. This function does that, and returns an array of the appropriate keys to get to said (first) value occurrence.
*/
public function recursive_search_key_map($needle, $haystack) {
    foreach($haystack as $first_level_key=>$value) {
        if ($needle === $value) {
            return array($first_level_key);
        } elseif (is_array($value)) {
            $callback = $this->recursive_search_key_map($needle, $value);
            if ($callback) {
                return array_merge(array($first_level_key), $callback);
            }
        }
    }
    return false;
}
/*
usage example:
-------------------

$nested_array = $sample_array = array(
    'a' => array(
        'one' => array ('aaa' => 'apple', 'bbb' => 'berry', 'ccc' => 'cantalope'),
        'two' => array ('ddd' => 'dog', 'eee' => 'elephant', 'fff' => 'fox')
    ),
    'b' => array(
        'three' => array ('ggg' => 'glad', 'hhh' => 'happy', 'iii' => 'insane'),
        'four' => array ('jjj' => 'jim', 'kkk' => 'kim', 'lll' => 'liam')
    ),
    'c' => array(
        'five' => array ('mmm' => 'mow', 'nnn' => 'no', 'ooo' => 'ohh'),
        'six' => array ('ppp' => 'pidgeon', 'qqq' => 'quail', 'rrr' => 'rooster')
    )
);

$search_value = 'insane';

$array_keymap = array_recursive_search_key_map($search_value, $nested_array);

var_dump($array_keymap);
// Outputs:
// array(3) {
// [0]=>
//  string(1) "b"
//  [1]=>
//  string(5) "three"
//  [2]=>
//  string(3) "iii"
//}

----------------------------------------------

But again, with the above solution, PHP again falls short on how to dynamically access a specific element's value within the nested array. For that, I wrote a 2nd function to pull the value that was mapped above.
*/
public function nested_value($keymap, $array)
{
    $nest_depth = sizeof($keymap);
    $value = $array;
    for ($i = 0; $i < $nest_depth; $i++) {
        $value = $value[$keymap[$i]];
    }

    return $value;
}
/*
usage example:
-------------------
echo nested_value($array_keymap, $nested_array);   // insane	
*/
	
/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 4
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */
  /**
     * Flatten and iterate
     *
     * @param  array  $array
     * @param  string $delimiter
     * @param  string $basePath
     * @return Generator|mixed[]
     */
    public static function flattenAndIterate(array $array, string $delimiter = '.', string $basePath = ''): Generator
    {
        foreach ($array as $key => $value) {
            $fullKey = implode($delimiter, array_filter([$basePath, $key]));
            if (is_array($value)) {
                yield from static::flattenAndIterate($value, $delimiter, $fullKey);
            } else {
                yield $fullKey => $value;
            }
        }
    }
	public static function flatten($arr){    	
		return (new self($arr))->flatted();
	}
	public function flatted(){    	
		$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->arr));  	
		return \iterator_to_array($it, true);
	}
	public static function unflatten($arr, $delimiter='.', $depth=-1){    	
		return (new self($arr))->unflatted($delimiter, $depth);
	}	
  public function unflatted($delimiter='.', $depth=-1) {
    $output = [];
    foreach ($this->arr as $key => $value) {
		if(($parts = @preg_split($delimiter, $key, null)) === false){
           //pattern is broken
		  $parts = ($depth>0)?explode($delimiter, $key, $depth):explode($delimiter, $key);
       }else{
           //pattern is real
			
       }
    //$parts = ($depth>0)?explode($delimiter, $key, $depth):explode($delimiter, $key);
    $nested = &$output;
    while (count($parts) > 1) {
      $nested = &$nested[array_shift($parts)];
      if (!is_array($nested)) $nested = [];
    }
    $nested[array_shift($parts)] = $value;
    }
    return $output;
  }	
    /**
     * Merge configuration arrays
     *
     * What I would wish that array_merge_recursive actually does.
     *
     * This is a cascading merge, with individual values being overwritten.
     * From: http://www.php.net/manual/en/function.array-merge-recursive.php#102379
     *
     * @param  array $arr1 Array #1
     * @param  array $arr2 Array #2
     * @return array
     */
    public static function merge(array $arr1, array $arr2): array
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value) && is_array($arr1[$key])) {
                $arr1[$key] = static::merge($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
    }
	
	
 public function chunk($blocknum, int $chunksize = 1) {

  $blocknum = $blocknum < 1 ? 1 : $blocknum;

  $start = ($blocknum - 1) * ($chunksize);
  $offset = $chunksize;

  $outArray = array_slice($this->arr, $start, $offset);

   return $outArray;
 }
	
 public static function paginate($input, $page, int $show_per_page = 10) {
   return (new self($input))->chunk($page, $show_per_page);
 }	
	
 public function getByHash($keymap,$hashIndex = null){
	 
    $nest_depth = sizeof($keymap);
	 if(null===$hashIndex){
		 $hashIndex=max(0,$nest_depth-1);
	 }
	 if(is_int($hashIndex)){
		  $hashIndex=max($hashIndex,$nest_depth);
	 }
    $value =  $this->arr;
    for ($i = 0; $i < $nest_depth; $i++) {
        $value = $value[$keymap[$i]];
		if(is_int($hashIndex) && $hashIndex === $i || $hashIndex === $keymap[$i])break;
		
    }

    return $value;
 }	
 
 public function find($search_value, $data = null, $hashIndex = null) {
   return $this->getByHash($this->hash($search_value), $hashIndex); 
 }	
 public function hash($needle) {
    return self::getHash($needle, $this->arr) ;
 }	
 public static function getHash($needle, $haystack) {
    foreach($haystack as $first_level_key=>$value) {
        if ($needle === $value || preg_match('/^'.$needle.'$\/', $value)) {
            return array($first_level_key);
        } elseif (is_array($value)) {
            $callback = self::getHash($needle, $value);
            if ($callback) {
                return array_merge(array($first_level_key), $callback);
            }
        }
    }
    return false;
  }
	
	
	
	public static function before(array $src,array $in, $pos){
			$this->index= ((!is_int($pos)) ?  ArrayHelper::getHash($pos, $this->arr)[0] : $pos) -1;
		return $this;
   }
	
	public function after( $pos){
			$this->index= ((!is_int($pos)) ?  ArrayHelper::getHash($pos, $this->arr)[0] : $pos) + 1;
		return $this;
    }	
	public function add( $data ){
			$this->arr= self::insert($this->arr, $data,  $this->index);
		return $this;
    }		
	
	
	public function up($index, $up = 1) {
      $new_array = $this->arr;
     
	 while($up > 0){
		$up-- ;
       if((count($new_array)>$index) && ($index>0)){
                 array_splice($new_array, $index-1, 0, $input[$index]);
                 array_splice($new_array, $index+1, 1);
             }

	 }
       return $new_array;
    }

	public function down($index, $down=1) {
       $new_array = $this->arr;
        while($down > 0){
		$down--  ;
       if(count($new_array)>$index) {
                 array_splice($new_array, $index+2, 0, $input[$index]);
                 array_splice($new_array, $index, 1);
             }
		 }
       return $new_array;
     }	
	
	public static function insert(array $array, $insertArray,  $position = null)
	{
     $ret = [];
		$count = count($array);
		
		if(!is_int($position)){
			$position = ArrayHelper::getHash($position,$array)[0];
		}
		
      if(null===$position || (is_int($position) && $position > $count )){
	   $position = $count - 1;
     }
		
     if (is_int($position) && $position === $count) {
		  $ret = $array;
		 array_push($ret, $insertArray);
     //  // $ret = $array + $insertArray;
     } else {
        $i = 0;
		 $f=false;
        foreach ($array as $key => $value) {
            if ((is_int($position) && $position === $i )
				|| (is_string($position) && $position === $key) 
				|| (is_scalar($position) &&  $position === $value)
				|| (is_string($position) &&  preg_match('/^'.$position.'$\/', $value))
			   ) {
		      // 	array_push($ret, $insertArray);
               //  $ret += $insertArray;
				$ret[(is_numeric($key))?$i:((is_numeric($position)|| isset($array[$position]))?$i:$position)] = $insertArray; 
				 $f=true;
				$i++;
            }      
			$ret[(($f===true && is_numeric($key))|| isset($array[$key]))?$i:$key] = $value;     
			$i++;
		}
  
	}

   
		return $ret;
	}
	
	
	
	
	
	/**
	{
  "name": "mcaskill/php-array-chunk-by",
  "description": "Splits an array into chunks using a callback function.",
  "license": "MIT",
  "authors": [
    {
      "name": "Chauncey McAskill",
      "email": "chauncey@mcaskill.ca",
      "homepage": "https://github.com/mcaskill"
    }
  ],
  "keywords": [
    "function"
  ],
  "extra": {
    "branch-alias": {
      "dev-master": "1.x-dev"
    }
  },
  "require": {
    "php": ">=5.4.0"
  },
  "autoload": {
    "files": ["Function.Array-Chunk-By.php"]
  }
}
	 * Splits an array into chunks using a callback function.
	 *
	 * Chunks an array into arrays by iteratively applying the $callback function
	 * to the elements of the $array.
	 *
	 * @see https://rlaanemets.com/post/show/group-array-by-adjacent-elements-in-javascript
	 *
	 * @param  array    $array         The array to have chunking performed on.
	 * @param  callable $callback      {
	 *     The callback function to use.
	 *
	 *     ```
	 *     bool callback ( mixed $previous, mixed $current )
	 *     ```
	 *
	 *     @param  mixed $previous Holds the value of the previous iteration.
	 *     @param  mixed $current  Holds the value of the current iteration.
	 *     @return bool If TRUE, the the current value from $array is split
	 *         into a new chunk.
	 * }
	 * @param  bool     $preserve_keys When set to TRUE keys will be preserved.
	 *     Default is FALSE which will reindex the chunk numerically.
	 * @return array Returns a multidimensional numerically indexed array,
	 *     starting with zero, with each dimension containing related elements.
	 */
	public function /*array_*/chunk_by(array $array, callable $callback, bool $preserve_keys = false) : array
	{
		$reducer = function ( array $carry, $key ) use ( $array, $callback, $preserve_keys ) {
			$current = $array[$key];
			$length  = count($carry);

			if ( $length > 0 ) {
				$chunk = &$carry[ $length - 1 ];
				end($chunk);
				$previous = $chunk[ key($chunk) ];

				if ( $callback($previous, $current) ) {
					// Split, create a new group.
					if ($preserve_keys) {
						$carry[] = [ $key => $current ];
					} else {
						$carry[] = [ $current ];
					}
				} else {
					// Put into the $currentrent group.
					if ($preserve_keys) {
						$chunk[$key] = $current;
					} else {
						$chunk[] = $current;
					}
				}
			} else {
				// The first group.
				if ($preserve_keys) {
					$carry[] = [ $key => $current ];
				} else {
					$carry[] = [ $current ];
				}
			}

			return $carry;
		};

		return array_reduce(array_keys($array), $reducer, []);
	}
	
	
	
	
	
	
	
	
	
	
	/**
	{
  "name": "mcaskill/php-array-group-by",
  "description": "Groups an array by a given key.",
  "license": "MIT",
  "authors": [
    {
      "name": "Chauncey McAskill",
      "email": "chauncey@mcaskill.ca",
      "homepage": "https://github.com/mcaskill"
    }
  ],
  "keywords": [
    "function"
  ],
  "extra": {
    "branch-alias": {
      "dev-master": "1.x-dev"
    }
  },
  "require": {
    "php": ">=5.4.0"
  },
  "autoload": {
    "files": ["Function.Array-Group-By.php"]
  }
}
	 * Groups an array by a given key.
	 *
	 * Groups an array into arrays by a given key, or set of keys, shared between all array members.
	 *
	 * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
	 * This variant allows $key to be closures.
	 *
	 * @param array $array   The array to have grouping performed on.
	 * @param mixed $key,... The key to group or split by. Can be a _string_,
	 *                       an _integer_, a _float_, or a _callable_.
	 *
	 *                       If the key is a callback, it must return
	 *                       a valid key from the array.
	 *
	 *                       If the key is _NULL_, the iterated element is skipped.
	 *
	 *                       ```
	 *                       string|int callback ( mixed $item )
	 *                       ```
	 *
	 * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
	 
	 
	 $records = [
	[
		"state"  => "IN",
		"city"   => "Indianapolis",
		"object" => "School bus"
	],
	[
		"state"  => "IN",
		"city"   => "Indianapolis",
		"object" => "Manhole"
	],
	[
		"state"  => "IN",
		"city"   => "Plainfield",
		"object" => "Basketball"
	],
	[
		"state"  => "CA",
		"city"   => "San Diego",
		"object" => "Light bulb"
	],
	[
		"state"  => "CA",
		"city"   => "Mountain View",
		"object" => "Space pen"
	]
];

$grouped = array_group_by( $records, "state", "city" );
The above example will output:

Array
(
	[IN] => Array
		(
			[Indianapolis] => Array
				(
					[0] => Array
						(
							[state] => IN
							[city] => Indianapolis
							[object] => School bus
						)

					[1] => Array
						(
							[state] => IN
							[city] => Indianapolis
							[object] => Manhole
						)

				)

			[Plainfield] => Array
				(
					[0] => Array
						(
							[state] => IN
							[city] => Plainfield
							[object] => Basketball
						)

				)

		)

	[CA] => Array
		(
			[San Diego] => Array
				(
					[0] => Array
						(
							[state] => CA
							[city] => San Diego
							[object] => Light bulb
						)

				)

			[Mountain View] => Array
				(
					[0] => Array
						(
							[state] => CA
							[city] => Mountain View
							[object] => Space pen
						)

				)

		)
)
	 */
	public function /*array_*/group_by(array $array, $key)
	{
		if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
			trigger_error('array_group_by(): The key should be a string, an integer, or a callback', \E_USER_ERROR);
			throw new \Exception('array_group_by(): The key should be a string, an integer, or a callback');
			return null;
		}

		$func = (!is_string($key) && \is_callable($key) ? $key : null);
		$_key = $key;

		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($array as $value) {
			$key = null;

			if (\is_callable($func)) {
				$key = \call_user_func($func, $value);
			} elseif (is_object($value) && \property_exists($value, $_key)) {
				$key = $value->{$_key};
			} elseif (isset($value[$_key])) {
				$key = $value[$_key];
			}

			if ($key === null) {
				continue;
			}

			$grouped[$key][] = $value;
		}

		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();

			foreach ($grouped as $key => $value) {
				$params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
				$grouped[$key] = \call_user_func_array('array_group_by', $params);
			}
		}

		return $grouped;
	}	
	
	
	
	
}

