<?php

namespace paz\Elasticsearch\Indexes;

use Elasticsearch\Client;

interface IndexInterface
{
    public function server(): string;

    public function credentials(): array;

    public function getTimeout(): string;

    public function getId(): string;

    public function prefix(): ?string;

    public function index(): string;

    public function mapping(): array;

    public function settings(): array;

    public function getIndex(): string;

    public function getConnection(): Client;

    public function isConnected(): bool;

    public function exists(): bool;

    public function search(array $params): array;

    public function scrollSearch(array $params): array;

    public function scrollAll(array $params): array;

    public function count(array $params): int;
    public function count_unique(array $params): array;

    public function insert(array $data, bool $refresh = true);
    public function insertResponseID(array $data, bool $refresh = true);

    public function insertWitOptype(array $data, bool $refresh = true);

    public function update(array $data, bool $refresh = true);

    public function updateOrInsert(array $data, array $conditions = [], bool $refresh = true);

    public function delete(array $data, bool $refresh = true);

}
