<?php


namespace Onedevs\QDump\Renderer\BasicRenderer;


class HtmlCommentRenderer extends BasicRenderer {

   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "\n" . '<!-- QDump' . "\n",
      'capture'       => "\n" . '<!-- QDump' . "\n",

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
      'header'        => '-->' . "\n",
      'capture'       => "\n" . '-->' . "\n",

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
