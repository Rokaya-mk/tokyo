<?php

namespace App\Http\Controllers\API;
use App\Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TaskResources;

use  Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class TaskController extends BaseController
{

    public function displayTodayTasks()
    {   $dt = Carbon::now();
        $tasks=Task::where('user_id' ,Auth::id())
                ->where('date_task',$dt->toDateString())
                ->get() ;
        if(count($tasks) > 0){
            return $this->sendResponse(TaskResources::collection($tasks),$dt );
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
            return $this->sendResponse(TaskResources::collection($tasks), 'success' );
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

    public function markDone($id)
    {
        $errorMessage = [] ;
        $task = Task::find($id);
        if ($task->user_id == Auth::id()){
            $task->status=1;
            $task->save();
            return $this->sendResponse(new TaskResources($task), 'task is marked as completed now' );
        }
        return $this->sendError('you don\'t have rights' , $errorMessage);

    }

    public function UncheckeTask($id)
    {
        $errorMessage = [] ;
        $task = Task::find($id);
        if ($task->user_id == Auth::id()){
            $task->status=0;
            $task->save();
            return $this->sendResponse(new TaskResources($task), 'task is marked as ongoing now' );
        }
        return $this->sendError('you don\'t have rights' , $errorMessage);

    }

    public function mergeTasks(Request $request)
    {   $errorMessage = [] ;
        $deadline = '14:20:00';
        $dadel =  date('H:i:s', strtotime( '14:20:00'));
        $date = date('H:i:s', strtotime($request));
        $d= date('Y-m-d', strtotime($request));
        if(date('H:i:s',strtotime($deadline))>=date('H:i:s',strtotime($request))){


            $tasks=Task::where('user_id' ,Auth::id())
                        ->where('status',0)
                        ->Where('date_task',$d)
                        ->get();

            foreach($tasks as $task){
                $task->date_task=Carbon::parse($task->date_task)->addDay();

                $task->save();
            }
            return $this->sendResponse(TaskResources::collection($tasks),'ok');

         }else{
            return $this->sendError('Error' , $errorMessage);
           }
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
                        return $this->sendResponse(new TaskResources($task),'task delete successfully');
                    }
                    else
                        return $this->sendError('Error ' , $errorMessage);
            }

    }
}
