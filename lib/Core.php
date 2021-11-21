<?php


namespace Cachitos\VarDebug;


class Core {

   /**
    * Variable to detect cycles in objects. It is an associative array in which
    * the keys are the ids of the objects being inspected.
    *
    * @var array
    */
   protected $ascending_objects_being_inspected = [];


   /**
    * Configuration of what to add to the output (properties and methods of the
    * objects)
    *
    * @var array
    */
   protected $config = [
      'max-strlen' => null, // max string length
      'privm' => null, // private methods
      'privp' => null, // private properties
      'protm' => null, // protected methods
      'protp' => null, // protected properties
      'pubm'  => null, // public methods
      'pubp'  => null  // public properties
   ];


   /**
    * Variable to detect cycles in arrays. It is a mark for the arrays being
    * iterated.
    *
    * @var string
    */
   protected $this_array_is_being_iterated = '__vardebug.this_array_is_being_iterated__';


   /**
    * Constructor.
    *
    * @param array $config
    */
   public function __construct($config)
   {
      $this->config = $config;
   }


   /**
    * It receives an object as a parameter and returns an associative array
    * with the namespace and the class name.
    *
    * @param object $object
    * @return array
    */
   protected function explode_class($object)
   {
      $class_namespace = explode('\\', get_class($object));
      $class_name = array_pop($class_namespace);
      if (substr($class_name, 0, 15) == 'class@anonymous') {
         $class_name = 'class@anonymous';
      }
      $class_namespace = implode('\\', $class_namespace);
      return [
         'class-namespace' => (strlen($class_namespace) > 0 ? $class_namespace . '\\' : ''),
         'class-name' => $class_name
      ];
   }


   /**
    * Converts the string $str to its representation in PHP and shortens it to
    * max-strlen characters. If max-strlen is negative the string is not
    * shortened.
    *
    * @param string $string
    * @return string
    */
   protected function format_string($str)
   {
      // shorten
      //
      $shortened = false;
      if ($this->config['max-strlen'] >= 0) {
         if (mb_strlen($str) > $this->config['max-strlen']) {
            $str = mb_substr($str, 0, $this->config['max-strlen']);
            $shortened = true;
         }
      }

      //
      // format
      //

      $str = addcslashes($str, '\\"');

      $str = str_replace("\x09", "\\t", $str);
      $str = str_replace("\x0a", "\\n", $str);
      $str = str_replace("\x0b", "\\v", $str);
      $str = str_replace("\x0c", "\\f", $str);
      $str = str_replace("\x0d", "\\r", $str);
      $str = str_replace("\x1b", "\\e", $str);

      $str = str_replace("\0", "\\0", $str);
      $str = str_replace("\1", "\\1", $str);
      $str = str_replace("\2", "\\2", $str);
      $str = str_replace("\3", "\\3", $str);
      $str = str_replace("\4", "\\4", $str);
      $str = str_replace("\5", "\\5", $str);
      $str = str_replace("\6", "\\6", $str);
      $str = str_replace("\7", "\\7", $str);

      $str = str_replace("\10", "\\10", $str);
      $str = str_replace("\11", "\\11", $str);
      $str = str_replace("\12", "\\12", $str);
      $str = str_replace("\13", "\\13", $str);
      $str = str_replace("\14", "\\14", $str);
      $str = str_replace("\15", "\\15", $str);
      $str = str_replace("\16", "\\16", $str);
      $str = str_replace("\17", "\\17", $str);

      $str = str_replace("\20", "\\20", $str);
      $str = str_replace("\21", "\\21", $str);
      $str = str_replace("\22", "\\22", $str);
      $str = str_replace("\23", "\\23", $str);
      $str = str_replace("\24", "\\24", $str);
      $str = str_replace("\25", "\\25", $str);
      $str = str_replace("\26", "\\26", $str);
      $str = str_replace("\27", "\\27", $str);

      $str = str_replace("\30", "\\30", $str);
      $str = str_replace("\31", "\\31", $str);
      $str = str_replace("\32", "\\32", $str);
      $str = str_replace("\33", "\\33", $str);
      $str = str_replace("\34", "\\34", $str);
      $str = str_replace("\35", "\\35", $str);
      $str = str_replace("\36", "\\36", $str);
      $str = str_replace("\37", "\\37", $str);

      $str = str_replace("\x7f", "\\177", $str);

      $str = '"' . $str . '"';

      if ($shortened) {
         $str .= ' ...';
      }

      return $str;
   }


   /**
    * Returns the file and the line in which the class of the object passed as
    * a parameter is declared.
    *
    * @param $object object
    * @return string
    */
   protected function get_class_file_line($object)
   {
      $refl = new \ReflectionClass($object);
      $file_path = $refl->getFileName();
      if ($file_path === false) {
         return '';
      } else {
         return $file_path . '(' . $refl->getStartLine() . ')';
      }
   }


   /**
    * Returns, in an associative array, the inspection of any variable
    * (recursive method).
    *
    * @param mixed &$var variable to inspect (it is passed by reference to mark
    *                    the arrays being iterated)
    * @param integer $depth depth level starting from 0
    * @return array
    */
   public function inspect(&$var, $depth = 0)
   {

      if ($var === null) {
         return ['type' => 'null', 'value' => 'null'];
      }

      if (is_bool($var)) {
         return ['type' => 'boolean', 'value' => ( $var === false ? 'false' : 'true' )];
      }

      if (is_int($var)) {
         return ['type' => 'integer', 'value' => strval($var)];
      }

      if (is_float($var)) {
         return ['type' => 'float', 'value' => strval($var)];
      }

      if (is_string($var)) {
         return ['type' => 'string', 'length' => mb_strlen($var), 'value' => $this->format_string($var)];
      }

      if (is_array($var)) {
         $size = count($var);
         $r = ['type' => 'array', 'size' => $size, 'cycle' => false, 'elements' => []];
         if (isset($var[$this->this_array_is_being_iterated])) {
            $r['size'] = $size - 1;
            $r['cycle'] = true;
            return $r;
         }
         if ($size > 0) {
            $var[$this->this_array_is_being_iterated] = true;
            foreach ($var as $array_key => &$array_value) {
               if ($array_key !== $this->this_array_is_being_iterated) {
                  $r['elements'][$array_key] = $this->inspect($array_value, $depth + 1);
               }
            }
            unset($var[$this->this_array_is_being_iterated]);
         }
         return $r;
      }

      if (is_object($var)) {
         $class = $this->explode_class($var);
         $object_id = spl_object_id($var);
         $r = ['type' => 'object',
               'class-namespace' => $class['class-namespace'],
               'class-name' => $class['class-name'],
               'object-id' => $object_id,
               'class-file-line' => $this->get_class_file_line($var),
               'cycle' => false,
               'properties' => [],
               'methods' => []
         ];
         if (in_array($object_id, array_keys($this->ascending_objects_being_inspected))) {
            $r['cycle'] = true;
            return $r;
         }
         if ($this->config['pubm']) {
            foreach (get_class_methods($var) as $method_name) {
               $r['methods'][] = [
                  'access' => 'public',
                  'name' => $method_name
               ];
            }
            sort($r['methods']);
         }
         if ($this->config['pubp']) {
            $this->ascending_objects_being_inspected[$object_id] = true;
            foreach (get_object_vars($var) as $property_name => &$property_value) {
               $r['properties'][] = [
                  'access' => 'public',
                  'name' => $property_name,
                  'value' => $this->inspect($property_value, $depth + 1)
               ];
            }
            unset($this->ascending_objects_being_inspected[$object_id]);
            ksort($r['properties']);
         }
         return $r;
      }

      if (is_resource($var)) {
         return ['type' => 'resource', 'resource-type' => get_resource_type($var)];
         // PHP 8:
         // return ['type' => 'resource',
         //         'resource-type' => get_resource_type($var),
         //         'resource-id' => get_resource_id($var)];
      }

      return ['type' => '(unknown)', 'value' => '(unknown)'];
   }
}
