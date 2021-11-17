<?php


namespace Cachito\VarDebug\OutputWriter;


class StdoutWriter implements WriterInterface {

   /**
    * Constructor.
    */
   public function __construct($not_used_1, $not_used_2) { }

   /**
    * Write $string to STDOUT.
    *
    * @param string $string string to write
    */
   public function write($string)
   {
      echo $string;
   }
}
