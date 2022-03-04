<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsSubCategory;

class NewsSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news_sub_category = NewsSubCategory::with(['category'])->get();
        return JsonResponse(200,'success',$news_sub_category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return JsonResponse(200,'success');
        // VALIDATION
        $validatedData = $request->validate([
            'name' => 'required|unique:news_sub_category'
        ]);
        $store_sub_category = NewsSubCategory::create($request->all());
        return JsonResponse(200,'success',$store_sub_category);
    }

    public function SubCategorybyid($id,Request $request)
    {
        $id = $request->id;
        $news_sub_category = NewsSubCategory::where('id',$id)->first();
        return JsonResponse(200,'success',$news_sub_category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return JsonResponse(200,'success');
         // VALIDATION
         $validatedData = $request->validate([
            'id' => 'required|exists:news_sub_category',
            'name' => 'required'
        ]);

        $id = $request->id;
        
        $update_sub_category = NewsSubCategory::findOrFail($id);
        $update_sub_category->update($request->all());
        return JsonResponse(200,'success',$update_sub_category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $id = $request->id;
        // NewsSubCategory::where(['id' => $id])->delete();
        // return JsonResponse(200,'success','Successfully deleted');
        return JsonResponse(200,'Successfully deleted');
    }
}
