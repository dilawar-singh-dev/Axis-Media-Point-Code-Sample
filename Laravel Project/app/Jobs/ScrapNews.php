<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsSubCategory;
use File;
use Webp;
use Image;
use Str;
use Session;
use Goutte;
use Storage;
use Illuminate\Support\Arr;
use App\Models\NewsCronJobs;

class ScrapNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $args = $this->args;

        // STORE JOB DATA 
        $NewsCronJobs = new NewsCronJobs();
        $NewsCronJobs->title = $args;
        $NewsCronJobs->save();

        // GET JOB DATA FOR UPDATE 
        $NewsCronJobsId = $NewsCronJobs->id;
        $NewsCronJobsUpdate = NewsCronJobs::where('id',$NewsCronJobsId)->first();

        $newsArray = [];
        $crawlPages = 10;
        $crawlUrl = 'https://indianexpress.com/section/india/';

        

        if($args == 'axis-news-interviews-bazaar-mail'){
            $crawlPages = 30;
            $crawlUrl = 'https://indianexpress.com/section/india/';
        }

        if($args == 'fashenta'){
            $crawlPages = 10;
            $crawlUrl = 'https://indianexpress.com/section/lifestyle/fashion/';
        }

        if($args == 'sports'){
            $crawlPages = 10;
            $crawlUrl = 'https://indianexpress.com/section/sports/';
        }

        if($args == 'business'){
            $crawlPages = 10;
            $crawlUrl = 'https://indianexpress.com/section/business/';
        }

        if($args == 'travel'){
            $crawlPages = 10;
            $crawlUrl = 'https://indianexpress.com/section/auto-travel/';
        }

        if($args == 'health-and-lifestyle'){
            $crawlPages = 10;
            $crawlUrl = 'https://indianexpress.com/section/lifestyle/health/';
        }

        if($args == 'science-and-technology'){
            $crawlPages = 10;
            $crawlUrl = 'https://indianexpress.com/section/technology/science/';
        }

        \Log::info('Cron Job Running - '.$this->args);

        // $news = new News();
        // $news->category_id = 25;
        // $news->sub_category_id = 30;
        // $news->title = 'dara2';
        // $news->slug = 'dara2';
        // $news->description = '6 Co2vid deaths reported in Chandigarh, Mohali, none in Panchkula';
        // $news->picture_xl = 'image2s/news/20210629_53910241680X327779835/53910241680X327.webp';
        // $news->user_id = 1;
        // $news->popular = 1;
        // $news->breaking = 1;
        // $news->trending = 1;
        // $news->save();

        // return 'okk';

        try {
        function scrapData($newsArray,$crawlerStartUrl){

            set_time_limit(0);

            $crawler = Goutte::request('GET', $crawlerStartUrl);
            $crawler->filter('.nation .articles')->each(function ($node) use($newsArray) {
    
                $crawlerIn1 = $node->filter('.title a');

                $crawlerIn1->each(function ($nodeIn1) use($newsArray) {
    
                    $crawlerIn2 = Goutte::request('GET', $nodeIn1->attr('href'));
    
                    $crawlerIn2->each(function ($nodeIn2) use($newsArray) {
    
                        $crawlerIn3 = $nodeIn2->filter('.container')->each(function ($nodeIn3,$index) use($newsArray) {

                            // dd($nodeIn3->filter('.heading-part h1')->text());
                            $title = '';
                            if($nodeIn3->filter('.heading-part h1')->count()){
                               $title = $nodeIn3->filter('.heading-part h1')->text();
                            }

                            $synopsis = '';
                            if($nodeIn3->filter('.synopsis')){
                              $synopsis = $nodeIn3->filter('.synopsis')->text();
                            }
                            
                            $content = '';
                            if($nodeIn3->filter('#pcl-full-content')->count()){
                                $content = $nodeIn3->filter('#pcl-full-content')->html();
                            }
                            
                            $imageItemProp = $nodeIn3->filter('.full-details span')->each(function ($nodeIn5) use($newsArray,$title,$synopsis,$content){
                                $imageMetaItemPropGet = $nodeIn5->attr('itemprop');
                                
                                if($imageMetaItemPropGet == 'image'){
                                    
                                    $crawlerIn3 = $nodeIn5->filter('meta')->each(function ($nodeIn4) use($newsArray,$title,$synopsis,$content){
                                        $imageMetaItemProp = $nodeIn4->attr('itemprop');
                                        
                                        if($imageMetaItemProp == 'url'){
                                            $imageUrl = $nodeIn4->attr('content');
                                            $newsArray['title'] = $title;
                                            $newsArray['synopsis'] = $synopsis;
                                            
                                            if ($imageUrl) {
                                                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                                $pin = mt_rand(1000000, 9999999)
                                                . mt_rand(1000000, 9999999)
                                                . $characters[rand(0, strlen($characters) - 1)];
                                                $randomString = str_shuffle($pin);
                                    
                                                // CREATE FOLDER DIRECTORY
                                                $folder_name = date('Ymd') . '_' . $randomString . mt_rand(1000, 990000);
                                    
                                                $directory_name = "news";
                                                $path = public_path().'/images';
                                                File::makeDirectory(public_path() .'/'. 'images/'.$directory_name.'/' . $folder_name, 0777, true);

                                                // DESTINATION PATH
                                                $destination_path = ('images/'.$directory_name.'/'.$folder_name);
                                                $filename =  $randomString . '.' . 'webp';

                                                if (strpos($imageUrl, 'express-photo') !== false) {
                                                    $imageUrl = public_path().'/logo-banner.png';
                                                }

                                                $contents = file_get_contents($imageUrl);

                                                Image::make($contents)->encode('webp', 50)->save(public_path() .'/'.$destination_path.'/'.$filename, 15);

                                                $newsArray['image'] = $destination_path.'/'.$filename;
                                            }

                                            $newsArray['content'] = $content;

                                            // $contents = file_get_contents($image);
                                            // $name = substr($image, strrpos($image, '/') + 1);
                                            // Storage::put($name, $contents);

                                            Session::push('newsarray', $newsArray);
                                        }
    
                                    });
                                }
                            });
                        
                        });
    
                    });
    
                });
    
            });
        }

        for($i=1; $i<=$crawlPages; $i++){

            set_time_limit(0);

            $crawlerStart = Goutte::request('GET', $crawlUrl);
            
                // $crawlerStartUrl = $nodeStart->filter('.next.page-numbers')->attr('href');
                $crawlerStartUrl = $crawlUrl;
                
                if($i != 1){
                    $crawlerStartUrl = $crawlUrl.'page/'.$i;
                }

                // dump($crawlerStartUrl);
                scrapData($newsArray,$crawlerStartUrl);
        
        }
       
        foreach(Session::get('newsarray') as $data){
            if($data['content']){

                $randCategory = NewsCategory::with('sub_category')->inRandomOrder()->first();
                $randCategorySlug = '';
                
                if($args == 'axis-news-interviews-bazaar-mail'){
                    $categories = [
                        'axis-news',
                        'interviews',
                        'bazaar-mail'
                    ];
                    // Get random item from array.
                    $randomItem = Arr::random($categories, 1);
                    $randCategorySlug = $randomItems[0];
                }

                if($args == 'fashenta'){
                    $randCategorySlug = 'fashenta';
                }

                if($args == 'sports'){
                    $randCategorySlug = 'sports';
                }
        
                if($args == 'business'){
                    $randCategorySlug = 'business';
                }
        
                if($args == 'travel'){
                    $randCategorySlug = 'travel';
                }
        
                if($args == 'health-and-lifestyle'){
                    $randCategorySlug = 'health-and-lifestyle';
                }
        
                if($args == 'science-and-technology'){
                    $randCategorySlug = 'science-and-technology';
                }

                if($args){
                    $randCategory = NewsCategory::with('sub_category')->where('slug',$randCategorySlug)->first();
                }
        
                $randSubCategoryId = null;
        
                if(count($randCategory->sub_category) > 0){
                    $randSubCategory = NewsSubCategory::where('category_id',$randCategory->id)->inRandomOrder()->first();
                    $randSubCategoryId = $randSubCategory->id;
                }

                $newsRecordExists = News::where('slug',Str::slug($data['title']))->exists();

                if(!$newsRecordExists){
                    $news = new News();
                    $news->category_id = $randCategory->id;
                    $news->sub_category_id = $randSubCategoryId;
                    $news->title = $data['title'];
                    $news->slug = Str::slug($data['title']);
                    $news->description = $data['content'];
                    $news->picture_xl = $data['image'];
                    $news->user_id = 1;
                    $news->popular = 1;
                    $news->breaking = 1;
                    $news->trending = 1;
                    $news->save();
                }
                
            }
        }

        $NewsCronJobsUpdate->status = 'success';

        }
        catch(Exception $e) {
            $NewsCronJobsUpdate->status = $e->getMessage();
        }

        $NewsCronJobsUpdate->update();

        return response()->json(Session::get('newsarray'));
       
    }
}
