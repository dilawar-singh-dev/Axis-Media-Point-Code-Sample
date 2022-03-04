<?php

namespace App\Http\Controllers;
use App\Models\AxisTv;
use Auth;
use Illuminate\Http\Request;

class AxisTvController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->query('id');
        $type = $request->query('type');
        if($id){
            $news = AxisTv::where(['id' => $id])->first();
            return JsonResponse(200,'success',$news);
        }
        if($type){
            $news = AxisTv::where(['type' => $type])->get();
            return JsonResponse(200,'success',$news);
        }
        $news = AxisTv::get();
        return JsonResponse(200,'success',$news);
    }
    public function store(Request $request)
    {
        
         // VALIDATION
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'yt_video' => 'required',
            'type' => 'required'
        ]);

        // STORE
        $request_fields = array_merge(
            $request->all(),
            [
                'user_id' => Auth::user()->id
            ]
        );

        $news = AxisTv::create($request_fields);
        return JsonResponse(200, 'success', $news);
    }
}
