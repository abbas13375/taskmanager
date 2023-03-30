<?php

namespace App\Http\Controllers;

use App\Http\Requests\task\CreateRequest;
use App\Http\Requests\task\UpdateRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * return all tasks for current user
     * @return json
     */
    public function actionList(){
        $tasks = Auth::user()->relTasks();

        $type = \request()->get('type');
        if($type == 'archive'){
            $tasks = $tasks->withTrashed()->whereNotNull('deleted_at');
        }else if($type == 'all'){
            $tasks = $tasks->withTrashed();
        }


        return $this->successResponse($tasks->get());
    }

    /**
     * create new task for current user.
     * @return json
     */
    public function actionCreate(CreateRequest $request){
        $taskData = $request->only('title', 'description');
        $taskData['user_id'] = Auth::id();
        $task = Task::create($taskData);
        if($request->hasFile('file')){
            $file = $request->file('file');
            $newFileName = 'task_' . $task->id . '_file.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $newFileName, Task::STORAGE_DIR_FILE);
            $task->file = $path;
            $task->save();
        }

        return $this->successResponse($task, 'task create successfully', 200);
    }

    /**
     * return detail one task.
     * @param $id
     * @return json
     */
    public function actionDetail($id){
        $task = Auth::user()->relTasks()->where('id', $id)->first();
        if(! $task){
            return $this->customErrorResponse([], 'Not Found any Task with this id', 404);
        }

        return $this->successResponse($task);
    }

    /**
     * update a task.
     * @return json
     */
    public function actionUpdate(Request $request, $id){
        $task = Auth::user()->relTasks()->where('id', $id)->first();
        if(! $task){
            return $this->customErrorResponse([], 'Not Found any Task with this id', 404);
        }

        $taskData = $request->only('title', 'description');
        if($request->hasFile('file')){
            Storage::delete(Task::STORAGE_DIR_FILE . DIRECTORY_SEPARATOR . $task->file);
            $file = $request->file('file');
            $newFileName = 'task_' . $task->id . '_file.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $newFileName, Task::STORAGE_DIR_FILE);
            $taskData['file'] = $path;
        }

        $task->update($taskData);
        return $this->successResponse($task);
    }

    /**
     * remove task
     * @param $id,
     * return json
     */
    public function actionRemove($id){
        $task = Auth::user()->relTasks()->where('id', $id)->first();
        if(! $task){
            return $this->customErrorResponse([], 'Not Found any Task with this id', 404);
        }

        // remove if has file.
        if($task->file){
            Storage::delete(Task::STORAGE_DIR_FILE. DIRECTORY_SEPARATOR . $task->file);
        }
        $task->delete();
        return $this->successResponse([], 'task remove successfully');
    }

    /**
     * complete a task by user.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actionComplete($id){
        $task = Auth::user()->relTasks()->where('id', $id)->first();
        if(! $task){
            return $this->customErrorResponse([], 'Not Found any Task with this id', 404);
        }

        if($task->completed){
            return $this->successResponse([], 'This task has already been completed', 200);
        }
        $task->update(['completed' => true]);

        return $this->successResponse([], 'operation done successfully');
    }

    /**
     * restore a task that deleted
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actionRestore($id)
    {
        $task = Auth::user()->relTasks()->onlyTrashed()->where('id', $id)->first();
        if(! $task){
            return $this->customErrorResponse([], 'Not Found any Task with this id', 404);
        }
        $task->restore();
        return $this->successResponse([], 'Task restored successfully');
    }



}
