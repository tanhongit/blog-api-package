<?php

namespace CSlant\Blog\Api\Services;

use CSlant\Blog\Api\Models\PostView;
use CSlant\Blog\Core\Http\Responses\Base\BaseHttpResponse;
use CSlant\Blog\Core\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class PostService
 *
 * @package CSlant\Blog\Api\Services
 *
 * @method BaseHttpResponse httpResponse()
 */
class PostService
{
    /**
     * Get posts by filters.
     *
     * @param  array<string, mixed>  $filters
     *
     * @return LengthAwarePaginator<Post>
     */
    public function getCustomFilters(array $filters): LengthAwarePaginator
    {
        $data = Post::query();

        if ($filters['tags'] !== null) {
            $tags = array_filter((array) $filters['tags']);

            $data = $data->whereHas('tags', function (Builder $query) use ($tags): void {
                $query->whereIn('tags.id', $tags);
            });
        }

        if ($filters['categories'] !== null) {
            $categories = array_filter((array) $filters['categories']);

            $data = $data->whereHas('categories', function (Builder $query) use ($categories): void {
                $query->whereIn('categories.id', $categories);
            });
        }

        if ($filters['categories_exclude'] !== null) {
            $data = $data
                ->whereHas('categories', function (Builder $query) use ($filters): void {
                    $query->whereNotIn('categories.id', array_filter((array) $filters['categories_exclude']));
                });
        }

        if ($filters['exclude'] !== null) {
            $data = $data->whereNotIn('id', array_filter((array) $filters['exclude']));
        }

        if ($filters['include'] !== null) {
            $data = $data->whereNotIn('id', array_filter((array) $filters['include']));
        }

        if ($filters['author'] !== null) {
            $data = $data->whereIn('author_id', array_filter((array) $filters['author']));
        }

        if ($filters['author_exclude'] !== null) {
            $data = $data->whereNotIn('author_id', array_filter((array) $filters['author_exclude']));
        }

        if ($filters['featured'] !== null) {
            $data = $data->where('is_featured', $filters['featured']);
        }

        if ($filters['search'] !== null) {
            $keyword = isset($filters['search']) ? (string) $filters['search'] : null;
            $data = $this->search($data, $keyword);
        }

        $orderBy = Arr::get($filters, 'order_by', 'updated_at');
        $order = Arr::get($filters, 'order', 'desc');

        $data = $data
            ->wherePublished()
            ->orderBy($orderBy, $order);

        return $data->paginate((int) $filters['per_page']);
    }

    /**
     * Track a view for a post.
     *
     * @param int $postId The post ID
     * @param string $ipAddress The IP address
     * @param string|null $userAgent The user agent
     * @return void
     */
    public function trackView(int $postId, string $ipAddress, ?string $userAgent = null): void
    {
        /** @var \CSlant\Blog\Api\Models\PostView|null $existingView */
        $existingView = PostView::query()
            ->where('post_id', '=', $postId)
            ->where('ip_address', '=', $ipAddress)
            ->first();

        $timeCheck = now();

        if (!$existingView) {
            /** @var array<string, mixed> $attributes */
            $attributes = [
                'post_id' => $postId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'time_check' => $timeCheck,
            ];

            PostView::query()->create($attributes);

            return;
        }

        // Only count as a new view if the last view was more than an hour ago
        if ($existingView->time_check->diffInHours(now()) >= 1) {
            /** @var array<string, mixed> $attributes */
            $attributes = [
                'time_check' => $timeCheck,
                'user_agent' => $userAgent,
            ];

            $existingView->update($attributes);
        }
    }
}
