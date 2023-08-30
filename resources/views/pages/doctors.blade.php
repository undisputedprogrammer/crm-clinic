<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}



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

       class=" h-[calc(100vh-3.5rem)] mt-14 pt-7 pb-3  bg-base-200 w-full flex justify-evenly">




        <x-tables.doctors-table :doctors="$doctors"/>



        <div
            x-data="{
                mode: 'add',
            }"
            class="w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
            <div x-show="mode=='add'" x-transition>
                <h2 class="text-lg font-semibold text-secondary ">Add Doctor</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="doctor-add-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('doctor-add-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('doctors.store')}}', formData: fd, target: 'doctor-add-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                        console.log($event.detail);
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Doctor Added!'});$dispatch('linkaction', {link: '{{route('doctors.index')}}', route: 'doctors.index'});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add doctor. Please make sure you have entered all details.'});
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
                            <span class="label-text">Department</span>
                            </label>
                            <input type="text" name="department" placeholder="Department" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="text-center py-8">
                            <button type="submit" class="btn btn-sm btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
            <div
                x-data="{
                    id: '',
                    name: '',
                    department: '',
                    reset() {
                        this.id = '';
                        this.name = '';
                        this.department = '';
                        mode = 'add';
                    }
                }"
                x-show="mode=='edit'"
                @doctoredit.window="
                    id = $event.detail.id;
                    name = $event.detail.name;
                    department = $event.detail.department;
                    mode='edit';
                "  x-transition>
                <h2 class="text-lg font-semibold text-primary ">Edit Doctor</h2>
                <div class=" mt-2 flex flex-col space-y-2">
                    <form id="doctor-edit-form"
                        x-data="{
                            doSubmit() {
                                let form = document.getElementById('doctor-edit-form');
                                let fd = new FormData(form);
                                $dispatch('formsubmit', {url: '{{route('doctors.update', '_X_')}}'.replace('_X_', id), formData: fd, target: 'doctor-edit-form'});
                            }
                        }"
                        class="flex flex-col items-center"
                        @submit.prevent.stop="doSubmit();"
                        @formresponse.window="
                            if ($event.detail.content.success) {
                                $dispatch('showtoast', {mode: 'success', message: 'Doctor Updated!'});
                                let params = {
                                    page: page
                                };
                                $dispatch('linkaction', {link: '{{route('doctors.index')}}', route: 'doctors.index', params: params, fresh: true});
                            } else {
                                $dispatch('shownotice', {mode: 'error', message: 'Failed to add doctor. Please make sure you have entered all details.'});
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
                            <span class="label-text">Department</span>
                            </label>
                            <input type="text" name="department" x-model="department" placeholder="Department" class="input input-bordered w-full max-w-xs" />
                        </div>
                        <div class="text-center py-8">
                            <button type="submit" class="btn btn-sm btn-secondary bg-secondary">Update</button><br/><br/>
                            <button @click.prevent.stop="reset();" type="button" class="btn btn-ghost btn-xs">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>

</x-easyadmin::app-layout>
