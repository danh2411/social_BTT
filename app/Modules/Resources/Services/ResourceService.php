<?php

namespace App\Modules\Resources\Services;


use App\Modules\Resources\Models\Resources;
use App\Modules\Resources\Repositories\Interfaces\ResourceRepository;
use App\Modules\Resources\Services\Interfaces\ResourceServiceInterface;
use App\Services\GoogleDriveService;
use App\Services\RabbitMQService;

class ResourceService implements ResourceServiceInterface
{
    public function __construct(
        protected ResourceRepository $resourceRepository,
        protected RabbitMQService    $rabbitMQ,
        protected GoogleDriveService $driveService,

    )
    {

    }

    public function createResource($data)
    {

        $file = $data['thumbnail'];
        $fileId = $this->driveService->uploadFile($file->path(), $file->getClientOriginalName());
        $thumbnailUrl = $this->driveService->getFileUrl($fileId);
        $data_save = [
            'type'=>$data['type']??'thumbnail',
            'resourceable_id'=>$data['resourceable_id']??null,
            'resourceable_type'=>$data['resourceable_type']??null,
            'description'=>$data['description']??null,
            'thumbnail'=>$thumbnailUrl,
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
            'must' => array_values(array_filter([
                !empty($data['type']) ? ['match' => ['type' => $data['type']]] : null,
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
}
