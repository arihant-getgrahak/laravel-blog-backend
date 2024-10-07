<?php

namespace App\Http\Controllers;

use App\Models\Trash;

class TrashController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user = auth()->user()->id;
    }

    public function display()
    {
        $trash = Trash::where("user_id", $this->user)->with(['users:id,name', 'blogs'])->get();

        return response()->json([
            "status" => "success",
            "data" => $trash
        ]);
    }
}
