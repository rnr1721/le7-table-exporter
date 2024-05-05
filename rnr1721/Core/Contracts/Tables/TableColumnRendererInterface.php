<?php

declare(strict_types=1);

namespace rnr1721\Core\Contracts\Tables;

interface TableColumnRendererInterface
{

    /**
     * Some processing for column content
     * 
     * @param string $value Current row value
     * @param  array<array-key, string>|object $row Key=>Value of current row content
     * @param array $options Some values for processor, any data
     * @return string Result of column content
     */
    public function process(string $value, mixed $row, array $options): string;
}
