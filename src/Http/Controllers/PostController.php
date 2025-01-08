<?php

namespace CSlant\Blog\Api\Http\Controllers;

use CSlant\Blog\Api\Helpers\SlugHelper;
use CSlant\Blog\Api\Enums\StatusEnum;
use CSlant\Blog\Api\Http\Resources\ListPostResource;
use CSlant\Blog\Api\Http\Resources\PostResource;
use CSlant\Blog\Core\Http\Controllers\Base\BasePostController;
use CSlant\Blog\Core\Http\Responses\Base\BaseHttpResponse;
use CSlant\Blog\Core\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PostController
 *
 * @package CSlant\Blog\Api\Http\Controllers
 *
 * @group Blog API
 *
 * @authenticated
 *
 * @method BaseHttpResponse httpResponse()
 * @method BaseHttpResponse setData(mixed $data)
 * @method BaseHttpResponse|JsonResource|JsonResponse|RedirectResponse toApiResponse()
 */
class PostController extends BasePostController
{
    /**
     * @group Blog API
     *
     * @param  Request  $request
     *
     * @return BaseHttpResponse|JsonResource|JsonResponse|RedirectResponse
     */
    public function index(Request $request): BaseHttpResponse|JsonResponse|JsonResource|RedirectResponse
    {
        $data = $this
            ->postRepository
            ->advancedGet([
                'with' => ['tags', 'categories', 'author', 'slugable'],
                'condition' => ['status' => StatusEnum::PUBLISHED->value],
                'paginate' => [
                    'per_page' => $request->integer('per_page', 10),
                    'current_paged' => $request->integer('page', 1),
                ],
            ]);

        return $this
            ->httpResponse()
            ->setData(ListPostResource::collection($data))
            ->toApiResponse();
    }

    /**
     * Get post by slug
     *
     * @group Blog
     * @queryParam slug Find by slug of post.
     */
    public function findBySlug(string $slug)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Post::class));

        if (!$slug) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        $post = Post::query()
            ->where([
                'id' => $slug->reference_id,
                'status' => StatusEnum::PUBLISHED,
            ])
            ->first();

        if (!$post) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Not found');
        }

        return $this
            ->httpResponse()
            ->setData(new PostResource($post))
            ->toApiResponse();
    }
}
