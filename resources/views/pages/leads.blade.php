<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>
      <x-sections.side-drawer/>
      {{-- page body --}}


        <h1 class=" text-primary text-xl font-semibold bg-base-200 px-[3.3%] pt-2.5">Leads</h1>

      <div x-data="{
        convert: false
      }"

        {{-- pagination event handler --}}
        @pageaction.window="
        console.log($event.detail);
        $dispatch('linkaction',{
            link: $event.detail.link,
            route: currentroute,
            fragment: 'page-content',
        })"

        {{-- Event handler to handle the change cutomer segment event --}}
        @changesegment.window="
        if($event.detail.current == $event.detail.new){
            console.log('cannot change status');
        }
        else{

        ajaxLoading = true;
        axios.get($event.detail.link,{
            params: {
                lead_id : lead.id,
                customer_segment : $event.detail.new
            }
        }).then(function(response){
            console.log(response);
            lead.customer_segment = $event.detail.new;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            console.log(error);
            ajaxLoading = false;
        });
        }"

        {{-- change is_valid status --}}
        @changevalid.window="
        ajaxLoading = true;
        axios.get($event.detail.link,{
            params:{
                lead_id : lead.id,
                is_valid : $event.detail.is_valid
            }
        }).then(function(response){

            lead.is_valid = response.data.is_valid;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            ajaxLoading = false;
            console.log(error);
        })"

        {{-- change is_genuine status --}}
        @changegenuine.window="
        ajaxLoading = true;
        axios.get($event.detail.link,{
            params:{
                lead_id : lead.id,
                is_genuine : $event.detail.is_genuine
            }
        }).then(function(response){

            lead.is_genuine = response.data.is_genuine;
            ajaxLoading = false;
            $dispatch('showtoast', {message: response.data.message, mode: 'success'});
        }).catch(function(error){
            ajaxLoading = false;
            console.log(error);
        })"
       class=" md:h-[calc(100vh-5.875rem)] pt-7 pb-[2.8rem]  bg-base-200 w-full md:flex justify-evenly">




        <x-tables.leads-table :leads="$leads"/>



        <div x-data="{
            selected_section: 'details'
        }" class="w-[96%] mx-auto mt-4 md:mt-0 md:w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div class=" flex space-x-4">
                <h2 @click="selected_section = 'details'" class="text-lg font-semibold text-secondary cursor-pointer" :class=" selected_section == 'details' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">Lead details</h2>

                <h2 @click="selected_section = 'qna'" class="text-lg font-semibold text-secondary cursor-pointer " :class=" selected_section == 'qna' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">QNA</h2>

                <h2 @click="selected_section = 'wp'" class="text-lg font-semibold text-secondary cursor-pointer " :class=" selected_section == 'wp' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">WhatsApp</h2>
            </div>

            <p x-show="!selected" class=" font-semibold text-base text-center mt-4">Select a lead...</p>

            <div x-show="selected && selected_section == 'details'" x-transition
            @detailsupdate.window="
            selected_section = 'details';
            selected = true;
            if(leads[$event.detail.id] == undefined){
                leads[$event.detail.id] = {};
                {{-- lead = JSON.parse($event.detail.lead);
                remarks = JSON.parse($event.detail.remarks);
                followups = JSON.parse($event.detail.followups); --}}
                lead = $event.detail.lead;
                remarks = $event.detail.remarks;
                followups = $event.detail.followups;
                answers = $event.detail.answers;
                name = lead.name;
                leads[lead.id] = lead;
                leads[lead.id].remarks = remarks;
                leads[lead.id].followups = followups;
                leads[lead.id].answers = answers;

            }
            else{
                lead = leads[$event.detail.id];
                remarks = leads[$event.detail.id].remarks;
                followups = leads[$event.detail.id].followups;
                name = lead.name;
                answers = lead.answers;


            }
            convert = false;



            {{-- lead = JSON.parse($event.detail.lead);
            remarks = JSON.parse($event.detail.remarks);
            name = lead.name;
            leads[lead.id] = lead;
            leads[lead.id].remarks = remarks;
            console.log(leads); --}}
            " class=" mt-2 flex flex-col space-y-2">
                <p class="text-base font-medium">Name : <span x-text="lead.name"> </span></p>
                <p class="text-base font-medium">City : <span x-text="lead.city"> </span></p>
                <p class="text-base font-medium">Phone : <span x-text="lead.phone"> </span></p>
                <p class="text-base font-medium">Email : <span x-text="lead.email"> </span></p>

                <div class=" flex items-center space-x-2">
                    <p class=" text-base font-medium">Is valid : </p>

                    <input @change.prevent.stop="$dispatch('changevalid',{
                        link: '{{route('change-valid')}}',
                        is_valid: lead.is_valid,
                    });" type="checkbox" name="is_valid" :checked=" lead.is_valid == 1 ? true : false" class="checkbox checkbox-sm checkbox-success focus:ring-0" />
                </div>

                <div class=" flex items-center space-x-2 ">
                    <p class=" text-base font-medium ">Is genuine : </p>

                    <input @change.prevent.stop="$dispatch('changegenuine',{
                        link: '{{route('change-genuine')}}',
                        is_genuine: lead.is_genuine,
                    });" type="checkbox" name="is_genuine" :checked=" lead.is_genuine == 1 ? true : false " class="checkbox checkbox-sm checkbox-success focus:ring-0" />
                </div>

                <div class=" flex items-center space-x-2">
                    <p class=" text-base font-medium">Lead Segment : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm" ><span x-text="lead.customer_segment" class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
                            <li><a @click.prevent.stop="
                                $dispatch('changesegment',{
                                    link: '{{route('change-segment')}}',
                                    current: lead.customer_segment,
                                    new : 'hot'
                                });" class=" " :class="lead.customer_segment == 'hot' ? ' text-primary hover:text-primary' : '' ">Hot</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changesegment',{
                                    link: '{{route('change-segment')}}',
                                    current: lead.customer_segment,
                                    new : 'warm'
                                });" class=" " :class="lead.customer_segment == 'warm' ? ' text-primary hover:text-primary' : '' ">Warm</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changesegment',{
                                    link: '{{route('change-segment')}}',
                                    current: lead.customer_segment,
                                    new : 'cold'
                                });" class="" :class="lead.customer_segment == 'cold' ? ' text-primary hover:text-primary' : '' ">Cold</a></li>
                        </ul>

                      </div>
                </div>

                <div class=" flex flex-col">

                    <p class=" text-base font-medium text-secondary">Remarks</p>

                    <ul class=" list-disc text-sm list-inside font-normal">
                        <template x-for="remark in remarks">

                        <li x-text="remark.remark"></li>

                        </template>
                    </ul>

                    <form
                    x-data = "{ doSubmit() {
                            let form = document.getElementById('add-remark-form');
                            let formdata = new FormData(form);
                            formdata.append('remarkable_id',lead.id);
                            formdata.append('remarkable_type','lead');
                            $dispatch('formsubmit',{url:'{{route('add-remark')}}', route: 'add-remark',fragment: 'page-content', formData: formdata, target: 'add-remark-form'});
                        }}"
                    @submit.prevent.stop="doSubmit()"
                    @formresponse.window="
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                axios.get('/api/get/remarks',{
                                    params: {
                                    remarkable_id: lead.id,
                                    remarkable_type: 'App\Models\Lead'
                                    }
                                  }).then(function (response) {

                                    remarks = response.data.remarks;
                                    leads[lead.id].remarks = remarks;
                                    document.getElementById('add-remark-form').reset();

                                  }).catch(function (error){
                                    console.log(error);
                                  });
                                $dispatch('formerrors', {errors: []});
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }"
                    action="" id="add-remark-form" class=" bg-base-200 flex flex-col space-y-2 mt-2 p-3 rounded-xl w-full max-w-[408px]">

                        <textarea placeholder="Remark" name="remark" required class="textarea textarea-bordered textarea-sm w-full max-w-sm"></textarea>

                        <button type="submit" class="btn btn-primary btn-sm self-end">Add remark</button>

                    </form>

                </div>

                <x-forms.message-form :templates="$messageTemplates"/>

                {{-- <x-sections.qna /> --}}

                <div>
                    <h1 class=" text-secondary text-base font-medium">Follow up details</h1>
                    <h1 x-text="lead.followup_created == 1 ? 'Follow up Initiated' : 'Follow up is not initiated for this lead' " class="  font-medium text-primary"></h1>

                    <p x-show="lead.followup_created == 1" class=" font-medium ">
                        <span>follow up scheduled : </span>
                        <span class="text-primary" x-text="lead.followup_created == 1 ? followups[0].scheduled_date : '' "></span>
                    </p>
                    <p x-show="lead.followup_created == 1" class=" font-medium">
                        <span>Followed up date : </span>
                        <span class="text-primary" x-text="lead.followup_created == 1 ? followups[0].actual_date : '---' " class="text-secondary"></span>
                    </p>

                    <p x-show="lead.status == 'Converted' && lead.followup_created == 0"  class=" font-medium text-success my-1">Appointment Scheduled</p>

                    <form x-show="lead.followup_created == 0 "
                    x-data = "{ doSubmit() {
                        let form = document.getElementById('initiate-followup-form');
                        let formdata = new FormData(form);
                        formdata.append('lead_id',lead.id);

                        $dispatch('formsubmit',{url:'{{route('initiate-followup')}}', route: 'initiate-followup',fragment: 'page-content', formData: formdata, target: 'initiate-followup-form'});
                    }}"

                    @formresponse.window="
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});
                                $el.reset();

                                followups.push($event.detail.content.followup);
                                leads[lead.id].followups = followups;


                                lead.followup_created = 1;
                                leads[lead.id].followup_created = lead.followup_created;


                                $dispatch('formerrors', {errors: []});
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }"
                    id="initiate-followup-form"
                    @submit.prevent.stop="doSubmit();"
                     action="" class="bg-base-200 flex flex-col space-y-2 mt-2 p-3 rounded-xl w-full max-w-[408px]">
                    <label for="scheduled-date" class="text-sm font-medium">Schedule a date for follow up</label>
                    <input id="scheduled-date" name="scheduled_date" type="date" class=" rounded-lg input-info bg-base-100">
                    <button type="submit" class="btn btn-primary btn-sm mt-1 self-start">Initiate follow up</button>
                    </form>

                    {{-- convert checkbox --}}
                    <label class="cursor-pointer label justify-start p-0 space-x-2 mt-5">

                        <input @click="convert = $el.checked" :disabled=" lead.followup_created == true || lead.status == 'Converted' ? true : false" type="checkbox" name="convert" x-model="convert" class="checkbox checkbox-success checkbox-xs" />
                        <span class="label-text">Schedule appointment</span>
                    </label>

                    <x-forms.add-appointment-form :doctors="$doctors"/>

                </div>


            </div>

            {{-- QNA section --}}
            <div x-show="selected_section == 'qna' " class=" py-3">
                <x-sections.qna />
            </div>


            {{-- Whatsapp section --}}
            <div x-show="selected_section == 'wp' " class=" py-3">
                <x-sections.whatsapp/>
            </div>

        </div>

      </div>
    </div>
  </div>
  <x-footer/>
</x-easyadmin::app-layout>
