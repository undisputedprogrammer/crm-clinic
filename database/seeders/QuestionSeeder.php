<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            'What is your favourite color ?',
            'What is your favourite food ?',
            'Do you like sports ?',
            'How often do you exercise ?',
            'How long have you been married',
            'What is your occupation ?'
        ];

        foreach($questions as $qn){
            $q = Question::create([
                'question_code'=>'qcode',
                'question'=>$qn,
            ]);
            $q->question_code = 'Q_'.$q->id;
            $q->save();
        }
    }
}
