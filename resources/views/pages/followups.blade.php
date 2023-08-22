<x-easyadmin::app-layout>

<div x-data="{
            fpselected : false,
            fp : [],
            fps : [],
            fpname : '',
            isValid : false,
            isGenuine : false,
            fpremarks : [],
            leadremarks: [],
            historyLoading: true,
            history: []
        }"

        >
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}
      <div class="h-[calc(100vh-3.5rem)] mt-14 pt-7 bg-base-200 w-full flex justify-evenly">

        {{-- followups table --}}
        <x-tables.followup-table :followups="$followups"/>

        {{-- details section --}}
        <div
        x-data = "{


        }"
        class="w-[50%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-neutral-content rounded-xl p-3 xl:px-6 py-3">
            <h1 class="text-lg text-secondary font-semibold text-center">Follow up details</h1>
            <p x-show="!fpselected" class=" font-semibold text-base text-center mt-4">Select a follow up...</p>

            <div x-show="fpselected" class="flex w-full mt-3">
                <div
                {{-- updating values in the details section --}}
                @fpupdate.window="

                if(fps[$event.detail.id] != null || fps[$event.detail.id] != undefined){
                    fp = fps[$event.detail.id];
                    fpname = fp.lead.name;
                }
                else{
                    fp = $event.detail.followup;
                    fp.lead = JSON.parse($event.detail.lead);

                    leadremarks = JSON.parse($event.detail.lead_remarks);
                    fp.lead.remarks = leadremarks;
                    fps[fp.id] = fp;
                }


                fpselected = true;
                isValid = fp.lead.is_valid;
                isGenuine = fp.lead.is_genuine;
                fpname = fp.lead.name;



                axios.get('/api/followup',{
                    params: {
                    id: fp.id,
                    lead_id: fp.lead.id

                    }
                  }).then(function (response) {
                    history = response.data.followup;
                    console.log(response.data.followup);
                    historyLoading = false;

                  }).catch(function (error){
                    console.log(error);
                    historyLoading = false;
                  });

                "

                @fetchHistory.window="
                console.log('api called for fetching history');
                "

                class=" w-1/2 border-r border-primary">
                <h1 class=" font-medium text-base text-secondary">Lead details</h1>
                    <p class="text-base font-medium">Name : <span x-text=" fp.lead != undefined ? fp.lead.name : '' "> </span></p>
                    <p class="text-base font-medium">City : <span x-text="fp.lead != undefined ? fp.lead.city : '' "> </span></p>
                    <p class="text-base font-medium">Phone : <span x-text=" fp.lead != undefined ? fp.lead.phone : '' "> </span></p>
                    <p class="text-base font-medium">Email : <span x-text=" fp.lead != undefined ? fp.lead.email : '' "> </span></p>

                    <div class=" flex items-center space-x-2">
                        <p class=" text-base font-medium">Is valid : </p>

                        <input  type="checkbox" name="is_valid"  :checked=" isValid == 1 ? true : false" class="checkbox checkbox-sm cursor-not-allowed pointer-events-none checkbox-success focus:ring-0" />
                    </div>

                    <div class=" flex items-center space-x-2  ">
                        <p class=" text-base font-medium ">Is genuine : </p>

                        <input  type="checkbox" name="is_genuine"  :checked=" isGenuine == 1 ? true : false " class="checkbox checkbox-sm cursor-not-allowed pointer-events-none checkbox-success focus:ring-0" />
                    </div>

                    <p class=" text-base font-medium">Lead Segment : <span class=" uppercase text-secondary" x-text="fp.lead != undefined ? fp.lead.customer_segment : '' "></span></p>

                    <div class="mt-2.5">
                        <p class=" text-base font-medium text-secondary">Lead remarks</p>

                        <ul class=" list-disc text-sm list-outside font-normal">
                            <template x-for="remark in leadremarks">

                                <li x-text="remark.remark"></li>

                            </template>
                        </ul>
                    </div>

                    <div class=" mt-2.5">
                        <p class="text-base font-medium text-secondary">Follow up history</p>

                        {{-- loading --}}
                        <div x-cloak x-show="historyLoading" class=" w-full flex justify-center">
                            <span class="loading loading-bars loading-xs text-center my-4 text-primary"></span>
                        </div>

                        {{-- looping through history --}}
                        <template x-show="!historyLoading" x-for="item in history" >
                            <div x-show="item.actual_date != null" class=" mt-2">
                                <p class=" font-medium">Date : <span class=" text-primary" x-text="item.actual_date"></span></p>
                                <p class="font-medium">Follow up remarks</p>
                                <ul>
                                    <template x-if="item.remarks != undefined">
                                        <template x-for="remark in item.remarks">
                                            <li x-text="remark.remark"></li>
                                        </template>
                                    </template>
                                </ul>
                            </div>
                        </template>

                        <p x-show="!historyLoading" class=" text-error" x-text=" history.length == 1 ? 'No follow ups completed yet' : '' "></p>


                    </div>

                </div>

                <div class=" w-1/2 px-2.5">
                    <h2 class=" text-secondary font-medium text-base">Follow up</h2>

                        <form
                        x-data ="
                        { doSubmit() {
                            let form = document.getElementById('followup-form');
                            let formdata = new FormData(form);
                            formdata.append('followup_id',fp.id);
                            formdata.append('lead_id',fp.lead.id);
                            $dispatch('formsubmit',{url:'{{route('process-followup')}}', route: 'process-followup',fragment: 'page-content', formData: formdata, target: 'followup-form'});
                        }}"

                        @submit.prevent.stop="doSubmit();"

                        @formresponse.window="
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                $el.reset();


                                if($event.detail.content.followup != null || $event.detail.content.followup != undefined)
                                {
                                fp.next_followup_date = $event.detail.content.followup.next_followup_date;
                                }

                                if($event.detail.content.followup_remark != null || $event.detail.content.followup_remark != undefined)
                                {
                                    fp.remarks.push($event.detail.content.followup_remark);

                                }

                                historyLoading = true;
                                axios.get('/api/followup',{
                                    params: {
                                    id: fp.id,
                                    lead_id: fp.lead.id

                                    }
                                  }).then(function (response) {
                                    history = response.data.followup;
                                    console.log(response.data.followup);
                                    historyLoading = false;

                                  }).catch(function (error){
                                    console.log(error);
                                    historyLoading = false;
                                  });


                                $dispatch('formerrors', {errors: []});
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }"

                        id="followup-form" class=" mt-2 bg-base-100 rounded-xl flex flex-col space-y-2" action="">

                        <ul class="">
                            <template x-if="fp.remarks != undefined || fp.remarks != null">
                                <template x-for="remark in fp.remarks">
                                    <li x-text="remark.remark"></li>
                                </template>
                            </template>
                        </ul>

                            <textarea name="remark" class="textarea bg-base-200 focus:ring-0" placeholder="Add new follow up remark"></textarea>

                            <div>
                                <label x-show="fp.next_followup_date == null" for="next-followup-date" class="text-sm font-medium">Schedule date for next follow up</label>

                                <input x-show="fp.next_followup_date == null" id="next-followup-date" name="next_followup_date" type="date" class=" rounded-lg input-info bg-base-200 w-full">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm mt-1 self-start">Save follow up</button>
                        </form>

                </div>
            </div>
        </div>

      </div>

    </div>



</div>

</x-easyadmin::app-layout>
