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
    * Core configuration.
    *
    * @var array
    */
   protected $config = null;


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
    * Returns, in an associative array, the inspection of any variable
    * (recursive method).
    *
    * @param mixed &$var variable to inspect (it is passed by reference to mark
    *                    the arrays being iterated)
    * @param int $depth depth level starting from 0
    * @return array
    */
   public function inspect(&$var, $depth = 0)
   {
      // null
      //
      if ($var === null) {
         return ['type' => 'null', 'value' => 'null'];
      }

      // bool
      //
      if (is_bool($var)) {
         return ['type' => 'bool', 'value' => ( $var === false ? 'false' : 'true' )];
      }

      // int
      //
      if (is_int($var)) {
         return ['type' => 'int', 'value' => strval($var)];
      }

      // float
      //
      if (is_float($var)) {
         return ['type' => 'float', 'value' => strval($var)];
      }

      // string
      //
      if (is_string($var)) {
         return ['type' => 'string', 'value' => $var];
      }

      // array
      //
      if (is_array($var)) {
         $size = count($var);
         $r = ['type' => 'array', 'size' => $size];
         if (isset($var[$this->this_array_is_being_iterated])) {
            $r['size'] = $size - 1;
            $r['cycle'] = true;
            return $r;
         }
         if ($size < 1) {
            return $r;
         }
         // recursive inspection of elements
         $var[$this->this_array_is_being_iterated] = true;
         foreach ($var as $array_key => &$array_value) {
            if ($array_key !== $this->this_array_is_being_iterated) {
               $r['elements'][$array_key] = $this->inspect($array_value, $depth + 1);
            }
         }
         unset($var[$this->this_array_is_being_iterated]);
         return $r;
      }

      // enum case
      //
      if (PHP_VERSION_ID >= 80100) {
         if ($var instanceof \UnitEnum) {
            $refl_enum = new \ReflectionEnum($var);
            $file_path = $refl_enum->getFileName();
            $start_line = $refl_enum->getStartLine();
            $file_line = ($file_path === false ? null : $file_path . '(' . $start_line . ')');
            $namespace = $refl_enum->getNamespaceName();
            if (strlen($namespace) > 0) {
               $namespace .= '\\';
            }
            $enum = explode('\\', $refl_enum->getName());
            $enum = end($enum);
            $r = ['type' => 'enumcase', 'file(line)' => $file_line, 'namespace' => $namespace, 'enum' => $enum, 'case' => $var->name];
            if ($var instanceof \BackedEnum) {
               $r['backing-type'] = $refl_enum->getBackingType()->getName();
               $backing_value = $var->value;
               $r['backing-value'] = $this->inspect($backing_value, $depth + 1);
            }
            return $r;
         }
      }

      // object
      //
      if (is_object($var)) {
         // initial inspection
         $r = $this->inspect_object($var);
         if (in_array($r['id'], array_keys($this->ascending_objects_being_inspected))) {
            unset($r['file(line)']);
            unset($r['namespace']);
            unset($r['class']);
            unset($r['constants']);
            unset($r['properties']);
            unset($r['methods']);
            $r['cycle'] = true;
            return $r;
         }
         // recursive inspection of constants
         if (isset($r['constants'])) {
            foreach ($r['constants'] as &$constant) {
               $constant['value'] = $this->inspect($constant['value'], $depth + 1);
            }
         }
         // recursive inspection of properties
         if (isset($r['properties'])) {
            $this->ascending_objects_being_inspected[$r['id']] = true;
            foreach ($r['properties'] as &$property) {
               if (array_key_exists('value', $property)) {
                  $property['value'] = $this->inspect($property['value'], $depth + 1);
               }
            }
            unset($this->ascending_objects_being_inspected[$r['id']]);
         }
         // recursive inspection of method parameter defaults
         if (isset($r['methods'])) {
            foreach ($r['methods'] as &$method) {
               if (isset($method['parameters'])) {
                  foreach ($method['parameters'] as &$parameter) {
                     if (isset($parameter['default-value']) && array_key_exists('value', $parameter['default-value'])) {
                        $parameter['default-value']['value'] = $this->inspect($parameter['default-value']['value'], $depth + 1);
                     }
                  }
               }
            }
         }
         return $r;
      }

      // resource
      //
      if (is_resource($var)) {
         $r = ['type' => 'resource', 'resource-type' => get_resource_type($var)];
         if (PHP_VERSION_ID >= 80000) {
            $r['id'] = get_resource_id($var);
         }
         return $r;
      }

      // unknown
      //
      return ['type' => '(unknown)', 'value' => '(unknown)'];
   }


   /**
    * Inspect an object and return the information found as an array.
    *
    * @var object $object
    * @return array
    */
   protected function inspect_object($object)
   {
      $r = [
         'type' => 'object',
         'file(line)' => null,
         'namespace' => null,
         'class' => null,
         'id' => null
      ];

      $refl_object = new \ReflectionObject($object);

      // file(line)
      //
      $file_path = $refl_object->getFileName();
      $start_line = $refl_object->getStartLine();
      $r['file(line)'] = ($file_path === false ? null : $file_path . '(' . $start_line . ')');

      // namespace
      //
      $namespace = $refl_object->getNamespaceName();
      $r['namespace'] = $namespace . (strlen($namespace) > 0 ? '\\' : '');

      // class
      //
      $class = explode('\\', $refl_object->getName());
      $class = end($class);
      if (substr($class, 0, 15) === 'class@anonymous') {
         $class = 'class@anonymous';
      }
      $r['class'] = $class;

      // object id
      //
      $r['id'] = spl_object_id($object);

      // constants
      //
      foreach ($refl_object->getConstants() as $name => $value) {
         $refl_constant = new \ReflectionClassConstant($object, $name);
         if ($refl_constant->isPrivate()) {
            $access = 'private';
         } elseif ($refl_constant->isProtected()) {
            $access = 'protected';
         } else {
            $access = 'public';
         }
         $r['constants'][] = ['access' => $access, 'name' => $name, 'value' => $value];
      }

      // properties
      //
      foreach ($refl_object->getProperties() as $refl_property) {

         $property = [];

         $refl_property->setAccessible(true);

         // access
         //
         if ($refl_property->isPrivate()) {
            $access = 'private';
         } elseif ($refl_property->isProtected()) {
            $access = 'protected';
         } else {
            $access = 'public';
         }
         $property['access'] = $access;

         // dynamic
         //
         if ($this->is_dynamic($object, $refl_property->getName())) {
            $property['dynamic'] = true;
         }

         // name
         //
         $property['name'] = $refl_property->getName();

         // readonly
         //
         if (PHP_VERSION_ID >= 80100) {
            if ($refl_property->isReadOnly()) {
               $property['readonly'] = true;
            }
         }

         // static
         //
         if ($refl_property->isStatic()) {
            $property['static'] = true;
         }

         // type
         //
         if (!isset($property['dynamic'])) {
            if ($refl_property->hasType()) {
               if ($refl_property->getType()->allowsNull()) {
                  $property['type']['null'] = true;
               }
               $property['type']['name'] = $refl_property->getType()->getName();
            }
         }

         // value
         //
         if ($refl_property->isInitialized($object)) {
            $property['value'] = $refl_property->getValue($object);
         } else {
            $property['uninitialized'] = true;
         }

         $r['properties'][] = $property;
      }

      // methods
      //
      foreach ($refl_object->getMethods() as $refl_method) {

         $method = [];

         // access
         //
         if ($refl_method->isPrivate()) {
            $access = 'private';
         } elseif ($refl_method->isProtected()) {
            $access = 'protected';
         } else {
            $access = 'public';
         }
         $method['access'] = $access;

         // final
         //
         if ($refl_method->isFinal()) {
            $method['final'] = true;
         }

         // name
         //
         $method['name'] = $refl_method->getName();

         // parameters
         //
         $parameters = [];
         foreach ($refl_method->getParameters() as $refl_parameter) {
            $parameter = [];
            if ($refl_parameter->isDefaultValueAvailable()) {
               $parameter['default-value'] = $refl_parameter->isDefaultValueConstant() ?
                  [ 'constant' => $refl_parameter->getDefaultValueConstantName() ] :
                  [ 'value' => $refl_parameter->getDefaultValue() ];
            }
            $parameter['name'] = $refl_parameter->getName();
            if ($refl_parameter->isPassedByReference()) {
               $parameter['reference'] = true;
            }
            if ($refl_parameter->hasType()) {
               if ($refl_parameter->getType()->allowsNull()) {
                  $parameter['type']['null'] = true;
               }
               $parameter['type']['name'] = $refl_parameter->getType()->getName();
            }
            if ($refl_parameter->isVariadic()) {
               $parameter['variadic'] = true;
            }
            $parameters[] = $parameter;
         }
         if (count($parameters) > 0) {
            $method['parameters'] = $parameters;
         }

         // reference
         //
         if ($refl_method->returnsReference()) {
            $method['reference'] = true;
         }

         // static
         //
         if ($refl_method->isStatic()) {
            $method['static'] = true;
         }

         // type (return type)
         //
         if ($refl_method->hasReturnType()) {
            if ($refl_method->getReturnType()->allowsNull()) {
               $method['type']['null'] = true;
            }
            $method['type']['name'] = $refl_method->getReturnType()->getName();
         }

         $r['methods'][] = $method;
      }

      return $r;
   }


   /**
    * Returns true if property $property_name is dynamic.
    *
    * @param object $object
    * @param string $property_name
    * @return bool
    */
   protected function is_dynamic($object, $property_name)
   {
      return  array_key_exists($property_name, get_object_vars($object)) &&
             !array_key_exists($property_name, get_class_vars(get_class($object)));
   }

}
