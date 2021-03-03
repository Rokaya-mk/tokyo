<?php

namespace App\Http\Controllers\Api;
use App\Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Resources\TaskResources;
use  Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class TaskController extends BaseController
{

    public function displayTodayTasks(Request $request)
    {
        $timezone=$request->timeZone;
        $dt=new Carbon();
        $dt->setTimezone($timezone);
        //$dt = Carbon::now();
        $tasks=Task::where('user_id' ,Auth::id())
                ->where('date_task',$dt->toDateString())
                ->get() ;
        if(count($tasks) > 0){
            return $this->sendResponse(TaskResources::collection($tasks),'Today Tasks');
        }
        else  {
            return $this->sendError('today Tasks list is empty' );
        }

    }
    public function displayTomorrowTasks(Request $request)
    {
        $timezone=$request->timeZone;
        $dt=new Carbon();
        $dt->setTimezone($timezone);
        $tommorowDate=$dt->addDay();
        $tasks=Task::where('user_id' ,Auth::id())
                ->where('date_task',$tommorowDate->toDateString())
                ->get() ;
        if(count($tasks) > 0){
            return $this->sendResponse(TaskResources::collection($tasks), 'Tomorrow tasks' );
        }
        else  {
            return $this->sendError('Tomorrow Tasks list is empty' );
        }
    }

    public function storeTodayTask(Request $request)
    {
        $timezone=$request->timeZone;
        $carbon=new Carbon();
        $carbon->setTimezone($timezone);
        $dt = $carbon->toDateString();
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
        $timezone=$request->timeZone;
        $dt=new Carbon();
        $dt->setTimezone($timezone);
        //$dt=$carbon;
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

    public function update(Request $request,Task $task)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required',

        ]);
        if ($validator->fails()) {
            return $this->sendError('Errror can\'t modify task name ' , $validator->errors());
        }

        if ( $task->user_id != Auth::id()) {
            return $this->sendError(' you dan\'t have rights to update  ' , $validator->errors());
        }
        $task->name = $input['name'];

        $task->save();

        return $this->sendResponse(new TaskResources($task), 'task updated ' );
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
        //$deadline = '11:15:00';
        $timezone=$request->timeZone;
        $dt=new Carbon();
        $dt->setTimezone($timezone);
        $deadline = new Carbon( '23:59:59');
        //$newDate=$dt->addDay()->toDateString();

        // $dadel =  date('H:i:s', strtotime( '14:20:00'));
        // $date = date('H:i:s', strtotime($request));
       // $d= date('Y-m-d', strtotime($request));
        if( $deadline->lessThanOrEqualTo($dt)){


            $tasks=Task::where('user_id' ,Auth::id())
                        ->where('status',0)
                        ->Where('date_task',$dt->toDateString())
                        ->get();

            foreach($tasks as $task){
                $task->date_task=Carbon::parse($task->date_task)->addDay()->toDateString();

                $task->save();
            }
            return $this->sendResponse(TaskResources::collection($tasks),'merge ');

         }else{
            return $this->sendError('not time to merge' , $errorMessage);
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
