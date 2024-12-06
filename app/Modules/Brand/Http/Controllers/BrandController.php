<?php

namespace App\Modules\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Brand\Models\Brands;
use App\Modules\Brand\Repositories\Elasticsearch\Interfaces\BrandRepositoryInterface;
use App\Modules\Brand\Services\Interfaces\BrandServiceInterface;
use App\Services\GoogleDriveService;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BrandController extends Controller
{

    public function __construct(
        protected BrandRepositoryInterface        $brandRepository,
        protected BrandServiceInterface $brandService,
        protected RabbitMQService        $rabbitMQ,
        protected GoogleDriveService     $driveService,
    ){

    }


    public function createBrand(Request $request): JsonResponse
    {



        $result= $this->brandService->createBrand($request);

        if (!empty($result['success']) && $result['success'] === true) {
            $response = $result['data']?? [];
            return $this->responseJsonSuccess($response, $result['message']);
        }
        return $this->responseJsonError($result['message'] , 200,
            $result['message'] );

    }
    public function listBrand(Request $request): JsonResponse
    {

        $data = $request->all();
        $result = $this->brandService->listBrand($data);
        if (!empty($result['success']) && $result['success'] === true) {
            $response = $result['data']['list'] ?? [];
            $meta=$response['meta'] ?? [];
            unset($response['meta']);
            return $this->responseJsonSuccess($response, $result['message'],$meta);
        }
        return $this->responseJsonError($result['message'] , 200,
            $result['message'] );
    }
    public function updateBrand($id, $data): JsonResponse
    {



        $result= $this->brandService->updateBrand($request);

        if (!empty($result['success']) && $result['success'] === true) {
            $response = $result['data']?? [];
            return $this->responseJsonSuccess($response, $result['message']);
        }
        return $this->responseJsonError($result['message'] , 200,
            $result['message'] );

    }
}
