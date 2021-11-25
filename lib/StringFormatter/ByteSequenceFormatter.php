<?php


namespace Cachitos\VarDebug\StringFormatter;


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

      $length = strlen($string);

      // shorten
      //
      $shortened = false;
      if ($this->max_length >= 0 && $length > $this->max_length) {
         $string = substr($string, 0, $this->max_length);
         $shortened = true;
      }

      //
      // format
      //

      $length_shortened = strlen($string);
      $bytes = '';
      for ($i=0; $i<$length_shortened; $i++) {
         $char = ord(substr($string, $i, 1));
         if (0) { }
         elseif ($this->byte_format === 'bits') {
            $byte = str_pad(decbin($char), 8, '0', STR_PAD_LEFT);
         }
         elseif ($this->byte_format === 'decimal') {
            $byte = str_pad($char, 3, '0', STR_PAD_LEFT);
         }
         elseif ($this->byte_format === 'hexlc') {
            $byte = str_pad(strtolower(dechex($char)), 2, '0', STR_PAD_LEFT);
         }
         elseif ($this->byte_format === 'hexuc') {
            $byte = str_pad(strtoupper(dechex($char)), 2, '0', STR_PAD_LEFT);
         }
         else {
            $byte = str_pad(decoct($char), 3, '0', STR_PAD_LEFT);
         }
         $bytes .= $byte . ' ';
      }

      if ($shortened) {
         $bytes .= '...';
      } else {
         $bytes = rtrim($bytes);
      }

      $string = $bytes;

      return $string;
   }

}
