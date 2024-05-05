<?php

declare(strict_types=1);

namespace rnr1721\Core\Contracts\Tables;

/**
 * The TableExporterInterface defines the contract for classes responsible for
 * exporting data into tabular formats.
 */
interface TableExporterInterface
{

    /**
     * Creates a table based on the provided data, columns, and processors.
     *
     * @param array $data       The data to be exported.
     * @param array $columns    Optional. The columns to include in the table.
     * @param array $processors Optional. An array of processors to apply to the data.
     *
     * @return array Information about the exported table.
     */
    public function create(
            array $data,
            array $columns = [],
            array $processors = []
    ): array;

    /**
     * Turn on or off table header
     * 
     * @param bool $state On or Off the table header
     * @return self
     */
    public function setWriteHeader(bool $state): self;

    /**
     * Turn on or off table header
     * 
     * @param bool $state On or Off the table body
     * @return self
     */
    public function setWriteBody(bool $state): self;
}
