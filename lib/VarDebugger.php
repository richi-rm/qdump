<?php


namespace Cachitos\VarDebug;


use Cachitos\VarDebug\Renderer\HtmlRenderer;


class VarDebugger {


   /**
    * Default options php cli.
    */
   const DEFAULT_OPTIONS_CLI = [
      'core-config' => [
         'max-strlen' => 50,
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
      'output-type' => 'stdout',
      'render-type' => 'ansi',
      'verbose'     => false
   ];


   /**
    * Default options php no-cli.
    */
   const DEFAULT_OPTIONS_NO_CLI = [
      'core-config' => [
         'max-strlen' => 50,
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
      'output-type' => 'stdout',
      'render-type' => 'html-comment',
      'verbose'     => false
   ];


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
      'ansi'             => 'Cachitos\VarDebug\Renderer\AnsiRenderer',
      'console-log-json' => 'Cachitos\VarDebug\Renderer\ConsoleLogJsonRenderer',
      'html'             => 'Cachitos\VarDebug\Renderer\HtmlRenderer',
      'html-comment'     => 'Cachitos\VarDebug\Renderer\HtmlCommentRenderer',
      'plain-text'       => 'Cachitos\VarDebug\Renderer\PlainTextRenderer'
   ];


   /**
    * Times dump() or dumpByRef() have been called.
    *
    * @var integer
    */
   protected $capture_sequence_number = 1;


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
    * Options specified when calling constructor.
    *
    * @var array
    */
   protected $options = null;


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

      $this->options = $this->parse_options($options);

      $this->core = new Core($this->options['core-config']);

      $renderer_class = self::RENDERERS[$this->options['render-type']];
      $this->renderer = new $renderer_class();

      $output_writer_class = self::OUTPUT_WRITERS[$this->options['output-type']];
      if ($this->options['output-type'] === 'file') {
         $this->output_writer = new $output_writer_class(
            $this->options['file-writer-config']['file'],
            $this->options['render-type']
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
      if ($this->options['verbose']) {
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
      if ($this->options['verbose']) {
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
    * Function to parse the first parameter of the constructor (VarDebugger
    * options).
    *
    * @param string $options_string
    * @return array
    */
   protected function parse_options($options_string)
   {
      $options = $this->context->sapiIsCli() ?
                    self::DEFAULT_OPTIONS_CLI :
                    self::DEFAULT_OPTIONS_NO_CLI;

      if (!is_string($options_string)) {
         return $options;
      }

      foreach (explode(',', $options_string) as $option) {

         $option = trim($option);

         if (0) { }

         elseif (preg_match('/^max-strlen:(.*)$/', $option, $matches)) {
            $max_strlen = trim($matches[1]);
            if ($max_strlen === 'no-limit') {
               $max_strlen = -1;
            } else {
               $max_strlen = (int)$max_strlen;
               if ($max_strlen < 0) {
                  $max_strlen = -1;
               }
            }
            $options['core-config']['max-strlen'] = $max_strlen;
         }

         elseif ($option === '+all') {
            $options['core-config']['privm'] = true;
            $options['core-config']['privp'] = true;
            $options['core-config']['protm'] = true;
            $options['core-config']['protp'] = true;
            $options['core-config']['pubm']  = true;
            $options['core-config']['pubp']  = true;
         }

         elseif ($option === '-all') {
            $options['core-config']['privm'] = false;
            $options['core-config']['privp'] = false;
            $options['core-config']['protm'] = false;
            $options['core-config']['protp'] = false;
            $options['core-config']['pubm']  = false;
            $options['core-config']['pubp']  = false;
         }

         elseif ($option === '+priv' ) { $options['core-config']['privm'] = true;  $options['core-config']['privp'] = true;  }
         elseif ($option === '+prot' ) { $options['core-config']['protm'] = true;  $options['core-config']['protp'] = true;  }
         elseif ($option === '+pub'  ) { $options['core-config']['pubm' ] = true;  $options['core-config']['pubp' ] = true;  }
         elseif ($option === '-priv' ) { $options['core-config']['privm'] = false; $options['core-config']['privp'] = false; }
         elseif ($option === '-prot' ) { $options['core-config']['protm'] = false; $options['core-config']['protp'] = false; }
         elseif ($option === '-pub'  ) { $options['core-config']['pubm' ] = false; $options['core-config']['pubp' ] = false; }

         elseif ($option === '+privm') { $options['core-config']['privm'] = true;  }
         elseif ($option === '+privp') { $options['core-config']['privp'] = true;  }
         elseif ($option === '+protm') { $options['core-config']['protm'] = true;  }
         elseif ($option === '+protp') { $options['core-config']['protp'] = true;  }
         elseif ($option === '+pubm' ) { $options['core-config']['pubm' ] = true;  }
         elseif ($option === '+pubp' ) { $options['core-config']['pubp' ] = true;  }
         elseif ($option === '-privm') { $options['core-config']['privm'] = false; }
         elseif ($option === '-privp') { $options['core-config']['privp'] = false; }
         elseif ($option === '-protm') { $options['core-config']['protm'] = false; }
         elseif ($option === '-protp') { $options['core-config']['protp'] = false; }
         elseif ($option === '-pubm' ) { $options['core-config']['pubm' ] = false; }
         elseif ($option === '-pubp' ) { $options['core-config']['pubp' ] = false; }

         elseif (preg_match('/^file:(.*)$/', $option, $matches)) {
            $options['output-type'] = 'file';
            $options['file-writer-config']['file'] = trim($matches[1]);
         }

         elseif (in_array($option, array_keys(self::OUTPUT_WRITERS))) {
            $options['output-type'] = $option;
         }

         elseif (in_array($option, array_keys(self::RENDERERS))) {
            $options['render-type'] = $option;
         }

         elseif ($option === 'verbose') { $options['verbose'] = true; }

      }

      return $options;
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
      if ($this->options['render-type'] === 'html') {
         $written .= $this->output_writer->write(HtmlRenderer::CSS_STYLES . "\n");
      }
      if ($this->options['verbose']) {
         $header_lines = [$this->context->getEnvironmentInfo()];
         if (!$this->context->sapiIsCli()) {
            $header_lines[] = $this->context->getRequestInfo();
         }
         $written .= $this->output_writer->write($this->renderer->renderHeader($header_lines));
      }
      return $written;
   }
}
