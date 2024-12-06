<?php

namespace App\Modules\Resources\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Resources\Http\Requests\CreateResourceRequest;
use App\Modules\Resources\Repositories\Elasticsearch\Interfaces\ESResourceRepository;
use App\Modules\Resources\Services\Interfaces\ResourceServiceInterface;
use App\Services\GoogleDriveService;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ResourceController extends Controller
{

    public function __construct(
        protected ESResourceRepository     $newsletterRepository,
        protected ResourceServiceInterface $resourceService,
        protected RabbitMQService          $rabbitMQ,
        protected GoogleDriveService       $driveService,
    )
    {

    }
    public function createResource(CreateResourceRequest $request): JsonResponse
    {

        $data = $request->all();

        // Kiểm tra xem người dùng có tải lên file không
        if (!$request->hasFile('image')) {
            return $this->responseJsonError('Tài nguyên tải lên không hợp lệ', 422, 'Tài nguyên tải lên không hợp lệ');

        }
        // Kiểm tra MIME type của file là hình ảnh
        $mimeType = $request->file('image')->getMimeType();
        $validMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

        if (!in_array($mimeType, $validMimeTypes)) {
            return $this->responseJsonError('Tài nguyên phải là hình ảnh', 422, 'Tài nguyên phải là hình ảnh');
        }

        // Kiểm tra phần mở rộng của file
        $extension = $request->file('image')->getClientOriginalExtension();
        $validExtensions = ['jpeg', 'png', 'jpg', 'gif'];

        if (!in_array($extension, $validExtensions)) {
            return $this->responseJsonError('Tài nguyên phải có định dạng hình ảnh hợp lệ', 422, 'Tài nguyên phải có định dạng hình ảnh hợp lệ');
        }


        $file = $request->file('image');
        $data['image'] = $file;

        //lưu hình ảnh
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
    public function updateResource(Request $request): JsonResponse
    {

        $data = $request->all();
        $result = $this->resourceService->updateResource($data);
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
