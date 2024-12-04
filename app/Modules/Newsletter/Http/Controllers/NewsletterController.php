<?php

namespace App\Modules\Newsletter\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Newsletter\Repositories\Interfaces\NewsletterRepository;
use App\Modules\Newsletter\Requests\CreateNewsletterRequest;
use App\Modules\Newsletter\Services\Interfaces\NewsletterServiceInterface;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        protected NewsletterRepository       $newsletterRepository,
        protected NewsletterServiceInterface $newsletterService,
        protected RabbitMQService            $rabbitMQ
    )
    {

    }

    public function createNewsletter(CreateNewsletterRequest $request): JsonResponse
    {
        // Các trường hợp sử dụng từ request
        $fields = ['content', 'title', 'thumbnail', 'tags', 'location', 'option', 'creator', 'user_id'];

        // Lấy dữ liệu hợp lệ từ request
        $data = $request->only($fields);

        // Nếu 'option' cần mã hóa JSON trước khi lưu
        if (!empty($data['option']) && is_array($data['option'])) {
            $data['option'] = json_encode($data['option']);
        }

        // Gọi service để tạo newsletter
        $new = $this->newsletterService->createNewsletter($data);

        // Trả về phản hồi JSON thành công
        return $this->responseJsonSuccess($new, 'Newsletter created successfully');
    }

}
