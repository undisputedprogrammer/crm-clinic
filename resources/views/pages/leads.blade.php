<x-easyadmin::app-layout>
<div x-data="x_leads" x-init="
@isset($selectedLeads)
    console.log('{{$selectedLeads}}');
@endisset">
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>
      <x-sections.side-drawer/>
      {{-- page body --}}

        <div class=" flex bg-base-200 items-center justify-between px-[3.3%]">
            <h1 class=" text-primary text-xl font-semibold bg-base-200  pt-2.5">Leads</h1>
            <button @click.prevent.stop="toggleTemplateModal()" x-show="Object.keys(selectedLeads).length != 0" x-transition class="btn btn-success flex btn-sm self-end"><x-icons.whatsapp-icon/><span>Bulk message</span></button>
        </div>

        <x-modals.template-select-modal :templates="$messageTemplates"/>
        <x-display.sending/>

      <div x-data="{
        convert: false
      }"

        {{-- pagination event handler --}}
        @pageaction.window="
        console.log($event.detail);
        $dispatch('linkaction',{
            link: $event.detail.link,
            route: currentroute,
            fragment: 'page-content'
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

        {{-- Change Questions --}}
        @changequestion.window="
        if($event.detail.current == $event.detail.q_answer){
            console.log('cannot change answer');
        }
        else{

        ajaxLoading = true;
        axios.get($event.detail.link,{
            params: {
                lead_id : lead.id,
                q_answer : $event.detail.q_answer,
                question : $event.detail.question
            }
        }).then(function(response){
            console.log(response);
            if(response.data.q_visit != undefined){
                if(response.data.q_visit == null || response.data.q_visit == 'null'){
                    lead.q_visit = null;
                }
                else{
                    lead.q_visit = response.data.q_visit;
                }
            }
            if(response.data.q_decide != undefined){
                if(response.data.q_decide == null || response.data.q_decide == 'null'){
                    lead.q_decide = null;
                }
                else{
                    lead.q_decide = response.data.q_decide;
                }
            }
            lead.customer_segment = response.data.customer_segment;
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
            selected_section: 'details',
            messageLoading : false,
            qnas: [],
            chats : [],
            loadWhatsApp(){
                this.selected_section = 'wp';
                this.messageLoading = true;

                axios.get('/api/get/chats',{
                    params : {
                        id : lead.id
                    }
                }).then((r)=>{
                    console.log(r);
                    this.chats = r.data.chats;
                    this.messageLoading = false;

                }).catch((e)=>{
                    console.log(e);
                });

            }
        }"

        class="w-[96%] mx-auto mt-4 md:mt-0 md:w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div class=" flex space-x-4">
                <h2 @click="selected_section = 'details'" class="text-lg font-semibold text-secondary cursor-pointer" :class=" selected_section == 'details' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">Lead details</h2>

                <h2 @click="selected_section = 'qna'" class="text-lg font-semibold text-secondary cursor-pointer " :class=" selected_section == 'qna' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">QNA</h2>

                <h2 @click="loadWhatsApp();" class="text-lg font-semibold text-secondary cursor-pointer " :class=" selected_section == 'wp' ? 'opacity-100' : ' hover:opacity-100 opacity-40' ">WhatsApp</h2>
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
                qnas = $event.detail.qnas;
                name = lead.name;
                leads[lead.id] = lead;
                leads[lead.id].remarks = remarks;
                leads[lead.id].followups = followups;
                leads[lead.id].qnas = qnas;

            }
            else{
                lead = leads[$event.detail.id];
                remarks = leads[$event.detail.id].remarks;
                followups = leads[$event.detail.id].followups;
                name = lead.name;
                qnas = lead.qnas;


            }
            convert = false;

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

                {{-- Questions for lead segment --}}

                {{-- question visit within a week --}}
                <div class="flex items-center space-x-2">
                    <p class=" text-base font-medium">Will they visit within a week ? : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm" ><span x-text="lead.q_visit == null || lead.q_visit == 'null' ? 'Not selected' : lead.q_visit " class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_visit,
                                    q_answer : 'null',
                                    question : 'q_visit'
                                });" class=" " :class="lead.q_visit == null ? ' text-primary hover:text-primary' : '' ">Not selected</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_visit,
                                    q_answer : 'yes',
                                    question : 'q_visit'
                                });" class=" " :class="lead.q_visit == 'yes' ? ' text-primary hover:text-primary' : '' ">Yes</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_visit,
                                    q_answer : 'no',
                                    question : 'q_visit'
                                });" class="" :class="lead.q_visit == 'no' ? ' text-primary hover:text-primary' : '' ">No</a></li>
                        </ul>

                      </div>
                </div>


                {{-- question decide within a week --}}
                <div x-show="lead.q_visit == 'no'" class="flex items-center space-x-2">
                    <p class=" text-base font-medium">Will they decide within a week ? : </p>
                    <div class="dropdown">
                        <label tabindex="0" class="btn btn-sm" ><span x-text="lead.q_decide == null || lead.q_decide == 'null' ? 'Not selected' : lead.q_decide " class=" text-secondary"></span><x-icons.down-arrow /></label>

                        <ul tabindex="0" class="dropdown-content z-[1] mt-1  menu p-2 shadow rounded-box w-52" :class="theme == 'light' ? ' bg-base-200' : 'bg-neutral' ">
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_decide,
                                    q_answer : 'null',
                                    question : 'q_decide'
                                });" class=" " :class="lead.q_decide == null ? ' text-primary hover:text-primary' : '' ">Not selected</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_decide,
                                    q_answer : 'yes',
                                    question : 'q_decide'
                                });" class=" " :class="lead.q_decide == 'yes' ? ' text-primary hover:text-primary' : '' ">Yes</a></li>
                            <li><a @click.prevent.stop="
                                $dispatch('changequestion',{
                                    link: '{{route('lead.answer')}}',
                                    current: lead.q_decide,
                                    q_answer : 'no',
                                    question : 'q_decide'
                                });" class="" :class="lead.q_decide == 'no' ? ' text-primary hover:text-primary' : '' ">No</a></li>
                        </ul>

                      </div>
                </div>


                <div class=" flex items-center space-x-2">
                    <p class=" text-base font-medium">Lead Segment : <span x-text = "lead.customer_segment != null ? lead.customer_segment : 'Unknown' " :class="lead.customer_segment != null ? ' uppercase' : '' "></span></p>

                </div>


                {{-- *********************************************************************************
                Remark area
                ********************************************************************************* --}}
                <div class=" flex flex-col">

                    <p class=" text-base font-medium text-secondary">Remarks</p>

                    <ul class=" list-disc text-sm list-outside flex flex-col space-y-2 font-normal">
                        <template x-for="remark in remarks">

                            <li class="">
                                <span x-text="remark.remark"></span>

                                <span>-</span>
                                <span x-text="formatDate(remark.created_at)"></span>

                            </li>

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

                {{-- **************************************************************** --}}


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

                </div>


                {{-- pwl --}}
                <div x-data="{
                    selected_action : 'Initiate Followup'
                }" class="pt-2.5">

                <x-dropdowns.leads-action-dropdown/>

                <x-forms.followup-initiate-form/>

                <x-forms.add-appointment-form :doctors="$doctors"/>

                <x-forms.lead-close-form/>

                </div>


            </div>

            {{-- QNA section --}}
            <div x-show="selected_section == 'qna' " class=" py-3">
                <x-sections.qna />
            </div>


            {{-- Whatsapp section --}}
            <div x-show="selected_section == 'wp' " class=" py-3" :class="messageLoading ? ' flex w-full ' : '' ">
                <x-sections.whatsapp :templates="$messageTemplates"/>

                <div x-show="messageLoading" class=" w-full flex flex-col space-y-2 justify-center items-center py-8">
                    <span class="loading loading-bars loading-md "></span>
                    <label for="">Please wait while we load messages...</label>
                </div>

            </div>

        </div>

      </div>
    </div>
  </div>
  <x-footer/>
</x-easyadmin::app-layout>
