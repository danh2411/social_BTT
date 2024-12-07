<?php

namespace App\Modules\Brand\Services;


use App\Modules\Brand\Repositories\Elasticsearch\Interfaces\BrandRepositoryInterface;
use App\Modules\Brand\Services\Interfaces\BrandServiceInterface;
use App\Services\RabbitMQService;

class BrandService implements BrandServiceInterface
{
    public function __construct(
        protected BrandRepositoryInterface $brandRepository,
        protected RabbitMQService          $rabbitMQ,

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
    public function updateBrand($id, $data)
    {
        // Tìm kiếm thương hiệu dựa vào ID
        $brand = $this->brandRepository->findById($id);

        if (!$brand) {
            return [
                'success' => false,
                'message' => 'Thương hiệu không tồn tại',
            ];
        }

        // Cập nhật các thông tin của thương hiệu
        $data_update = [
            'name' => $data['name'] ?? $brand->name,
            'description' => $data['description'] ?? $brand->description,
            'thumbnail' => $data['thumbnail'] ?? $brand->thumbnail,
            'updated_at' => time(),
        ];

        // Lưu lại vào cơ sở dữ liệu (MySQL)
        $updateResult = $this->brandRepository->updateBrand($id, $data_update);

        if (!$updateResult) {
            return [
                'success' => false,
                'message' => 'Cập nhật thương hiệu không thành công',
            ];
        }

        // Cập nhật Elasticsearch (nếu cần)
        $updateES = $this->brandRepository->updateBrandInES($id, $data_update);

        if (!$updateES['success']) {
            return [
                'success' => false,
                'message' => 'Cập nhật thương hiệu vào Elasticsearch không thành công',
            ];
        }

        // Gửi thông báo qua RabbitMQ nếu cần thiết
        $data_update['id'] = $id;
        $this->rabbitMQ->publish('brand_queue', [
            'action' => 'update',
            'data' => $data_update
        ]);

        return [
            'success' => true,
            'data' => $data_update,
            'message' => 'Cập nhật thương hiệu thành công',
        ];
    }

}
