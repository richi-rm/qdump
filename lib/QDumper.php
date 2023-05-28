<?php


namespace RichiRM\QDump;


use RichiRM\QDump\Renderer\BasicRenderer\HtmlRenderer;


class QDumper {

   /**
    * Byte formats.
    */
   protected const BYTE_FORMATS = [ 'bits', 'decimal', 'hexlc', 'hexuc', 'octal' ];


   /**
    * Default configuration.
    */
   protected const DEFAULT_CONFIG = [
      'core-config' => [
         'byte-format'       => 'hexlc',
         'max-depth'         => 3,
         'max-string-length' => 100,
         'string-format'     => 'utf-8'
      ],
      'filewriter-config' => [
         'file' => '/tmp/qdump/*username*'
      ],
      'render-config'        => [
         'expand-arrays'     => false,
         'sort'              => true,
         'verbose'           => false
      ],
      'qdumper-config' => [
         'output-type' => 'stdout',
         'render-type' => 'html',
         'verbose'     => false
      ]
   ];


   /**
    * Output identifiers and corresponding handler classes.
    */
   protected const OUTPUT_WRITERS = [
      'file'    => 'RichiRM\QDump\OutputWriter\FileWriter',
      'no-dump' => 'RichiRM\QDump\OutputWriter\NullWriter',
      'stdout'  => 'RichiRM\QDump\OutputWriter\StdoutWriter'
   ];


   /**
    * Render identifiers and corresponding handler classes.
    */
   protected const RENDERERS = [
      'ansi'             => 'RichiRM\QDump\Renderer\BasicRenderer\AnsiTextRenderer',
      'html'             => 'RichiRM\QDump\Renderer\BasicRenderer\HtmlRenderer',
      'html-comment'     => 'RichiRM\QDump\Renderer\BasicRenderer\HtmlCommentRenderer',
      'plain-text'       => 'RichiRM\QDump\Renderer\BasicRenderer\PlainTextRenderer'
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

      $renderer_class = self::RENDERERS[$this->config['qdumper-config']['render-type']];
      $this->renderer = new $renderer_class($this->config['render-config']);

      $output_writer_class = self::OUTPUT_WRITERS[$this->config['qdumper-config']['output-type']];
      if ($this->config['qdumper-config']['output-type'] === 'file') {
         $this->output_writer = new $output_writer_class(
            $this->config['filewriter-config']['file'],
            $this->config['qdumper-config']['render-type']
         );
      } else {
         $this->output_writer = new $output_writer_class();
      }
   }


   /**
    * Inspect the passed variables and send the result to the output.
    * Returns a string with the dump.
    *
    * @param $vars variables to inspect
    * @return string 
    */
   public function dump(...$vars)
   {
      $written = '';

      // initial write
      //
      if (!$this->first_dump_done) {
         $written .= $this->initial_write();
         $this->first_dump_done = true;
      }

      foreach ($vars as $var) {

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

      }

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
         $config['qdumper-config']['render-type'] = 'ansi';
      }

      if (!\is_string($options)) {
         return $config;
      }

      foreach (\explode(',', $options) as $option) {

         $option = \trim($option);

         if (0) { }

         //
         // core config
         //

         elseif (\in_array($option, self::BYTE_FORMATS)) {
            $config['core-config']['byte-format'] = $option;
            if ($option === 'bits') {
               $config['core-config']['string-format'] = 'bytes';
            }
         }

         elseif ($option === 'hex') {
            $config['core-config']['byte-format'] = 'hexlc';
         }

         elseif (\preg_match('/^max-depth:(unlimited|[+]?[0-9]+)$/', $option, $matches)) {
            $config['core-config']['max-depth'] = ( $matches[1] === 'unlimited' ? $matches[1] : (int)$matches[1] );
         }

         elseif (\preg_match('/^max-string-length:(unlimited|[+-]?[0-9]+)$/', $option, $matches)) {
            $config['core-config']['max-string-length'] = ( $matches[1] === 'unlimited' ? $matches[1] : (int)$matches[1] );
         }

         elseif (\in_array($option, self::STRING_FORMATS)) {
            $config['core-config']['string-format'] = $option;
         }

         //
         // filewriter config
         //

         elseif (\preg_match('/^file:(.*)$/', $option, $matches)) {
            $config['qdumper-config']['output-type'] = 'file';
            $config['filewriter-config']['file'] = \trim($matches[1]);
         }

         //
         // render config
         //

         elseif ($option === 'expand-arrays') {
            $config['render-config']['expand-arrays'] = true;
         }

         elseif ($option === 'no-sort') {
            $config['render-config']['sort'] = false;
         }

         elseif ($option === 'verbose') {
            $config['render-config']['verbose'] = true;
            $config['qdumper-config']['verbose'] = true;
         }

         //
         // qdumper config
         //

         elseif (\in_array($option, \array_keys(self::OUTPUT_WRITERS))) {
            $config['qdumper-config']['output-type'] = $option;
         }

         elseif (\in_array($option, \array_keys(self::RENDERERS))) {
            $config['qdumper-config']['render-type'] = $option;
         }

         elseif ($option === 'plaintext') {
            $config['qdumper-config']['render-type'] = 'plain-text';
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
      if ($this->config['qdumper-config']['render-type'] === 'html') {
         $written .= $this->output_writer->write("\n" . HtmlRenderer::CSS_STYLES . "\n");
      }
      if ($this->config['qdumper-config']['verbose']) {
         $header_lines = [$this->context->getEnvironmentInfo()];
         if (!$this->context->sapiIsCli()) {
            $header_lines[] = $this->context->getRequestInfo();
         }
         $written .= $this->output_writer->write($this->renderer->renderHeader($header_lines));
      }
      return $written;
   }


   /**
    * Dump the call stack.
    */
   public function trace()
   {
      $written = '';

      if (!$this->first_dump_done) {
         $written .= $this->initial_write();
         $this->first_dump_done = true;
      }

      $trace = [
         'type' => 'trace',
         'trace' => \array_reverse(\debug_backtrace())
      ];

      foreach ($trace['trace'] as $i_line => &$line) {

         // avoid possible warnings
         //
         if (!\array_key_exists('file', $line)) {
            $line['file'] = '?';
         }
         if (!\array_key_exists('line', $line)) {
            $line['line'] = 0;
         }
         if (!\array_key_exists('class', $line)) {
            $line['class'] = '?';
         }
         if (!\array_key_exists('object', $line)) {
            $line['object'] = null;
         }
         if (!\array_key_exists('type', $line)) {
            $line['type'] = 'function';
         }
         if (!\array_key_exists('function', $line)) {
            $line['function'] = '?';
         }
         $line['params'] = [];
         if (!\array_key_exists('args', $line)) {
            $line['args'] = [];
         }

         // function or object
         //
         switch ($line['type']) {
            case '->':
            case '::':
               $line['class'] = $this->core->inspect_class($line['class']);
               $refl_class = new \ReflectionClass($line['class']['class']);
               foreach ($refl_class->getMethods() as $refl_method) {
                  if ($refl_method->getName() === $line['function']) {
                     $line['function'] = $this->core->inspect_method($refl_method);
                     break;
                  }
               }
               break;
            case 'function':
               if (\function_exists($line['function'])) {
                  $line['function'] = $this->core->inspect_function(new \ReflectionFunction($line['function']));
               }
               break;
         }

         // inspect arguments
         //
         foreach ($line['args'] as &$arg) {
            $arg = $this->core->inspect($arg);
         }

      }

      $capture = '';
      $capture .= $this->renderer->preRender($this->capture_sequence_number,
                                             $this->context->getTraceFileLine(),
                                             $this->context->getElapsedTime());
      $capture .= $this->renderer->renderCoreVar($trace);
      $capture .= $this->renderer->postRender();

      $written .= $this->output_writer->write($capture);

      $this->capture_sequence_number++;

      return $written;
   }

}
