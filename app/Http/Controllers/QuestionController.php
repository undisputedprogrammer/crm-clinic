<?php

namespace App\Http\Controllers;

use App\Models\Question;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class QuestionController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function store(Request $request){
        if(!Gate::allows('is-admin')){
            return response(['success'=>false,'message'=>'Unauthorized action'],401);
        }
        $question = Question::create([
            'question'=>$request->question
        ]);
        $question->question_code='Q_'.$question->id;
        $question->save();
        $questions = Question::orderBy('created_at', 'desc')->get();
        return response()->json(['success'=>true, 'message'=>'Question added','questions'=>$questions]);
    }
}
