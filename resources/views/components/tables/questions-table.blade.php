@props(['questions'])

<div x-data="{
    questions: {{ json_encode($questions->items()) }},
    edit_question: '',
    edit_question_id: null
}"
    @pageaction.window="
            console.log($event.detail);
            $dispatch('linkaction',{
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
    })"
    class="w-fit mx-auto">

    <x-forms.add-question-form />

    <x-modals.edit-question-modal />

    <div class="overflow-x-auto w-fit mx-auto border border-primary rounded-xl mt-3">

        <table class="table w-fit mx-auto  ">

            <thead>
                <tr class=" text-secondary">
                    <th></th>
                    <th>Question Code</th>
                    <th>Question</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>


                <template x-for="question in questions">
                    <tr :id="question.question_code" class=" text-base-content hover:bg-base-100"
                        @questionupdate.window="
        if($el.id == $event.detail.target){
            question.question = $event.detail.question;
        }">
                        <th x-text="question.id"></th>
                        <td x-text="question.question_code"></td>
                        <td x-text="question.question"></td>
                        <td x-text="question.created_at"></td>
                        <td class="flex ">

                            <svg @click.prevent.stop="
            edit_question=question.question;
            edit_question_id=question.id;
            edit_question_modal.showModal();
            "
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 p-1 rounded-md stroke-primary hover:bg-base-200">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>




                        </td>
                    </tr>
                </template>


            </tbody>
        </table>


    </div>
    <div class="mt-1.5">
        {{ $questions->links() }}
    </div>

</div>
