<x-easyadmin::app-layout>
<div x-init="
    selectedCenter = null;
    @isset($selectedCenter)
        selectedCenter = {{$selectedCenter}};
    @endisset">
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>
      <x-sections.side-drawer/>
      {{-- page body --}}

      <div class=" flex justify-start items-center space-x-2 bg-base-200 pt-4 lg:px-14">
        <h2 class=" text-lg font-semibold text-primary bg-base-200">Manage Agents</h2>
        <div>
            @can('is-admin')
                @php
                $route = "agents.index";
                @endphp
                <x-forms.filter-leads :route="$route" :centers="$centers"/>
            @endcan
        </div>
      </div>



      <div x-data="{page: 0}"
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

       class=" lg:h-[calc(100vh-3.5rem)] pt-7 pb-12 lg:pb-0  bg-base-200 w-full flex flex-col lg:flex-row space-y-4 lg:space-y-0 items-center lg:items-start justify-evenly">


        <x-tables.agents-table :agents="$agents"/>



        <div
            x-data="{
                mode: 'add',
            }"
            class=" w-[96%] lg:w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div x-show="mode=='add'" x-transition>
                <h2 class="text-lg font-semibold text-secondary ">Add Agent</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="agent-add-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('agent-add-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('agents.store')}}', formData: fd, target: 'agent-add-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        if($el.id == $event.detail.target){
                            console.log($event.detail);
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Agent Added!'});$dispatch('linkaction', {link: '{{route('agents.index')}}', route: 'agents.index'});
                            } else {
                                $dispatch('showtoast', {mode: 'error', message: $event.detail.content.message});
                            }


                        }
                        "
                        >
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Name</span>
                            </label>
                            <input type="text" name="name" placeholder="Name" class="input input-bordered w-full max-w-xs" />
                        </div>

                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Email</span>
                            </label>
                            <input type="email" name="email" placeholder="Email" class="input input-bordered w-full max-w-xs" />
                        </div>

                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Password</span>
                            </label>
                            <input type="password" name="password" placeholder="Password" class="input input-bordered w-full max-w-xs" />
                        </div>

                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Confirm Password</span>
                            </label>
                            <input type="password" name="password_confirmation" placeholder="Confirm password" class="input input-bordered w-full max-w-xs" />
                        </div>


                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Select Center</span>
                            </label>
                            <select name="center" id="agent-center" required class=" select text-base-content w-full max-w-xs select-bordered">
                                <option value="" disabled selected>-- choose center --</option>
                                @foreach ($centers as $center)
                                    <option value="{{$center->id}}">{{$center->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-center py-8">
                            <button type="submit" class="btn btn-sm btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- agent edit window --}}
            <div
                x-data="{
                    id: '',
                    name: '',
                    email: '',
                    center_id: '',
                    reset() {
                        this.id = '';
                        this.name = '';
                        this.email = '';
                        this.center_id = '';
                        mode = 'add';
                    }
                }"
                x-show="mode=='edit'"
                @agentedit.window="
                    id = $event.detail.id;
                    name = $event.detail.name;
                    email = $event.detail.email;
                    center_id = $event.detail.center_id;
                    mode='edit';
                "  x-transition>
                <h2 class="text-lg font-semibold text-primary ">Edit Agent</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="agent-edit-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('agent-edit-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('agents.update', '_X_')}}'.replace('_X_', id), formData: fd, target: 'agent-edit-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        if($el.id == $event.detail.target){
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: $event.detail.content.message});
                                let params = {
                                    page: page
                                };
                                $dispatch('linkaction', {link: '{{route('agents.index')}}', route: 'agents.index', params: params, fresh: true});
                            } else {
                                $dispatch('showtoast', {mode: 'error', message: 'Failed to update agent. Please make sure you have entered all details.'});
                            }
                        }
                        "
                        >
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Name</span>
                            </label>
                            <input type="text" name="name" x-model="name" placeholder="Name" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                            <span class="label-text">Email</span>
                            </label>
                            <input type="email" name="email" x-model="email" placeholder="Email" class="input input-bordered w-full max-w-xs" />
                        </div>



                        <div class="text-center py-8">
                            <button type="submit" class="btn btn-sm btn-secondary bg-secondary">Update</button><br/><br/>
                            <button @click.prevent.stop="reset();" type="button" class="btn btn-ghost btn-xs">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- attendance window --}}

            <div x-data="{
                name:'',
                id: null,
                audits:[],
                auditsLoading: false,
                fetchaudits(){
                    this.auditsLoading = true;
                    axios.get('/fetch/audits',{
                        params:{
                            user_id : this.id
                        }
                    }).then((r)=>{
                        console.log(r.data);
                        this.audits = r.data;
                    }).catch((e)=>{
                        this.$dispatch('showtoast',{mode:'error',message: 'Could not load audits'});
                    });
                    this.auditsLoading = false;
                },
                formatDate(isoDateString) {
                    const options = { year: 'numeric', month: 'short', day: '2-digit' };
                    return new Date(isoDateString).toLocaleDateString('en-US', options);
                },
                formatTime(isoDateString) {
                    const date = new Date(isoDateString);
                    const hours = (date.getHours() % 12 || 12).toString().padStart(2, '0');
                    const minutes = date.getMinutes().toString().padStart(2, '0');
                    const ampm = date.getHours() >= 12 ? 'PM' : 'AM';

                    return `${hours}:${minutes} ${ampm}`;
                }
            }" x-show="mode=='attendance'"
            @agentattendance.window="
                    mode='attendance';
                    id=$event.detail.id;
                    name=$event.detail.name;
                    fetchaudits();
                " x-transition>
                <h1 class=" text-primary font-medium text-lg" x-text="'Attendance of '+name"></h1>

                <div x-show="auditsLoading" class=" w-full flex flex-col space-y-2 justify-center items-center py-8">
                    <span class="loading loading-bars loading-md "></span>
                    <label for="">Please wait while we load the audits...</label>
                </div>
                <div x-show="!auditsLoading">
                    {{-- Showing audits --}}
                    <template x-if="audits.length > 0">
                        <template x-for="audit in audits">
                            <div class="flex flex-col text-xs p-1 my-1 bg-base-200 rounded-md">
                                <p class=" text-secondary font-medium" x-text='"Date : "+formatDate(audit.created_at) '></p>
                                <p class=" flex space-x-2">
                                    <span x-text="'Login : '+formatTime(audit.login)"></span>
                                    <span>|</span>
                                    <span x-text="'Logout : '+formatTime(audit.logout)"></span>
                                </p>
                                <p class=" flex space-x-2">
                                    <span x-text=" audit.break_in != null ? 'Break-in : '+formatTime(audit.break_in) : 'Break-in : Unavailable'"></span>
                                    <span>|</span>
                                    <span x-text=" audit.break_out != null ? 'Break-out : '+formatTime(audit.break_out) : 'Break-out : Unavailable' "></span>
                                </p>
                            </div>
                        </template>
                    </template>
                    <template x-if="audits.length < 1">
                        <div class=" p-4 w-full text-center font-medium text-error">
                            No audits available
                        </div>
                    </template>
                </div>

                <div class=" flex justify-center">
                    <button @click.prevent.stop="mode='add'" type="button" class="btn btn-ghost btn-xs">Cancel</button>
                </div>
            </div>


        </div>

      </div>
    </div>
  </div>
  <x-footer/>

</x-easyadmin::app-layout>
