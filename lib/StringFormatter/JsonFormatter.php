<?php


namespace Onedevs\QDump\StringFormatter;


class JsonFormatter extends AbstractStringFormatter {

   /**
    * Returns a shortened and json version of $raw_string.
    * Returns the length of the string in $length.
    * 
    * The string is assumed to be valid UTF-8.
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
      $length = \mb_strlen($string);

      // shorten
      //
      if (\is_int($this->max_string_length)) {
         if ($this->max_string_length >= 0) {
            if ($length > $this->max_string_length) {
               $string = \mb_substr($string, 0, $this->max_string_length) . '...';
            }
         }
         else {
            if ($length > \abs($this->max_string_length)) {
               $string = '...' . \mb_substr($string, $this->max_string_length);
            }
         }
      }

      $string = \json_encode($string);
      $string = \str_replace("\x7f", '\\u007f', $string);

      return $string;
   }

}
