<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class HtmlCommentRenderer extends BasicRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "<!-- VarDebug\n",
      'capture'       => "<!-- VarDebug\n",

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
      'header'        => "-->\n",
      'capture'       => "\n-->",

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
