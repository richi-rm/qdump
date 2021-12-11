<?php


namespace Onedevs\QDump;


use Onedevs\QDump\Renderer\BasicRenderer\HtmlRenderer;


class VarDebugger {

   /**
    * Byte formats.
    */
   protected const BYTE_FORMATS = [ 'bits', 'decimal', 'hexlc', 'hexuc', 'octal' ];


   /**
    * Default configuration.
    */
   protected const DEFAULT_CONFIG = [
      'core-config' => [
         'max-depth' => 3
      ],
      'filewriter-config' => [
         'file' => '/tmp/vardebug/*username*'
      ],
      'render-config'    => [
         'byte-format'   => 'hexlc',
         'expand-arrays' => false,
         'max-length'    => 100,
         'sort'          => true,
         'string-format' => 'utf-8',
         'verbose'       => false
      ],
      'vardebugger-config' => [
         'output-type' => 'stdout',
         'render-type' => 'html',
         'verbose'     => false
      ]
   ];


   /**
    * Output identifiers and corresponding handler classes.
    */
   protected const OUTPUT_WRITERS = [
      'file'    => 'Onedevs\QDump\OutputWriter\FileWriter',
      'no-dump' => 'Onedevs\QDump\OutputWriter\NullWriter',
      'stdout'  => 'Onedevs\QDump\OutputWriter\StdoutWriter'
   ];


   /**
    * Render identifiers and corresponding handler classes.
    */
   protected const RENDERERS = [
      'ansi'             => 'Onedevs\QDump\Renderer\BasicRenderer\AnsiTextRenderer',
      'console-log-json' => 'Onedevs\QDump\Renderer\ConsoleLogJsonRenderer',
      'html'             => 'Onedevs\QDump\Renderer\BasicRenderer\HtmlRenderer',
      'html-comment'     => 'Onedevs\QDump\Renderer\BasicRenderer\HtmlCommentRenderer',
      'plain-text'       => 'Onedevs\QDump\Renderer\BasicRenderer\PlainTextRenderer'
   ];


   /**
    * String formats.
    */
   protected const STRING_FORMATS = [ 'ascii', 'bytes', 'iso-8859-1', 'json', 'utf-8' ];


   /**
    * Times dump() have been called.
    *
    * @var int
    */
   protected $capture_sequence_number = 1;


   /**
    * Configuration.
    *
    * @var array
    */
   protected $config = null;


   /**
    * Context.
    *
    * @var Context
    */
   protected $context = null;


   /**
    * Core.
    *
    * @var Core
    */
   protected $core = null;


   /**
    * First dump() done.
    *
    * @var bool
    */
   protected $first_dump_done = false;


   /**
    * Output writer.
    *
    * @var OutputWriter
    */
   protected $output_writer = null;


   /**
    * Renderer.
    *
    * @var Renderer
    */
   protected $renderer = null;


   /**
    * Constructor.
    *
    * @param string $options
    */
   public function __construct($options = '')
   {
      $this->context = new Context();

      $this->config = $this->get_config($options);

      $this->core = new Core($this->config['core-config']);

      $renderer_class = self::RENDERERS[$this->config['vardebugger-config']['render-type']];
      $this->renderer = new $renderer_class($this->config['render-config']);

      $output_writer_class = self::OUTPUT_WRITERS[$this->config['vardebugger-config']['output-type']];
      if ($this->config['vardebugger-config']['output-type'] === 'file') {
         $this->output_writer = new $output_writer_class(
            $this->config['filewriter-config']['file'],
            $this->config['vardebugger-config']['render-type']
         );
      } else {
         $this->output_writer = new $output_writer_class();
      }
   }


   /**
    * Inspect the passed variable and send the result to the output.
    * Returns a string with the dump.
    *
    * @param $var variable to inspect
    * @return string 
    */
   public function dump($var = null)
   {
      $written = '';

      // initial write
      //
      if (!$this->first_dump_done) {
         $written .= $this->initial_write();
         $this->first_dump_done = true;
      }

      // capture
      //
      $capture = '';
      $capture .= $this->renderer->preRender($this->capture_sequence_number,
                                             $this->context->getTraceFileLine(),
                                             $this->context->getElapsedTime());
      $capture .= $this->renderer->renderCoreVar($this->core->inspect($var));
      $capture .= $this->renderer->postRender();

      // dump
      //
      $written .= $this->output_writer->write($capture);

      $this->capture_sequence_number++;

      return $written;
   }


   /**
    * Returns a configuration based on the options passed.
    *
    * @param string $options
    * @return array
    */
   protected function get_config($options)
   {
      $config = self::DEFAULT_CONFIG;
      if ($this->context->sapiIsCli()) {
         $config['vardebugger-config']['render-type'] = 'ansi';
      }

      if (!is_string($options)) {
         return $config;
      }

      foreach (explode(',', $options) as $option) {

         $option = trim($option);

         if (0) { }

         //
         // core config
         //

         elseif (preg_match('/^d:([0-9]+)$/', $option, $matches)) {
            $config['core-config']['max-depth'] = (int)$matches[1];
         }

         elseif ($option === 'd:unlimited') {
            $config['core-config']['max-depth'] = \PHP_INT_MAX;
         }

         //
         // filewriter config
         //

         elseif (preg_match('/^file:(.*)$/', $option, $matches)) {
            $config['vardebugger-config']['output-type'] = 'file';
            $config['filewriter-config']['file'] = trim($matches[1]);
         }

         //
         // render config
         //

         elseif (in_array($option, self::BYTE_FORMATS)) {
            $config['render-config']['byte-format'] = $option;
            if ($option === 'bits') {
               $config['render-config']['string-format'] = 'bytes';
            }
         }

         elseif ($option === 'hex') {
            $config['render-config']['byte-format'] = 'hexlc';
         }

         elseif ($option === 'expand-arrays') {
            $config['render-config']['expand-arrays'] = true;
         }

         elseif (preg_match('/^sl:([0-9]+)$/', $option, $matches)) {
            $config['render-config']['max-length'] = (int)$matches[1];
         }

         elseif ($option === 'sl:unlimited') {
            $config['render-config']['max-length'] = -1;
         }

         elseif ($option === 'no-sort') {
            $config['render-config']['sort'] = false;
         }

         elseif (in_array($option, self::STRING_FORMATS)) {
            $config['render-config']['string-format'] = $option;
         }

         elseif ($option === 'verbose') {
            $config['render-config']['verbose'] = true;
            $config['vardebugger-config']['verbose'] = true;
         }

         //
         // vardebugger config
         //

         elseif (in_array($option, array_keys(self::OUTPUT_WRITERS))) {
            $config['vardebugger-config']['output-type'] = $option;
         }

         elseif (in_array($option, array_keys(self::RENDERERS))) {
            $config['vardebugger-config']['render-type'] = $option;
         }

         elseif ($option === 'plaintext') {
            $config['vardebugger-config']['render-type'] = 'plain-text';
         }

      } // foreach

      return $config;
   }


   /**
    * Write output (like HTML styles or verbose headers) before doing the first
    * dump().
    *
    * @return string the written string
    */
   protected function initial_write(): string
   {
      $written = '';
      if ($this->config['vardebugger-config']['render-type'] === 'html') {
         $written .= $this->output_writer->write("\n" . HtmlRenderer::CSS_STYLES . "\n");
      }
      if ($this->config['vardebugger-config']['verbose']) {
         $header_lines = [$this->context->getEnvironmentInfo()];
         if (!$this->context->sapiIsCli()) {
            $header_lines[] = $this->context->getRequestInfo();
         }
         $written .= $this->output_writer->write($this->renderer->renderHeader($header_lines));
      }
      return $written;
   }
}
