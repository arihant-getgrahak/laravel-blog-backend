<?php

namespace App\Http\Controllers;
use App\Models\Blog;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Trash;
use Storage;
use Str;

class BlogController extends Controller
{
    public function display()
    {
        $blogs = Blog::where("isdeleted", false)
            ->where("type", "publish")
            ->with(["users:id,name", "deletedBy:id,name", "parentCategory:id,name", "childCategory:id,name", "media"])
            ->paginate(10);

        $returnData = [];

        foreach ($blogs as $blog) {
            $returnData[] = [
                "id" => $blog->id,
                "slug" => $blog->slug,
                "title" => $blog->title,
                "description" => $blog->description,
                "photo" => $blog->getFirstMediaUrl('blog_photo', 'original') ?? null,
                "category" => $blog->parentCategory->name ?? "",
                "sub_category" => $blog->childCategory->name ?? "",
                "tag" => $blog->tag ?? "",
                "created_at" => $blog->created_at,
                "created_by" => $blog->users->name,
                "is_deleted" => $blog->isdeleted,
                "seo" => [
                    "meta.name" => $blog->title,
                    "meta.desc" => $blog->description,
                    "meta.robots" => "noindex, nofollow"
                ]
            ];
        }
        $pagination = [
            "next_page_url" => $blogs->nextPageUrl(),
            "previous_page_url" => $blogs->previousPageUrl(),
            "total" => $blogs->total(),
        ];
        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $returnData,
            "pagination" => $pagination,
            // "media" => $mediaItems
        ], 200);

    }

    public function store(BlogStoreRequest $request)
    {
        $slug = $request->slug ?? $this->slug($request->title);
        if ($request->slug && Blog::where("slug", $slug)->exists()) {
            return response()->json([
                "status" => false,
                "message" => "Blog with this slug already exists",
            ]);
        }

        $filldata = [
            "user_id" => auth()->id(),
            "title" => $request->title,
            "description" => $request->description,
            "parent_category" => $request->category,
            "tag" => $request->tag,
            "child_category" => $request->sub_category,
            "slug" => $slug,
            "type" => $request->type,
            "photo" => ""
        ];

        $blog = Blog::create($filldata);
        $blog->addMediaFromRequest("image")->toMediaCollection('blog_photo');
        $mediaItems = $blog->getMedia("blog_photo");
        $blog["photo"] = $mediaItems[0]->original_url;
        $blog->makeHidden("media");

        // $this->sendBlogWebhook($request->title, $request->description);

        $message = $request->type === "draft" ? "Draft created successfully" : "Blog created successfully";
        return response()->json([
            "status" => true,
            "message" => $message,
            "data" => $blog,
        ], 200);
    }

    public function update(BlogUpdateRequest $request, string $slug)
    {
        $blog = Blog::where('slug', $slug)->where('user_id', auth()->user()->id)->first();
        if (!$blog) {
            $this->error = "You are not allowed to update other person blog";
            return false;
        }

        $filldata = $request->only(['title', 'description', 'parent_category', 'tag', 'child_category', 'type']);

        // if ($request->hasFile('image')) {
        //     $filldata['photo'] = $this->uploadImage($request->file('image'));
        // }
        $sendData = [
            "subject" => "Blog with id." . $slug . " updated",
            "title" => $request->title,
            "description" => $request->description
        ];

        $isUpdate = $blog->update($filldata);

        if ($request->hasFile('image')) {
            $blog->clearMediaCollection('blog_photo');
            $blog->addMediaFromRequest("image")->toMediaCollection('blog_photo');
            $mediaItems = $blog->getMedia("blog_photo");
            $blog["photo"] = $mediaItems[0]->original_url;
            $blog->makeHidden("media");
        }
        if (!$isUpdate) {
            return response()->json([
                "status" => false,
                "message" => "Unable to update blog",
            ]);
        }

        // Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzNTA0MzI1MjZlNTUzMDUxMzQi_pc", $sendData);
        return response()->json([
            "status" => true,
            "message" => "Blog updated successfully",
            "data" => $blog
        ]);
    }

    public function destroy(int $blog_id)
    {
        if (!auth()->check()) {
            return response()->json([
                "status" => false,
                "message" => "Please login to delete blog",
            ]);
        }
        $isBlogExist = Blog::find($blog_id);

        if (!$isBlogExist) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",

            ]);
        }

        if ($isBlogExist->isDeleted) {
            return response()->json([
                "status" => false,
                "message" => "Blog already deleted",

            ]);
        }

        $isUpdate = $isBlogExist->update([
            "isDeleted" => true,
            "deleted_by" => auth()->user()->id
        ]);

        Trash::create([
            "user_id" => auth()->user()->id,
            "blog_id" => $blog_id
        ]);

        if ($isUpdate)
            return response()->json([
                "status" => true,
                "message" => "Blog deleted successfully",
                "deletedBy" => "Blog is deleted by You."
            ]);
    }

    public function displayuserBlog()
    {
        //only authenticate user can see their blog
        if (!auth()->check()) {
            return response()->json([
                "status" => false,
                "message" => "Please login first",
            ], 401);
        }
        $id = auth()->user()->id;
        $blogs = Blog::where("user_id", $id)
            ->where("isdeleted", false)
            ->with(["users:id,name", "deletedBy:id,name", "parentCategory:id,name", "childCategory:id,name"])
            ->paginate(20);

        $returnData = [];

        foreach ($blogs as $blog) {
            $returnData[] = [
                "id" => $blog->id,
                "slug" => $blog->slug,
                "title" => $blog->title,
                "description" => $blog->description,
                "photo" => $blog->photo,
                "category" => $blog->parentCategory->name ?? "",
                "sub_category" => $blog->childCategory->name ?? "",
                "tag" => $blog->tag ?? "",
                "created_at" => $blog->created_at,
                "created_by" => $blog->users->name,
                "is_deleted" => $blog->isdeleted,
                "type" => $blog->type,
                "seo" => [
                    "meta.name" => $blog->title,
                    "meta.desc" => $blog->description,
                    "meta.robots" => "noindex, nofollow"
                ]
            ];
        }
        $pagination = [
            "next_page_url" => $blogs->nextPageUrl(),
            "previous_page_url" => $blogs->previousPageUrl(),
            "total" => $blogs->total(),
        ];
        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $returnData,
            "pagination" => $pagination
        ], 200);

    }

    public function displaySpecificBlog(string $slug)
    {
        $blog = Blog::where("slug", $slug)
            ->with(["users:id,name,email,type", "deletedBy:id,name", "parentCategory:id,name", "childCategory:id,name"])->first();

        if (!$blog) {
            return response()->json([
                "status" => false,
                "message" => "Blog not found",
            ]);
        }

        if ($blog->draft) {
            if (!auth()->check() || auth()->user()->id !== $blog->user_id) {
                return response()->json([
                    "status" => false,
                    "message" => "Unauthorized to view this blog",
                ]);
            }
        }

        return response()->json([
            "status" => true,
            "message" => "Blog fetched successfully",
            "data" => $blog,
            "seo" => [
                "title" => $blog->title,
                "description" => $blog->description,
                "meta.robots" => "noindex, nofollow"
            ]
        ]);
    }
    protected function slug($title)
    {
        $slug = Str::slug($title);
        $isBlogExist = Blog::where("slug", $slug)->first();
        if ($isBlogExist) {
            $slug = $slug . "-" . rand(1000, 9999);
        }
        return $slug;
    }
}
