<?php


namespace Onedevs\QDump\StringFormatter;


class ByteSequenceFormatter extends AbstractStringFormatter {

   /**
    * Returns a shortened and formatted binary version of $raw_string.
    * Returns the length of the string in $length.
    *
    * @param string $raw_string
    * @param int &$length
    * @return string
    */
   public function format($raw_string, &$length): string
   {
      $string = $raw_string;

      // string length
      //
      $length = \strlen($string);

      // shorten
      //
      $shortened = false;
      if (\is_int($this->max_string_length)) {
         if ($this->max_string_length >= 0) {
            if ($length > $this->max_string_length) {
               $string = \substr($string, 0, $this->max_string_length);
               $shortened = true;
            }
         }
         else {
            if ($length > \abs($this->max_string_length)) {
               $string = \substr($string, $this->max_string_length);
               $shortened = true;
            }
         }
      }

      // convert
      //
      $length_shortened = \strlen($string);
      $bytes = '';
      for ($i=0; $i<$length_shortened; $i++) {
         $c = \ord(\substr($string, $i, 1));
         if (0) { }
         // bits
         elseif ($this->byte_format === 'bits') {
            $byte = \str_pad(\decbin($c), 8, '0', \STR_PAD_LEFT);
         }
         // decimal
         elseif ($this->byte_format === 'decimal') {
            $byte = \str_pad($c, 3, '0', \STR_PAD_LEFT);
         }
         // hexadecimal lower case
         elseif ($this->byte_format === 'hexlc') {
            $byte = \str_pad(\strtolower(\dechex($c)), 2, '0', \STR_PAD_LEFT);
         }
         // hexadecimal upper case
         elseif ($this->byte_format === 'hexuc') {
            $byte = \str_pad(\strtoupper(\dechex($c)), 2, '0', \STR_PAD_LEFT);
         }
         // octal
         else {
            $byte = \str_pad(\decoct($c), 3, '0', \STR_PAD_LEFT);
         }
         $bytes .= $byte . ' ';
      }

      $bytes = \rtrim($bytes);

      if ($shortened) {
         if ($this->max_string_length >= 0) {
            $bytes .= '...';
         } else {
            $bytes = '...' . $bytes;
         }
      }

      $string = $bytes;

      return $string;
   }

}
