<?php


namespace Cachitos\VarDebug;


use Cachitos\VarDebug\Renderer\HtmlRenderer;


class VarDebugger {

   /**
    * Default configuration.
    */
   const DEFAULT_CONFIG = [
      'core-config' => [
         'privm' => false,
         'privp' => false,
         'protm' => false,
         'protp' => false,
         'pubm'  => false,
         'pubp'  => true
      ],
      'file-writer-config' => [
         'file' => '/tmp/vardebug/*username*'
      ],
      'render-config' => [
         'byte-format' => 'hexlc',
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
    * Byte formats.
    */
   const BYTE_FORMATS = [ 'bits', 'decimal', 'hexlc', 'hexuc', 'octal' ];


   /**
    * Output identifiers and corresponding handler classes.
    */
   const OUTPUT_WRITERS = [
      'file'    => 'Cachitos\VarDebug\OutputWriter\FileWriter',
      'no-dump' => 'Cachitos\VarDebug\OutputWriter\NullWriter',
      'stdout'  => 'Cachitos\VarDebug\OutputWriter\StdoutWriter'
   ];


   /**
    * Render identifiers and corresponding handler classes.
    */
   const RENDERERS = [
      'color-text'       => 'Cachitos\VarDebug\Renderer\AnsiTextRenderer',
      'console-log-json' => 'Cachitos\VarDebug\Renderer\ConsoleLogJsonRenderer',
      'html'             => 'Cachitos\VarDebug\Renderer\HtmlRenderer',
      'html-comment'     => 'Cachitos\VarDebug\Renderer\HtmlCommentRenderer',
      'plain-text'       => 'Cachitos\VarDebug\Renderer\PlainTextRenderer'
   ];


   /**
    * String formats.
    */
   const STRING_FORMATS = [ 'ascii', 'bytes', 'iso-8859-1', 'json', 'utf-8' ];


   /**
    * Times dump() or dumpByRef() have been called.
    *
    * @var integer
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
    * @var boolean
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

      $this->config = $this->parse_options($options);

      $this->core = new Core($this->config['core-config']);

      $renderer_class = self::RENDERERS[$this->config['vardebugger-config']['render-type']];
      $this->renderer = new $renderer_class($this->config['render-config']);

      $output_writer_class = self::OUTPUT_WRITERS[$this->config['vardebugger-config']['output-type']];
      if ($this->config['vardebugger-config']['output-type'] === 'file') {
         $this->output_writer = new $output_writer_class(
            $this->config['file-writer-config']['file'],
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
    * Function to parse the first parameter of the constructor.
    *
    * @param string $options
    * @return array
    */
   protected function parse_options($options)
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

         elseif ($option === '+all') {
            $config['core-config']['privm'] = true;
            $config['core-config']['privp'] = true;
            $config['core-config']['protm'] = true;
            $config['core-config']['protp'] = true;
            $config['core-config']['pubm']  = true;
            $config['core-config']['pubp']  = true;
         }

         elseif ($option === '-all') {
            $config['core-config']['privm'] = false;
            $config['core-config']['privp'] = false;
            $config['core-config']['protm'] = false;
            $config['core-config']['protp'] = false;
            $config['core-config']['pubm']  = false;
            $config['core-config']['pubp']  = false;
         }

         elseif ($option === '+priv' ) { $config['core-config']['privm'] = true;  $config['core-config']['privp'] = true;  }
         elseif ($option === '+prot' ) { $config['core-config']['protm'] = true;  $config['core-config']['protp'] = true;  }
         elseif ($option === '+pub'  ) { $config['core-config']['pubm' ] = true;  $config['core-config']['pubp' ] = true;  }
         elseif ($option === '-priv' ) { $config['core-config']['privm'] = false; $config['core-config']['privp'] = false; }
         elseif ($option === '-prot' ) { $config['core-config']['protm'] = false; $config['core-config']['protp'] = false; }
         elseif ($option === '-pub'  ) { $config['core-config']['pubm' ] = false; $config['core-config']['pubp' ] = false; }

         elseif ($option === '+privm') { $config['core-config']['privm'] = true;  }
         elseif ($option === '+privp') { $config['core-config']['privp'] = true;  }
         elseif ($option === '+protm') { $config['core-config']['protm'] = true;  }
         elseif ($option === '+protp') { $config['core-config']['protp'] = true;  }
         elseif ($option === '+pubm' ) { $config['core-config']['pubm' ] = true;  }
         elseif ($option === '+pubp' ) { $config['core-config']['pubp' ] = true;  }
         elseif ($option === '-privm') { $config['core-config']['privm'] = false; }
         elseif ($option === '-privp') { $config['core-config']['privp'] = false; }
         elseif ($option === '-protm') { $config['core-config']['protm'] = false; }
         elseif ($option === '-protp') { $config['core-config']['protp'] = false; }
         elseif ($option === '-pubm' ) { $config['core-config']['pubm' ] = false; }
         elseif ($option === '-pubp' ) { $config['core-config']['pubp' ] = false; }

         elseif (preg_match('/^file:(.*)$/', $option, $matches)) {
            $config['vardebugger-config']['output-type'] = 'file';
            $config['file-writer-config']['file'] = trim($matches[1]);
         }

         elseif (in_array($option, self::BYTE_FORMATS)) {
            $config['render-config']['byte-format'] = $option;
         }

         elseif ($option === 'hex') {
            $config['render-config']['byte-format'] = 'hexlc';
         }

         elseif (preg_match('/^([0-9]+)$/', $option, $matches)) {
            $max_length = (int)trim($matches[1]);
            if ($max_length < 0) {
               $max_length = -1;
            }
            $config['render-config']['max-length'] = $max_length;
         }

         elseif (in_array($option, self::STRING_FORMATS)) {
            $config['render-config']['string-format'] = $option;
         }

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
