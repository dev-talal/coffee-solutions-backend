<?php

namespace App\Repositories\Contracts;

interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}