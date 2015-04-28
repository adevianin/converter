<?php

namespace AppBundle\Tests\Services;

use AppBundle\Services\FormatValidator;

class FormatValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $supportedFormats = ['mp3'];
        $validator = new FormatValidator($supportedFormats);
        $result = $validator->validate('mp3');

        $this->assertEquals(count($result), 0);

        $result = $validator->validate('qwerty');

        $this->assertEquals(count($result), 1);
    }
}
