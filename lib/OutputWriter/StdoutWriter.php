<?php


namespace Cachito\VarDebug\OutputWriter;


class StdoutWriter implements WriterInterface {

   /**
    * Write $string to STDOUT.
    *
    * @param string $string string to write
    * @return string the written output
    */
   public function write($string): string
   {
      echo $string;
      return $string;
   }
}
