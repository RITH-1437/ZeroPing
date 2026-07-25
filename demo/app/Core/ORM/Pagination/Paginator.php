<?php

namespace App\Core\ORM\Pagination;

use App\Core\ORM\Collection;

class Paginator
{
    /**
     * The collection of items.
     *
     * @var \App\Core\ORM\Collection
     */
    protected Collection $items;

    /**
     * The total number of items.
     *
     * @var int
     */
    protected int $total;

    /**
     * The number of items per page.
     *
     * @var int
     */
    protected int $perPage;

    /**
     * The current page.
     *
     * @var int
     */
    protected int $currentPage;

    /**
     * Create a new paginator instance.
     *
     * @param  \App\Core\ORM\Collection  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int  $currentPage
     * @return void
     */
    public function __construct(Collection $items, int $total, int $perPage, int $currentPage)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    /**
     * Get the items for the current page.
     *
     * @return \App\Core\ORM\Collection
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * Get the total number of items.
     *
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Get the number of items per page.
     *
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the current page.
     *
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function lastPage(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    /**
     * Determine if there are more items in the data store.
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage();
    }
}
