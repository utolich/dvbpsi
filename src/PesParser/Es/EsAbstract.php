<?php

namespace PhpBg\DvbPsi\PesParser\Es;

abstract class EsAbstract {

    /**
     * PhpBg\DvbPsi\Descriptors\Values\EsType
     * @var int $streamType
     */
    protected int $streamType;

    public function __construct($streamType) {
        if (empty($this->streamType)) {
            $this->streamType = $streamType;
        } else if ($this->streamType !== $streamType) {
            throw new \Exception('Wrong stream type');
        }
    }

    public function getStreamType() {
        return $this->streamType;
    }
}