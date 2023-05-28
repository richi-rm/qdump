<?php


namespace RichiRM\QDump\StringFormatter;


abstract class AbstractStringFormatter {

   /**
    * Byte format ('bits', decimal', 'hexlc', 'hexuc', 'octal').
    *     
    * @var string
    */
   protected $byte_format = null;


   /**
    * Maximum visible string length.
    *
    * @var int|string
    */
   protected $max_string_length = null;


   /**
    * Constructor.
    *
    * @param string $byte_format byte format
    * @param int|string $max_string_length maximum string length
    */
   public function __construct($byte_format, $max_string_length)
   {
      $this->byte_format = $byte_format;
      $this->max_string_length = $max_string_length;
   }


   /**
    * Returns a shortened and formatted version of $raw_string.
    * Returns the length of the string in $length.
    *
    * @param string $raw_string
    * @param int &$length
    * @return string
    */
   abstract public function format($raw_string, &$length): string;

}
