<div x-show="selected_action == 'Initiate Followup'">

    <template x-if="lead.followup_created != 0">
        <p class=" text-primary font-medium py-4">Follow up is initiated for this lead</p>
    </template>

    <template x-if="lead.status == 'Closed'">
        <p class=" text-error font-medium py-4 text-base">This lead is closed.</p>
    </template>

    <form x-show="lead.followup_created == 0 && lead.status != 'Closed'" x-data="{
        doSubmit() {
                let form = document.getElementById('initiate-followup-form');
                let formdata = new FormData(form);
                formdata.append('lead_id', lead.id);

                $dispatch('formsubmit', { url: '{{ route('initiate-followup') }}', route: 'initiate-followup', fragment: 'page-content', formData: formdata, target: 'initiate-followup-form' });
            }
        }"
        @formresponse.window="
            if ($event.detail.target == $el.id) {
                if ($event.detail.content.success) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                    $el.reset();

                    followups.push($event.detail.content.followup);
                    leads[lead.id].followups = followups;


                    lead.followup_created = 1;
                    lead.status = 'Follow-up Started';
                    leads[lead.id].followup_created = lead.followup_created;

                    document.getElementById('lead-tick-'+lead.id).classList.remove('hidden');
                    $dispatch('formerrors', {errors: []});
                } else if (typeof $event.detail.content.errors != undefined) {
                    $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                } else{
                    $dispatch('formerrors', {errors: $event.detail.content.errors});
                }
            }"
        id="initiate-followup-form"
        @submit.prevent.stop="doSubmit();"
        action=""
        class="bg-base-200 flex flex-col space-y-2 mt-2 p-3 rounded-xl w-full max-w-[408px]">

        <label for="scheduled-date" class="text-sm font-medium">Schedule a date for follow up</label>
        <input id="scheduled-date" name="scheduled_date" type="date" class=" rounded-lg input-info bg-base-100">

        <button type="submit" class="btn btn-primary btn-sm mt-1 self-start">Initiate follow up</button>

    </form>
</div>
