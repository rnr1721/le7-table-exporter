<?php

declare(strict_types=1);

namespace rnr1721\Core\Utils\Tables\Writer;

use rnr1721\Core\Contracts\Tables\TableRendererInterface;
use rnr1721\Core\Exceptions\Tables\TableRendererException;

/**
 * The TableCsvRendererString class implements the TableRendererInterface,
 * providing functionality to render data into a CSV string.
 */
class TableCsvRendererString implements TableRendererInterface
{

    /**
     * CSV delimiter.
     *
     * @var string
     */
    private string $delimiter;

    /**
     * CSV string content.
     *
     * @var string
     */
    private string $csvContent = '';

    /**
     * File handle for CSV stream.
     *
     * @var resource|closed-resource|null
     */
    private $fileHandle = null;

    /**
     * Constructor.
     *
     * @param string $delimiter The delimiter used for CSV rendering.
     */
    public function __construct(string $delimiter = ',')
    {
        $this->delimiter = $delimiter;
        if (strlen($delimiter) !== 1) {
            throw new TableRendererException('Delimiter must be a single character');
        }
    }

    /**
     * Starts the rendering process.
     *
     * @throws TableRendererException If unable to create or open the file for writing.
     */
    public function startRender(): void
    {
        $this->fileHandle = fopen('php://memory', 'rw');
        if ($this->fileHandle === false) {
            throw new TableRendererException('Unable to open memory stream for writing');
        }
    }

    /**
     * Writes the header row to the CSV string.
     *
     * @param array $columns The columns to be written as the header row.
     *
     * @throws TableRendererException If unable to write the header row to the file.
     */
    public function writeHeader(array $columns): void
    {
        $this->writeRow($columns);
    }

    /**
     * Writes a data row to the CSV string.
     *
     * @param array $rowData The data to be written as a row.
     *
     * @throws TableRendererException If unable to write the data row to the file.
     */
    public function writeRow(array $rowData): void
    {
        if (!is_resource($this->fileHandle)) {
            throw new TableRendererException('Memory stream is not open');
        }

        fputcsv($this->fileHandle, $rowData, $this->delimiter);
    }

    /**
     * Ends the rendering process and returns the exported CSV string.
     *
     * @return array The exported CSV string.
     */
    public function endRender(): array
    {
        if (!is_resource($this->fileHandle)) {
            throw new TableRendererException('Memory stream is not open');
        }

        rewind($this->fileHandle);

        $this->csvContent = stream_get_contents($this->fileHandle);

        fclose($this->fileHandle);

        return ['content' => $this->csvContent];
    }

    /**
     * Drops any error action if needed, such as deleting the exported file.
     */
    public function dropErrorAction(): void
    {
        if (is_resource($this->fileHandle)) {
            fclose($this->fileHandle);
        }
    }
}
