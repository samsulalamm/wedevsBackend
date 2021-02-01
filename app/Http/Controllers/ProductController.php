<?php

namespace App\Http\Controllers;

use App\Models\FileSystem;
use App\Models\Product;
use App\Traits\ApiStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiStatusTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = array();
        $response['items'] = Product::with('fileAttach')->orderBy('id', 'desc')->get();
        return $this->successApiResponse($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            $response['message'] = $validator->errors()->first();
            return $this->failureApiResponse($response);
        } else {
            $pdata = new Product();
            $pdata->title = $request->title;
            $pdata->description = $request->description;
            $pdata->price = $request->price;
            if ($request->file()) {
                $new_file = new FileSystem();
                $upload = $new_file->upload('product_image', $request->image);
                if (@$upload->code != 100) {
                    $pdata->file_id = $upload->id;
                }
            }
            $pdata->save();
            $response['message'] = "Product has been added successfully.";
            return $this->successApiResponse($response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response['item'] = Product::with('fileAttach')->find($id);
        return $this->successApiResponse($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            $response['message'] = $validator->errors()->first();
            return $this->failureApiResponse($response);
        } else {
            $pdata = Product::find($id);
            $pdata->title = $request->title;
            $pdata->description = $request->description;
            $pdata->price = $request->price;
            if ($request->file()) {
                $new_file = new FileSystem();
                $upload = $new_file->upload('product_image', $request->image);
                if (@$upload->code != 100) {
                    $pdata->file_id = $upload->id;
                }
            }
            $pdata->save();
            $response['message'] = "Product has been updated successfully.";
            return $this->successApiResponse($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Product::with('fileAttach')->find($id);
        if ($data->fileAttach) {
            if (Storage::disk('public')->exists($data->fileAttach->FileDir)) {
                Storage::disk('public')->delete($data->fileAttach->FileDir);
            }
        }
        $data->delete();
        $response['message'] = "Product has been deleted successfully.";
        return $this->successApiResponse($response);
    }
}
