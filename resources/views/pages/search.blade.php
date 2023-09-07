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
            history: [],
            pagination_type: null,
            appointment: null


        }"

        {{-- pagination event handler --}}
        @pageaction.window="
        console.log($event.detail);
        $dispatch('nextpage',{link: $event.detail.link, page:$event.detail.page});

        "

        >
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}
      <div class="min-h-[calc(100vh-3.5rem)] pt-7 pb-[2.8rem] bg-base-200 w-full ">
            <h1 class=" text-primary text-xl font-medium text-start px-9">Search follow ups</h1>

            <form
            x-data = "{ doSubmit() {

                let form = document.getElementById('search-form');
                let formdata = new FormData(form);
                formdata.append('search_type',searchtype);
                pagination_data = formdata;
                console.log(formdata.get('is_valid'));
                searchFormState.is_valid = formdata.get('is_valid');
                searchFormState.is_genuine = formdata.get('is_genuine');
                searchFormState.lead_status = formdata.get('lead_status');
                searchFormState.agent = formdata.get('agent');
                $dispatch('formsubmit',{url:'{{route('get-results')}}', route: 'get-results',fragment: 'page-content', formData: formdata, target: 'search-form'});
            }}"

            @nextpage.window="
            console.log('going to link'+$event.detail.link);
            fpselected = false;
            {{-- let form = document.getElementById('search-form');
            let formdata = new FormData(form);
            formdata.append('search_type',pagination_type); --}}
            $dispatch('formsubmit',{url:$event.detail.link, route: 'get-results',fragment: 'page-content', formData: pagination_data, target: 'search-form'});
            "

            @submit.prevent.stop="doSubmit();"

            @formresponse.window="

                        console.log($event.detail.content);
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {

                                {{-- $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'}); --}}

                                $dispatch('resultupdate',{html: $event.detail.content.table_html});
                                pagination_type = $event.detail.content.pagination_type;
                                $dispatch('formerrors', {errors: []});
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }"
            id="search-form"
             action="" class=" flex justify-start border border-secondary w-[95.3%] mx-auto items-end flex-wrap  py-3 px-2 rounded-xl mt-3">

                <div class="w-1/4 px-1 mt-1.5">
                    <label for="search-by" class="text-sm text-primary font-medium">Search by</label>
                    <select name="search_type" @change.prevent.stop="searchtype = $el.value;" class="select  select-bordered w-full max-w-xs bg-base-100 text-base-content">
                        <option disabled>--Search by--</option>

                        <option :selected="searchtype == 'scheduled_date' ? true : false " value="scheduled_date">Scheduled date</option>

                        <option :selected="searchtype == 'actual_date' ? true : false " value="actual_date">Actual date</option>

                    </select>
                </div>

                <div class="w-1/4 px-1 mt-1.5">
                    <label for="from-date" class="text-sm text-primary font-medium">Select from date</label>
                    <input id="from-date" type="date" :required = "searchtype == 'scheduled_date' || searchtype == 'actual_date' ? true : false " placeholder="from date" name="from_date" class="input input-bordered w-full max-w-xs text-base-content" x-model="fromDate" :value="fromDate"/>
                </div>

                <div class="w-1/4 px-1 mt-1.5">
                    <label for="to-date" class=" text-sm text-primary font-medium">Select to date</label>
                    <input id="to-date" type="date" :required = "searchtype == 'scheduled_date' || searchtype == 'actual_date' ? true : false " placeholder="to date" name="to_date" class="input input-bordered w-full max-w-xs text-base-content" x-model="toDate" :value="toDate" />
                </div>

                <div class="w-1/4 px-1 mt-1.5">
                    <label for="" class=" text-sm text-primary font-medium">Select valid status</label>
                    <select class="select  select-bordered w-full max-w-xs bg-base-100 text-base-content" name="is_valid">
                        <option value="null" :disabled="searchFormState.is_valid == null ? true : false"
                        :selected="searchFormState.is_valid == null ? true : false ">--Not selected--</option>

                        <option :selected="searchFormState.is_valid == 1 ? true : false " value="1">Validated</option>

                        <option :selected="searchFormState.is_valid == 0 ? true : false " value="0">Not validated</option>

                    </select>
                </div>

                <div class="w-1/4 px-1 mt-1.5">
                    <label for="" class=" text-sm text-primary font-medium">Select genuine status</label>
                    <select name="is_genuine" class="select  select-bordered w-full max-w-xs bg-base-100 text-base-content">
                        <option value="null" :disabled="searchFormState.is_genuine == null ? true : false " :selected="searchFormState.is_genuine == null ? true : false ">--Not selected--</option>

                        <option :selected="searchFormState.is_genuine == 1 ? true : false " value="1">Genuine</option>

                        <option :selected="searchFormState.is_genuine == 0 ? true : false " value="0">Not genuine</option>

                    </select>
                </div>

                <div class="w-1/4 px-1 mt-1.5">
                    <label for="" class=" text-sm text-primary font-medium">Select lead status</label>
                    <select name="lead_status" class="select  select-bordered w-full max-w-xs bg-base-100 text-base-content">
                        <option value="null" :disabled="searchFormState.lead_status == null ? true : false " :selected="searchFormState.lead_status == null ? true : false " >--Not selected--</option>

                        <option :selected="searchFormState.lead_status == 'Created' ? true : false " value="Created">Created</option>

                        <option :selected="searchFormState.lead_status == 'Converted' ? true : false " value="Converted">Converted</option>

                        <option :selected="searchFormState.lead_status == 'Closed' ? true : false " value="Closed">Closed</option>

                    </select>
                </div>

                @can('is-admin')
                <div class="w-1/4 px-1 mt-1.5">
                    <label for="" class=" text-sm text-primary font-medium">Select Agent</label>
                    <select name="agent" class="select  select-bordered w-full max-w-xs bg-base-100 text-base-content">
                        <option value="null" :disabled="searchFormState.agent == null ? true : false " :selected="searchFormState.agent == null ? true : false " >--Not selected--</option>

                        @foreach ($agents as $agent)
                            <option :selected="searchFormState.agent == '{{$agent->id}}' ? true : false " value="{{$agent->id}}">{{$agent->name}}</option>
                        @endforeach


                    </select>
                </div>
                @endcan



                <button :disabled = " searchtype == '' ? true : false " class=" btn btn-primary" type="submit">Search</button>


            </form>


            {{-- results section --}}

            <div class="w-full flex justify-evenly items-start my-4">

                {{-- table displayer --}}
                <div x-html="searchResults"
                x-show="showresults"
                x-transition
                x-cloak
                @resultupdate.window="
                showresults=false;
                searchResults = $event.detail.html;
                setTimeout(() => {
                    showresults=true;
                    $el.innerHTML = searchResults;
                  }, '400');
                "
                id="result-table" class="w-[53%]">

                </div>


                {{-- details section --}}
                <div x-show="showresults"
                x-transition
                x-cloak
                    x-data = "{


                    }"
                    class="w-[40%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-neutral-content rounded-xl p-3 xl:px-6 py-3">

                    <h1 class="text-lg text-secondary font-semibold text-center">Follow up details</h1>

                    <p x-show="!fpselected" class=" font-semibold text-base text-center mt-4">Select a follow up...</p>


                    <div x-show="fpselected" class="flex w-full mt-3">
                        <div
                        {{-- updating values in the details section --}}
                        @dataupdate.window="


                        if(fps[$event.detail.id] != null || fps[$event.detail.id] != undefined){
                            fp = fps[$event.detail.id];
                            fpname = fp.lead.name;
                        }
                        else{
                            fp = $event.detail.followup;
                            fp.lead = $event.detail.lead;

                            leadremarks = $event.detail.lead_remarks;
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
                            console.log(history);

                          }).catch(function (error){
                            console.log(error);
                            historyLoading = false;
                          });

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



                        </div>

                        <div class=" w-1/2 px-1 mt-1.5.5">
                            <div class="">
                                <p class="text-base font-medium text-secondary">Follow up history</p>

                                {{-- loading --}}
                                <div x-cloak x-show="historyLoading" class=" w-full flex justify-center">
                                    <span class="loading loading-bars loading-xs text-center my-4 text-primary"></span>
                                </div>

                                {{-- looping through history --}}
                                <template x-show="!historyLoading" x-for="item in history" >
                                    <div>


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

                                    <p x-show="item.actual_date == null" class="font-medium">Next follow up date : <span class=" text-error" x-text="item.scheduled_date"></span></p>

                                </div>







                                </template>

                                <p x-show="!historyLoading" class=" text-error" x-text=" history.length == 1 ? 'No follow ups completed yet' : '' "></p>

                                <template x-if="fp.lead != undefined && fp.lead != null && fp.lead.appointment != null">
                                    <p x-show="!historyLoading" class=" text-success font-medium mt-1.5"><span>Appointment scheduled for : </span><span class="text-primary" x-text="fp.lead.appointment != null ? fp.lead.appointment.appointment_date : '' "></span></p>
                                </template>
                            </div>



                        </div>
                    </div>



                </div>
            </div>
      </div>

    </div>

</div>
<x-footer/>
</x-easyadmin::app-layout>
