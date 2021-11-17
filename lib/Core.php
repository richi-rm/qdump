<?php


namespace Cachito\VarDebug;


class Core {

   /**
    * Indicates whether to retrieve the methods of the classes or not.
    * 
    * @var boolean
    */
   protected $add_class_methods = false;


   /**
    * Variable to detect cycles in objects. It is an associative array in which
    * the keys are the ids of the objects being inspected.
    *
    * @var array
    */
   protected $ascending_objects_being_inspected = [];


   /**
    * Variable to detect cycles in arrays. It is a mark for the arrays being
    * iterated.
    *
    * @var string
    */
   protected $this_array_is_being_iterated = '__vardebug.this_array_is_being_iterated__';


   /**
    * Add class methods to render_core_var() output.
    *
    * @param boolean $add true | false
    */
   public function addClassMethods($add = true)
   {
      $this->add_class_methods = (boolean)$add;
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
         return ['type' => 'string', 'length' => strlen($var), 'value' => $var];
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
         $this->ascending_objects_being_inspected[$object_id] = true;
         foreach (get_object_vars($var) as $property_name => &$property_value) {
            $r['properties'][$property_name] = $this->inspect($property_value, $depth + 1);
         }
         unset($this->ascending_objects_being_inspected[$object_id]);
         ksort($r['properties']);
         if ($this->add_class_methods) {
            foreach (get_class_methods($var) as $method_name) {
               $r['methods'][] = $method_name;
            }
            sort($r['methods']);
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
