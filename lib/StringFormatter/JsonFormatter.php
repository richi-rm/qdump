<?php


namespace Onedevs\QDump\StringFormatter;


class JsonFormatter extends AbstractStringFormatter {

   /**
    * Replacement token for character 0x7f.
    */
   const REPLACEMENT_TOKEN_0x7f = '__vardebug.replacement_token_for_character_0x7f__';


   /**
    * Returns a shortened and json version of $raw_string.
    * Returns the length of the string in $length.
    *
    * @param string $raw_string
    * @param int &$length
    * @return string
    */
   public function format($raw_string, &$length): string
   {
      $string = $raw_string;

      // clean up UTF-8
      //
      $substitute_character = mb_substitute_character();
      mb_substitute_character(ord('?'));
      $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
      mb_substitute_character($substitute_character);

      $length = mb_strlen($string);

      // shorten
      //
      if ($this->max_length >= 0 && $length > $this->max_length) {
         $string = mb_substr($string, 0, $this->max_length) . '...';
      }

      $string = str_replace("\x7f", self::REPLACEMENT_TOKEN_0x7f, $string);
      $string = json_encode($string);
      $string = str_replace(self::REPLACEMENT_TOKEN_0x7f, '\\u007f', $string);

      return $string;
   }

}
