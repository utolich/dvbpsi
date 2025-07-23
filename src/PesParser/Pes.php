<?php
namespace PhpBg\DvbPsi\PesParser;

class Pes {
    public $pid;

    public $streamId;

    public $scrabling;

    public $priority;

    public $alignmentIndicator;

    public $copyright;

    public $originalOrCopy;

    public $flagsPtsDts;

    public $flagEscr;

    public $flagEsRate;

    public $flagDsmTrickMode;

    public $flagAdditionalCopyInfo;

    public $flagCrc;

    public $flagExtension;

    public $timestamp;

    public $pts;

    public $dts;

    public $es = null;

    public function __construct()
    {
        $this->timestamp = microtime(true);
    }

}