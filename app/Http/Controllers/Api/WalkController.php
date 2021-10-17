<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Walk;
use App\Http\Requests\WalkRequest;

class WalkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $walks = Walk::all();
        return $walks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WalkRequest $request)
    {
        $walk = new Walk();
        $walk->title = $request->title;
        $walk->latitude = $request->latitude;
        $walk->longitude = $request->longitude;
        $walk->description = $request->description;
        $walk->category_id = $request->category_id;
        $walk->user_id = $request->user_id;
        $walk->save();
        return $walk;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $walk = Walk::find($id);
        return $walk;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WalkRequest $request, $id)
    {
        $walk = Walk::find($id);
        $walk->title = $request->title;
        $walk->latitude = $request->latitude;
        $walk->longitude = $request->longitude;
        $walk->description = $request->description;
        $walk->category_id = $request->category_id;
        $walk->user_id = $request->user_id;
        $walk->save();
        return $walk;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $walk = Walk::find($id);
        $walk->delete();
    }
}
