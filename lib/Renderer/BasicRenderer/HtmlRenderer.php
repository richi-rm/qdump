<?php


namespace Onedevs\QDump\Renderer\BasicRenderer;


class HtmlRenderer extends BasicRenderer {

   /**
    * CSS styles.
    */
   const CSS_STYLES = <<<HTML
<style type="text/css">
   .qdump-pre-header    { color: #a0a0a0; background-color: #f0f0f0; padding: 10px; font-size: 11pt; margin: 5px; }
   .qdump-pre-capture   { color: black; background-color: #f0f0f0; padding: 10px; font-size: 11pt; margin: 5px; }
   .qdump-abstract      { font-style: italic; }
   .qdump-class         { color: #008000; font-weight: bold; }
   .qdump-cycle         { color: #ff0000; }
   .qdump-enum          { color: #008000; font-weight: bold; }
   .qdump-fileline      { color: #a0a0a0; text-decoration: underline; }
   .qdump-key           { color: #d08000; }
   .qdump-method        { color: #008000; font-weight: bold; }
   .qdump-modifier      { font-style: italic; }
   .qdump-name          { color: #008000; }
   .qdump-namespace     { color: #006000; }
   .qdump-property      { color: #008000; }
   .qdump-resource-type { color: #ff00ff; }
   .qdump-scalar        { color: #0080ff; font-weight: bold; }
   .qdump-time          { color: #a0a0a0; }
   .qdump-type          { font-style: italic; }
   .qdump-uninitialized { font-style: italic; }
   .qdump-unknown       { color: #ff0000; }
</style>
HTML;


   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "\n" . '<pre class="qdump qdump-pre-header">',
      'capture'       => "\n" . '<pre class="qdump qdump-pre-capture">',

      'abstract'      => '<span class="qdump qdump-abstract">',
      'class'         => '<span class="qdump qdump-class">',
      'cycle'         => '<span class="qdump qdump-cycle">',
      'enum'          => '<span class="qdump qdump-enum">',
      'file(line)'    => '<span class="qdump qdump-fileline">',
      'key'           => '<span class="qdump qdump-key">',
      'method'        => '<span class="qdump qdump-method">',
      'modifier'      => '<span class="qdump qdump-modifier">',
      'name'          => '<span class="qdump qdump-name">',
      'namespace'     => '<span class="qdump qdump-namespace">',
      'property'      => '<span class="qdump qdump-property">',
      'resource-type' => '<span class="qdump qdump-resource-type">',
      'scalar'        => '<span class="qdump qdump-scalar">',
      'time'          => '<span class="qdump qdump-time">',
      'type'          => '<span class="qdump qdump-type">',
      'uninitialized' => '<span class="qdump qdump-uninitialized">',
      'unknown'       => '<span class="qdump qdump-unknown">'
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '</pre>' . "\n",
      'capture'       => '</pre>' . "\n",

      'abstract'      => '</span>',
      'class'         => '</span>',
      'cycle'         => '</span>',
      'enum'          => '</span>',
      'file(line)'    => '</span>',
      'key'           => '</span>',
      'method'        => '</span>',
      'modifier'      => '</span>',
      'name'          => '</span>',
      'namespace'     => '</span>',
      'property'      => '</span>',
      'resource-type' => '</span>',
      'scalar'        => '</span>',
      'time'          => '</span>',
      'type'          => '</span>',
      'uninitialized' => '</span>',
      'unknown'       => '</span>'
   ];
}
