<?php

namespace AppBundle\Tests\Services;

use AppBundle\Services\UploadedFileValidator;

class UploadedFileValidatorTest extends \PHPUnit_Framework_TestCase
{
    protected $file;

    protected function setUp()
    {
        $this->file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testValidate()
    {
    	$supportedTypes = ['audio/mpeg'];
        $this->file->expects($this->any())->method('getClientSize')->will($this->returnValue(50));
        $this->file->expects($this->any())->method('getMimeType')->will($this->returnValue('audio/mpeg'));

        $validator = new UploadedFileValidator($supportedTypes);

        $validationErrors = $validator->validate($this->file);

        $this->assertEquals(count($validationErrors), 0);
    }

    public function testValidateMimeType()
    {
    	$supportedTypes = ['audio/mpeg'];
        $this->file->expects($this->any())->method('getClientSize')->will($this->returnValue(50));
        $this->file->expects($this->any())->method('getMimeType')->will($this->returnValue('image/jpeg'));

        $validator = new UploadedFileValidator($supportedTypes);

        $validationErrors = $validator->validate($this->file);

        $this->assertEquals(count($validationErrors), 1);
    }

    public function testValidateFileSize()
    {
    	$supportedTypes = ['audio/mpeg'];
        $this->file->expects($this->any())->method('getClientSize')->will($this->returnValue(50000001));
        $this->file->expects($this->any())->method('getMimeType')->will($this->returnValue('audio/mpeg'));

        $validator = new UploadedFileValidator($supportedTypes);

        $validationErrors = $validator->validate($this->file);

        $this->assertEquals(count($validationErrors), 1);
    }
}
