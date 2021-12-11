<?php


namespace Onedevs\QDump\Renderer\BasicRenderer;


class AnsiTextRenderer extends BasicRenderer {

   /**
    * ANSI color escape sequences.
    */
   const ANSI_CODES = [

      'reset'       => "\033[0m",

      'bold'        => "\033[1m",
      'italic'      => "\033[3m",
      'underline'   => "\033[4m",

      'black'       => "\033[30m",
      'black_bri'   => "\033[90m",
      'blue'        => "\033[34m",
      'blue_bri'    => "\033[94m",
      'cyan'        => "\033[36m",
      'cyan_bri'    => "\033[96m",
      'green'       => "\033[32m",
      'green_bri'   => "\033[92m",
      'magenta'     => "\033[35m",
      'magenta_bri' => "\033[95m",
      'red'         => "\033[31m",
      'red_bri'     => "\033[91m",
      'white'       => "\033[37m",
      'white_bri'   => "\033[97m",
      'yellow'      => "\033[33m",
      'yellow_bri'  => "\033[93m"

   ];


   /**
    * Data prefixes.
    *
    * @var array
    */
   protected $prefixes = [
      'header'        => "\n" . self::ANSI_CODES['blue_bri'] . '---' . "\n",
      'capture'       => "\n",

      'class'         => self::ANSI_CODES['bold'] . self::ANSI_CODES['green_bri'],
      'cycle'         => self::ANSI_CODES['red_bri'],
      'enum'          => self::ANSI_CODES['bold'] . self::ANSI_CODES['green_bri'],
      'file(line)'    => self::ANSI_CODES['underline'] . self::ANSI_CODES['black_bri'],
      'key'           => self::ANSI_CODES['yellow_bri'],
      'method'        => self::ANSI_CODES['bold'] . self::ANSI_CODES['green_bri'],
      'modifier'      => self::ANSI_CODES['italic'],
      'name'          => self::ANSI_CODES['green_bri'],
      'namespace'     => self::ANSI_CODES['green'],
      'property'      => self::ANSI_CODES['green_bri'],
      'resource-type' => self::ANSI_CODES['magenta_bri'],
      'scalar'        => self::ANSI_CODES['bold'] . self::ANSI_CODES['cyan_bri'],
      'time'          => self::ANSI_CODES['blue_bri'],
      'type'          => self::ANSI_CODES['italic'] . self::ANSI_CODES['white_bri'],
      'uninitialized' => self::ANSI_CODES['italic'] . self::ANSI_CODES['white_bri'],
      'unknown'       => self::ANSI_CODES['red_bri']
   ];


   /**
    * Data suffixes.
    *
    * @var array
    */
   protected $suffixes = [
      'header'        => '---' . self::ANSI_CODES['reset'] . "\n",
      'capture'       => "\n",

      'class'         => self::ANSI_CODES['reset'],
      'cycle'         => self::ANSI_CODES['reset'],
      'enum'          => self::ANSI_CODES['reset'],
      'file(line)'    => self::ANSI_CODES['reset'],
      'key'           => self::ANSI_CODES['reset'],
      'method'        => self::ANSI_CODES['reset'],
      'modifier'      => self::ANSI_CODES['reset'],
      'name'          => self::ANSI_CODES['reset'],
      'namespace'     => self::ANSI_CODES['reset'],
      'property'      => self::ANSI_CODES['reset'],
      'resource-type' => self::ANSI_CODES['reset'],
      'scalar'        => self::ANSI_CODES['reset'],
      'time'          => self::ANSI_CODES['reset'],
      'type'          => self::ANSI_CODES['reset'],
      'uninitialized' => self::ANSI_CODES['reset'],
      'unknown'       => self::ANSI_CODES['reset']
   ];
}
