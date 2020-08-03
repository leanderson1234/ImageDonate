<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private $user;
    private $path = '/perfil';
    private $totalPage = 10;
    public function __construct(User $user)
    {
        $this->user = $user;
        
        $this->middleware('auth:api')->except('store');
    }

    /**
     * Display a listing of the resource.
     *@param  App\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $this->user->getResults($this->totalPage,$request->all());
        if(!$user) return response()->json(['Error'=> 'User not Found'],404);
        return response()->json($user,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreUpdateUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateUserRequest $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $user = $this->user->create($data);
        return response()->json($user,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->user->with(['photos','coments'])->find($id);
        if(!$user) return response()->json(['error' => 'Not Found'],404);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\StoreUpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateUserRequest $request, $id)
    {
        $user = $this->user->find($id);
        if(!$user) return response()->json(['error' => 'Not Found'],404);
        //dd(auth()->user()->id);
        $user_session = auth()->user();

        if($user_session->id == $id){
            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            if($request->hasFile('image_path') && $request->file('image_path')->isValid()){
                if($user->image_path){
                    if(Storage::exists("{$this->path}/{$user->image_path}")){
                        Storage::delete("{$this->path}/{$user->image_path}");
                    }
                }
    
                $name = Str::kebab($request->name);
                
                $extension = $request->image_path->extension();
               
                $nameFile = md5(time().$name).'.'.$extension;
                $data['image_path'] ="storage/perfil/{$nameFile}";
                $upload = $request->image_path->storeAs($this->path,$nameFile);
    
                if(!$upload) return response()->json(['error' =>'Fail Upload'],500);
            }
            
            
            $user->update($data);
            
            return response()->json($user);
        }else{
            return response()->json(['error'=>'permission denied'],403);
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
        $user = $this->user->find($id);
        if(!$user) return response()->json(['error', 'Not Found'],404);
        $user_session = auth()->user();

        if($user_session->id == $id){
            if($user->image_path){
                if(Storage::exists($user->image_path)){
                    Storage::delete($user->image_path);
                }
            }
            $user->delete();
            return response()->json(['success'=> 'user deleted'],204);
        }
        else{
            return response()->json(['error'=>'permission denied'],403);
        }
    }
}
