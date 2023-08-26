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
        $questions = ['Q-FC'=>'What is your favourite color ?','Q-FF'=>'What is your favourite food ?','Q-DLS'=>'Do you like sports ?','Q-FOE'=>'How often do you exercise ?','Q-POM'=>'How long have you been married','Q-YO'=>'What is your occupation ?'];

        $keys = array_keys($questions);
        foreach($keys as $key){
            Question::create([
                'question_code'=>$key,
                'question'=>$questions[$key],
            ]);
        }
    }
}
