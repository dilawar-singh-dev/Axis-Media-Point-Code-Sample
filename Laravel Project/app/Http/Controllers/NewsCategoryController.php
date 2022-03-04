<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsCategory;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news_category = NewsCategory::all();
        return JsonResponse(200,'success',$news_category);
    }


    public function subCategory()
    {
        $news_category_sub_category = NewsCategory::with('sub_category')->get();
        return JsonResponse(200,'success',$news_category_sub_category);
    }

    public function Categorybyid($id,Request $request)
    {
        $id = $request->id;
        $news_category_sub_category = NewsCategory::with('sub_category')->where('id',$id)->first();
        return JsonResponse(200,'success',$news_category_sub_category);
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
            'name' => 'required|unique:news_category'
        ]);

        $store_category = NewsCategory::create($request->all());
        return JsonResponse(200,'success',$store_category);
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
            'id' => 'required|exists:news_category',
            'name' => 'required'
        ]);
        $id = $request->id;
        $update_category = NewsCategory::findOrFail($id);
        $update_category->update($request->all());
        return JsonResponse(200,'success',$update_category);
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
        // NewsCategory::where(['id' => $id])->delete();
        // return JsonResponse(200,'success','Successfully deleted - '.$id);
        return JsonResponse(200,'Successfully deleted');
    }
}
