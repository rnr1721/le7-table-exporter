<?php

declare(strict_types=1);

namespace rnr1721\Core\Utils\Tables\Writer;

use rnr1721\Core\Contracts\Tables\TableRendererInterface;
use rnr1721\Core\Exceptions\Tables\TableRendererException;
use \Exception;
use function \file_exists,
             \unlink,
             \tempnam,
             \sys_get_temp_dir,
             \fputcsv,
             \fopen,
             \fclose;

/**
 * The TableCsvRendererFile class implements the TableRendererInterface, providing functionality to render data into a CSV file.
 */
class TableCsvRendererFile implements TableRendererInterface
{

    /**
     * CSV delimiter
     * 
     * @var string
     */
    private string $delimiter;

    /**
     * Path to file for export
     * @var string
     */
    private string $exportPath = '';

    /**
     * unlink export file if exists
     * @var bool
     */
    private bool $unlinkIfExists;

    /**
     * 
     * @var mixed File handler
     */
    private $fileHandle;

    /**
     * Constructor.
     *
     * @param string $exportPath      Optional. The path to export the CSV file. If not provided, a temporary file will be created.
     * @param string $delimiter       The delimiter used for CSV rendering.
     * @param bool   $unlinkIfExists Optional. Whether to unlink the file if it already exists.
     */
    public function __construct(
            string $exportPath = '',
            string $delimiter = ',',
            bool $unlinkIfExists = false
    )
    {
        $this->delimiter = $delimiter;
        if (strlen($delimiter) !== 1) {
            throw new TableRendererException('Delimiter must be a single character');
        }
        $this->exportPath = $exportPath;
        $this->unlinkIfExists = $unlinkIfExists;
    }

    /**
     * Starts the rendering process.
     *
     * @throws TableRendererException If unable to create or open the file for writing.
     */
    public function startRender(): void
    {
        if ($this->unlinkIfExists) {
            if (!empty($this->exportPath) && file_exists($this->exportPath)) {
                unlink($this->exportPath);
            }
        }

        if (empty($this->exportPath)) {
            $this->exportPath = tempnam(sys_get_temp_dir(), 'csv_export_');
            if ($this->exportPath === false) {
                throw new TableRendererException('Error: Unable to create temporary file for writing');
            }
        }

        $this->fileHandle = fopen($this->exportPath, 'w');
        if ($this->fileHandle === false) {
            throw new TableRendererException('Error: Unable to open file for writing');
        }
    }

    /**
     * Writes the header row to the CSV file.
     *
     * @param array $columns The columns to be written as the header row.
     *
     * @throws TableRendererException If unable to write the header row to the file.
     */
    public function writeHeader(array $columns): void
    {
        try {
            fputcsv($this->fileHandle, $columns, $this->delimiter);
        } catch (Exception $ex) {
            $this->whenSomethingWentWrong();
            throw new TableRendererException($ex->getMessage());
        }
    }

    /**
     * Writes a data row to the CSV file.
     *
     * @param array $rowData The data to be written as a row.
     *
     * @throws TableRendererException If unable to write the data row to the file.
     */
    public function writeRow(array $rowData): void
    {
        try {
            fputcsv($this->fileHandle, $rowData, $this->delimiter);
        } catch (Exception $ex) {
            $this->whenSomethingWentWrong();
            throw new TableRendererException($ex->getMessage());
        }
    }

    /**
     * Ends the rendering process and returns information about the exported CSV file.
     *
     * @return array Information about the exported CSV file, including its path and delimiter.
     *
     * @throws TableRendererException If unable to close the file handle.
     */
    public function endRender(): array
    {
        try {
            fclose($this->fileHandle);
        } catch (Exception $ex) {
            throw new TableRendererException($ex->getMessage());
        }

        return [
            'export_path' => $this->exportPath,
            'delimiter' => $this->delimiter
        ];
    }

    /**
     * Drops any error action if needed, such as deleting the exported file.
     */
    public function dropErrorAction(): void
    {
        if (file_exists($this->exportPath)) {
            unlink($this->exportPath);
        }
    }

    /**
     * Set export path
     * 
     * @param string $exportPath Path for export
     * @return self Instance of this class
     */
    public function setExportPath(string $exportPath): self
    {
        $this->exportPath = $exportPath;
        return $this;
    }

    private function whenSomethingWentWrong(): void
    {
        if (file_exists($this->exportPath)) {
            unlink($this->exportPath);
        }
    }
}
