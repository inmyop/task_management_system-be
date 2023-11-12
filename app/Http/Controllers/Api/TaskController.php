<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Task;

class TaskController extends ApiController
{
    public function addTask(Request $request)
    {
        if (Auth::check()) {
            $userCode = Auth::user()->users_code;

            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required',
                'deadline' => 'required|date_format:Y-m-d',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->sendError(1, 'Validate erorr', $validator->errors());
            }

            $data['task_code'] = generateCode('TASK');
            $data['users_code'] = $userCode;
            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['document'] = $request->document;
            $data['status'] = $request->status;
            $data['deadline'] = $request->deadline . ' 23:59';;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filePath = uploadFile($file, $userCode);
                $data['document'] = $filePath;
            }

            $created = Task::create($data);
            
            if(!$created){
                return $this->sendError(1,'Failed added task', []);
            }

            return $this->sendCreatedResponse(1, 'Add task successfully', $data);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
    public function getTasks(Request $request)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $limit = $request->input('limit', 10);
            $keyword = $request->input('search');
            $date = $request->input('date');
    
            $query = Task::where('users_code', $user_code)
                ->where('is_delete', 0);
    
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%")
                      ->orWhere('description', 'like', "%$keyword%");
                });
            }
    
            if ($date) {
                $query->whereDate('deadline', $date);
            }
    
            $result = $query->select(['task_code', 'users_code', 'title', 'description', 'document', 'status', 'deadline'])
             ->paginate($limit);

            if(empty($result)){
                return $this->sendResponse(1, 'Task retrieved successfully', []);
            }

            return $this->sendResponse(1, 'Task retrieved successfully', $result);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
    public function getDetail(Request $request, $task_code)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $result = Task::where('users_code', $user_code)
                ->where('task_code', $task_code)
                ->where('is_delete', 0)
                ->select(['task_code', 'users_code', 'title', 'description', 'document', 'status', 'deadline'])
                ->first();

            if (empty($result)) {
                return $this->sendError(1, 'Task not found', []);
            }

            if ($request->has('download') && $request->get('download') === 'true') {
                return downloadFile($result->document);
            }

            return $this->sendResponse(1, 'Task detail retrieved successfully', $result);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
    public function updateTask(Request $request)
    {
        if (Auth::check()) {
            $userCode = Auth::user()->users_code;

            $result = Task::where('users_code', $userCode)
                ->where('task_code', $request->task_code)
                ->where('is_delete', 0)
                ->first();

            if (empty($result)) {
                return $this->sendError(1, 'Task not found', []);
            }

            $data['title'] = $request->title;
            $data['description'] = $request->description;
            $data['document'] = $request->document;
            $data['status'] = $request->status;
            $data['deadline'] = $request->deadline;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = uploadFile($file, $userCode);
                $data['document'] = $filePath;
            }

            $updated = Task::where('task_code', $request->task_code)->update($data);

            if(!$updated){
                return $this->sendError(0,'Failed updated task', []);
            }
            
            return $this->sendCreatedResponse(1, 'Add task successfully', $data);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
    public function deleteTask(Request $request, $task_code)
    {
        if (Auth::check()) {
            $user_code = Auth::user()->users_code;
            $deleted = Task::where('users_code', $user_code)
                ->where('task_code', $task_code)
                ->update(['is_delete' => 1]);

            if(!$deleted){
                return $this->sendError(0,'Failed delete task', []);
            }
                
            return $this->sendResponse(1, 'Task deleted successfully', []);
        } else {
            $errors = [
                'Unauthenticated' => 'You must be logged in to access this resource.',
            ];
            return $this->sendUnauthorized(2, "Unauthenticated", $errors);
        }
    }
}
