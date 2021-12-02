<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class PlainTextRenderer extends BasicRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "---\n",
      'capture'       => '',

      'class'         => '',
      'cycle'         => '',
      'file(line)'    => '',
      'key'           => '',
      'method'        => '',
      'modifier'      => '',
      'name'          => '',
      'namespace'     => '',
      'property'      => '',
      'resource-type' => '',
      'time'          => '',
      'type'          => '',
      'uninitialized' => '',
      'unknown'       => '',
      'value'         => ''
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '---',
      'capture'       => '',

      'class'         => '',
      'cycle'         => '',
      'file(line)'    => '',
      'key'           => '',
      'method'        => '',
      'modifier'      => '',
      'name'          => '',
      'namespace'     => '',
      'property'      => '',
      'resource-type' => '',
      'time'          => '',
      'type'          => '',
      'uninitialized' => '',
      'unknown'       => '',
      'value'         => ''
   ];
}
