<?php


namespace Cachito\VarDebug;


use Cachito\VarDebug\Renderer\HtmlRenderer;


class VarDebugger {


   const DEFAULT_OPTIONS = [
      'output-type' => 'stdout',
      'render-type' => 'html-comment',
      'mpriv'       => false,
      'mprot'       => false,
      'mpub'        => false,
      'ppriv'       => false,
      'pprot'       => false,
      'ppub'        => true,
      'verbose'     => false
   ];


   /**
    * Default render type.
    */
   const DEFAULT_OUTPUT_DIR_PATH = '/tmp/';


   /**
    * Output identifiers and corresponding handler classes.
    */
   const OUTPUT_WRITERS = [
      'file'    => 'Cachito\VarDebug\OutputWriter\FileWriter',
      'no-dump' => 'Cachito\VarDebug\OutputWriter\NullWriter',
      'stdout'  => 'Cachito\VarDebug\OutputWriter\StdoutWriter'
   ];


   /**
    * Render identifiers and corresponding handler classes.
    */
   const RENDERERS = [
      'ansi'             => 'Cachito\VarDebug\Renderer\AnsiRenderer',
      'console-log-json' => 'Cachito\VarDebug\Renderer\ConsoleLogJsonRenderer',
      'html'             => 'Cachito\VarDebug\Renderer\HtmlRenderer',
      'html-comment'     => 'Cachito\VarDebug\Renderer\HtmlCommentRenderer',
      'plain-text'       => 'Cachito\VarDebug\Renderer\PlainTextRenderer'
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
    * @param $options render, output and verbose options
    * @param $output_dir_path directory where the data will be saved in case
    *                         output type == 'file'
    */
   public function __construct($options = '', $output_dir_path = self::DEFAULT_OUTPUT_DIR_PATH)
   {
      $this->options = $this->parse_options($options);

      if (!is_string($output_dir_path)) {
         $output_dir_path = self::DEFAULT_OUTPUT_DIR_PATH;
      }

      // instantiate objects
      //
      $this->context = new Context();
      $this->core = new Core();

      $renderer_class = self::RENDERERS[$this->options['render-type']];
      $this->renderer = new $renderer_class();

      $output_writer_class = self::OUTPUT_WRITERS[$this->options['output-type']];
      if ($this->options['output-type'] === 'file') {
         $this->output_writer = new $output_writer_class($output_dir_path, $this->options['render-type']);
      } else {
         $this->output_writer = new $output_writer_class();
      }
   }


   /**
    * Add class methods to the output.
    *
    * @param boolean $add true | false
    */
   public function addClassMethods($add = true)
   {
      $this->core->addClassMethods($add);
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
      $options = self::DEFAULT_OPTIONS;

      if (!is_string($options_string)) {
         return $options;
      }

      foreach (explode(',', $options_string) as $option) {

         $option = trim($option);

         if (0) { }

         elseif (in_array($option, array_keys(self::OUTPUT_WRITERS))) {
            $options['output-type'] = $option;
         }

         elseif (in_array($option, array_keys(self::RENDERERS))) {
            $options['render-type'] = $option;
         }

         elseif ($option === '+mpriv' ) { $options['mpriv']   = true;  }
         elseif ($option === '+mprot' ) { $options['mprot']   = true;  }
         elseif ($option === '+mpub'  ) { $options['mpub' ]   = true;  }
         elseif ($option === '-mpriv' ) { $options['mpriv']   = false; }
         elseif ($option === '-mprot' ) { $options['mprot']   = false; }
         elseif ($option === '-mpub'  ) { $options['mpub' ]   = false; }
         elseif ($option === '+ppriv' ) { $options['ppriv']   = true;  }
         elseif ($option === '+pprot' ) { $options['pprot']   = true;  }
         elseif ($option === '+ppub'  ) { $options['ppub' ]   = true;  }
         elseif ($option === '-ppriv' ) { $options['ppriv']   = false; }
         elseif ($option === '-pprot' ) { $options['pprot']   = false; }
         elseif ($option === '-ppub'  ) { $options['ppub' ]   = false; }

         elseif ($option === 'verbose') { $options['verbose'] = true;  }

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
