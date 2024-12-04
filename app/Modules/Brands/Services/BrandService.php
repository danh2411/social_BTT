<?php

namespace App\Modules\Brands\Services;


use App\Modules\Brands\Models\Brand;
use App\Modules\Brands\Repositories\Interfaces\BrandRepository;
use App\Modules\Brands\Services\Interfaces\BrandServiceInterface;
use App\Services\RabbitMQService;

class BrandService implements BrandServiceInterface
{
    public function __construct(
        protected BrandRepository $brandRepository,
        protected RabbitMQService      $rabbitMQ,

    )
    {

    }

    public function createBrand($data)
    {

        $data_save = [
            'name'=>$data['name']??1,
            'description'=>$data['description']??'',
            'thumbnail'=>$data['thumbnail']??'',
            'created_at'=>time(),
            'updated_at'=>time(),
        ];

        $saveES = $this->brandRepository->createBrand($data_save);
        if (!$saveES['success']) {
            return [
                'success' => false,
                'message' => 'Lưu thương hiệu vào es không thành công',
            ];
        }
        $data_save['id']=$saveES['id'];
        $this->rabbitMQ->publish('brand_queue', [
            'action' => 'create',
            'data' => $data_save
        ]);
        return [
            'success' => true,
            'data' => $data_save,
            'message' => 'Lưu thương hiệu thành công',
        ];
    }
    public function listBrand($data): array
    {

        $param = [
            'page' =>!empty($data['page'])? (int)$data['page'] : 1,
            'number' =>!empty($data['per_page'])?  (int)$data['per_page'] : 10,
            'sort' => ['created_at' => 'desc'],
            'filter' =>[],
            'must' => array_values(array_filter([
                !empty($data['thumbnail']) ? ['match' => ['thumbnail' => $data['thumbnail']]] : null,
                !empty($data['name']) ? ['match' => ['name' => $data['name']]] : null,
                !empty($data['description']) ? ['match' => ['description' => $data['description']]] : null,
                !empty($data['brand_id']) ? ['match' => ['brand_id' => $data['brand_id']]] : null,
            ])),
        ];

        $search_brand=$this->brandRepository->searchPagination($param);
        if (empty($search_brand))
            return [
                'success' => false,
                'message' => 'Không có dữ liệu',
            ];

        return [
            'success' => true,
            'message' => 'Danh sách tài nguyên ',
            'data'=>$search_brand,
        ];
    }
}
