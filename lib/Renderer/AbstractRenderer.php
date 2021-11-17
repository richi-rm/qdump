<?php


namespace Cachito\VarDebug\Renderer;


class AbstractRenderer {


   /**
    * Level prefix.
    *
    * @var string
    */
   protected $level_prefix = '   ';


   /**
    * Returns the prefix of the indicated element.
    *
    * @param string $what what you want to prefix
    * @return string
    */
   protected function p($what)
   {
      return $this->prefixes[$what];
   }


   /**
    * Returns a string that is suffixed to the content of the capture.
    *
    * @return string
    */
   public function postRender()
   {
      return $this->s('capture') . "\n\n";
   }


   /**
    * Returns a string that is prefixed to the content of the capture.
    *
    * @param integer $capture_sequence_number
    * @param string $file_line
    * @param string $time
    *
    * @return string
    */
   public function preRender($capture_sequence_number, $file_line = null, $time = null)
   {
      if ($file_line === null) {
         return $this->p('capture') . $capture_sequence_number . ') ';
      } else {
         return $this->p('capture') .
                $capture_sequence_number . ') ' .
                $this->p('file-line') . $file_line . $this->s('file-line') . ' ' .
                $this->p('time') . $time . $this->s('time') . "\n";
      }
   }


   /**
    * Converts the array returned by Core::inspect() to a human-readable representation.
    *
    * @param array $core_var variable returned by Core::inspect()
    * @param integer $depth depth level starting from 0
    *
    * @return string
    */
   public function renderCoreVar($core_var, $depth = 0)
   {
      if (!is_array($core_var) || !isset($core_var['type'])) {
         return $this->p('unknown') . '(unknown)' . $this->s('unknown');
      }

      if ($core_var['type'] === 'null') {
         return $this->p('value') . 'null' . $this->s('value');
      }

      if ($core_var['type'] === 'boolean') {
         return $this->p('type') . 'boolean' . $this->s('type') . ' ' . $this->p('value') . $core_var['value'] . $this->s('value');
      }

      if ($core_var['type'] === 'integer') {
         return $this->p('type') . 'integer' . $this->s('type') . ' ' . $this->p('value') . $core_var['value'] . $this->s('value');
      }

      if ($core_var['type'] === 'float') {
         return $this->p('type') . 'float' . $this->s('type') . ' ' . $this->p('value') . $core_var['value'] . $this->s('value');
      }

      if ($core_var['type'] === 'string') {
         return $this->p('type') . 'string(' . $core_var['length'] . ')' . $this->s('type') . ' ' . $this->p('value') . '"' . addcslashes($core_var['value'], '"') . '"' . $this->s('value');
      }

      if ($core_var['type'] === 'array') {
         $r = $this->p('type') . 'array(' . $core_var['size'] . ')' . $this->s('type');
         if ($core_var['cycle'] === true) {
            $r .= ' ' . $this->p('cycle') . '(CYCLE array)' . $this->s('cycle');
            return $r;
         }
         foreach ($core_var['elements'] as $array_key => $array_value) {
            $array_key_formatted = (is_int($array_key) ? $array_key : '\'' . addcslashes($array_key, '\'') . '\'');
            $r .= "\n" . str_repeat($this->level_prefix, $depth + 1) . '[' . $this->p('key') . $array_key_formatted . $this->s('key') . '] => ' . $this->renderCoreVar($array_value, $depth + 1);
         }
         return $r;
      }

      if ($core_var['type'] === 'object') {
         $r = $this->p('type') . 'object' . $this->s('type') . ' ' .
              $this->p('namespace') . $core_var['class-namespace'] . $this->s('namespace') .
              $this->p('class') . $core_var['class-name'] . $this->s('class') . ' ' .
              '#' . $core_var['object-id'];
         if ($core_var['cycle'] === true) {
            $r .= ' ' . $this->p('cycle') . '(CYCLE object)' . $this->s('cycle');
            return $r;
         }
         $r .= ' ' . $this->p('file-line') . $core_var['class-file-line'] . $this->s('file-line');
         foreach ($core_var['properties'] as $property_name => $property_value) {
            $r .= "\n" . str_repeat($this->level_prefix, $depth + 1) . '->' . $this->p('property') . $property_name . $this->s('property') . ' : ' . $this->renderCoreVar($property_value, $depth + 1);
         }
         foreach ($core_var['methods'] as $method_name) {
            $r .= "\n" . str_repeat($this->level_prefix, $depth + 1) . '->' . $this->p('method') . $method_name . '()' . $this->s('method');
         }
         return $r;
      }

      if ($core_var['type'] === 'resource') {
         return $this->p('type') . 'resource' . $this->s('type') . ' ' . $this->p('resource-type') . $core_var['resource-type'] . $this->s('resource-type');
      }

      return $this->p('unknown') . '(unknown)' . $this->s('unknown');
   }


   /**
    * Returns the rendering of the header.
    *
    * @param array $header_lines header lines to render
    * @return string
    */
   public function renderHeader($header_lines)
   {
      $header = $this->p('header');
      foreach ($header_lines as $header_line) {
         $header .= $header_line . "\n";
      }
      $header .= $this->s('header') . "\n";

      return $header;
   }


   /**
    * Returns the suffix of the indicated element.
    *
    * @param string $what what you want to suffix
    * @return string
    */
   protected function s($what)
   {
      return $this->suffixes[$what];
   }
}
