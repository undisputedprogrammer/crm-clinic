<div x-show=" answers && answers.length != 0" class=" flex flex-col">

    {{-- <p class=" text-base font-medium text-secondary">QNA</p> --}}

    <ul class=" text-sm  font-normal">
        <template x-for="(answer,i) in answers">

        <li>
            <p class=" font-medium">
                <span x-text=" i+1"></span>
                <span class="" x-text="answer.question.question"></span>
            </p>
            <p class="px-3" x-text="answer.answer">

            </p>
        </li>

        </template>
    </ul>

</div>
