<?php


namespace App\Servicss;


use App\Models\CourseModule;
use App\Models\LessonBlock;
use App\Models\Task;
use App\Models\TaskQuestion;
use App\Models\UserCourseModuleTest;
use App\Models\UserLesson;
use App\Models\UserLessonBlock;
use App\Models\UserModule;
use App\Models\UserTask;
use App\Models\UserTaskPoint;

class TaskService
{

    public $course_modal_finish;
    public $course_finish;

    public function __construct()
    {

    }

    public function user_task($id, $task_id, $lesson_id, $next_lesson_id, $module_id, $next_module_lesson, $next_module)
    {
        $module_test = Task::where('id', $task_id)->pluck('module_test_id')->first();
        if ($module_test) {
            UserCourseModuleTest::where('course_module_test_id', $module_test)->where('user_id', auth('api')->id())->update(['status' => 'done']);
        } else {
            $taskStatus = UserTask::where('task_id', $task_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();
            if ($taskStatus && $taskStatus->point !== 0) {
                $user_task = UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first();
                $lesson_block_id = Task::where('id', $task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $lesson_point = (+$user_lesson) - (+$user_task->point);
                $lesson_block_point = (+$user_lesson_block) - (+$user_task->point);
                UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                $user_module = UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->first();
                $module_point = (+$user_module->point) - (+$user_task->point);
                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                $d = [
                    'point' => '0'
                ];
                $user_task->update($d);
            }
            $questionSum = UserTaskPoint::where('question_id', $id)->where('task_id', $task_id)->where('user_id', auth('api')->id())->orderBy('point', 'asc')->first();
            $taskPoint = UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first();
            if ($taskPoint && $questionSum) {
                $sum = (+$taskPoint->point) + (+$questionSum->point);
                $data = [
                    'point' => $sum,
                    'status' => 'done',
                ];
                UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->update($data);
                UserTaskPoint::where('question_id', $id)->where('task_id', $task_id)->where('user_id', auth('api')->id())->delete();
            }
            $user_task = UserTask::where('task_id', $task_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();
            if ($user_task) {
                $lesson_block_id = Task::where('id', $task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $lesson_point = (+$user_lesson) + (+$user_task->point);
                $lesson_block_point = (+$user_lesson_block) + (+$user_task->point);
                UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                $user_module = UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->first();
                $module_point = (+$user_task->point) + (+$user_module->point);
                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                $array = array();
                $user_task_status = UserTask::where('user_id', auth('api')->id())->where('lesson_id', $lesson_id)->pluck('status');
                foreach ($user_task_status as $status) {
                    if ($status) {
                        if ($status === 'done') {
                            array_push($array, 'done');
                        } else {
                            array_push($array, null);
                        }
                    }
                }

                $module_status = UserModule::where('module_id', $module_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();
                $lesson_status = UserLesson::where('lesson_id', $lesson_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();


                if (!$lesson_status) {
                    if (!in_array(null, $array)) {
                        UserLesson::where('lesson_id', $lesson_id)->where('user_id', auth('api')->id())->update(['status' => 'done']);
                        UserLesson::where('lesson_id', $next_lesson_id)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);

                        $course_id = CourseModule::where('id', $module_id)->first()->course_id; // course finish
                        $course_module_id = CourseModule::where('course_id', $course_id)->pluck('id');  // course finish

                        if (!$module_status) {
                            if ($next_module && $next_module_lesson) {
                                UserLesson::where('lesson_id', $next_module_lesson)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                                UserModule::where('module_id', $next_module)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['status' => 'done']);

                                $arrayStatus = array();
                                $modules_status = UserModule::whereIn('module_id', $course_module_id)->where('user_id', auth('api')->id())->pluck('status')->toArray();// course finish
                                foreach ($modules_status as $status) {
                                    if ($status) {
                                        if ($status === 'done') {
                                            array_push($arrayStatus, 'done');
                                        } else {
                                            array_push($arrayStatus, null);
                                        }
                                    }
                                }
                                if (!in_array(null, $arrayStatus)) {
                                    $this->course_finish = true;
                                }
                                $this->course_modal_finish = true;
                            } else if (!$next_module && !$next_module_lesson && !$next_lesson_id && $module_id) {
                                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['status' => 'done']);

                                $arrayStatus = array();
                                $modules_status = UserModule::whereIn('module_id', $course_module_id)->where('user_id', auth('api')->id())->pluck('status')->toArray();// course finish
                                foreach ($modules_status as $status) {
                                    if ($status) {
                                        if ($status === 'done') {
                                            array_push($arrayStatus, 'done');
                                        } else {
                                            array_push($arrayStatus, null);
                                        }
                                    }
                                }
                                if (!in_array(null, $arrayStatus)) {
                                    $this->course_finish = true;
                                }

                                $this->course_modal_finish = true;
                            } else {
                                $this->course_finish = false;
                                $this->course_modal_finish = false;
                            }
                        }
                    } else {
                        UserLesson::where('lesson_id', $lesson_id)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                    }
                }
            }
        }
    }

    public function user_task_many($id, $task_id, $finish, $lesson_id, $next_lesson_id, $module_id, $next_module_lesson, $next_module)
    {
        $module_test = Task::where('id', $task_id)->pluck('module_test_id')->first();
        if ($module_test) {
            UserCourseModuleTest::where('course_module_test_id', $module_test)->where('user_id', auth('api')->id())->update(['status' => 'done']);
        } else {
            $taskStatus = UserTask::where('task_id', $task_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();
            if ($taskStatus) {
                $user_task = UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first();
                $lesson_block_id = Task::where('id', $task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $lesson_point = (+$user_lesson) - (+$user_task->point);
                $lesson_block_point = (+$user_lesson_block) - (+$user_task->point);
                UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                $user_module = UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->first();
                $module_point = (+$user_module->point) - (+$user_task->point);
                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['point' => $module_point]);
                $d = [
                    'point' => '0'
                ];
                $user_task->update($d);
            }
            $questionSum = UserTaskPoint::where('question_id', $id)->where('task_id', $task_id)->where('user_id', auth('api')->id())->orderBy('point', 'asc')->first();
            $taskPoint = UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first();
            if ($taskPoint && $questionSum) {
                $sum = (+$taskPoint->point) + (+$questionSum->point);
                $data = [
                    'task_id' => $task_id,
                    'point' => $sum,
                    'status' => $finish ? 'done' : 'not_performed',
                    'user_id' => auth('api')->id(),
                ];
                UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->update($data);
                UserTaskPoint::where('question_id', $id)->where('task_id', $task_id)->where('user_id', auth('api')->id())->delete();
            }
            if ($finish) {
                $user_task = UserTask::where('task_id', $task_id)->where('user_id', auth('api')->id())->first();
                $lesson_block_id = Task::where('id', $task_id)->pluck('lesson_block_id')->first();
                $lessons_id = LessonBlock::where('id', $lesson_block_id)->pluck('lesson_id')->first();
                $user_lesson = UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $user_lesson_block = UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->pluck('point')->first();
                $lesson_point = (+$user_lesson) + (+$user_task->point);
                $lesson_block_point = (+$user_lesson_block) + (+$user_task->point);
                UserLesson::where('lesson_id', $lessons_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_point]);
                UserLessonBlock::where('lesson_block_id', $lesson_block_id)->where('user_id', auth('api')->id())->update(['point' => $lesson_block_point]);
                $user_module = UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->first();
                $module_point = (+$user_task->point) + (+$user_module->point);
                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['point' => $module_point]);

                $array = array();
                $user_task_status = UserTask::where('user_id', auth('api')->id())->where('lesson_id', $lesson_id)->pluck('status');
                foreach ($user_task_status as $status) {
                    if ($status) {
                        if ($status === 'done') {
                            array_push($array, 'done');
                        } else {
                            array_push($array, null);
                        }
                    }
                }
                $module_status = UserModule::where('module_id', $module_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();
                $lesson_status = UserLesson::where('lesson_id', $lesson_id)->where('status', 'done')->where('user_id', auth('api')->id())->first();


                if (!$lesson_status) {
                    if (!in_array(null, $array)) {
                        UserLesson::where('lesson_id', $lesson_id)->where('user_id', auth('api')->id())->update(['status' => 'done']);
                        UserLesson::where('lesson_id', $next_lesson_id)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                        $course_id = CourseModule::where('id', $module_id)->first()->course_id;
                        $course_module_id = CourseModule::where('course_id', $course_id)->pluck('id');
                        if (!$module_status) {
                            if ($next_module && $next_module_lesson) {
                                UserLesson::where('lesson_id', $next_module_lesson)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                                UserModule::where('module_id', $next_module)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['status' => 'done']);

                                $arrayStatus = array();
                                $modules_status = UserModule::whereIn('module_id', $course_module_id)->where('user_id', auth('api')->id())->pluck('status')->toArray();// course finish
                                foreach ($modules_status as $status) {
                                    if ($status) {
                                        if ($status === 'done') {
                                            array_push($arrayStatus, 'done');
                                        } else {
                                            array_push($arrayStatus, null);
                                        }
                                    }
                                }
                                if (!in_array(null, $arrayStatus)) {
                                    $this->course_finish = true;
                                }

                                $this->course_modal_finish = true;
                            } else if (!$next_module && !$next_module_lesson && !$next_lesson_id && $module_id) {
                                UserModule::where('module_id', $module_id)->where('user_id', auth('api')->id())->update(['status' => 'done']);

                                $arrayStatus = array();
                                $modules_status = UserModule::whereIn('module_id', $course_module_id)->where('user_id', auth('api')->id())->pluck('status')->toArray();// course finish
                                foreach ($modules_status as $status) {
                                    if ($status) {
                                        if ($status === 'done') {
                                            array_push($arrayStatus, 'done');
                                        } else {
                                            array_push($arrayStatus, null);
                                        }
                                    }
                                }
                                if (!in_array(null, $arrayStatus)) {
                                    $this->course_finish = true;
                                }
                                $this->course_modal_finish = true;
                            } else {
                                $this->course_finish = false;
                                $this->course_modal_finish = false;
                            }
                        }
                    } else {
                        UserLesson::where('lesson_id', $lesson_id)->where('user_id', auth('api')->id())->update(['status' => 'in_progress']);
                    }
                }
            }
        }
    }

    public function user_task_point($id, $task_id)
    {
        $questionSum = UserTaskPoint::where('question_id', $id)->where('task_id', $task_id)->where('user_id', auth('api')->id())->get();
        $data = [
            'question_id' => $id,
            'task_id' => $task_id,
            'point' => '',
            'user_id' => auth('api')->id(),
        ];
        if (count($questionSum) === 0) {
            $data['point'] = 1;
            UserTaskPoint::create($data);
        } else if (count($questionSum) === 1) {
            $data['point'] = 0.5;
            UserTaskPoint::create($data);
        } else if (count($questionSum) === 2) {
            $data['point'] = 0.3;
            UserTaskPoint::create($data);
        } else {
            $data['point'] = 0;
            UserTaskPoint::create($data);
        }
    }

}
