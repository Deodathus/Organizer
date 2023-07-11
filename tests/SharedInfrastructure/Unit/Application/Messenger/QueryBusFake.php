<?php
declare(strict_types=1);

namespace App\Tests\SharedInfrastructure\Unit\Application\Messenger;

use App\Shared\Application\Messenger\Query;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Application\Messenger\QueryHandler;

final class QueryBusFake implements QueryBus
{
    /**
     * @var array<string, QueryHandler> $mapping
     */
    private array $mapping = [];

    public function handle(Query $query): mixed
    {
        if (array_key_exists(get_class($query), $this->mapping)) {
            return ($this->mapping[get_class($query)])($query);
        }

        throw new \LogicException(sprintf('There is no handler specified for "%s" query', get_class($query)));
    }

    public function addHandler(string $query, QueryHandler $queryHandler): void
    {
        $this->mapping[$query] = $queryHandler;
    }
}
