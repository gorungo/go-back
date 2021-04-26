<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPhoto;
use App\Http\Requests\UploadPhoto;
use App\Photo;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Support\Facades\Log;


class PhotoController extends Controller{

    public $photo;


    public function __construct(){
        $this->photo = new Photo(request()->route('controller'));
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove photo from the storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Making photo main.
     *
     * @param  String  $controller
     * @param  Integer  $itemId
     * @param  Integer  $imgId
     *
     * @return \Illuminate\Http\Response
     */
    public function setMainImage(Request $request, $controller = Null, $itemId = Null, $imgId = Null)
    {



            $photo = new Photo($controller);

            // сохраняем изображение на диске в нужной папке, если нужно ресайзим
            $result = $photo->setMainImage($request, $itemId, $imgId);

            if ($result['type'] == 'ok') {

                // сохраняем инфу про имя картинки в базу
                $item->thmb_img = $result['file_name'];

                $item->save();

                // если все было хорошо, то отправляем ответ на фронт

                return response()->json($result);


            } else {

                // если были ошибки при сохранении изображения,

                // удаляем инфу из БД о картинке
                $photos->delete();

                // отправляем данные в iframe об ошибке
                return response()->json($result);
            }


        // отправляем данные в iframe об ошибке
        return response('"<script>window.top.img_infomess("err"); </script>"', 200)->header('Content-Type', 'application/javascript');
    }


    /**
     * Deleting photo main.
     *
     * @param  $request  Request
     * @param  String  $controller
     * @param  Integer  $itemId
     * @param  Integer  $imgId
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $controller = Null, $itemId = Null, $imgId = Null)
    {

        // загружаем данные о картинке
        $img = Photos::find($request->input('img_id'));

        $owner_id = 0;


        if($img->item_id){
            $company = Company::find($img->item_id);


            if ($request->input('controller') == 'company') {

                $item = Company::find($img->item_id);
                $owner_id = $item->user_id;

            } elseif ($request->input('controller') == 'job') {

                $item = Job::find($img->item_id);
                $owner_id = $item->company->user_id;

            }elseif($request->input('controller') == 'candidates'){

                $item = Candidate::find($img->item_id);
                $owner_id = $item->user_id;

            }elseif($request->input('controller') == 'posts'){

                $item = Posts::find($img->item_id);
                $owner_id = $item->user_id;

            }elseif($request->input('controller') == 'events'){

                $item = Events::find($img->item_id);
                $owner_id = $item->user_id;

            }
            elseif($request->input('controller') == 'offers'){

                $item = Offers::find($img->item_id);
                $owner_id = $item->user_id;

            }elseif($request->input('controller') == 'food'){

                $item = Food::find($img->item_id);
                $owner_id = $item->user_id;

            }elseif($request->input('controller') == 'products'){

                $item = Products::find($img->item_id);
                $owner_id = $item->user_id;

            }elseif($request->input('controller') == 'suppliers'){

                $item = Supplier::find($img->item_id);
                $owner_id = $item->user_id;

            }


            // проверяем, может ли данный пользователь удалять фотки в данный элемент
            if($this->can_upload_photo($owner_id) ){


                // удаляем картинку с диска
                array_map('unlink', glob('images/'. $request->input('controller'). '/' . $img->item_id . '/' . $img->img_name));


                //удаляем картинку из базы
                $img->delete();
                return response()->json(['type'=>'ok', 'item_id' =>$img->item_id ]);

            }
        }

// отправляем данные в iframe об ошибке
        return response()->json(['type','error']);
    }

}
