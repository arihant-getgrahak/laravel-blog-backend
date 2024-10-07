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

    public function delete(int $trashId)
    {
        $trashExist = Trash::find($trashId);
        if (!$trashExist) {
            return response()->json([
                "status" => "error",
                "message" => "Trash not found"
            ]);
        }

        $delete = $trashExist->delete();

        if (!$delete) {
            return response()->json([
                "status" => "error",
                "message" => "Trash delete failed"
            ]);
        }
        return response()->json([
            "status" => "success",
            "message" => "Trash deleted successfully"
        ]);
    }
}
