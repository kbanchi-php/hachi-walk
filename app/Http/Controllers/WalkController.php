<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Walk;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WalkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $query = Walk::query();
        if (!empty($keyword)) {
            $query->where('title', 'like', '%' . $keyword . '%');
            $query->orWhere('description', 'like', '%' . $keyword . '%');
            $query->orWhereHas('category', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }
        $walks = $query->with('photos')->paginate(8);
        $walks->appends(compact('keyword'));
        return view('walks.index', compact('walks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $walk = new Walk();
        $walk->latitude = 39.91402764039571;
        $walk->longitude = 141.1007601246386;
        $zoom = 15;
        $categories = Category::all();
        return view('walks.create', compact('walk', 'zoom', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // create walk
        $walk = new Walk();
        // set request form data
        $walk->fill($request->all());
        // set user id
        $walk->user_id = auth()->user()->id;

        // get file info and set file name
        $files = $request->file;

        // begin transaction
        DB::beginTransaction();

        try {
            // Article保存
            $walk->save();

            $paths = [];
            foreach ($files as $file) {
                $name = $file->getClientOriginalName();
                // save files
                $path = Storage::putFile('walks', $file);
                if (!$path) {
                    throw new \Exception("Faild to save image...");
                }
                $paths[] = $path;
                // set photo info
                $photo = new Photo([
                    'walk_id' => $walk->id,
                    'org_name' => $name,
                    'name' => basename($path)
                ]);
                // save photo
                $photo->save();
            }

            // commit
            DB::commit();
        } catch (\Exception $e) {
            // rollback
            foreach ($paths as $path) {
                if (!empty($path)) {
                    Storage::delete($path);
                }
            }
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }
        return redirect(route('walks.index'))->with(['flash_message' => 'Complete Create New Walk.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function show(Walk $walk)
    {
        $zoom = 15;
        return view('walks.show', compact('walk', 'zoom'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function edit(Walk $walk)
    {
        return view('walks.edit', compact('walk'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Walk $walk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Walk  $walk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Walk $walk)
    {
        $delete_file_paths = $walk->image_paths;

        DB::beginTransaction();
        try {
            $walk->delete();

            foreach ($delete_file_paths as $delete_file_path) {
                if (!Storage::delete($delete_file_path)) {
                    throw new \Exception('Faild to delete old image...');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }
        return redirect()
            ->route('walks.index')
            ->with(['flash_message' => '削除が完了しました']);
    }
}
