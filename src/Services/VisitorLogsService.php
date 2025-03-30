<?php

namespace CSlant\Blog\Api\Services;

use Carbon\Carbon;
use CSlant\Blog\Api\Models\VisitorLog;
use CSlant\Blog\Core\Models\Post;

class VisitorLogsService
{
    /**
     * Track a view for a post by ID.
     *
     * @param int $postId The post ID
     * @param null|string $ipAddress The viewer's IP address
     * @param null|string $userAgent The viewer's user agent
     * @return Post The updated post
     */
    public function trackPostView(int $postId, ?string $ipAddress, ?string $userAgent = null): Post
    {
        $expirationMinutes = (int) config('blog-core.view_throttle_minutes');
        $ipAddress = $ipAddress ?: '';
        $now = Carbon::now();

        $post = Post::query()->lockForUpdate()->findOrFail($postId);

        $visitorLog = VisitorLog::query()->firstOrNew([
            'viewable_id' => $post->getKey(),
            'viewable_type' => Post::class,
            'ip_address' => $ipAddress,
        ]);

        $shouldCountView = !$visitorLog->exists || $now->isAfter($visitorLog->expired_at);

        if ($shouldCountView) {
            $visitorLog->fill([
                'user_agent' => $userAgent,
                'expired_at' => $now->copy()->addMinutes($expirationMinutes),
            ]);
            $visitorLog->save();

            Post::where('id', $postId)->increment('views');
        }

        return $post->refresh();
    }
}
