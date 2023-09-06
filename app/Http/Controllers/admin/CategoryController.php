<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request){

        $categories = Category::latest();

        //Record found as per input
        if(!empty($request->get('keyword'))){
            $categories=$categories->where('name','like','%'.$request->get('keyword').'%');
        }

        // $data['categories']= $categories;
        $categories = $categories->paginate(10);
        return view('admin.category.allcategory', compact('categories'));
    }
    


    public function create(){

       return view('admin.category.create');

    }

    
    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'name' =>'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()){

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();



            // save image
            if(!empty($request->image_id)){
                $tempImage= TempImage::find($request->image_id);
                $extArray=explode('.',$tempImage->name);
                $ext=last($extArray);

                $newImageName= $category->id.'.'.$ext;

                // $sPath= public_path().'/temp'.$tempImage->image;
                $sPath = public_path().'/temp/'.$tempImage->image;
                $dPath = public_path().'/uploads/category/'.$newImageName;
                
                File::copy($sPath, $dPath);


                //Thumbnail
                $dPath = public_path().'/uploads/category/thumb'.$newImageName;

                $img = Image::make($sPath);
                $img->resize(450, 900);
                $img->save($dPath);



                $category->image=$newImageName;
                $category->save();
            }


        

            // $request->session()->flash('Success','Category Added Successfully');
          
            session()->flash('Success', 'Category Added Successfully');

            return response()->json([

                'status' => true,
                'message' => 'Category Added Successfully'

            ]);

        }
        else {
            return response()->json([

                'status' => false,
                'errors' => $validator->errors(),

            ]);
        }


    }

    public function edit(){

    }

    public function update(){

    }


    public function destroy(){

    }
}