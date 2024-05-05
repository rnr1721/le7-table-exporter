<?php

declare(strict_types=1);

use rnr1721\Core\Exceptions\Tables\TableRendererException;
use rnr1721\Core\Utils\Tables\Writer\TableCsvRendererFile;

class TableCsvRendererTest extends PHPUnit\Framework\TestCase
{

    /** @var string Temporary file path */
    private string $tempFilePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempFilePath = tempnam(sys_get_temp_dir(), 'test_csv_');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
        }
    }

    public function testCsvRendering(): void
    {
        $renderer = new TableCsvRendererFile($this->tempFilePath);

        $renderer->startRender();
        $renderer->writeHeader(['Name', 'Age', 'Country']);
        $renderer->writeRow(['John Doe', 30, 'USA']);
        $renderer->writeRow(['Jane Smith', 25, 'Canada']);
        $renderer->endRender();

        $expectedCsvContent = 'Name,Age,Country"John Doe",30,USA"Jane Smith",25,Canada';
        $currentCsvContent = str_replace(PHP_EOL, "", file_get_contents($this->tempFilePath));

        $this->assertFileExists($this->tempFilePath);
        $this->assertEquals($expectedCsvContent, $currentCsvContent);
    }

    public function testInvalidDelimiter(): void
    {
        $this->expectException(TableRendererException::class);
        $this->expectExceptionMessage('Delimiter must be a single character');

        new TableCsvRendererFile('', ',,');
    }
}
