<?php

namespace App\Repositories\Contracts;

/**
 * Contract every repository implements. Controllers/Services depend
 * on this interface, never on Eloquent models directly — mirrors the
 * AuthRepository pattern on the Flutter side. Lets us swap query
 * logic (e.g. add caching later) without touching controllers.
 */
interface RepositoryInterface
{
    public function all(array $filters = []);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id): bool;
}