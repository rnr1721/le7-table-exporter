<?php

declare(strict_types=1);

namespace rnr1721\Core\Factories;

use rnr1721\Core\Contracts\Tables\TableExporterInterface;
use rnr1721\Core\Utils\Tables\Writer\TableCsvRendererFile;
use rnr1721\Core\Utils\Tables\Writer\TableCsvRendererString;
use rnr1721\Core\Utils\Tables\Writer\TableHtmlRendererString;
use rnr1721\Core\Utils\Tables\Writer\TableExporter;
use rnr1721\Core\Contracts\Tables\TableExporterFactoryInterface;

class TableExporterWriterFactory implements TableExporterFactoryInterface
{

    public function getTableExporterCsvFile(
            string $exportPath = '',
            string $delimiter = ',',
            bool $unlinkIfExists = false
    ): TableExporterInterface
    {
        $renderer = new TableCsvRendererFile($exportPath, $delimiter, $unlinkIfExists);

        return new TableExporter($renderer);
    }

    public function getTableExporterCsvString(string $delimiter = ','): TableExporterInterface
    {
        $renderer = new TableCsvRendererString($delimiter);

        return new TableExporter($renderer);
    }

    public function getTableExporterHtmlString(
            array $tableClasses = [],
            array $trClasses = [],
            array $tdClasses = [],
            array $thClasses = [],
            string $tableId = ''
    ): TableExporterInterface
    {
        $renderer = new TableHtmlRendererString(
                $tableClasses,
                $trClasses,
                $tdClasses,
                $thClasses,
                $tableId
        );
        return new TableExporter($renderer);
    }
}
