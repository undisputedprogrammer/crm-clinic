<div x-show= "selected_action == 'Complete'">

    <template x-if="lead.status == 'Closed'">
        <p class=" font-medium text-error py-4 text-base">This lead is closed</p>
    </template>
    {{-- <template x-if="lead.status == 'Consulted'">
        <p class=" font-medium text-error py-4 text-base">This lead is Consulted</p>
    </template> --}}

    <form x-show="lead.status != 'Closed'" x-data="{
        doSubmit() {
            let form = document.getElementById('lead-close-form');
            let formdata = new FormData(form);
            {{-- formdata.append('no_followup',true); --}}
            formdata.append('lead_id',lead.id);
            $dispatch('formsubmit',{url:'{{route('lead.close')}}', route: 'lead.close',fragment: 'page-content', formData: formdata, target: 'lead-close-form'});
        }
    }" action="" class=" flex flex-col space-y-2" id="lead-close-form"
    @submit.prevent.stop="doSubmit()"
    @formresponse.window="
    if($event.detail.target == $el.id){
        if ($event.detail.content.success) {
            $dispatch('linkaction',{link:'{{route('followups')}}',route: 'followups', fragment: 'page-content', fresh:true});
            lead.status = 'Closed';
            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
            $el.reset();
        }else if (typeof $event.detail.content.errors != undefined) {
            $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

        } else{
            $dispatch('formerrors', {errors: $event.detail.content.errors});
        }
    }
    ">
        <div class=" mt-3 flex flex-col space-y-2">
            <p class=" font-medium text-error">Are you sure you want to complete this lead? </p>
            <label class="cursor-pointer label justify-start p-0 space-x-2">
                <span class="label-text">Yes</span>
                <input type="checkbox" required class="checkbox checkbox-success checkbox-xs" />
            </label>
        </div>

        <button type="submit" class="btn btn-sm btn-success w-fit">Mark lead as complete</button>

    </form>
</div>