<?php

namespace App\Modules\Resources\Services;


use App\Modules\Resources\Models\Resources;
use App\Modules\Resources\Repositories\Elasticsearch\Interfaces\ESResourceRepository;
use App\Modules\Resources\Repositories\Mysql\Interfaces\MysqlResourceRepository;
use App\Modules\Resources\Services\Interfaces\ResourceServiceInterface;
use App\Services\GoogleDriveService;
use App\Services\RabbitMQService;

class ResourceService implements ResourceServiceInterface
{
    public function __construct(
        protected ESResourceRepository $resourceRepository,
        protected RabbitMQService      $rabbitMQ,
        protected GoogleDriveService   $driveService,
        protected  MysqlResourceRepository $resourceMysqlRepository,

    )
    {

    }

    public function createResource($data)
    {

        $file = $data['image'];
        $fileId = $this->driveService->uploadFile($file->path(), $file->getClientOriginalName());
        $thumbnailUrl = $this->driveService->getFileUrl($fileId);
        $data_save = [
            'type'=>$data['type']??'thumbnail',
            'name'=>$data['name']??null,
            'resourceable_id'=>$data['resourceable_id']??null,
            'resourceable_type'=>$data['resourceable_type']??null,
            'description'=>$data['description']??null,
            'path'=>$thumbnailUrl,
            'status'=>1,
            'created_at'=>time(),
            'resource_id'=>time()
        ];

        $saveES = $this->resourceRepository->createResource($data_save);

        if (!$saveES['success']) {
            return [
                'success' => false,
                'message' => 'Lưu tài nguyên vào es không thành công',
            ];
        }
        $data_save['path']=$thumbnailUrl;
        $data_save['id']=$saveES['id'];
        $this->rabbitMQ->publish('resource_queue', [
            'action' => 'create',
            'data' => $data_save
        ]);

        return [
            'success' => true,
            'data' => $data_save,
            'message' => 'Lưu tài nguyên  thành công',
        ];

    }

    public function listResource($data){

        $param = [
            'page' =>!empty($data['page'])? (int)$data['page'] : 1,
            'number' =>!empty($data['per_page'])?  (int)$data['per_page'] : 10,
            'sort' => ['created_at' => 'desc'],
            'filter' =>[],
            'must_not' =>[
                'match'=>['status'=>0],
            ],
            'must' => array_values(array_filter([
                !empty($data['type']) ? ['match' => ['type' => $data['type']]] : null,
                !empty($data['id']) ? ['match' => ['id' => $data['id']]] : null,

                !empty($data['resourceable_id']) ? ['match' => ['resourceable_id' => $data['resourceable_id']]] : null,
                !empty($data['resourceable_type']) ? ['match' => ['resourceable_type' => $data['resourceable_type']]] : null,
                !empty($data['resource_id']) ? ['match' => ['resource_id' => $data['resource_id']]] : null,
            ])),
        ];

        $search_resource=$this->resourceRepository->searchPagination($param);
        if (empty($search_resource))
            return [
                'success' => false,
                'message' => 'Không có dữ liệu',
            ];

        return [
            'success' => true,
            'message' => 'Danh sách tài nguyên ',
            'data'=>$search_resource,
        ];
    }

    public function updateResource($data)
    {

        $search_resource=$this->resourceRepository->findByAttributes(['resource_id'=>$data['resource_id']??'']);

        // Kiểm tra nếu không tìm thấy tài nguyên
        if (empty($search_resource)) {
            return [
                'success' => false,
                'message' => 'Tài nguyên không tồn tại.',
            ];
        }
        // Giả sử chúng ta lấy được tài nguyên đầu tiên từ kết quả tìm kiếm
        $resource = $search_resource['_source']; // hoặc có thể xử lý theo yêu cầu của bạn

        // Cập nhật các trường cần thiết, ví dụ: type, resourceable_id, resourceable_type, v.v.
        $updated_data = [
            'id'=>$resource['id'],
            'type' => !empty($data['type']) ? $data['type'] : $resource['type'],
            'name' => !empty($data['name']) ? $data['name'] : $resource['name'],
            'resourceable_id' => !empty($data['resourceable_id']) ? $data['resourceable_id'] : $resource['resourceable_id'],
            'resourceable_type' => !empty($data['resourceable_type']) ? $data['resourceable_type'] : $resource['resourceable_type'],
            'description' => !empty($data['description']) ? $data['description'] : $resource['description'],
            'updated_at'=>time()
            // Cập nhật các trường khác theo yêu cầu
        ];

        // Cập nhật tài nguyên trong Elasticsearch (bạn có thể gọi phương thức update từ repository của bạn)
        $update_result = $this->resourceRepository->update($updated_data);

        $updated_data['id']=$resource['resource_id'];
        $updated_data['id_es']=$resource['id'];
        $this->rabbitMQ->publish('resource_queue', [
            'action' => 'update',
            'data' => $updated_data
        ]);
        if (!$update_result) {
            return [
                'success' => false,
                'message' => 'Cập nhật tài nguyên không thành công.',
            ];

        }
        return [
        'success' => true,
        'message' => 'Tài nguyên đã được cập nhật thành công.',
    ];
    }
}
