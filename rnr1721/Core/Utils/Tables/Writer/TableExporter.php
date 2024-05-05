<?php

declare(strict_types=1);

namespace rnr1721\Core\Utils\Tables\Writer;

use rnr1721\Core\Contracts\Tables\TableColumnRendererInterface;
use rnr1721\Core\Contracts\Tables\TableExporterInterface;
use rnr1721\Core\Contracts\Tables\TableRendererInterface;
use rnr1721\Core\Exceptions\Tables\TableExporterException;
use function reset,
             array_keys,
             is_array,
             is_object,
             count,
             implode;

/**
 * The TableExporter class implements the TableExporterInterface, providing
 * functionality to export data with customizable rendering and processing options.
 */
class TableExporter implements TableExporterInterface
{

    /**
     * Flag for write table header
     * 
     * @var bool
     */
    private bool $writeHeader = true;

    /**
     * Flag for write table body
     * 
     * @var bool
     */
    private bool $writeBody = true;

    /**
     * @var TableRendererInterface The table renderer instance.
     */
    private TableRendererInterface $tableRenderer;

    /**
     * Constructor.
     *
     * @param TableRendererInterface $tableRenderer The table renderer instance.
     */
    public function __construct(TableRendererInterface $tableRenderer)
    {
        $this->tableRenderer = $tableRenderer;
    }

    /**
     * Creates a table based on the provided data, columns, and processors.
     *
     * @param array $data            The data to be exported.
     * @param array $columns         The columns to include in the table.
     * @param array $processors      Optional. An array of processors to apply to the data.
     * @param array $processorOptions Optional. Additional options to be passed to the processors.
     *
     * @return array Information about the exported table.
     *
     * @throws TableExporterException If there is an issue with the data or rendering process.
     */
    public function create(
            array $data,
            array $columns = [],
            array $processors = [],
            array $processorOptions = []
    ): array
    {

        if (empty($data)) {
            throw new TableExporterException('No data to export');
        }

        $this->tableRenderer->startRender();

        $tableColumns = $this->formatColumns($data, $columns);

        $this->tableRenderer->writeHeader($tableColumns);

        $this->formatData($data, $tableColumns, $processors, $processorOptions);

        $rendererResult = $this->tableRenderer->endRender();

        $result = [
            'count_records' => count($data),
            'count_columns' => count($columns),
            'processed_columns' => implode(',', array_keys($processors))
        ];

        $resultFinal = array_merge($result, $rendererResult);

        return $resultFinal;
    }

    /**
     * Formats the columns based on the provided data.
     *
     * @param array $data    The data to be exported.
     * @param array $columns The columns to include in the table.
     *
     * @return array The formatted columns.
     *
     * @throws TableExporterException If there is an issue with the data format.
     */
    private function formatColumns(array $data, array $columns): array
    {
        if (empty($columns)) {
            $firstItem = reset($data);
            if (is_array($firstItem) || is_object($firstItem)) {
                $columnsToPrepare = array_keys((array) $firstItem);
                $columns = [];
                foreach ($columnsToPrepare as $currentColumnToPrepare) {
                    $columns[$currentColumnToPrepare] = $currentColumnToPrepare;
                }
            } else {
                throw new TableExporterException('Invalid data format: each element of $data must be an object or an array.');
            }
        }
        return $columns;
    }

    /**
     * Formats the data rows based on the provided columns and processors.
     *
     * @param array $data            The data to be exported.
     * @param array $tableColumns    The columns to include in the table.
     * @param array $processors      The processors to apply to the data.
     * @param array $processorOptions The additional options to be passed to the processors.
     *
     * @throws TableExporterException If there is an issue with the data format or rendering process.
     */
    private function formatData(array $data, array $tableColumns, array $processors, array $processorOptions): void
    {
        foreach ($data as $item) {
            if (!is_object($item) && !is_array($item)) {
                $this->tableRenderer->dropErrorAction();
                throw new TableExporterException('Invalid data format: each element of $data must be an object or an array.');
            }
            $rowData = array();
            foreach (array_keys($tableColumns) as $columnName) {
                if (is_object($item) && isset($item->{$columnName})) {
                    $preValue = $item->{$columnName};
                } elseif (is_array($item) && isset($item[$columnName])) {
                    $preValue = $item[$columnName];
                } else {
                    $preValue = '';
                }

                if (isset($processors[$columnName]) && $processors[$columnName] instanceof TableColumnRendererInterface) {
                    $value = $processors[$columnName]->process($preValue, $item, $processorOptions);
                } elseif (isset($processors[$columnName]) && is_callable($processors[$columnName])) {
                    $value = $processors[$columnName]($preValue, $item, $processorOptions);
                } else {
                    $value = $preValue;
                }

                $rowData[] = $value;
            }
            $this->tableRenderer->writeRow($rowData);
        }
    }

    /**
     * Turn on or off table header
     * 
     * @param bool $state On or Off the table header
     * @return self
     */
    public function setWriteHeader(bool $state): self
    {
        $this->writeHeader = $state;
        return $this;
    }

    /**
     * Turn on or off table header
     * 
     * @param bool $state On or Off the table body
     * @return self
     */
    public function setWriteBody(bool $state): self
    {
        $this->writeBody = $state;
        return $this;
    }
}
