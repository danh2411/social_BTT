<?php

namespace App\Modules\Brands\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Brand\Models\Brand;
use App\Modules\Brand\Models\Brands;
use App\Modules\Brands\Repositories\Interfaces\BrandRepository;
use App\Modules\Brands\Services\Interfaces\BrandServiceInterface;
use App\Services\RabbitMQService;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BrandController extends Controller
{

    public function __construct(
        protected BrandRepository       $brandRepository,
        protected BrandServiceInterface $brandService,
        protected RabbitMQService            $rabbitMQ,
        protected GoogleDriveService  $driveService,
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
}
