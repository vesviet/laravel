<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCopywriterService
{
    /**
     * Generate a product description using AI with a static fallback.
     *
     * @param string $productName
     * @return string
     */
    public function generateProductDescription(string $productName): string
    {
        try {
            $apiKey = config('services.gemini.api_key');
            
            if (!$apiKey) {
                return $this->getStaticFallback($productName);
            }

            $prompt = "Viết một đoạn mô tả sản phẩm hấp dẫn, chuyên nghiệp bằng tiếng Việt cho sản phẩm có tên: '{$productName}'. "
                    . "Yêu cầu: Viết theo phong cách bán hàng trên thương mại điện tử (có sử dụng emoji hợp lý), "
                    . "tôn lên điểm nổi bật của sản phẩm, tối đa 150 chữ, định dạng HTML cơ bản (dùng <p>, <strong>, <ul>, <li> nếu cần). "
                    . "Không đưa ra những lời khuyên y tế, pháp lý hay thông tin sai lệch.";

            $response = Http::timeout(3)->withHeaders([
                'Content-Type'    => 'application/json',
                // P1-02: Key passed as header, not URL query param.
                // URL query params are logged by Laravel HTTP client and web server access logs.
                // The x-goog-api-key header is accepted by all Gemini API endpoints.
                'x-goog-api-key' => $apiKey,
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent', [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful() && isset($response->json()['candidates'][0]['content']['parts'][0]['text'])) {
                $content = $response->json()['candidates'][0]['content']['parts'][0]['text'];
                // Clean up any markdown code block wrappers if they exist
                $content = preg_replace('/```html\n?/', '', $content);
                $content = preg_replace('/```\n?/', '', $content);
                return trim($content);
            }

            Log::warning('AI Copywriter API failed or returned unexpected structure.', ['response' => $response->body()]);
            return $this->getStaticFallback($productName);

        } catch (\Exception $e) {
            Log::warning('AI Copywriter timeout or exception: ' . $e->getMessage());
            return $this->getStaticFallback($productName);
        }
    }

    /**
     * Fallback template when AI is unavailable or times out.
     */
    private function getStaticFallback(string $productName): string
    {
        return <<<HTML
<p>🌟 <strong>{$productName}</strong> - Sự lựa chọn hoàn hảo dành cho bạn!</p>
<ul>
    <li>✨ Thiết kế tinh tế, chất lượng vượt trội.</li>
    <li>🔥 Sản phẩm đang được rất nhiều khách hàng yêu thích và tin dùng.</li>
    <li>💯 Đảm bảo hài lòng 100% khi nhận hàng.</li>
</ul>
<p>👉 <em>Nhanh tay đặt hàng ngay hôm nay để nhận được ưu đãi tốt nhất từ Shop nhé!</em></p>
HTML;
    }
}
