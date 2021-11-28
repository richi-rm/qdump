<?php


namespace Cachitos\VarDebug\OutputWriter;


class StdoutWriter implements OutputWriterInterface {

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
