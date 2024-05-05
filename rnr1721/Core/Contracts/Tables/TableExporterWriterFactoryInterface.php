<?php

declare(strict_types=1);

namespace rnr1721\Core\Contracts\Tables;

interface TableExporterWriterFactoryInterface
{

    public function getTableExporterCsvFile(
            string $exportPath = '',
            string $delimiter = ',',
            bool $unlinkIfExists = false
    ): TableExporterInterface;

    public function getTableExporterCsvString(string $delimiter = ','): TableExporterInterface;

    public function getTableExporterHtmlString(
            array $tableClasses = [],
            array $trClasses = [],
            array $tdClasses = [],
            array $thClasses = [],
            string $tableId = ''
    ): TableExporterInterface;
}
