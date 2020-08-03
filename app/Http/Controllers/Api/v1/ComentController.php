<?php

namespace App\Http\Controllers\Api\v1;

use App\Coment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateComentRequest;
use Illuminate\Http\Request;

class ComentController extends Controller
{
    private $coment;
    
    public function __construct(Coment $coment)
    {
        $this->coment = $coment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coment = $this->coment->all();
        if(!$coment) return response()->json(['error' => 'coment not found'],404);
        return response()->json($coment);
    } 

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreUpdateComentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateComentRequest $request)
    {
        $user_session = auth()->user();
        $data = $request->all();
        $data['user_id'] =$user_session->id;

        if($user_session->id == $data['user_id']){
            $coment =$this->coment->create($data);
            return response()->json($coment,201);
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
        $coment = $this->coment->with(['user','photo'])->find($id);
        if(!$coment)return response()->json(['error' => 'Not Found'],404);
        return response()->json($coment);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StoreUpdateComentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateComentRequest $request, $id)
    {
        $coment = $this->coment->find($id);
        if(!$coment) return response()->json(['error' =>'Not found'],404);
        
        $user_session = auth()->user();

        if($user_session->id == $id){
            
            $data = $request->all();
            $data['user_id'] = $coment->user_id;
            $data['photo_id'] = $coment->photo_id;
            $coment->update($data);
            
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
        $user_session = auth()->user();

        if($user_session->id == $id){
            $coment = $this->coment->find($id);
            if(!$coment) return response()->json(['error' => 'not found'],404);
            $coment->delete();
            return response()->json($coment,204);
        }else{
            return response()->json(['error' => 'permission denied'],403);
        }
    }
}
