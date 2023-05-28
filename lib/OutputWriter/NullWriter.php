<?php


namespace RichiRM\QDump\OutputWriter;


class NullWriter implements OutputWriterInterface {

   /**
    * It does not write $string anywhere, it just returns it.
    *
    * @param string $string
    * @return string
    */
   public function write($string): string
   {
      return $string;
   }
}
