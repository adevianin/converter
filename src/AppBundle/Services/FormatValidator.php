<?php

namespace AppBundle\Services;

class FormatValidator
{
    private $supportedFormats;

    public function __construct($supportedFormats)
    {
        $this->supportedFormats = $supportedFormats;
    }

    public function validate($format)
    {
        $result = [];
        if (!in_array($format, $this->supportedFormats)) {
            $result[] = $format.' format is not supported';
        }

        return $result;
    }
}
