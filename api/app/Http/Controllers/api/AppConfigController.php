<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Entities\AppConfig;
use Illuminate\Http\Request;
use Validator;

class AppConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $appConfig = AppConfig::getConfig();
            return $this->success($appConfig, "Success", 200);
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Entities\AppConfig  $appConfig
     * @return \Illuminate\Http\Response
     */
    public function show(AppConfig $appConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Entities\AppConfig  $appConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AppConfig $appConfig)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error('ValidationError', $validator->errors());
        }

        try {
            $data = $request->all();
            $config = AppConfig::where('id', $data['id'])->update($data);
            if ($config) {
                return $this->success($config, "Saved successfully", 201);
            } else {
                return $this->error('ValidationError', 'Unable to save');
            }
        } catch (Exception $e) {
            return $this->error('Some internal error', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Entities\AppConfig  $appConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(AppConfig $appConfig)
    {
        //
    }
}
