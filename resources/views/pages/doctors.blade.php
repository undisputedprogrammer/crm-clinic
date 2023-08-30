<x-easyadmin::app-layout>
<div >
    <div class=" flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}



      <div

        {{-- pagination event handler --}}
        @pageaction.window="
        console.log($event.detail);
        $dispatch('linkaction',{
            link: $event.detail.link,
            route: currentroute,
            fragment: 'page-content',
        })"

       class=" h-[calc(100vh-3.5rem)] mt-14 pt-7 pb-3  bg-base-200 w-full flex justify-evenly">




        <x-tables.doctors-table :doctors="$doctors"/>



        <div class="w-[35%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-base-content rounded-xl p-3 xl:px-6 py-3">
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
                        $dispatch('showtoast', {mode: 'success', message: 'Doctor Created!'});
                        $dispatch('linkaction', {link: '{{route('doctors.index')}}', route: 'doctors.index'});
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

      </div>
    </div>
  </div>

</x-easyadmin::app-layout>
