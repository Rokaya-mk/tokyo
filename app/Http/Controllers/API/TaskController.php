<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Task as ResourcesTask;
use App\Models\Task;
use  Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends BaseController
{

    public function displayTodayTasks()
    {   $dt = Carbon::now();
        $tasks=Task::where('user_id' ,Auth::id())
                ->where('date_task',$dt->toDateString())
                ->get() ;
        if(count($tasks) > 0){
            return $this->sendResponse(ResourcesTask::collection($tasks), 'success' );
        }
        else  {
            return $this->sendError('today Tasks list is empty' );
        }

    }
    public function displayTomorrowTasks()
    {
        $dt = Carbon::now();
        $tommorowDate=$dt->addDay();
        $tasks=Task::where('user_id' ,Auth::id())
                ->where('date_task',$tommorowDate->toDateString())
                ->get() ;
        if(count($tasks) > 0){
            return $this->sendResponse(ResourcesTask::collection($tasks), 'success' );
        }
        else  {
            return $this->sendError('Tomorrow Tasks list is empty' );
        }
    }

    public function storeTodayTask(Request $request)
    {
        $dt = Carbon::now()->toDateString();
        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required'

        ]);
        if ($validator->fails()) {
            return $this->sendError('Error failed to store task',$validator->errors() );
        }

        $user = Auth::user();
        $input['status'] = 0;
        $input['date_task'] =$dt;
        $input['user_id'] = $user->id;
        $task = Task::create($input);
        return $this->sendResponse($task,'success');
    }

    public function storeTomorrowTask(Request $request)
    {
        $dt = Carbon::now();
        $tommorowDate=$dt->addDay()->toDateString();
        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required'

        ]);
        if ($validator->fails()) {
            return $this->sendError('Error failed to store task',$validator->errors() );
        }

        $user = Auth::user();
        $input['status'] = 0;
        $input['date_task'] =$tommorowDate;
        $input['user_id'] = $user->id;
        $task = Task::create($input);
        return $this->sendResponse($task,'success');
    }


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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
        {$errorMessage = [] ;
            //$note = Note::destroy($id);
            $task = Task::find($id);
            if ($task->user_id != Auth::id()) {
                return $this->sendError('you don\'t have rights ' , $errorMessage);
            } else {
                    if(!is_null($task)){
                        $task->delete();
                        return $this->sendResponse(new ResourcesTask($task),'task delete successfully');
                    }
                    else
                        return $this->sendError('Error ' , $errorMessage);
            }

    }
}
