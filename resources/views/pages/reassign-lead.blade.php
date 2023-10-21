<x-easyadmin::app-layout>
<div x-data="{center : null, agent : null}" x-init="
@if(isset($selectedCenter))
center = '{{$selectedCenter}}';
@endif">
    <div class=" flex flex-col h-screen flex-auto flex-shrink-0 antialiased bg-base-200  text-black ">


      <x-sections.side-drawer/>
      {{-- page body --}}
      <h2 class="pt-4 px-12 text-lg font-semibold text-primary bg-base-200">Re-assign leads</h2>


      <div x-data="{page: 0,
                    selected : [],
                    confirmed : false,
                     }"
        x-init="
            page = {{request()->input('page', 0)}};
        "

        {{-- pagination event handler --}}
        @pageaction.window="
            page = $event.detail.page;
            $dispatch('linkaction',{
                link: $event.detail.link,
                route: currentroute,
                fragment: 'page-content',
            })"

       class="  pt-7 pb-12 lg:pb-0  bg-base-200 w-full flex flex-col lg:flex-row space-y-4 lg:space-y-0 items-center lg:items-start justify-evenly">




        <x-tables.reassign-leads-table :leads="$leads"/>

        <div class=" w-[96%] lg:w-[35%] flex flex-col ">

            <h1 class=" text-lg font-semibold text-secondary mb-2.5">Actions</h1>

            <div class=" rounded-xl bg-base-100  px-3 py-2 mb-8" >
                <h1 class=" font-semibold text-sm text-base-content pb-1">Filter by Agent</h1>
                <form
                x-data="{
                    selectedCenter : center,
                    doFilter(){
                        ajaxLoading = true;
                        axios.get('/leads/reassign',{
                            params : {
                                filter : document.getElementById('select-filter').value,
                                center : document.getElementById('select_center').value
                            },
                            headers: {
                                'X-Fr' : 'page-content'
                            }
                        }).then((res)=>{
                            showPage = false;
                            setTimeout(
                            () => {
                            ajaxLoading = false;
                            showPage = true;
                            this.$dispatch('contentupdate', {content: res.data.html, target: 'renderedpanel'});
                            }, 100);
                        }).catch((err)=>{
                            ajaxLoading = false;
                            console.log(err);
                        })
                    }
                }"
                 class="flex flex-col space-y-3" id="filter-form" @submit.prevent.stop="doFilter()" >

                    <select name="center" @change="center = $el.value" required id="select_center" class="select select-primary text-base-content select-bordered border-primary w-full  max-w-xs">
                        <option value="all" selected >All Centers</option>
                        @foreach ($centers as $center)
                            <option :selected="center == '{{$center->id}}' " value="{{$center->id}}">{{$center->name}}</option>
                        @endforeach
                    </select>

                    <select @change="searchFilter = $el.value" name="filter" id="select-filter" class="select select-primary text-base-content select-bordered border-primary w-full  max-w-xs">
                        <option value="" disabled selected >Select Agent</option>
                        <option value="all">All agents</option>
                        @if ($agents != null && count($agents) > 0)
                           @foreach ($agents as $agent)

                           <template x-if="center == null || center == 'all' || center == '{{$agent->centers[0]->id}}' ">

                                <option :selected = "searchFilter == $el.value ? true : false" value="{{$agent->id}}">{{$agent->name}}</option>

                           </template>

                           @endforeach
                        @endif

                    </select>
                    <button type="submit" class="btn btn-primary w-fit">Filter</button>
                </form>
               </div>


               <div class= "rounded-xl bg-base-100  px-3 py-2">
                <h1 class=" font-semibold text-base text-base-content pb-1">Assign selected leads to</h1>
                <form
                x-data="{
                    doSubmit(){
                        let form = document.getElementById('leads-reassign-form');
                        let fd = new FormData(form);
                        fd.append('selectedLeads', selected);
                        $dispatch('formsubmit', {url: '{{route('leads.assign')}}', formData: fd, target: 'leads-reassign-form'});
                    }
                }"
                @formresponse.window="
                if($event.detail.target == $el.id){
                    console.log($event.detail.content);
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Successfully assigned'});

                                setTimeout(()=>{
                                    $dispatch('linkaction',{link: '{{route('leads.reassign')}}',route: 'leads.reassign', fragment: 'page-content'});
                                },1000);

                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add update. Please make sure you have entered all details.'});
                            }
                        }
                        "
                action="" id="leads-reassign-form" @submit.prevent.stop="doSubmit();" class=" flex space-x-3 ">
                    <div>
                    <select name="agent" id="selected-agent" required class="select select-primary text-base-content select-bordered border-primary w-full  max-w-xs">
                        <option value="" disabled selected >Select Agent</option>

                        @if ($agents != null && count($agents) > 0)

                           @foreach ($agents as $agent)
                           <template x-if = "center == '{{$agent->centers[0]->id}}' ">
                                <option :disabled = "searchFilter == $el.value ? true : false" value="{{$agent->id}}">{{$agent->name}}</option>
                           </template>

                           @endforeach
                        @endif

                    </select>
                    <p x-show="selected.length == 0" class=" text-sm text-error">You haven't selected any leads</p>

                    <div x-show="selected.length > 0" class=" flex flex-col space-y-2">
                        <p class=" text-base-content text-sm">The selected leads will be assigned to this Agent</p>
                        <label for="" class=" flex space-x-1 items-center">
                            <input @change="
                            if($el.checked){
                                confirmed = true
                            }
                            else{
                                confirmed = false
                            }
                            " type="checkbox" id="confirm-checkbox" class=" checkbox checkbox-success checkbox-xs">
                            <span for="confirm-checkbox" class=" text-sm text-base-content">I Acknowledge</span>
                        </label>

                    </div>
                </div>

                    <button type="submit" :disabled="confirmed ? false : true " class="btn btn-primary">Assign</button>
                </form>
               </div>
        </div>


      </div>

    </div>

</div>
<x-footer/>
</x-easyadmin::app-layout>
