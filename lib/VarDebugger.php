<?php


namespace Cachitos\VarDebug;


use Cachitos\VarDebug\Renderer\BasicRenderer\HtmlRenderer;


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
      ],
      'filewriter-config' => [
         'file' => '/tmp/vardebug/*username*'
      ],
      'render-config' => [
         'byte-format' => 'hexlc',
         'expand-arrays' => true,
         'max-length' => -1,
         'string-format' => 'utf-8'
      ],
      'vardebugger-config' => [
         'output-type' => 'stdout',
         'render-type' => 'html-comment',
         'verbose'     => false
      ]
   ];


   /**
    * Output identifiers and corresponding handler classes.
    */
   protected const OUTPUT_WRITERS = [
      'file'    => 'Cachitos\VarDebug\OutputWriter\FileWriter',
      'no-dump' => 'Cachitos\VarDebug\OutputWriter\NullWriter',
      'stdout'  => 'Cachitos\VarDebug\OutputWriter\StdoutWriter'
   ];


   /**
    * Render identifiers and corresponding handler classes.
    */
   protected const RENDERERS = [
      'color-text'       => 'Cachitos\VarDebug\Renderer\BasicRenderer\AnsiTextRenderer',
      'console-log-json' => 'Cachitos\VarDebug\Renderer\ConsoleLogJsonRenderer',
      'html'             => 'Cachitos\VarDebug\Renderer\BasicRenderer\HtmlRenderer',
      'html-comment'     => 'Cachitos\VarDebug\Renderer\BasicRenderer\HtmlCommentRenderer',
      'plain-text'       => 'Cachitos\VarDebug\Renderer\BasicRenderer\PlainTextRenderer'
   ];


   /**
    * String formats.
    */
   protected const STRING_FORMATS = [ 'ascii', 'bytes', 'iso-8859-1', 'json', 'utf-8' ];


   /**
    * Times dump() or dumpByRef() have been called.
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
    * First dump() / dumpByRef() done.
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
    * Main public method: inspect the passed variable and send the result to
    * the output.
    *
    * @param $var variable to inspect
    */
   public function dump($var = null, &$output = null)
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
      if ($this->config['vardebugger-config']['verbose']) {
         $capture .= $this->renderer->preRender($this->capture_sequence_number,
                                                $this->context->getTraceFileLine(),
                                                $this->context->getElapsedTime());
      } else {
         $capture .= $this->renderer->preRender($this->capture_sequence_number);
      }
      $capture .= $this->renderer->renderCoreVar($this->core->inspect($var));
      $capture .= $this->renderer->postRender();

      // dump
      //
      $written .= $this->output_writer->write($capture);

      // output
      //
      $output = $written;

      $this->capture_sequence_number++;
   }


   /**
    * This method is the same as dump(), except that the parameter is passed by
    * reference to properly inspect recursive arrays.
    *
    * @param $var variable to inspect
    */
   public function dumpByRef(&$var = null, &$output = null)
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
      if ($this->config['vardebugger-config']['verbose']) {
         $capture .= $this->renderer->preRender($this->capture_sequence_number,
                                                $this->context->getTraceFileLine(),
                                                $this->context->getElapsedTime());
      } else {
         $capture .= $this->renderer->preRender($this->capture_sequence_number);
      }
      $capture .= $this->renderer->renderCoreVar($this->core->inspect($var));
      $capture .= $this->renderer->postRender();

      // dump
      //
      $written .= $this->output_writer->write($capture);

      // output
      //
      $output = $written;

      $this->capture_sequence_number++;
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
         $config['vardebugger-config']['render-type'] = 'color-text';
      }

      if (!is_string($options)) {
         return $config;
      }

      foreach (explode(',', $options) as $option) {

         $option = trim($option);

         if (0) { }

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

         elseif ($option === '-arrays') {
            $config['render-config']['expand-arrays'] = false;
         }

         elseif ($option === '+arrays') {
            $config['render-config']['expand-arrays'] = true;
         }

         elseif (preg_match('/^s([0-9]+)$/', $option, $matches)) {
            $max_length = (int)trim($matches[1]);
            if ($max_length < 0) {
               $max_length = -1;
            }
            $config['render-config']['max-length'] = $max_length;
         }

         elseif (in_array($option, self::STRING_FORMATS)) {
            $config['render-config']['string-format'] = $option;
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

         elseif ($option === 'verbose') {
            $config['vardebugger-config']['verbose'] = true;
         }

      }

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
         $written .= $this->output_writer->write(HtmlRenderer::CSS_STYLES . "\n");
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
