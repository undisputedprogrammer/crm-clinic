<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\User;
use App\Models\Answer;
use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToArray, WithHeadingRow
{
    private $agents = [];
    private $x = 0;
    private $headings = [];
    public function __construct(array $headings)
    {
        $this->headings = $headings;
        $this->agents = User::havingRoles(['agent'])->get()->pluck('id');
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function array(array $rows)
    {
        // info('showing headings');
        // info($this->headings);
        // $row = $row[0];
        foreach ($rows as $row) {
            if($row['name'] == null){
                return null;
            }
            $lead = Lead::create([
                'name' => $row['full_name'],
                'phone' => $row['phone_number'],
                'email' => $row['email'],
                'city' => $row['city'],
                'is_valid' => false,
                'is_genuine' => false,
                'history' => $row['history'] ?? '',
                'customer_segment' => 'cold',
                'status' => 'Created',
                'followup_created' => false,
                'assigned_to' => $this->agents[$this->x]
            ]);
            $this->x++;
            if ($this->x == count($this->agents)) {
                $this->x = 0;
            }

            foreach ($this->getQuestionHeaders() as $qh) {
                $q = Question::where('question_code', $qh)->get()->first();
                $ans = Answer::create([
                    'question_id' => $q->id,
                    'lead_id' => $lead->id,
                    'question_code' => $qh,
                    'answer' => $row[strtolower($qh)]
                ]);
            }
        }

        return $lead;
    }

    private function getQuestionHeaders()
    {
        $h = [];
        foreach ($this->headings as $heading) {
            if (substr(strtoupper($heading), 0, 2) == 'Q_') {
                $h[] = strtoupper($heading);
            }
        }
        return $h;
    }
}
