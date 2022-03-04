<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsSubCategory;
use Auth;
use File;
use Webp;
use Image;
use Str;
use Session;
use Cache;
use Carbon\Carbon;
use App\Models\ENewsPaper;
use App\Models\ENewsPaperPages;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\AxisTvController;
use Goutte;
use Storage;
use App\Jobs\ScrapNews;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->query('id');
        $category_id = $request->query('category_id');
        $breaking = $request->query('breaking');
        $sub_category_id = $request->query('sub_category_id');
        

        if ($id) {
            $news = Cache::remember('news_id', 5, function () use ($id) {
                return News::where(['id' => $id])->with(['main_category','sub_category'])->first();
            });
            return JsonResponse(200, 'success', $news);
        }
        if ($category_id) {
            $news = Cache::remember('news_category_id', 5, function () use ($category_id) {
                return News::where(['category_id' => $category_id])->with(['main_category','sub_category'])->orderBy('id', 'DESC')->limit(20)->get();
            });

            return JsonResponse(200, 'success', $news);
        }

        if ($sub_category_id) {
            $news = Cache::remember('news_sub_category_id', 5, function () use ($sub_category_id) {
                return News::where(['sub_category_id' => $sub_category_id])->with(['main_category','sub_category'])->orderBy('id', 'DESC')->limit(20)->get();
            });

            return JsonResponse(200, 'success', $news);
        }

        if ($breaking) {
            $news = Cache::remember('news_breaking', 5, function () use ($breaking) {
                return  News::where(['breaking' => $breaking])->with(['main_category','sub_category'])->orderBy('id', 'DESC')->limit(20)->get();
            });

            return JsonResponse(200, 'success', $news);
        }

        // Instantiate NewsCategoryController
        $NewsCategoryController = new NewsCategoryController;

        // Access method in NewsCategoryController
        $categories = $NewsCategoryController->subCategory();


        // Instantiate AxisTvController
        $AxisTvController = new AxisTvController;

        // Access method in AxisTvController
        $axistv = $AxisTvController->index($request);


        $news = Cache::remember('news', 5, function () {
            return  News::with(['main_category','sub_category'])->orderBy('id', 'DESC')->exclude(['description'])->inRandomOrder()->paginate(2000);;
        });

         $response = [
            'categories' => $categories->original,
            'news' => $news,
            'axistv' => $axistv->original
        ];

        return $response;

        // return JsonResponsePaginate(200, 'success', $news);
        
        // $news = News::with(['main_category','sub_category'])->orderBy('id', 'DESC')->exclude(['description'])->inRandomOrder()->paginate(3000);
        
        // $response = [
        //     'categories' => $categories->original,
        //     'news' => $news,
        //     'axistv' => $axistv->original
        // ];

        // return $response;

        // $news = Cache::remember('news', 5, function () {
        //     return  News::with(['main_category','sub_category'])->orderBy('id', 'DESC')->paginate(1000);
        // });

        // return JsonResponsePaginate(200, 'success', $news);
    }

    public function archive(Request $request)
    {
        return response()->json($request->date);
    }

    public function getArchives($date)
    {
        $date = Carbon::parse($date);

        $news = Cache::remember('getArchives', 5, function () use ($date) {
            return  News::with(['main_category','sub_category'])->whereDate('created_at', $date)->orderBy('id', 'DESC')->limit(20)->get();
        });

        return JsonResponse(200, 'success', $news);
    }

    public function Getindex(Request $request, $id)
    {
        $news = Cache::remember('Getindex', 5, function () use ($id) {
            return News::where(['id' => $id])->with(['main_category','sub_category'])->first();
        });

        return JsonResponse(200, 'success', $news);
    }

    public function GetNewsSlug(Request $request, $slug)
    {
        $news = Cache::remember('GetNewsSlug', 5, function () use ($slug) {
            return News::where(['slug' => $slug])->first();
        });

        return JsonResponse(200, 'success', $news);
    }

    public function category()
    {
        $news = Cache::remember('category', 5, function () {
            return NewsCategory::with('category_news')->get();
        });
        return JsonResponse(200, 'success', $news);
    }

    public function subcategory()
    {
        $news = Cache::remember('subcategory', 5, function () {
            return  NewsSubCategory::with('sub_category_news')->get();
        });
        return JsonResponse(200, 'success', $news);
    }

    public function store(Request $request)
    {
        
         // VALIDATION
        $validatedData = $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'description' => 'required'
        ]);

        // CHECK IF PICTURE AVAILABLE
        if ($request->has('picture_xl')) {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pin = mt_rand(1000000, 9999999)
        . mt_rand(1000000, 9999999)
        . $characters[rand(0, strlen($characters) - 1)];
            $randomString = str_shuffle($pin);

            // CREATE FOLDER DIRECTORY
            $folder_name = date('Ymd') . '_' . mt_rand(1000, 990000);

            $image = $request->file('picture_xl');
            $directory_name = "news";
            $path = public_path().'/images';
            File::makeDirectory(public_path() .'/'. 'images/'.$directory_name.'/' . $folder_name, 0777, true);
            // DESTINATION PATH
            $destination_path = ('images/'.$directory_name.'/'.$folder_name);
            $filename =  $randomString . '.' . 'webp';
            Image::make($image)->encode('webp', 50)->save($destination_path.'/'.$filename, 15);
        }

        
        $sub_category_id = $request->sub_category_id;
        if (!$sub_category_id) {
            $sub_category_id = null;
        }
        // STORE
        $request_fields = array_merge(
            $request->all(),
            [
                'user_id' => Auth::user()->id,
                'picture_xl' => $destination_path.'/'.$filename,
                'slug' => Str::slug($request->title),
                'sub_category_id' => $sub_category_id

            ]
        );

        $news = News::create($request_fields);
        return JsonResponse(200, 'success', $news);
    }

    public function storeEPaperDate(Request $request)
    {
        Session::put('newsPaperDate', $request->date);

        $ENewsPaper = ENewsPaper::where('date', $request->date)->exists();

        if ($ENewsPaper) {
            return response()->json('Duplicate Data', 409);
        }

        return response()->json(Session::get('newsPaperDate'));
    }

    public function storeEPaperDateSession(Request $request)
    {
        Session::put('newsPaperDateSession', $request->date);

        return response()->json(Session::get('newsPaperDateSession'));
    }

    public function getENewsPaperDates(Request $request)
    {
        $data = ENewsPaper::orderBy('date', 'DESC')->paginate(10);
        return JsonResponsePaginate(200, 'success', $data);
    }

    public function storeEPaperSuccess()
    {
        return response()->json('success', 200);
    }

    public function storeEPaper(Request $request)
    {
        try {
            if (Session::has('newsPaperDate')) {
                $filesData = $request->all();

                // CREATE FOLDER DIRECTORY
                $folder_name = date('Ymd') . '_' . mt_rand(1000, 990000);

                function randomString()
                {
                    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $pin = mt_rand(1000000, 9999999)
                . mt_rand(1000000, 9999999)
                . $characters[rand(0, strlen($characters) - 1)];
                    $randomString = str_shuffle($pin);
                    return $randomString;
                }
                $directory_name = "epaper";

                $path = public_path().'/images';

                $folder_name = randomString().randomString();

                File::makeDirectory(public_path() .'/'. 'images/'.$directory_name.'/' . $folder_name, 0777, true);

                if (ENewsPaper::where('date', Session::get('newsPaperDate'))->exists()) {
                    $data_get = ENewsPaper::where('date', Session::get('newsPaperDate'))->first();
                    $ENewsPaperId = $data_get->id;
                } else {
                    $ENewsPaper = new ENewsPaper;
                    $ENewsPaper->date = Session::get('newsPaperDate');
                    $ENewsPaper->save();
                    $ENewsPaperId = $ENewsPaper->id;
                }
                

                foreach ($filesData['files'] as $key => $file) {

                    // DESTINATION PATH
                    $destination_path = ('images/'.$directory_name.'/'.$folder_name);
                    $filename =  randomString() . '.' . 'webp';
                    Image::make(file_get_contents($file['dataURL']))->encode('webp', 100)->save($destination_path.'/'.$filename, 100);

                    $ENewsPaperPages = new ENewsPaperPages;
                    $ENewsPaperPages->e_news_paper_id = $ENewsPaperId;
                    $ENewsPaperPages->page_no = $key + 1;
                    $ENewsPaperPages->image = $destination_path.'/'.$filename;
                    $ENewsPaperPages->save();
                }
            }
            return response()->json('success', 200);
        } catch (\Throwable $exception) {
            return response()->json($exception->getMessage(), 200);
        }
    }

    public function getENewsPaper(Request $request)
    {
        try {
            $date = $request->query('date');

            if ($date) {
                Cache::put('newsPaperDateSession', $date, 120);
            }
           
            if (ENewsPaper::where('date', Cache::get('newsPaperDateSession'))->exists()) {
                if (Cache::has('newsPaperDateSession')) {
                    $data = ENewsPaper::where('date', Cache::get('newsPaperDateSession'))->first();
                    $data->setRelation('papers', $data->papers()->paginate(1));
                    return JsonResponse(200, 'success', $data);
                }
                
                if ($date) {
                    $data = ENewsPaper::where('date', $date)->first();
                    $data->setRelation('papers', $data->papers()->paginate(1));
                    return JsonResponse(200, 'success', $data);
                }
        
                $data = ENewsPaper::orderBy('id', 'DESC')->first();
                $data->setRelation('papers', $data->papers()->paginate(1));
                return JsonResponse(200, 'success', $data);
            } else {
                if (ENewsPaper::orderBy('id', 'DESC')->exists()) {
                    $data = ENewsPaper::orderBy('id', 'DESC')->first();
                    $data->setRelation('papers', $data->papers()->paginate(1));
                    return JsonResponse(200, 'success', $data);
                } else {
                    return JsonResponse(409, 'empty data', 'Success');
                }
            }
            return JsonResponse(409, 'empty data', 'Success');
        } catch (\Throwable $exception) {
            return response()->json($exception->getMessage(), 200);
        }
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
      
       
        
                 // VALIDATION
        $validatedData = $request->validate([
                    'id' => 'required',
                    'category_id' => 'required',
                    'title' => 'required',
                    'description' => 'required'
                ]);
        
        // CHECK IF PICTURE AVAILABLE
        if ($request->has('picture_xl')) {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pin = mt_rand(1000000, 9999999)
                . mt_rand(1000000, 9999999)
                . $characters[rand(0, strlen($characters) - 1)];
            $randomString = str_shuffle($pin);
        
        
        
            // CREATE FOLDER DIRECTORY
            $folder_name = date('Ymd') . '_' . mt_rand(1000, 990000);
        
            $image = $request->file('picture_xl');
            $directory_name = "news";
            $path = public_path().'/images';
            File::makeDirectory(public_path() .'/'. 'images/'.$directory_name.'/' . $folder_name, 0777, true);
            // DESTINATION PATH
            $destination_path = ('images/'.$directory_name.'/'.$folder_name);
            $filename =  $randomString . '.' . 'webp';
            Image::make($image)->encode('webp', 50)->save($destination_path.'/'.$filename, 15);
        }
        
                
        $sub_category_id = $request->sub_category_id;
        if (!$sub_category_id) {
            $sub_category_id = null;
        }

        $id = $request->id;
               
        $news_get = News::where('id', $id)->first();

              

        if ($request->has('picture_xl')) {
            $image = $destination_path.'/'.$filename;
        } else {
            $image = $news_get->picture_xl;
        }

        // STORE
        $request_fields = array_merge(
            $request->all(),
            [
                        'picture_xl' => $image,
                        'slug' => Str::slug($request->title),
                        'sub_category_id' => $sub_category_id
        
                    ]
        );
        
        $news = $news_get->update($request_fields);
        return JsonResponse(200, 'success', $news);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        News::where(['id' => $id])->delete();
        return JsonResponse(200, 'success', 'Successfully deleted');
    }

    public function deleteENewsPaperDates(Request $request)
    {
        $id = $request->id;
        ENewsPaper::where(['id' => $id])->delete();
        return JsonResponse(200, 'success', 'Successfully deleted');
    }

    // SCRAPING
    public function newsScraping()
    {
        // dispatch(new ScrapNews('axis-news-interviews-bazaar-mail'));
        // dispatch(new ScrapNews('fashenta'));
        // dispatch(new ScrapNews('sports'));
        dispatch(new ScrapNews('business'));
        dispatch(new ScrapNews('travel'));
        dispatch(new ScrapNews('health-and-lifestyle'));
        dispatch(new ScrapNews('science-and-technology'));

        return 'Cron Jobs Running...';
    }
}
