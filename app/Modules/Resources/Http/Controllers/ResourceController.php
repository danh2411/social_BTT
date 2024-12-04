<?php

namespace App\Modules\Resources\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Resources\Http\Requests\CreateResourceRequest;
use App\Modules\Resources\Repositories\Interfaces\ResourceRepository;
use App\Modules\Resources\Services\Interfaces\ResourceServiceInterface;
use App\Services\GoogleDriveService;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ResourceController extends Controller
{

    public function __construct(
        protected ResourceRepository       $newsletterRepository,
        protected ResourceServiceInterface $resourceService,
        protected RabbitMQService          $rabbitMQ,
        protected GoogleDriveService       $driveService,
    )
    {

    }


    public function createResource(Request $request): JsonResponse
    {

        $data = $request->all();


        // Upload ảnh lên Google Drive
        if (!$request->hasFile('thumbnail')) {
            return $this->responseJsonError('Tài nguyên tải lên không hợp lệ', 422, 'Tài nguyên tải lên không hợp lệ');

        }

        $file = $request->file('thumbnail');
        $data['thumbnail'] = $file;
        $data['resourceable_id']=0;
        $data['resourceable_type']='brand';
        $result = $this->resourceService->createResource($data);

        if (!empty($result['success']) && $result['success'] === true) {
            $response = $result['data'] ?? [];
            return $this->responseJsonSuccess($response, $result['message']);
        }
        return $this->responseJsonError($result['message'] ?? 'Tạo tài nguyên không thành công ', 500,
            'Tạo tài nguyên không thành công ');
    }
    public function listResource(Request $request): JsonResponse
    {

        $data = $request->all();
        $result = $this->resourceService->listResource($data);
        if (!empty($result['success']) && $result['success'] === true) {
            $response = $result['data']['list'] ?? [];
            $meta=$response['meta'] ?? [];
            unset($response['meta']);
            return $this->responseJsonSuccess($response, $result['message'],$meta);
        }
        return $this->responseJsonError($result['message'] , 200,
            $result['message'] );
    }
}
