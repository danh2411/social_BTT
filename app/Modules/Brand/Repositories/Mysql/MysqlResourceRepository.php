<?php

namespace App\Modules\Resources\Repositories\Mysql;

use App\Modules\Resources\Repositories\Mysql\Interfaces\MysqlResourceRepository as MysqlRepositoryInterface;
use App\Modules\Resources\Models\Resources;

class MysqlResourceRepository implements MysqlRepositoryInterface
{

    public function all()
    {
        return Resources::all();
    }

    public function find($id)
    {
      return Resources::find($id);
    }

    public function create(array $data)
    {
        $resource=new Resources();
        $resource->fill($data);
        $resource->save();
        return   $resource;
    }

    public function update($id, array $data)
    {
       $resource=Resources::find($id);

       $resource->update($data);
        return $resource;
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }
}
