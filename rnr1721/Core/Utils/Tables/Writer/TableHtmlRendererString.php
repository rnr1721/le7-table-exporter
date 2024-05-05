<?php

declare(strict_types=1);

namespace rnr1721\Core\Utils\Tables\Writer;

use rnr1721\Core\Contracts\Tables\TableRendererInterface;

/**
 * The TableHtmlRendererString class implements the TableRendererInterface,
 * providing functionality to render data into an HTML table string.
 */
class TableHtmlRendererString implements TableRendererInterface
{

    /**
     * HTML table string content.
     *
     * @var string
     */
    private string $htmlContent = '';

    /**
     * CSS classes for the table.
     *
     * @var string
     */
    private string $tableClasses = '';

    /**
     * CSS classes for table rows.
     *
     * @var string
     */
    private string $trClasses = '';

    /**
     * CSS classes for table cells.
     *
     * @var string
     */
    private string $tdClasses = '';

    /**
     * CSS classes for table header cells.
     *
     * @var string
     */
    private string $thClasses = '';

    /**
     * Table ID.
     *
     * @var string
     */
    private string $tableId = '';

    /**
     * Constructor.
     *
     * @param array  $tableClasses CSS classes for the table.
     * @param array  $trClasses    CSS classes for table rows.
     * @param array  $tdClasses    CSS classes for table cells.
     * @param array  $thClasses    CSS classes for table header cells.
     * @param string $tableId      Table ID.
     */
    public function __construct(
            array $tableClasses = [],
            array $trClasses = [],
            array $tdClasses = [],
            array $thClasses = [],
            string $tableId = ''
    )
    {
        $this->tableClasses = implode(' ', $tableClasses);
        $this->trClasses = implode(' ', $trClasses);
        $this->tdClasses = implode(' ', $tdClasses);
        $this->thClasses = implode(' ', $thClasses);
        $this->tableId = $tableId;
    }

    /**
     * Starts the rendering process.
     */
    public function startRender(): void
    {
        $this->htmlContent .= '<table';

        // Add table ID if provided
        if (!empty($this->tableId)) {
            $this->htmlContent .= ' id="' . htmlspecialchars($this->tableId) . '"';
        }

        // Add table classes if provided
        if (!empty($this->tableClasses)) {
            $this->htmlContent .= ' class="' . htmlspecialchars($this->tableClasses) . '"';
        }

        $this->htmlContent .= '>' . PHP_EOL;
    }

    /**
     * Writes the header row to the HTML table string.
     *
     * @param array $columns The columns to be written as the header row.
     */
    public function writeHeader(array $columns): void
    {
        // Start the table header row
        $this->htmlContent .= '<thead>' . PHP_EOL . '<tr';

        // Add row classes if provided
        if (!empty($this->trClasses)) {
            $this->htmlContent .= ' class="' . htmlspecialchars($this->trClasses) . '"';
        }

        $this->htmlContent .= '>' . PHP_EOL;
        foreach ($columns as $column) {
            $this->htmlContent .= '<th';

            // Add cell classes if provided
            if (!empty($this->thClasses)) {
                $this->htmlContent .= ' class="' . htmlspecialchars($this->thClasses) . '"';
            }

            $this->htmlContent .= '>' . htmlspecialchars($column) . '</th>';
        }
        $this->htmlContent .= '</tr></thead>' . PHP_EOL;
    }

    /**
     * Writes a data row to the HTML table string.
     *
     * @param array $rowData The data to be written as a row.
     */
    public function writeRow(array $rowData): void
    {
        $this->htmlContent .= '<tr';

        // Add row classes if provided
        if (!empty($this->trClasses)) {
            $this->htmlContent .= ' class="' . htmlspecialchars($this->trClasses) . '"';
        }

        $this->htmlContent .= '>' . PHP_EOL;
        foreach ($rowData as $cell) {
            $this->htmlContent .= '<td';

            // Add cell classes if provided
            if (!empty($this->tdClasses)) {
                $this->htmlContent .= ' class="' . htmlspecialchars($this->tdClasses) . '"';
            }

            $this->htmlContent .= '>' . htmlspecialchars($cell) . '</td>';
        }
        $this->htmlContent .= '</tr>' . PHP_EOL;
    }

    /**
     * Ends the rendering process and returns the exported HTML table string.
     *
     * @return array The exported HTML table string.
     */
    public function endRender(): array
    {
        $this->htmlContent .= '</table>';

        // Return the HTML content
        $result = ['content' => $this->htmlContent];
        $this->htmlContent = '';
        return $result;
    }

    /**
     * Drops any error action if needed.
     */
    public function dropErrorAction(): void
    {
        // Nothing to do for HTML rendering
    }
}
