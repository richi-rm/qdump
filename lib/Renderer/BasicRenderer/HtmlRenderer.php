<?php


namespace Cachitos\VarDebug\Renderer\BasicRenderer;


class HtmlRenderer extends BasicRenderer {

   /**
    * CSS styles.
    */
   const CSS_STYLES = <<<HTML
<style type="text/css">
   .vardebug-pre-header    { color: #a0a0a0; background-color: #f0f0f0; padding: 10px; font-size: 11pt; margin: 5px; }
   .vardebug-pre-capture   { color: black; background-color: #f0f0f0; padding: 10px; font-size: 11pt; margin: 5px; }
   .vardebug-class         { color: #008000; font-weight: bold; }
   .vardebug-cycle         { color: #ff0000; }
   .vardebug-enum          { color: #008000; font-weight: bold; }
   .vardebug-fileline      { color: #a0a0a0; text-decoration: underline; }
   .vardebug-key           { color: #d08000; }
   .vardebug-method        { color: #008000; font-weight: bold; }
   .vardebug-modifier      { font-style: italic; }
   .vardebug-name          { color: #008000; }
   .vardebug-namespace     { color: #004000; }
   .vardebug-property      { color: #008000; }
   .vardebug-resource-type { color: #ff00ff; }
   .vardebug-scalar        { color: #0080ff; font-weight: bold; }
   .vardebug-time          { color: #a0a0a0; }
   .vardebug-type          { font-style: italic; }
   .vardebug-uninitialized { font-style: italic; }
   .vardebug-unknown       { color: #ff0000; }
</style>
HTML;


   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => '<pre class="vardebug vardebug-pre-header">',
      'capture'       => '<pre class="vardebug vardebug-pre-capture">',

      'class'         => '<span class="vardebug vardebug-class">',
      'cycle'         => '<span class="vardebug vardebug-cycle">',
      'enum'          => '<span class="vardebug vardebug-enum">',
      'file(line)'    => '<span class="vardebug vardebug-fileline">',
      'key'           => '<span class="vardebug vardebug-key">',
      'method'        => '<span class="vardebug vardebug-method">',
      'modifier'      => '<span class="vardebug vardebug-modifier">',
      'name'          => '<span class="vardebug vardebug-name">',
      'namespace'     => '<span class="vardebug vardebug-namespace">',
      'property'      => '<span class="vardebug vardebug-property">',
      'resource-type' => '<span class="vardebug vardebug-resource-type">',
      'scalar'        => '<span class="vardebug vardebug-scalar">',
      'time'          => '<span class="vardebug vardebug-time">',
      'type'          => '<span class="vardebug vardebug-type">',
      'uninitialized' => '<span class="vardebug vardebug-uninitialized">',
      'unknown'       => '<span class="vardebug vardebug-unknown">'
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '</pre>' . "\n\n",
      'capture'       => '</pre>',

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
