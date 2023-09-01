<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Facades\Gate;

class QuestionService{

    public function processAndStore($question){
        if(!Gate::allows('is-admin')){
            return response(['success'=>false,'message'=>'Unauthorized action'],401);
        }
        $question = Question::create([
            'question'=>$question
        ]);
        $question->question_code='Q_'.$question->id;
        $question->save();
        $questions = Question::orderBy('created_at', 'desc')->paginate(8);
        return ['success'=>true, 'message'=>'Question added','questions'=>$questions->items()];
    }
}
