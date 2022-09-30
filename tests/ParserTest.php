<?php

use Benlipp\SrtParser\Exceptions\FileNotFoundException;
use Benlipp\SrtParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{

    /**
     * Check for possible syntax errors
     */
    public function testSyntax()
    {
        $var = new Parser();
        $this->assertTrue(is_object($var));
        unset($var);
    }

    public function testFileNotFound()
    {
        $parser = new Parser();
        $this->expectException(FileNotFoundException::class);
        $result = $parser->loadFile('NotARealFile');
        $this->assertFalse(is_object($result));
    }

    public function testReadFile()
    {
        $tests = $this->parseProvider();

        foreach ($tests as $test) {
            $parser = new Parser();
            $result = $parser->loadFile(__DIR__ . $test['file']);
            $this->assertTrue(is_object($result));
        }
    }

    public function testLoadString()
    {
        $tests = $this->parseProvider();

        foreach ($tests as $test) {
            $captionString = file_get_contents(__DIR__ . $test['file']);
            $parser = new Parser();
            $result = $parser->loadString($captionString);
            $this->assertTrue(is_object($result));
        }
    }

    public function testParse()
    {
        $tests = $this->parseProvider();

        foreach ($tests as $test) {
            $parser = new Parser();
            $parser->loadFile(__DIR__ . $test['file']);
            $captions = $parser->parse();

            foreach ($captions as $key => $caption) {
                $this->assertEquals($test['captions'][$key]['startTime'], $caption->startTime);
                $this->assertEquals($test['captions'][$key]['endTime'], $caption->endTime);
                $this->assertEquals($test['captions'][$key]['text'], $caption->text);
            }
        }
    }

    public function parseProvider()
    {
        return [
            [
                'file' => '/files/SampleCaptions.srt',
                'captions' => [
                    [
                        'startTime' => 0,
                        'endTime'   => 3,
                        'text'      => "Type Caption Text HereCar is backing up a little bit"
                    ],
                    [
                        'startTime' => 3,
                        'endTime'   => 4,
                        'text'      => "Yep there it goes"
                    ],
                    [
                        'startTime' => 4,
                        'endTime'   => 5,
                        'text'      => "Don't hit it!\nTesting Multi"
                    ],
                ],
            ],
            [
                'file' => '/files/SampleCaptions2.srt',
                'captions' => [
                    [
                        'startTime' => 5,
                        'endTime'   => 6,
                        'text'      => "Hey, what's up man?"
                    ],
                    [
                        'startTime' => 7,
                        'endTime'   => 8,
                        'text'      => "Well, good."
                    ],
                    [
                        'startTime' => 10,
                        'endTime'   => 13,
                        'text'      => "So we had like 10 minutes, I thought I'm going to quickly steal 10 minutes of your time."
                    ],
                ],
            ]
        ];
    }
}
