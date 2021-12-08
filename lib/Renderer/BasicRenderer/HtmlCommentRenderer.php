<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class HtmlCommentRenderer extends BasicRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => '<!-- VarDebug' . "\n",
      'capture'       => '<!-- VarDebug' . "\n",

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
      'header'        => '-->' . "\n\n",
      'capture'       => "\n" . '-->',

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
