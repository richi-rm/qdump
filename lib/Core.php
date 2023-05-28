<?php


namespace RichiRM\QDump;


class Core {

   /**
    * String formatters.
    */
   const STRING_FORMATTERS = [
      'ascii'      => 'RichiRM\QDump\StringFormatter\AsciiToUTF8Formatter',
      'bytes'      => 'RichiRM\QDump\StringFormatter\ByteSequenceFormatter',
      'iso-8859-1' => 'RichiRM\QDump\StringFormatter\ISO88591ToUTF8Formatter',
      'json'       => 'RichiRM\QDump\StringFormatter\JsonFormatter',
      'utf-8'      => 'RichiRM\QDump\StringFormatter\UTF8Formatter'
   ];


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
   protected $config = [
      'byte-format'       => null, // byte format
      'max-depth'         => null, // maximum inspection depth
      'max-string-length' => null, // maximum visible string length
      'string-format'     => null  // string format
   ];


   /**
    * Variable to detect cycles in arrays. It is a mark for the arrays being
    * iterated.
    *
    * @var string
    */
   protected $this_array_is_being_iterated = '__qdump.this_array_is_being_iterated__';


   /**
    * String formatter.
    */
   protected $string_formatter = null;


   /**
    * Constructor.
    *
    * @param array $config
    */
   public function __construct($config)
   {
      $this->config = $config;
      $string_formatter_class = self::STRING_FORMATTERS[$config['string-format']];
      $this->string_formatter = new $string_formatter_class($config['byte-format'], $config['max-string-length']);
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
      if (\is_bool($var)) {
         return ['type' => 'bool', 'value' => ( $var ? 'true' : 'false' )];
      }

      // int
      //
      if (\is_int($var)) {
         return ['type' => 'int', 'value' => \strval($var)];
      }

      // float
      //
      if (\is_float($var)) {
         return ['type' => 'float', 'value' => \strval($var)];
      }

      // string
      //
      if (\is_string($var)) {
         $length = 0;
         $string_formatted = $this->string_formatter->format($var, $length);
         return ['type' => 'string', 'length' => $length, 'value' => $string_formatted];
      }

      // array
      //
      if (\is_array($var)) {
         $size = \count($var);
         $r = ['type' => 'array', 'size' => $size];
         if (isset($var[$this->this_array_is_being_iterated])) {
            $r['size'] = $size - 1;
            $r['cycle'] = true;
            return $r;
         }
         if ($size < 1) {
            return $r;
         }
         // depth
         if (\is_int($this->config['max-depth'])) {
            // maximum depth has been indicated
            if ($depth >= $this->config['max-depth']) {
               return $r;
            }
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
      if (\PHP_VERSION_ID >= 80100) {
         if ($var instanceof \UnitEnum) {
            $refl_enum = new \ReflectionEnum($var);
            $file_path = $refl_enum->getFileName();
            $start_line = $refl_enum->getStartLine();
            $file_line = ($file_path === false ? null : $file_path . '(' . $start_line . ')');
            $namespace = $refl_enum->getNamespaceName();
            if (\strlen($namespace) > 0) {
               $namespace .= '\\';
            }
            $enum = \explode('\\', $refl_enum->getName());
            $enum = \end($enum);
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
      if (\is_object($var)) {

         // initial inspection
         //
         $r = $this->inspect_object($var);

         // cycle
         //
         if (\in_array($r['id'], \array_keys($this->ascending_objects_being_inspected))) {
            unset($r['classes']);
            unset($r['constants']);
            unset($r['properties']);
            unset($r['methods']);
            $r['cycle'] = true;
            return $r;
         }

         // depth
         //
         if (\is_int($this->config['max-depth'])) {
            // maximum depth has been indicated
            if ($depth >= $this->config['max-depth']) {
               unset($r['constants']);
               unset($r['properties']);
               unset($r['methods']);
               return $r;
            }
         }

         // recursive inspection of constants
         //
         if (isset($r['constants'])) {
            foreach ($r['constants'] as &$constant) {
               $constant['value'] = $this->inspect($constant['value'], $depth + 1);
            }
         }

         // recursive inspection of properties
         //
         if (isset($r['properties'])) {
            $this->ascending_objects_being_inspected[$r['id']] = true;
            foreach ($r['properties'] as &$property) {
               if (\array_key_exists('value', $property)) {
                  $property['value'] = $this->inspect($property['value'], $depth + 1);
               }
            }
            unset($this->ascending_objects_being_inspected[$r['id']]);
         }

         // recursive inspection of method parameter defaults
         //
         if (isset($r['methods'])) {
            foreach ($r['methods'] as &$method) {
               if (isset($method['parameters'])) {
                  foreach ($method['parameters'] as &$parameter) {
                     if (isset($parameter['default-value']) && \array_key_exists('value', $parameter['default-value'])) {
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
      if (\is_resource($var)) {
         $r = ['type' => 'resource', 'resource-type' => \get_resource_type($var)];
         if (\PHP_VERSION_ID >= 80000) {
            $r['id'] = \get_resource_id($var);
         }
         return $r;
      }

      // unknown
      //
      return ['type' => '(unknown)', 'value' => '(unknown)'];
   }


   /**
    * Returns an array with the ancestor classes of the given class.
    *
    * @param string class
    * @return array
    */
   protected function inspect_ancestor_classes(string $class): array
   {
      $classes = [
         1 => $this->inspect_class($class)
      ];

      do {
         $parent_class = \get_parent_class($class);
         if ($parent_class === false) {
            break;
         }
         $classes[] = $this->inspect_class($parent_class);
         $class = $parent_class;
      } while (true);

      $classes = \array_combine(\array_keys($classes), \array_reverse($classes));

      return $classes;
   }


   /**
    * Inspect the given class.
    *
    * @param string class
    * @return array
    */
   public function inspect_class(string $class): array
   {
      $refl_class = new \ReflectionClass($class);

      // file(line)
      $file_path = $refl_class->getFileName();
      $file_line = ( $file_path === false ? '' : $file_path . '(' . $refl_class->getStartLine() . ')' );

      // class
      $class = $refl_class->getName();

      // namespace
      $namespace = $refl_class->getNamespaceName();
      $namespace = ( \strlen($namespace) > 0 ? $namespace . '\\' : '' );

      // classname
      $classname = \explode('\\', $class);
      $classname = \end($classname);

      $r = [
         'file(line)' => $file_line,
         'class'      => $class,
         'namespace'  => $namespace,
         'classname'  => $classname
      ];
      if ($refl_class->isAbstract()) {
         $r['abstract'] = true;
      }

      return $r;
   }


   /**
    * Inspect the given ReflectionFunction.
    *
    * @param ReflectionFunction $refl_function
    * @return array
    */
   public function inspect_function(\ReflectionFunction $refl_function): array
   {
      $function = [];

      // name
      //
      $function['name'] = $refl_function->getName();

      // parameters
      //
      $parameters = $this->inspect_parameters($refl_function->getParameters());
      if (\count($parameters) > 0) {
         $function['parameters'] = $parameters;
      }

      // returns reference
      //
      if ($refl_function->returnsReference()) {
         $function['reference'] = true;
      }

      // return type
      //
      if ($refl_function->hasReturnType()) {
         $return_type = $refl_function->getReturnType();
         if ($return_type->allowsNull()) {
            $function['type']['null'] = true;
         }
         if ($return_type instanceof \ReflectionNamedType) {
            $function['type']['name'] = $return_type->getName();
         } elseif ($return_type instanceof \ReflectionUnionType) {
            $types = [];
            foreach ($return_type->getTypes() as $type) {
               $types[] = $type->getName();
            }
            \sort($types, \SORT_NATURAL | \SORT_FLAG_CASE);
            $function['type']['name'] = \implode('|', $types);
         }
      }

      return $function;
   }


   /**
    * Inpect the given ReflectionMethod.
    *
    * @param ReflectionMethod $refl_method
    * @return array
    */
   public function inspect_method(\ReflectionMethod $refl_method): array
   {
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

      // declaring class
      //
      $method['declaring-class'] = $refl_method->getDeclaringClass()->getName();

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
      $parameters = $this->inspect_parameters($refl_method->getParameters());
      if (\count($parameters) > 0) {
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

      // return type
      //
      if ($refl_method->hasReturnType()) {
         $return_type = $refl_method->getReturnType();
         if ($return_type->allowsNull()) {
            $method['type']['null'] = true;
         }
         if ($return_type instanceof \ReflectionNamedType) {
            $method['type']['name'] = $return_type->getName();
         } elseif ($return_type instanceof \ReflectionUnionType) {
            $types = [];
            foreach ($return_type->getTypes() as $type) {
               $types[] = $type->getName();
            }
            \sort($types, \SORT_NATURAL | \SORT_FLAG_CASE);
            $method['type']['name'] = \implode('|', $types);
         }
      }

      return $method;
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
         'id' => null,
         'classes' => []
      ];

      // object id
      //
      $r['id'] = \spl_object_id($object);

      // \ReflectionObject is used instead of \ReflectionClass to be able to capture dynamic properties
      $refl_object = new \ReflectionObject($object);

      // classes
      //
      $r['classes'] = $this->inspect_ancestor_classes(\get_class($object));

      // constants
      //
      foreach ($refl_object->getConstants() as $name => $value) {

         $constant = [];

         $refl_constant = new \ReflectionClassConstant($object, $name);

         // declaring class
         //
         $constant['declaring-class'] = $refl_constant->getDeclaringClass()->getName();

         // access
         //
         if ($refl_constant->isPrivate()) {
            $access = 'private';
         } elseif ($refl_constant->isProtected()) {
            $access = 'protected';
         } else {
            $access = 'public';
         }
         $constant['access'] = $access;

         // name
         //
         $constant['name'] = $name;

         // value
         //
         $constant['value'] = $value;

         $r['constants'][] = $constant;
      }

      // properties
      //
      foreach ($refl_object->getProperties() as $refl_property) {

         $property = [];

         $refl_property->setAccessible(true);

         // declaring class
         //
         $property['declaring-class'] = $refl_property->getDeclaringClass()->getName();

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
         if (\PHP_VERSION_ID >= 80100) {
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
               if ($refl_property->getType() instanceof \ReflectionNamedType) {
                  $property['type']['name'] = $refl_property->getType()->getName();
               } elseif ($refl_property->getType() instanceof \ReflectionUnionType) {
                  $types = [];
                  foreach ($refl_property->getType()->getTypes() as $type) {
                     $types[] = $type->getName();
                  }
                  \sort($types, \SORT_NATURAL | \SORT_FLAG_CASE);
                  $property['type']['name'] = \implode('|', $types);
               }
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
         $r['methods'][] = $this->inspect_method($refl_method);
      }

      return $r;
   }


   /**
    * Inspect the passed array of ReflectionParameters.
    *
    * @param array $refl_parameters
    * @return array
    */
   protected function inspect_parameters(array $refl_parameters): array
   {
      $parameters = [];
      foreach ($refl_parameters as $refl_parameter) {
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
            if ($refl_parameter->getType() instanceof \ReflectionNamedType) {
               $parameter['type']['name'] = $refl_parameter->getType()->getName();
            } elseif ($refl_parameter->getType() instanceof \ReflectionUnionType) {
               $types = [];
               foreach ($refl_parameter->getType()->getTypes() as $type) {
                  $types[] = $type->getName();
               }
               \sort($types, \SORT_NATURAL | \SORT_FLAG_CASE);
               $parameter['type']['name'] = \implode('|', $types);
            }
         }
         if ($refl_parameter->isVariadic()) {
            $parameter['variadic'] = true;
         }
         $parameters[] = $parameter;
      }

      return $parameters;
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
      return  \array_key_exists($property_name, \get_object_vars($object)) &&
             !\array_key_exists($property_name, \get_class_vars(\get_class($object)));
   }

}
