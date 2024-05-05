<?php

declare(strict_types=1);

namespace rnr1721\Core\Contracts\Tables;

/**
 * The TableRendererInterface defines the contract for rendering tabular data.
 */
interface TableRendererInterface
{

    /**
     * Starts the rendering process.
     */
    public function startRender(): void;

    /**
     * Writes the header row to the rendered output.
     *
     * @param array $columns The columns to be written as the header row.
     */
    public function writeHeader(array $columns): void;

    /**
     * Writes a data row to the rendered output.
     *
     * @param array $rowData The data to be written as a row.
     */
    public function writeRow(array $rowData): void;

    /**
     * Ends the rendering process and returns any additional information about the rendered output.
     *
     * @return array Information about the rendered output.
     */
    public function endRender(): array;

    /**
     * Performs any necessary action to handle errors during rendering.
     * For example, it may delete any partially generated output.
     */
    public function dropErrorAction(): void;
}
