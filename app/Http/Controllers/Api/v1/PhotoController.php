<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdatePhotoRequest;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Constraint\FileExists;

class PhotoController extends Controller
{
    private $photo;
    private $path = 'galery';
    public function __construct(Photo $request){
        $this->photo = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photo = $this->photo->all();
        if(!$photo) return response()->json(['error' => 'image not found']);
        return response()->json($photo);
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreUpdatePhotoRequest   $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdatePhotoRequest $request)
    {

        $data = $request->all();
        if(!$data) return response()->json(['error' => 'data not found'],404);
        $user_session = auth()->user();
        $data['user_id'] = $user_session->id;
        
        if($user_session->id ===  $data['user_id']){
            if($request->hasFile('path') && $request->file('path')->isValid()){
                
                $name = Str::kebab($request->name);
                $extension = $data['path']->extension();
                $namecrip = md5(time().$name);
                $nameFile =  "{$namecrip}.{$extension}";
                $data['path'] = "storage/galery/{$nameFile}";
            
                $upload = $request->path->storeAs($this->path,$nameFile);
                
                if(!$upload) return response()->json(['error'=>'upload failed'],500);
            }
            
            $photo = $this->photo->create($data);
            return response()->json($photo,201);
    }else{
        return response()->json(['error' => 'permission denied'],403);
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
        $photo = $this->photo->with(['user','coments'])->find($id);
        if(!$photo)return response()->json(['error' => 'Not Found'],404);
        return response()->json($photo);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param   @param  App\Http\Requests\StoreUpdatePhotoRequest   $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,StoreUpdatePhotoRequest $request)
    {
        $photo = $this->photo->find($id);
        if(!$photo) return response()->json(['error'=> 'Not Found'],404);
        $user_session = auth()->user();

        if($user_session->id == $id){
            $data = $request->all();
        //dd($data);
            if($request->hasFile('path') && $request->file('path')->isValid()){
                $name = Str::kebab($data['name']);
                $extension = $request->path->extension();
                $nameFile = md5(time().$name).'.'.$extension;
                $data['path'] = "storage/galery/{$nameFile}";

                $upload = $request->path->storeAs($this->path,$nameFile);
                if(!$upload) return response()->json(['error' => 'upload failed'],500);
            }
            $photo->update($data);
        
            return response()->json($data);
    }else{
        return response()->json(['error' => 'permission denied'],403);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $photo = $this->photo->find($id);
        if(!$photo) return response()->json(['error'=> "not found"]);
        $user_session = auth()->user();

        if($user_session->id == $id){

            if($photo->path){
                if(Storage::exists($photo->path)){
                    Storage::delete($photo->path);
                }
            }
            $photo->delete();
            return response()->json(['success' => 'photo deleted'],204);
    }else{
        return response()->json(['error' => 'permission denied'],403);
    }
    }
/*
    public function coments($id){
      
        $photo = $this->photo->find($id);

        if(!$photo) return response()->json(['error'=>'not found'],404);
         $photo->coments;

        return response()->json([
            'photo' => $photo 
        ]);
    }
    */
}
