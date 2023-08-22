<x-easyadmin::app-layout>

<div x-data="{
            showresults : false,
            searchtype : '',


        }"

        >
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-base-100  text-black ">

      <!-- Header -->
      <x-display.header/>

      {{-- page body --}}
      <div class="min-h-[calc(100vh-3.5rem)] mt-14 pt-7 bg-base-200 w-full ">
            <h1 class=" text-secondary text-xl font-medium text-center">Leads - Advanced Search</h1>

            <form
            x-data = "{ doSubmit() {
                let form = document.getElementById('search-form');
                let formdata = new FormData(form);
                formdata.append('search_type',searchtype);
                {{-- formdata.append('remarkable_type','lead'); --}}
                $dispatch('formsubmit',{url:'{{route('get-results')}}', route: 'get-results',fragment: 'page-content', formData: formdata, target: 'search-form'});
            }}"

            @submit.prevent.stop="doSubmit();"

            @formresponse.window="
                        console.log($event.detail.content);
                        if ($event.detail.target == $el.id) {
                            if ($event.detail.content.success) {

                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'success'});

                                $dispatch('resultupdate',{html: $event.detail.content.table_html});

                                $dispatch('formerrors', {errors: []});
                            } else if (typeof $event.detail.content.errors != undefined) {
                                $dispatch('showtoast', {message: $event.detail.content.message, mode: 'error'});

                            } else{
                                $dispatch('formerrors', {errors: $event.detail.content.errors});
                            }
                        }"
            id="search-form"
             action="" class=" flex justify-start items-end space-x-3 py-2 px-14">

                <select @change.prevent.stop="searchtype = $el.value;" class="select  select-bordered w-full max-w-xs bg-base-100 text-neutral-content">
                    <option disabled selected>--Search by--</option>
                    <option value="scheduled_date">Scheduled date</option>
                    <option value="actual_date">Actual date</option>
                </select>

                <div>
                    <label for="from-date" class="text-sm text-primary font-medium">Select from date</label>
                    <input id="from-date" type="date" :required = "searchtype == 'scheduled_date' || searchtype == 'actual_date' ? true : false " placeholder="from date" name="from_date" class="input input-bordered w-full max-w-xs text-neutral-content" />
                </div>

                <div>
                    <label for="to-date" class=" text-sm text-primary font-medium">Select to date</label>
                    <input id="to-date" type="date" :required = "searchtype == 'scheduled_date' || searchtype == 'actual_date' ? true : false " placeholder="to date" name="to_date" class="input input-bordered w-full max-w-xs text-neutral-content" />
                </div>

                <button :disabled = " searchtype == '' ? true : false " class=" btn btn-primary" type="submit">Search</button>


            </form>


            {{-- results section --}}

            <div class="w-full flex justify-evenly items-start my-4">

                <div @resultupdate.window="
                showresults=true;
                $el.innerHTML = $event.detail.html;" id="result-table" class="w-[40%]">

                </div>

                <div x-show="showresults"
                    x-data = "{


                    }"
                    class="w-[50%] min-h-[16rem] max-h-[100%] h-fit hide-scroll overflow-y-scroll  bg-base-100 text-neutral-content rounded-xl p-3 xl:px-6 py-3">

                </div>
            </div>
      </div>

    </div>

</div>

</x-easyadmin::app-layout>
