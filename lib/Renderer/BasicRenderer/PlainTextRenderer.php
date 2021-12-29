<?php


namespace Onedevs\QDump\Renderer\BasicRenderer;


class PlainTextRenderer extends BasicRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "\n" . '---' . "\n",
      'capture'       => "\n" . '',

      'abstract'      => '',
      'class'         => '',
      'cycle'         => '',
      'enum'          => '',
      'file(line)'    => '',
      'key'           => '',
      'method'        => '',
      'modifier'      => '',
      'name'          => '',
      'namespace'     => '',
      'property'      => '',
      'resource-type' => '',
      'scalar'        => '',
      'time'          => '',
      'type'          => '',
      'uninitialized' => '',
      'unknown'       => ''
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '---' . "\n",
      'capture'       => "\n",

      'abstract'      => '',
      'class'         => '',
      'cycle'         => '',
      'enum'          => '',
      'file(line)'    => '',
      'key'           => '',
      'method'        => '',
      'modifier'      => '',
      'name'          => '',
      'namespace'     => '',
      'property'      => '',
      'resource-type' => '',
      'scalar'        => '',
      'time'          => '',
      'type'          => '',
      'uninitialized' => '',
      'unknown'       => ''
   ];
}
